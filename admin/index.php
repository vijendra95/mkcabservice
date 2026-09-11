<?php
// MK Cab Service — admin panel (bookings, routes, tours, pages, blog, reviews, FAQs, media, settings).
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/media-lib.php';

const ADMIN_DEFAULT_PASSWORD = 'mkcab123';
$DATA_DIR = dirname(__DIR__) . '/data';
$ROUTES_FILE = $DATA_DIR . '/routes.json';
$ADMIN_FILE = $DATA_DIR . '/admin.json';
$SETTINGS_FILE = $DATA_DIR . '/settings.json';
$POSTS_FILE = $DATA_DIR . '/posts.json';
$BOOKINGS_FILE = $DATA_DIR . '/bookings.json';

function admin_password_hash(string $file): string {
    if (is_file($file)) {
        $j = json_decode((string)file_get_contents($file), true);
        if (!empty($j['password_hash'])) return $j['password_hash'];
    }
    return password_hash(ADMIN_DEFAULT_PASSWORD, PASSWORD_DEFAULT);
}

function save_json(string $file, $data): bool {
    $dir = dirname($file);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
}

function admin_posts(): array {
    global $POSTS_FILE;
    return is_file($POSTS_FILE) ? (json_decode((string)file_get_contents($POSTS_FILE), true) ?: []) : [];
}

function admin_bookings(): array {
    global $BOOKINGS_FILE;
    return is_file($BOOKINGS_FILE) ? (json_decode((string)file_get_contents($BOOKINGS_FILE), true) ?: []) : [];
}

function make_slug_from(string $text, string $suffix = ''): string {
    $s = strtolower($text . $suffix);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim(preg_replace('/-+/', '-', $s), '-');
}

// Reviews / FAQs / tours share one list store (data/<name>.json).
function admin_list(string $name): array {
    global $REVIEWS_DEFAULT, $FAQS_DEFAULT, $TOURS_DEFAULT;
    $defaults = ['reviews' => $REVIEWS_DEFAULT, 'faqs' => $FAQS_DEFAULT, 'tours' => $TOURS_DEFAULT];
    return data_list($name, $defaults[$name] ?? []);
}

function save_list(string $name, array $list): bool {
    global $DATA_DIR, $DATA_LISTS;
    $list = array_values($list);
    if (!save_json($DATA_DIR . '/' . $name . '.json', $list)) return false;
    $DATA_LISTS[$name] = $list;
    return true;
}

/** Insert or replace an item by id; new items go to the end. */
function upsert_item(array $list, array $item): array {
    foreach ($list as $i => $it) {
        if (($it['id'] ?? '') === $item['id']) { $list[$i] = $item; return $list; }
    }
    $list[] = $item;
    return $list;
}

function find_item(array $list, string $id): ?array {
    foreach ($list as $it) if (($it['id'] ?? '') === $id) return $it;
    return null;
}

function new_id(string $prefix): string {
    return $prefix . substr(bin2hex(random_bytes(4)), 0, 7);
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];
$logged_in = !empty($_SESSION['mk_admin']);
$msg = '';
$err = '';
$WRITE_ERR = 'Save nahi hua — data/ folder writable nahi hai. Hostinger File Manager me data folder ki permission 755 karein.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        if (password_verify($_POST['password'] ?? '', admin_password_hash($ADMIN_FILE))) {
            $_SESSION['mk_admin'] = true;
            session_regenerate_id(true);
            header('Location: /admin/');
            exit;
        }
        $err = 'Galat password. Dobara try karein.';
    } elseif (!$logged_in) {
        $err = 'Session expire ho gaya — dobara login karein.';
    } elseif (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $err = 'Form expire ho gaya — page refresh karke dobara try karein.';
    } elseif ($action === 'logout') {
        session_destroy();
        header('Location: /admin/');
        exit;
    } elseif ($action === 'booking_status') {
        $id = $_POST['id'] ?? '';
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['new', 'contacted', 'done'], true)) {
            $list = admin_bookings();
            foreach ($list as $i => $b) {
                if ($b['id'] === $id) { $list[$i]['status'] = $status; break; }
            }
            if (save_json($BOOKINGS_FILE, $list)) { $msg = 'Booking status update ho gaya.'; } else { $err = $WRITE_ERR; }
        }
    } elseif ($action === 'delete_booking') {
        $id = $_POST['id'] ?? '';
        $list = array_values(array_filter(admin_bookings(), fn($b) => $b['id'] !== $id));
        if (save_json($BOOKINGS_FILE, $list)) { $msg = 'Enquiry delete ho gayi.'; } else { $err = $WRITE_ERR; }
    } elseif ($action === 'save_route') {
        $from = trim($_POST['from'] ?? '');
        $to = trim($_POST['to'] ?? '');
        $orig = trim($_POST['orig_slug'] ?? '');
        if ($from === '' || $to === '') {
            $err = 'From aur To dono bharna zaroori hai.';
        } else {
            $slug = $orig !== '' ? $orig : make_slug_from("$from-to-$to", '-one-way-taxi');
            $ROUTES[$slug] = [
                'from' => $from,
                'to' => $to,
                'km' => max(1, (int)($_POST['km'] ?? 0)),
                'time' => trim($_POST['time'] ?? ''),
                'sedan' => max(0, (int)($_POST['sedan'] ?? 0)),
                'suv' => max(0, (int)($_POST['suv'] ?? 0)),
                'innova' => max(0, (int)($_POST['innova'] ?? 0)),
                'tempo' => max(0, (int)($_POST['tempo'] ?? 0)),
                'tag' => trim($_POST['tag'] ?? ''),
            ];
            if (save_json($ROUTES_FILE, $ROUTES)) {
                $msg = ($orig !== '' ? 'Route update ho gaya' : 'Naya route add ho gaya') . ": $from → $to";
            } else {
                $err = $WRITE_ERR;
            }
        }
    } elseif ($action === 'delete_route') {
        $slug = $_POST['slug'] ?? '';
        if (isset($ROUTES[$slug])) {
            $name = $ROUTES[$slug]['from'] . ' → ' . $ROUTES[$slug]['to'];
            unset($ROUTES[$slug]);
            $msg = save_json($ROUTES_FILE, $ROUTES) ? "Route delete ho gaya: $name" : '';
            if ($msg === '') $err = $WRITE_ERR;
        }
    } elseif ($action === 'save_page') {
        $page = $_POST['page'] ?? '';
        if (!isset($PAGE_DEFAULTS[$page])) {
            $err = 'Page nahi mila.';
        } else {
            $data = [];
            foreach (array_keys($PAGE_DEFAULTS[$page]['fields']) as $key) {
                $val = (string)($_POST['field_' . $key] ?? '');
                // Saving the exact default keeps the JSON clean.
                if (trim($val) !== '' && $val !== $PAGE_DEFAULTS[$page]['fields'][$key]) {
                    $data[$key] = $val;
                }
            }
            if (save_json($DATA_DIR . '/pages/' . $page . '.json', $data)) {
                $msg = $PAGE_DEFAULTS[$page]['label'] . ' save ho gaya.';
            } else {
                $err = $WRITE_ERR;
            }
        }
    } elseif ($action === 'save_post') {
        $title = trim($_POST['title'] ?? '');
        $orig = trim($_POST['orig_slug'] ?? '');
        $body = (string)($_POST['body'] ?? '');
        if ($title === '' || trim($body) === '') {
            $err = 'Title aur content dono bharna zaroori hai.';
        } else {
            $posts = admin_posts();
            $slug = $orig !== '' ? $orig : make_slug_from($title);
            $img_err = null;
            $entry = [
                'slug' => $slug,
                'title' => $title,
                'date' => trim($_POST['date'] ?? '') ?: date('j M Y'),
                'excerpt' => trim($_POST['excerpt'] ?? ''),
                'image' => media_from_form('image', 'image_file', $img_err),
                'seo_title' => trim($_POST['seo_title'] ?? ''),
                'seo_desc' => trim($_POST['seo_desc'] ?? ''),
            ];
            if ($img_err) $err = 'Thumbnail: ' . $img_err;
            $found = false;
            foreach ($posts as $i => $p) {
                if ($p['slug'] === $slug) { $posts[$i] = $entry; $found = true; break; }
            }
            if (!$found) array_unshift($posts, $entry);
            $ok = save_json($POSTS_FILE, $posts);
            if ($ok) {
                if (!is_dir($DATA_DIR . '/posts')) mkdir($DATA_DIR . '/posts', 0755, true);
                $ok = file_put_contents($DATA_DIR . '/posts/' . $slug . '.html', $body) !== false;
            }
            if ($ok) {
                $msg = ($found ? 'Post update ho gaya: ' : 'Naya post publish ho gaya: ') . $title;
            } else {
                $err = $WRITE_ERR;
            }
        }
    } elseif ($action === 'save_tour') {
        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            $err = 'Package ka title bharna zaroori hai.';
        } else {
            $img_err = null;
            $id = trim($_POST['id'] ?? '') ?: new_id('t');
            $item = [
                'id' => $id,
                'title' => $title,
                'duration' => trim($_POST['duration'] ?? ''),
                'price' => trim($_POST['price'] ?? ''),
                'image' => media_from_form('image', 'image_file', $img_err),
                'desc' => trim($_POST['desc'] ?? ''),
                'highlights' => trim(str_replace("\r", '', $_POST['highlights'] ?? '')),
            ];
            if (save_list('tours', upsert_item(admin_list('tours'), $item))) {
                $msg = 'Tour package save ho gaya: ' . $title . ($img_err ? ' (image upload nahi hua: ' . $img_err . ')' : '');
            } else {
                $err = $WRITE_ERR;
            }
        }
    } elseif ($action === 'delete_tour') {
        $id = $_POST['id'] ?? '';
        if (save_list('tours', array_filter(admin_list('tours'), fn($t) => ($t['id'] ?? '') !== $id))) { $msg = 'Tour package delete ho gaya.'; } else { $err = $WRITE_ERR; }
    } elseif ($action === 'save_review') {
        $name = trim($_POST['name'] ?? '');
        $text = trim($_POST['text'] ?? '');
        if ($name === '' || $text === '') {
            $err = 'Customer ka naam aur review dono bharna zaroori hai.';
        } else {
            $item = [
                'id' => trim($_POST['id'] ?? '') ?: new_id('r'),
                'name' => $name,
                'trip' => trim($_POST['trip'] ?? ''),
                'stars' => max(1, min(5, (int)($_POST['stars'] ?? 5))),
                'text' => $text,
            ];
            if (save_list('reviews', upsert_item(admin_list('reviews'), $item))) { $msg = 'Review save ho gaya: ' . $name; } else { $err = $WRITE_ERR; }
        }
    } elseif ($action === 'delete_review') {
        $id = $_POST['id'] ?? '';
        if (save_list('reviews', array_filter(admin_list('reviews'), fn($r) => ($r['id'] ?? '') !== $id))) { $msg = 'Review delete ho gaya.'; } else { $err = $WRITE_ERR; }
    } elseif ($action === 'save_faq') {
        $q = trim($_POST['q'] ?? '');
        $a = trim($_POST['a'] ?? '');
        if ($q === '' || $a === '') {
            $err = 'Question aur answer dono bharna zaroori hai.';
        } else {
            $item = ['id' => trim($_POST['id'] ?? '') ?: new_id('f'), 'q' => $q, 'a' => $a];
            if (save_list('faqs', upsert_item(admin_list('faqs'), $item))) { $msg = 'FAQ save ho gaya.'; } else { $err = $WRITE_ERR; }
        }
    } elseif ($action === 'delete_faq') {
        $id = $_POST['id'] ?? '';
        if (save_list('faqs', array_filter(admin_list('faqs'), fn($f) => ($f['id'] ?? '') !== $id))) { $msg = 'FAQ delete ho gaya.'; } else { $err = $WRITE_ERR; }
    } elseif ($action === 'move_item') {
        $list_name = $_POST['list'] ?? '';
        $id = $_POST['id'] ?? '';
        $dir = ($_POST['dir'] ?? '') === 'up' ? -1 : 1;
        if (in_array($list_name, ['reviews', 'faqs', 'tours'], true)) {
            $list = admin_list($list_name);
            foreach ($list as $i => $it) {
                if (($it['id'] ?? '') === $id) {
                    $j = $i + $dir;
                    if (isset($list[$j])) { [$list[$i], $list[$j]] = [$list[$j], $list[$i]]; }
                    break;
                }
            }
            if (!save_list($list_name, $list)) $err = $WRITE_ERR;
        }
    } elseif ($action === 'upload_media') {
        $done = 0; $errs = [];
        if (!empty($_FILES['files']['name'][0])) {
            foreach ($_FILES['files']['name'] as $i => $n) {
                $res = media_store(['name' => $n, 'type' => $_FILES['files']['type'][$i], 'tmp_name' => $_FILES['files']['tmp_name'][$i], 'error' => $_FILES['files']['error'][$i], 'size' => $_FILES['files']['size'][$i]]);
                if ($res['ok']) $done++; else $errs[] = $n . ': ' . $res['error'];
            }
        }
        if ($done) $msg = $done . ' image' . ($done > 1 ? 's' : '') . ' upload ho gayi.';
        if ($errs) $err = implode(' | ', $errs);
        if (!$done && !$errs) $err = 'Koi image select nahi ki.';
    } elseif ($action === 'delete_media') {
        if (media_delete($_POST['name'] ?? '')) { $msg = 'Image delete ho gayi.'; } else { $err = 'Image delete nahi hui.'; }
    } elseif ($action === 'delete_post') {
        $slug = basename($_POST['slug'] ?? '');
        $posts = array_values(array_filter(admin_posts(), fn($p) => $p['slug'] !== $slug));
        if (save_json($POSTS_FILE, $posts)) {
            $file = $DATA_DIR . '/posts/' . $slug . '.html';
            if (is_file($file)) unlink($file);
            $msg = 'Post delete ho gaya.';
        } else {
            $err = $WRITE_ERR;
        }
    } elseif ($action === 'save_settings') {
        $new = [
            'site_name' => trim($_POST['site_name'] ?? '') ?: 'MK Cab Service',
            'phone_display' => trim($_POST['phone_display'] ?? ''),
            'whatsapp_number' => preg_replace('/\D+/', '', $_POST['whatsapp_number'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'rate_sedan' => max(1, (int)($_POST['rate_sedan'] ?? 0)),
            'rate_suv' => max(1, (int)($_POST['rate_suv'] ?? 0)),
            'rate_innova' => max(1, (int)($_POST['rate_innova'] ?? 0)),
            'rate_tempo' => max(1, (int)($_POST['rate_tempo'] ?? 0)),
            'allowance_default' => max(0, (int)($_POST['allowance_default'] ?? 0)),
            'allowance_tempo' => max(0, (int)($_POST['allowance_tempo'] ?? 0)),
            'wa_button_text' => trim($_POST['wa_button_text'] ?? '') ?: 'WhatsApp Us',
            'wa_default_msg' => trim($_POST['wa_default_msg'] ?? ''),
            'analytics_code' => trim($_POST['analytics_code'] ?? ''),
        ];
        foreach (['facebook', 'instagram', 'youtube', 'google'] as $sk) {
            $u = trim($_POST['social_' . $sk] ?? '');
            if ($u !== '' && !preg_match('#^https?://#i', $u)) $u = 'https://' . $u;
            $new['social_' . $sk] = $u;
        }
        if (save_json($SETTINGS_FILE, $new)) {
            $msg = 'Settings save ho gayi. Website pe turant live hain.';
            $SETTINGS = array_merge($SETTINGS, $new);
        } else {
            $err = $WRITE_ERR;
        }
    } elseif ($action === 'change_password') {
        $new = $_POST['new_password'] ?? '';
        if (strlen($new) < 6) {
            $err = 'Naya password kam se kam 6 characters ka ho.';
        } elseif (!password_verify($_POST['current_password'] ?? '', admin_password_hash($ADMIN_FILE))) {
            $err = 'Current password galat hai.';
        } else {
            $ok = save_json($ADMIN_FILE, ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
            if ($ok) { $msg = 'Password badal gaya.'; } else { $err = $WRITE_ERR; }
        }
    }
}

$tab = $_GET['tab'] ?? 'bookings';
if (!in_array($tab, ['bookings', 'routes', 'tours', 'pages', 'blog', 'reviews', 'faqs', 'media', 'settings', 'password'], true)) $tab = 'bookings';

$bookings = admin_bookings();
$new_bookings = count(array_filter($bookings, fn($b) => ($b['status'] ?? 'new') === 'new'));
$booking_filter = $_GET['status'] ?? 'all';
if (!in_array($booking_filter, ['all', 'new', 'contacted', 'done'], true)) $booking_filter = 'all';

$edit_slug = $_GET['edit'] ?? '';
$edit_route = ($tab === 'routes' && $edit_slug !== '' && isset($ROUTES[$edit_slug])) ? $ROUTES[$edit_slug] : null;

$edit_page = ($tab === 'pages' && isset($PAGE_DEFAULTS[$_GET['page'] ?? ''])) ? ($_GET['page'] ?? '') : '';

$edit_post = null;
if ($tab === 'blog' && $edit_slug !== '') {
    foreach (admin_posts() as $p) {
        if ($p['slug'] === $edit_slug) { $edit_post = $p; break; }
    }
}
$new_post = ($tab === 'blog' && isset($_GET['new']));

$edit_item = null;
$new_item = isset($_GET['new']);
if (in_array($tab, ['tours', 'reviews', 'faqs'], true) && $edit_slug !== '') {
    $edit_item = find_item(admin_list($tab), $edit_slug);
}
$show_item_form = $edit_item || ($new_item && in_array($tab, ['tours', 'reviews', 'faqs'], true));

function tab_url(string $t): string { return '/admin/?tab=' . $t; }

/** Image picker field: URL input + upload + "choose from media library" (JS in footer). */
function image_field(string $name, string $value): void {
    $v = htmlspecialchars($value);
    echo '<div class="img-field" data-img-field>'
       . '<img class="img-field__prev" src="' . ($v !== '' ? $v : 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27/%3E') . '" alt="" data-prev>'
       . '<div class="img-field__ctl">'
       . '<input name="' . $name . '" value="' . $v . '" placeholder="/assets/uploads/photo.jpg ya https://..." data-url>'
       . '<div class="row">'
       . '<label class="btn btn--ghost btn--sm" style="margin:0;cursor:pointer;color:var(--ink)">Upload new <input type="file" name="' . $name . '_file" accept="image/*" style="display:none" data-file></label>'
       . '<button type="button" class="btn btn--ghost btn--sm" data-pick>Choose from Media Library</button>'
       . '<button type="button" class="btn btn--danger btn--sm" data-clear>Remove</button>'
       . '</div><span class="muted">JPG/PNG/WebP, max 5 MB.</span></div></div>';
}
function move_buttons(string $list, string $id, int $i, int $n, string $csrf): void {
    foreach (['up' => ['↑', $i > 0], 'down' => ['↓', $i < $n - 1]] as $dir => [$arrow, $enabled]) {
        echo '<form method="post" class="inline-form"><input type="hidden" name="action" value="move_item"><input type="hidden" name="csrf" value="' . $csrf . '">'
           . '<input type="hidden" name="list" value="' . $list . '"><input type="hidden" name="id" value="' . htmlspecialchars($id) . '"><input type="hidden" name="dir" value="' . $dir . '">'
           . '<button class="btn btn--ghost btn--sm" type="submit" title="Move ' . $dir . '"' . ($enabled ? '' : ' disabled style="opacity:.35"') . '>' . $arrow . '</button></form> ';
    }
}

function short(string $s, int $n): string {
    if (function_exists('mb_strimwidth')) return mb_strimwidth($s, 0, $n, '…');
    return strlen($s) > $n ? substr($s, 0, $n - 1) . '…' : $s;
}
$needs_editor = ($edit_page !== '') || $edit_post || $new_post;
$needs_picker = $needs_editor || ($tab === 'tours' && $show_item_form) || $tab === 'media';
$media_json = json_encode(array_map(fn($m) => ['url' => $m['url'], 'name' => $m['name'], 'deletable' => $m['deletable']], media_files()), JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin | <?= htmlspecialchars(SITE_NAME) ?></title>
<?php if ($needs_editor): ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@4.2.27/es2021/jodit.fat.min.css">
<script src="https://cdn.jsdelivr.net/npm/jodit@4.2.27/es2021/jodit.fat.min.js"></script>
<?php endif; ?>
<style>
  :root { --orange: #e8590c; --orange-dark: #c74b0a; --navy: #141c3d; --navy-2: #1d2750; --ink: #1c2434; --ink-soft: #5b6472; --line: #e7ebf1; --bg: #f5f6fa; }
  * { box-sizing: border-box; margin: 0; }
  body { font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; background: var(--bg); color: var(--ink); min-height: 100vh; }
  h2 { font-size: 1.02rem; margin-bottom: 14px; letter-spacing: -0.01em; }

  /* ---- Login ---- */
  .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background: linear-gradient(150deg, var(--navy) 0%, #2a3566 100%); }
  .login { background: #fff; border-radius: 16px; padding: 34px 30px; width: 100%; max-width: 400px; box-shadow: 0 24px 60px rgba(10,15,40,.45); }
  .login h1 { font-size: 1.25rem; margin-bottom: 4px; }
  .login .sub { color: var(--ink-soft); font-size: 0.86rem; margin-bottom: 20px; }
  .login-badge { width: 46px; height: 46px; border-radius: 12px; background: var(--orange); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.15rem; margin-bottom: 16px; }

  /* ---- Layout ---- */
  .layout { display: flex; min-height: 100vh; }
  .side { width: 232px; flex-shrink: 0; background: var(--navy); color: #cdd4ee; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
  .brand { padding: 20px 18px 16px; border-bottom: 1px solid rgba(255,255,255,.08); }
  .brand b { color: #fff; font-size: 1.02rem; display: block; letter-spacing: -0.01em; }
  .brand span { font-size: 0.72rem; text-transform: uppercase; letter-spacing: .12em; color: #8d97c4; }
  .nav { padding: 14px 10px; display: flex; flex-direction: column; gap: 3px; flex: 1; }
  .nav a { display: flex; align-items: center; gap: 11px; padding: 10px 12px; border-radius: 9px; color: #cdd4ee; text-decoration: none; font-weight: 600; font-size: 0.89rem; }
  .nav a svg { width: 17px; height: 17px; flex-shrink: 0; opacity: .75; }
  .nav a:hover { background: rgba(255,255,255,.07); color: #fff; }
  .nav a.on { background: var(--orange); color: #fff; }
  .nav a.on svg { opacity: 1; }
  .side__foot { padding: 14px 10px; border-top: 1px solid rgba(255,255,255,.08); display: flex; flex-direction: column; gap: 3px; }
  .side__foot a, .side__foot button { display: flex; align-items: center; gap: 11px; width: 100%; padding: 9px 12px; border: 0; border-radius: 9px; background: none; color: #8d97c4; text-align: left; font: inherit; font-weight: 600; font-size: 0.85rem; text-decoration: none; cursor: pointer; }
  .side__foot a:hover, .side__foot button:hover { background: rgba(255,255,255,.07); color: #fff; }
  .main { flex: 1; min-width: 0; padding: 26px 30px 70px; }
  .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
  .topbar h1 { font-size: 1.3rem; letter-spacing: -0.02em; }
  .topbar .crumb { color: var(--ink-soft); font-size: 0.82rem; margin-top: 2px; }

  /* ---- Components ---- */
  .card { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 22px; box-shadow: 0 1px 3px rgba(16,24,40,.05); margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; font-size: 0.89rem; }
  th, td { text-align: left; padding: 10px 11px; border-bottom: 1px solid var(--line); }
  th { font-size: 0.74rem; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-soft); white-space: nowrap; background: #fafbfd; }
  tbody tr:hover { background: #fafbfd; }
  tbody tr:last-child td { border-bottom: 0; }
  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; }
  .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--ink-soft); margin-bottom: 5px; }
  input, textarea { width: 100%; padding: 10px 12px; border: 1px solid #d4d9e0; border-radius: 9px; font-size: 0.92rem; font-family: inherit; background: #fff; transition: border-color .12s, box-shadow .12s; }
  input:focus, textarea:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(232,89,12,.13); }
  .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 10px 18px; border: 0; border-radius: 9px; font-weight: 700; font-size: 0.88rem; cursor: pointer; text-decoration: none; font-family: inherit; transition: background .12s; }
  .btn--primary { background: var(--orange); color: #fff; }
  .btn--primary:hover { background: var(--orange-dark); }
  .btn--ghost { background: #eef1f6; color: var(--ink); }
  .btn--ghost:hover { background: #e3e8ef; }
  .btn--danger { background: #fee2e2; color: #b91c1c; }
  .btn--danger:hover { background: #fecaca; }
  .btn--sm { padding: 6px 12px; font-size: 0.8rem; white-space: nowrap; }
  .btn--block { width: 100%; }
  .msg { padding: 12px 15px; border-radius: 10px; margin-bottom: 18px; font-size: 0.9rem; font-weight: 600; display: flex; gap: 9px; align-items: flex-start; }
  .msg--ok { background: #ecfdf3; color: #067647; border: 1px solid #abefc6; }
  .msg--err { background: #fef3f2; color: #b42318; border: 1px solid #fecdca; }
  .muted { color: #7a8494; font-size: 0.8rem; }
  .actions { margin-top: 18px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  .field { margin-bottom: 14px; }
  .hint { background: #fff8f1; border: 1px solid #ffd9b3; color: #7c3b00; padding: 11px 14px; border-radius: 10px; font-size: 0.83rem; margin-bottom: 16px; line-height: 1.55; }
  .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; background: #eef1f6; color: var(--ink-soft); }
  .pill--new { background: #fff1e6; color: var(--orange-dark); }
  .pill--contacted { background: #e8f0fe; color: #1d4ed8; }
  .pill--done { background: #ecfdf3; color: #067647; }
  .pill--booking { background: #fdf2f8; color: #be185d; }
  .pill--contact { background: #eef2ff; color: #4338ca; }
  .pill--estimate { background: #f0fdf4; color: #15803d; }
  .badge { margin-left: auto; background: #fff; color: var(--orange-dark); font-size: 0.7rem; font-weight: 800; padding: 2px 7px; border-radius: 999px; min-width: 20px; text-align: center; }
  .nav a:not(.on) .badge { background: var(--orange); color: #fff; }
  .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; margin-bottom: 20px; }
  .stat { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(16,24,40,.05); }
  .stat b { display: block; font-size: 1.6rem; letter-spacing: -0.02em; }
  .stat span { font-size: 0.78rem; color: var(--ink-soft); font-weight: 600; }
  .filters { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
  .filters a { padding: 6px 13px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; text-decoration: none; background: #eef1f6; color: var(--ink-soft); }
  .filters a.on { background: var(--navy); color: #fff; }
  .enq { font-size: 0.86rem; line-height: 1.55; }
  .enq b { color: var(--ink-soft); font-weight: 600; }
  .enq a { color: var(--orange-dark); font-weight: 700; text-decoration: none; }
  .inline-form { display: inline; }
  select { padding: 6px 10px; border: 1px solid #d4d9e0; border-radius: 8px; font: inherit; font-size: 0.8rem; background: #fff; }
  select.input { width: 100%; padding: 10px 12px; border-radius: 9px; font-size: 0.92rem; }
  .section-title { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .08em; color: var(--ink-soft); margin: 22px 0 12px; padding-top: 16px; border-top: 1px dashed var(--line); }
  .thumb { width: 64px; height: 44px; object-fit: cover; border-radius: 7px; background: #eef1f6; display: block; }
  .thumb--empty { display: flex; align-items: center; justify-content: center; color: #b0b8c6; font-size: 0.7rem; }
  .img-field { display: flex; gap: 12px; align-items: flex-start; flex-wrap: wrap; }
  .img-field__prev { width: 150px; height: 96px; border-radius: 10px; object-fit: cover; background: #eef1f6; border: 1px solid var(--line); flex-shrink: 0; }
  .img-field__ctl { flex: 1; min-width: 220px; display: flex; flex-direction: column; gap: 8px; }
  .img-field__ctl .row { display: flex; gap: 8px; flex-wrap: wrap; }
  .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 14px; }
  .media-item { background: #fff; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
  .media-item img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; background: #eef1f6; }
  .media-item__body { padding: 8px 10px; font-size: 0.74rem; color: var(--ink-soft); word-break: break-all; }
  .media-item__act { display: flex; gap: 5px; margin-top: 6px; }
  .media-item__act .btn { padding: 4px 8px; font-size: 0.72rem; flex: 1; white-space: nowrap; }
  .drop { border: 2px dashed #cfd6e0; border-radius: 12px; padding: 22px; text-align: center; color: var(--ink-soft); background: #fafbfd; }
  .drop input { display: none; }
  .stars-in { color: #f59e0b; letter-spacing: 2px; }
  .modal { position: fixed; inset: 0; background: rgba(10,15,40,.55); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
  .modal.open { display: flex; }
  .modal__box { background: #fff; border-radius: 16px; width: 100%; max-width: 900px; max-height: 88vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 30px 80px rgba(0,0,0,.4); }
  .modal__head { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--line); }
  .modal__head h3 { font-size: 1rem; }
  .modal__body { padding: 18px; overflow: auto; }
  .modal__body .media-item { cursor: pointer; transition: box-shadow .12s, transform .12s; }
  .modal__body .media-item:hover { box-shadow: 0 0 0 3px rgba(232,89,12,.35); transform: translateY(-2px); }
  .jodit-container:not(.jodit_inline) { border-radius: 9px; }

  @media (max-width: 860px) {
    .layout { flex-direction: column; }
    .side { width: 100%; height: auto; position: static; }
    .nav { flex-direction: row; flex-wrap: wrap; padding: 10px; }
    .nav a { padding: 8px 13px; font-size: 0.84rem; }
    .side__foot { flex-direction: row; }
    .main { padding: 18px 14px 60px; }
    .table-scroll { overflow-x: auto; }
    .grid2 { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>
<?php if (!$logged_in): ?>
<div class="login-wrap">
  <div class="login">
    <div class="login-badge">MK</div>
    <h1><?= htmlspecialchars(SITE_NAME) ?></h1>
    <p class="sub">Admin panel — login karein</p>
    <?php if ($err): ?><div class="msg msg--err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="action" value="login">
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" autofocus required>
      </div>
      <button class="btn btn--primary btn--block" type="submit">Login</button>
    </form>
  </div>
</div>
<?php else: ?>
<?php
$TAB_TITLES = [
    'bookings' => ['Bookings', 'Website se aayi booking aur contact enquiries'],
    'routes' => ['Routes & Fares', 'One-way routes aur fares manage karein'],
    'tours' => ['Tour Packages', 'Tour packages add/edit/delete — tour-packages page pe dikhte hain'],
    'pages' => ['Pages & SEO', 'Website ke pages ka content aur SEO meta edit karein'],
    'blog' => ['Blog', 'Blog posts likhein aur manage karein'],
    'reviews' => ['Reviews', 'Home page ke customer reviews'],
    'faqs' => ['FAQs', 'Home page ke sawal-jawab'],
    'media' => ['Media Library', 'Images upload karein — pages, blog aur tours me use karne ke liye'],
    'settings' => ['Settings', 'Business details, WhatsApp, social links, Google Analytics, rates'],
    'password' => ['Password', 'Admin password badlein'],
];
$ICONS = [
    'bookings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>',
    'routes' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="19" r="2"/><circle cx="18" cy="5" r="2"/><path d="M8 19h8.5a3.5 3.5 0 0 0 0-7h-9a3.5 3.5 0 0 1 0-7H16"/></svg>',
    'pages' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>',
    'blog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>',
    'tours' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
    'reviews' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg>',
    'faqs' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/></svg>',
    'media' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    'password' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
];
?>
<div class="layout">
  <aside class="side">
    <div class="brand"><b><?= htmlspecialchars(SITE_NAME) ?></b><span>Admin Panel</span></div>
    <nav class="nav">
      <?php foreach ($TAB_TITLES as $t => $info): ?>
      <a href="<?= tab_url($t) ?>" class="<?= $tab === $t ? 'on' : '' ?>"><?= $ICONS[$t] ?><?= htmlspecialchars($info[0]) ?><?php if ($t === 'bookings' && $new_bookings > 0): ?><span class="badge"><?= $new_bookings ?></span><?php endif; ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="side__foot">
      <a href="/" target="_blank"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>Website dekhein</a>
      <form method="post">
        <input type="hidden" name="action" value="logout">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <button type="submit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>Logout</button>
      </form>
    </div>
  </aside>
  <main class="main">
  <div class="topbar">
    <div>
      <h1><?= htmlspecialchars($TAB_TITLES[$tab][0]) ?></h1>
      <div class="crumb"><?= htmlspecialchars($TAB_TITLES[$tab][1]) ?></div>
    </div>
  </div>

  <?php if ($msg): ?><div class="msg msg--ok">✓ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="msg msg--err">✕ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<?php if ($tab === 'bookings'): ?>
  <?php
  $counts = ['all' => count($bookings), 'new' => 0, 'contacted' => 0, 'done' => 0];
  foreach ($bookings as $b) { $counts[$b['status'] ?? 'new'] = ($counts[$b['status'] ?? 'new'] ?? 0) + 1; }
  $TYPE_LABELS = ['booking' => 'Booking', 'contact' => 'Contact', 'estimate' => 'Fare estimate'];
  $FIELD_LABELS = ['trip' => 'Trip', 'pickup' => 'Pickup', 'drop' => 'Drop', 'date' => 'Date', 'time' => 'Time', 'name' => 'Name', 'phone' => 'Mobile', 'email' => 'Email', 'message' => 'Message', 'route' => 'Route', 'car' => 'Car', 'estimate' => 'Estimate'];
  $shown = array_values(array_filter($bookings, fn($b) => $booking_filter === 'all' || ($b['status'] ?? 'new') === $booking_filter));
  ?>
  <div class="stats">
    <div class="stat"><b><?= $counts['all'] ?></b><span>Total enquiries</span></div>
    <div class="stat"><b style="color:var(--orange)"><?= $counts['new'] ?></b><span>New (pending)</span></div>
    <div class="stat"><b style="color:#1d4ed8"><?= $counts['contacted'] ?></b><span>Contacted</span></div>
    <div class="stat"><b style="color:#067647"><?= $counts['done'] ?></b><span>Done</span></div>
  </div>
  <div class="card">
    <div class="filters">
      <?php foreach (['all' => 'Sab', 'new' => 'New', 'contacted' => 'Contacted', 'done' => 'Done'] as $f => $fl): ?>
      <a href="<?= tab_url('bookings') ?>&status=<?= $f ?>" class="<?= $booking_filter === $f ? 'on' : '' ?>"><?= $fl ?> (<?= $counts[$f] ?>)</a>
      <?php endforeach; ?>
    </div>
    <?php if (!$shown): ?>
    <p class="muted" style="padding:20px 0;text-align:center">Abhi koi enquiry nahi hai. Website ke booking form, contact form ya fare estimator se aane wali har enquiry yahan dikhegi (WhatsApp pe bhi jati hai).</p>
    <?php else: ?>
    <div class="table-scroll">
    <table>
      <thead><tr><th>Date</th><th>Type</th><th>Details</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($shown as $b): $st = $b['status'] ?? 'new'; $ph = preg_replace('/\D+/', '', $b['fields']['phone'] ?? ''); ?>
        <tr>
          <td style="white-space:nowrap"><?= htmlspecialchars(date('j M Y', strtotime($b['created_at']))) ?><br><span class="muted"><?= htmlspecialchars(date('g:i A', strtotime($b['created_at']))) ?></span></td>
          <td><span class="pill pill--<?= htmlspecialchars($b['type']) ?>"><?= htmlspecialchars($TYPE_LABELS[$b['type']] ?? $b['type']) ?></span></td>
          <td class="enq">
            <?php foreach ($b['fields'] as $k => $v): ?>
              <b><?= htmlspecialchars($FIELD_LABELS[$k] ?? ucfirst($k)) ?>:</b>
              <?php if ($k === 'phone' && $ph !== ''): ?>
                <a href="tel:<?= htmlspecialchars($ph) ?>"><?= htmlspecialchars($v) ?></a>
                · <a href="https://wa.me/<?= strlen($ph) === 10 ? '91' . $ph : $ph ?>" target="_blank" rel="noopener">WhatsApp</a>
              <?php else: ?>
                <?= nl2br(htmlspecialchars($v)) ?>
              <?php endif; ?><br>
            <?php endforeach; ?>
          </td>
          <td>
            <form method="post" action="<?= tab_url('bookings') ?>&status=<?= $booking_filter ?>" class="inline-form">
              <input type="hidden" name="action" value="booking_status">
              <input type="hidden" name="csrf" value="<?= $csrf ?>">
              <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
              <select name="status" onchange="this.form.submit()">
                <option value="new" <?= $st === 'new' ? 'selected' : '' ?>>New</option>
                <option value="contacted" <?= $st === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                <option value="done" <?= $st === 'done' ? 'selected' : '' ?>>Done</option>
              </select>
            </form>
          </td>
          <td>
            <form method="post" action="<?= tab_url('bookings') ?>&status=<?= $booking_filter ?>" onsubmit="return confirm('Ye enquiry delete karein?')">
              <input type="hidden" name="action" value="delete_booking">
              <input type="hidden" name="csrf" value="<?= $csrf ?>">
              <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
              <button class="btn btn--danger btn--sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
  </div>

<?php elseif ($tab === 'routes'): ?>
  <div class="card">
    <h2><?= $edit_route ? 'Route edit karein: ' . htmlspecialchars($edit_route['from'] . ' → ' . $edit_route['to']) : 'Naya route add karein' ?></h2>
    <form method="post" action="<?= tab_url('routes') ?>">
      <input type="hidden" name="action" value="save_route">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="orig_slug" value="<?= htmlspecialchars($edit_route ? $edit_slug : '') ?>">
      <div class="grid">
        <div><label>From (city)</label><input name="from" value="<?= htmlspecialchars($edit_route['from'] ?? 'Jaipur') ?>" required></div>
        <div><label>To (city)</label><input name="to" value="<?= htmlspecialchars($edit_route['to'] ?? '') ?>" required></div>
        <div><label>Distance (km)</label><input name="km" type="number" min="1" value="<?= (int)($edit_route['km'] ?? '') ?: '' ?>" required></div>
        <div><label>Travel time (e.g. 5h 30m)</label><input name="time" value="<?= htmlspecialchars($edit_route['time'] ?? '') ?>" required></div>
        <div><label>Sedan fare (₹)</label><input name="sedan" type="number" min="0" value="<?= (int)($edit_route['sedan'] ?? '') ?: '' ?>" required></div>
        <div><label>SUV fare (₹)</label><input name="suv" type="number" min="0" value="<?= (int)($edit_route['suv'] ?? '') ?: '' ?>" required></div>
        <div><label>Innova fare (₹)</label><input name="innova" type="number" min="0" value="<?= (int)($edit_route['innova'] ?? '') ?: '' ?>" required></div>
        <div><label>Tempo Traveller fare (₹) — website pe "On Demand" dikhta hai</label><input name="tempo" type="number" min="0" value="<?= (int)($edit_route['tempo'] ?? '') ?: '' ?>"></div>
        <div><label>Tag (optional, e.g. Most booked)</label><input name="tag" value="<?= htmlspecialchars($edit_route['tag'] ?? '') ?>"></div>
      </div>
      <div class="actions">
        <button class="btn btn--primary" type="submit"><?= $edit_route ? 'Update route' : 'Add route' ?></button>
        <?php if ($edit_route): ?><a class="btn btn--ghost" href="<?= tab_url('routes') ?>">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>

  <div class="card table-scroll">
    <h2>Saare routes (<?= count($ROUTES) ?>)</h2>
    <table>
      <thead><tr><th>Route</th><th>KM</th><th>Time</th><th>Sedan</th><th>SUV</th><th>Innova</th><th>Tempo</th><th>Tag</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($ROUTES as $slug => $r): ?>
        <tr>
          <td style="white-space:nowrap"><b><?= htmlspecialchars($r['from']) ?> → <?= htmlspecialchars($r['to']) ?></b><br><span class="muted">/<?= htmlspecialchars($slug) ?></span></td>
          <td><?= (int)$r['km'] ?></td>
          <td style="white-space:nowrap"><?= htmlspecialchars($r['time']) ?></td>
          <td><?= inr((int)$r['sedan']) ?></td>
          <td><?= inr((int)$r['suv']) ?></td>
          <td><?= inr((int)$r['innova']) ?></td>
          <td><?= inr((int)$r['tempo']) ?></td>
          <td><?= htmlspecialchars($r['tag'] ?? '') ?></td>
          <td style="text-align:right;white-space:nowrap">
            <a class="btn btn--ghost btn--sm" href="<?= tab_url('routes') ?>&edit=<?= urlencode($slug) ?>">Edit</a>
            <a class="btn btn--ghost btn--sm" href="<?= route_url($slug) ?>" target="_blank">View</a>
            <form method="post" action="<?= tab_url('routes') ?>" style="display:inline" onsubmit="return confirm('Ye route delete karein?')">
              <input type="hidden" name="action" value="delete_route">
              <input type="hidden" name="csrf" value="<?= $csrf ?>">
              <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
              <button class="btn btn--danger btn--sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p class="muted" style="margin-top:12px">Fares flat one-way hain; sirf parking extra. Changes turant website pe live ho jate hain.</p>
  </div>

<?php elseif ($tab === 'pages'): ?>
<?php if ($edit_page !== ''): $fields = $PAGE_DEFAULTS[$edit_page]['fields']; ?>
  <div class="card">
    <h2>Page edit karein: <?= htmlspecialchars($PAGE_DEFAULTS[$edit_page]['label']) ?></h2>
    <div class="hint">In shortcodes ka use kar sakte ho — website pe apne aap replace ho jate hain: <b>{{PHONE}}</b> <b>{{EMAIL}}</b> <b>{{ADDRESS}}</b> <b>{{WHATSAPP_LINK}}</b> <b>{{SITE_NAME}}</b>. Kisi field ko khali chhodne par default content wapas aa jata hai.</div>
    <form method="post" action="<?= tab_url('pages') ?>">
      <input type="hidden" name="action" value="save_page">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="page" value="<?= htmlspecialchars($edit_page) ?>">
      <?php
      $field_labels = $PAGE_DEFAULTS[$edit_page]['field_labels'] ?? [];
      $default_labels = [
          'hero_title' => 'Heading (page ke top ka title)',
          'hero_sub' => 'Sub-heading (heading ke niche ki line)',
          'body' => 'Page content',
          'seo_title' => 'Meta title (browser tab / Google me dikhne wala title, 50-60 chars)',
          'seo_desc' => 'Meta description (Google search result ki 1-2 line, 150-160 chars)',
      ];
      $seo_keys = ['seo_title', 'seo_desc'];
      foreach (array_diff(array_keys($fields), $seo_keys) as $fkey):
          $flabel = $field_labels[$fkey] ?? $default_labels[$fkey] ?? ucwords(str_replace('_', ' ', $fkey));
          if ($fkey === 'body'): ?>
      <div class="field"><label><?= htmlspecialchars($flabel) ?></label>
        <textarea id="body-editor" name="field_body" rows="16"><?= htmlspecialchars(page_raw_field($edit_page, 'body')) ?></textarea></div>
      <?php else: ?>
      <div class="field"><label><?= htmlspecialchars($flabel) ?></label>
        <input name="field_<?= htmlspecialchars($fkey) ?>" value="<?= htmlspecialchars(page_raw_field($edit_page, $fkey)) ?>"></div>
      <?php endif; endforeach; ?>
      <div class="section-title">SEO — search engine settings</div>
      <?php foreach ($seo_keys as $fkey): if (!isset($fields[$fkey])) continue; $cur = page_raw_field($edit_page, $fkey); ?>
      <div class="field"><label><?= htmlspecialchars($default_labels[$fkey]) ?></label>
        <?php if ($fkey === 'seo_desc'): ?>
        <textarea name="field_<?= $fkey ?>" rows="3" data-count><?= htmlspecialchars($cur) ?></textarea>
        <?php else: ?>
        <input name="field_<?= $fkey ?>" value="<?= htmlspecialchars($cur) ?>" data-count>
        <?php endif; ?>
        <span class="muted count"></span></div>
      <?php endforeach; ?>
      <div class="actions">
        <button class="btn btn--primary" type="submit">Save page</button>
        <a class="btn btn--ghost" href="<?= tab_url('pages') ?>">Cancel</a>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="card table-scroll">
    <h2>Website pages</h2>
    <table>
      <thead><tr><th>Page</th><th>Heading</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($PAGE_DEFAULTS as $key => $def): ?>
        <tr>
          <td><b><?= htmlspecialchars($def['label']) ?></b></td>
          <td><?= htmlspecialchars(html_entity_decode(strip_tags(page_raw_field($key, 'hero_title')), ENT_QUOTES | ENT_HTML5)) ?></td>
          <td style="text-align:right;white-space:nowrap">
            <a class="btn btn--primary btn--sm" href="<?= tab_url('pages') ?>&page=<?= urlencode($key) ?>">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p class="muted" style="margin-top:12px">Har page ki heading, sub-heading aur main content yahan se edit hota hai. Route pages "Routes &amp; Fares" tab se manage hote hain.</p>
  </div>
<?php endif; ?>

<?php elseif ($tab === 'blog'): ?>
<?php if ($edit_post || $new_post): ?>
  <div class="card">
    <h2><?= $edit_post ? 'Post edit karein' : 'Naya blog post likhein' ?></h2>
    <form method="post" action="<?= tab_url('blog') ?>" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save_post">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="orig_slug" value="<?= htmlspecialchars($edit_post['slug'] ?? '') ?>">
      <div class="field"><label>Title</label>
        <input name="title" value="<?= htmlspecialchars($edit_post['title'] ?? '') ?>" required></div>
      <div class="grid2">
        <div class="field"><label>Date (e.g. 1 Sep 2026; khali = aaj)</label>
          <input name="date" value="<?= htmlspecialchars($edit_post['date'] ?? '') ?>"></div>
        <div class="field"><label>Excerpt (blog listing pe chhota intro)</label>
          <input name="excerpt" value="<?= htmlspecialchars($edit_post['excerpt'] ?? '') ?>"></div>
      </div>
      <div class="field"><label>Thumbnail / featured image (blog list aur post ke top pe dikhti hai)</label>
        <?php image_field('image', $edit_post['image'] ?? ''); ?></div>
      <div class="field"><label>Content</label>
        <textarea id="body-editor" name="body" rows="18"><?= $edit_post ? htmlspecialchars((string)file_get_contents(dirname(__DIR__) . '/data/posts/' . basename($edit_post['slug']) . '.html')) : '' ?></textarea></div>
      <div class="section-title">SEO — search engine settings</div>
      <div class="field"><label>Meta title (khali = post title | site name)</label>
        <input name="seo_title" value="<?= htmlspecialchars($edit_post['seo_title'] ?? '') ?>" data-count><span class="muted count"></span></div>
      <div class="field"><label>Meta description (khali = excerpt)</label>
        <textarea name="seo_desc" rows="3" data-count><?= htmlspecialchars($edit_post['seo_desc'] ?? '') ?></textarea><span class="muted count"></span></div>
      <div class="actions">
        <button class="btn btn--primary" type="submit"><?= $edit_post ? 'Update post' : 'Publish post' ?></button>
        <a class="btn btn--ghost" href="<?= tab_url('blog') ?>">Cancel</a>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="card table-scroll">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2 style="margin:0">Blog posts (<?= count(admin_posts()) ?>)</h2>
      <a class="btn btn--primary" href="<?= tab_url('blog') ?>&new=1">+ Naya post</a>
    </div>
    <table>
      <thead><tr><th></th><th>Title</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach (admin_posts() as $p): ?>
        <tr>
          <td style="width:70px"><?php if (!empty($p['image'])): ?><img class="thumb" src="<?= htmlspecialchars($p['image']) ?>" alt=""><?php else: ?><span class="thumb thumb--empty">No image</span><?php endif; ?></td>
          <td><b><?= htmlspecialchars($p['title']) ?></b><br><span class="muted"><?= htmlspecialchars(blog_url($p['slug'])) ?></span></td>
          <td style="white-space:nowrap"><?= htmlspecialchars($p['date']) ?></td>
          <td style="text-align:right;white-space:nowrap">
            <a class="btn btn--ghost btn--sm" href="<?= tab_url('blog') ?>&edit=<?= urlencode($p['slug']) ?>">Edit</a>
            <a class="btn btn--ghost btn--sm" href="<?= blog_url($p['slug']) ?>" target="_blank">View</a>
            <form method="post" action="<?= tab_url('blog') ?>" style="display:inline" onsubmit="return confirm('Ye post delete karein?')">
              <input type="hidden" name="action" value="delete_post">
              <input type="hidden" name="csrf" value="<?= $csrf ?>">
              <input type="hidden" name="slug" value="<?= htmlspecialchars($p['slug']) ?>">
              <button class="btn btn--danger btn--sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php elseif ($tab === 'tours'): ?>
<?php if ($show_item_form): $t = $edit_item ?? []; ?>
  <div class="card">
    <h2><?= $edit_item ? 'Tour package edit karein' : 'Naya tour package' ?></h2>
    <form method="post" action="<?= tab_url('tours') ?>" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save_tour">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="id" value="<?= htmlspecialchars($t['id'] ?? '') ?>">
      <div class="field"><label>Package title</label>
        <input name="title" value="<?= htmlspecialchars($t['title'] ?? '') ?>" required placeholder="e.g. Golden Triangle"></div>
      <div class="grid2">
        <div class="field"><label>Duration</label>
          <input name="duration" value="<?= htmlspecialchars($t['duration'] ?? '') ?>" placeholder="e.g. 3-4 days"></div>
        <div class="field"><label>Price (optional, jaisa dikhana ho)</label>
          <input name="price" value="<?= htmlspecialchars($t['price'] ?? '') ?>" placeholder="e.g. From ₹12,500 / On Demand"></div>
      </div>
      <div class="field"><label>Short description</label>
        <textarea name="desc" rows="3"><?= htmlspecialchars($t['desc'] ?? '') ?></textarea></div>
      <div class="field"><label>Highlights (har line me ek point)</label>
        <textarea name="highlights" rows="4" placeholder="Amber Fort &amp; Hawa Mahal&#10;Taj Mahal at sunrise"><?= htmlspecialchars($t['highlights'] ?? '') ?></textarea></div>
      <div class="field"><label>Package image</label>
        <?php image_field('image', $t['image'] ?? ''); ?></div>
      <div class="actions">
        <button class="btn btn--primary" type="submit"><?= $edit_item ? 'Update package' : 'Add package' ?></button>
        <a class="btn btn--ghost" href="<?= tab_url('tours') ?>">Cancel</a>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="card table-scroll">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2 style="margin:0">Tour packages (<?= count(admin_list('tours')) ?>)</h2>
      <a class="btn btn--primary" href="<?= tab_url('tours') ?>&new=1">+ Naya package</a>
    </div>
    <table>
      <thead><tr><th></th><th>Package</th><th>Duration</th><th>Price</th><th></th></tr></thead>
      <tbody>
      <?php $tours = admin_list('tours'); foreach ($tours as $i => $t): ?>
        <tr>
          <td style="width:70px"><?php if (!empty($t['image'])): ?><img class="thumb" src="<?= htmlspecialchars($t['image']) ?>" alt=""><?php else: ?><span class="thumb thumb--empty">No image</span><?php endif; ?></td>
          <td><b><?= htmlspecialchars($t['title']) ?></b><br><span class="muted"><?= htmlspecialchars(short($t['desc'] ?? '', 80)) ?></span></td>
          <td style="white-space:nowrap"><?= htmlspecialchars($t['duration'] ?? '') ?></td>
          <td style="white-space:nowrap"><?= htmlspecialchars($t['price'] ?? '') ?: '<span class="muted">—</span>' ?></td>
          <td style="text-align:right;white-space:nowrap">
            <?php move_buttons('tours', $t['id'], $i, count($tours), $csrf); ?>
            <a class="btn btn--ghost btn--sm" href="<?= tab_url('tours') ?>&edit=<?= urlencode($t['id']) ?>">Edit</a>
            <form method="post" action="<?= tab_url('tours') ?>" class="inline-form" onsubmit="return confirm('Ye package delete karein?')">
              <input type="hidden" name="action" value="delete_tour"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
              <button class="btn btn--danger btn--sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p class="muted" style="margin-top:12px">Ye packages <a href="/tour-packages.php" target="_blank">tour-packages.php</a> page pe cards ki tarah dikhte hain. Page ki heading/content "Pages &amp; SEO" tab me edit hota hai.</p>
  </div>
<?php endif; ?>

<?php elseif ($tab === 'reviews'): ?>
<?php if ($show_item_form): $r = $edit_item ?? []; ?>
  <div class="card" style="max-width:720px">
    <h2><?= $edit_item ? 'Review edit karein' : 'Naya review' ?></h2>
    <form method="post" action="<?= tab_url('reviews') ?>">
      <input type="hidden" name="action" value="save_review">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="id" value="<?= htmlspecialchars($r['id'] ?? '') ?>">
      <div class="grid2">
        <div class="field"><label>Customer name</label>
          <input name="name" value="<?= htmlspecialchars($r['name'] ?? '') ?>" required></div>
        <div class="field"><label>Trip / route (e.g. Jaipur → Delhi)</label>
          <input name="trip" value="<?= htmlspecialchars($r['trip'] ?? '') ?>"></div>
      </div>
      <div class="field"><label>Rating</label>
        <select name="stars" class="input">
          <?php for ($s = 5; $s >= 1; $s--): ?><option value="<?= $s ?>" <?= (int)($r['stars'] ?? 5) === $s ? 'selected' : '' ?>><?= str_repeat('★', $s) . str_repeat('☆', 5 - $s) ?> (<?= $s ?>)</option><?php endfor; ?>
        </select></div>
      <div class="field"><label>Review text</label>
        <textarea name="text" rows="4" required><?= htmlspecialchars($r['text'] ?? '') ?></textarea></div>
      <div class="actions">
        <button class="btn btn--primary" type="submit"><?= $edit_item ? 'Update review' : 'Add review' ?></button>
        <a class="btn btn--ghost" href="<?= tab_url('reviews') ?>">Cancel</a>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="card table-scroll">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2 style="margin:0">Customer reviews (<?= count(admin_list('reviews')) ?>)</h2>
      <a class="btn btn--primary" href="<?= tab_url('reviews') ?>&new=1">+ Naya review</a>
    </div>
    <table>
      <thead><tr><th>Customer</th><th>Rating</th><th>Review</th><th></th></tr></thead>
      <tbody>
      <?php $reviews = admin_list('reviews'); foreach ($reviews as $i => $r): ?>
        <tr>
          <td style="white-space:nowrap"><b><?= htmlspecialchars($r['name']) ?></b><br><span class="muted"><?= htmlspecialchars($r['trip'] ?? '') ?></span></td>
          <td class="stars-in" style="white-space:nowrap"><?= str_repeat('★', (int)($r['stars'] ?? 5)) ?></td>
          <td><?= htmlspecialchars(short($r['text'], 110)) ?></td>
          <td style="text-align:right;white-space:nowrap">
            <?php move_buttons('reviews', $r['id'], $i, count($reviews), $csrf); ?>
            <a class="btn btn--ghost btn--sm" href="<?= tab_url('reviews') ?>&edit=<?= urlencode($r['id']) ?>">Edit</a>
            <form method="post" action="<?= tab_url('reviews') ?>" class="inline-form" onsubmit="return confirm('Ye review delete karein?')">
              <input type="hidden" name="action" value="delete_review"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
              <button class="btn btn--danger btn--sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p class="muted" style="margin-top:12px">Home page ke "Rider reviews" section me isi order me dikhte hain. Section ki heading "Pages &amp; SEO → Home page" me hai.</p>
  </div>
<?php endif; ?>

<?php elseif ($tab === 'faqs'): ?>
<?php if ($show_item_form): $f = $edit_item ?? []; ?>
  <div class="card" style="max-width:720px">
    <h2><?= $edit_item ? 'FAQ edit karein' : 'Naya FAQ' ?></h2>
    <form method="post" action="<?= tab_url('faqs') ?>">
      <input type="hidden" name="action" value="save_faq">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="id" value="<?= htmlspecialchars($f['id'] ?? '') ?>">
      <div class="field"><label>Question</label>
        <input name="q" value="<?= htmlspecialchars($f['q'] ?? '') ?>" required></div>
      <div class="field"><label>Answer (shortcode {{PHONE}} use kar sakte ho)</label>
        <textarea name="a" rows="4" required><?= htmlspecialchars($f['a'] ?? '') ?></textarea></div>
      <div class="actions">
        <button class="btn btn--primary" type="submit"><?= $edit_item ? 'Update FAQ' : 'Add FAQ' ?></button>
        <a class="btn btn--ghost" href="<?= tab_url('faqs') ?>">Cancel</a>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="card table-scroll">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2 style="margin:0">FAQs (<?= count(admin_list('faqs')) ?>)</h2>
      <a class="btn btn--primary" href="<?= tab_url('faqs') ?>&new=1">+ Naya FAQ</a>
    </div>
    <table>
      <thead><tr><th>Question</th><th>Answer</th><th></th></tr></thead>
      <tbody>
      <?php $faqs = admin_list('faqs'); foreach ($faqs as $i => $f): ?>
        <tr>
          <td><b><?= htmlspecialchars($f['q']) ?></b></td>
          <td><?= htmlspecialchars(short($f['a'], 110)) ?></td>
          <td style="text-align:right;white-space:nowrap">
            <?php move_buttons('faqs', $f['id'], $i, count($faqs), $csrf); ?>
            <a class="btn btn--ghost btn--sm" href="<?= tab_url('faqs') ?>&edit=<?= urlencode($f['id']) ?>">Edit</a>
            <form method="post" action="<?= tab_url('faqs') ?>" class="inline-form" onsubmit="return confirm('Ye FAQ delete karein?')">
              <input type="hidden" name="action" value="delete_faq"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="id" value="<?= htmlspecialchars($f['id']) ?>">
              <button class="btn btn--danger btn--sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p class="muted" style="margin-top:12px">Home page ke FAQ section me isi order me dikhte hain (pehla wala khula hua).</p>
  </div>
<?php endif; ?>

<?php elseif ($tab === 'media'): $media = media_files(); ?>
  <div class="card">
    <h2>Images upload karein</h2>
    <form method="post" action="<?= tab_url('media') ?>" enctype="multipart/form-data" id="media-upload-form">
      <input type="hidden" name="action" value="upload_media">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <label class="drop" for="media-files" style="display:block;cursor:pointer;margin:0">
        <div style="font-size:1.6rem;margin-bottom:6px">🖼️</div>
        <b style="color:var(--ink)">Click karke images choose karein</b> — ek saath kai images select kar sakte ho<br>
        <span class="muted">JPG, PNG, GIF, WebP · max 5 MB har image</span>
        <input id="media-files" type="file" name="files[]" accept="image/*" multiple onchange="this.form.submit()">
      </label>
    </form>
  </div>
  <div class="card">
    <h2>Media library (<?= count($media) ?> images)</h2>
    <?php if (!$media): ?><p class="muted">Abhi koi image nahi hai — upar se upload karein.</p><?php else: ?>
    <div class="media-grid">
      <?php foreach ($media as $m): ?>
      <div class="media-item">
        <a href="<?= htmlspecialchars($m['url']) ?>" target="_blank"><img src="<?= htmlspecialchars($m['url']) ?>" alt="" loading="lazy"></a>
        <div class="media-item__body">
          <?= htmlspecialchars($m['name']) ?> · <?= round($m['size'] / 1024) ?> KB
          <div class="media-item__act">
            <button type="button" class="btn btn--ghost" data-copy="<?= htmlspecialchars($m['url']) ?>">Copy URL</button>
            <?php if ($m['deletable']): ?>
            <form method="post" action="<?= tab_url('media') ?>" style="display:contents" onsubmit="return confirm('Ye image delete karein? Jahan use ho rahi hai wahan se hat jayegi.')">
              <input type="hidden" name="action" value="delete_media"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="name" value="<?= htmlspecialchars($m['name']) ?>">
              <button class="btn btn--danger" type="submit">Delete</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <p class="muted" style="margin-top:14px">Ye images page/blog editor me "Media Library" button se, ya blog thumbnail / tour image me "Choose from Media Library" se lag jati hain. Editor me image drag-drop karne par bhi yahin upload hoti hai.</p>
  </div>

<?php elseif ($tab === 'settings'): ?>
  <div class="card">
    <h2>Site settings</h2>
    <form method="post" action="<?= tab_url('settings') ?>">
      <input type="hidden" name="action" value="save_settings">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <div class="grid2">
        <div class="field"><label>Business name</label>
          <input name="site_name" value="<?= htmlspecialchars($SETTINGS['site_name']) ?>" required></div>
        <div class="field"><label>Phone (jaisa website pe dikhega)</label>
          <input name="phone_display" value="<?= htmlspecialchars($SETTINGS['phone_display']) ?>" required></div>
        <div class="field"><label>WhatsApp number (country code ke saath, sirf digits)</label>
          <input name="whatsapp_number" value="<?= htmlspecialchars($SETTINGS['whatsapp_number']) ?>" required></div>
        <div class="field"><label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($SETTINGS['email']) ?>" required></div>
      </div>
      <div class="field"><label>Address</label>
        <input name="address" value="<?= htmlspecialchars($SETTINGS['address']) ?>" required></div>

      <div class="section-title">WhatsApp button</div>
      <div class="grid2">
        <div class="field"><label>Button text (header ka green button)</label>
          <input name="wa_button_text" value="<?= htmlspecialchars($SETTINGS['wa_button_text']) ?>" placeholder="WhatsApp Us"></div>
        <div class="field"><label>Default message (WhatsApp kholne pe pehle se likha hua)</label>
          <input name="wa_default_msg" value="<?= htmlspecialchars($SETTINGS['wa_default_msg']) ?>"></div>
      </div>
      <p class="muted" style="margin-top:-6px">WhatsApp number upar "WhatsApp number" field me hai — wahi har button/link me use hota hai.</p>

      <div class="section-title">Social media links (footer me icons — khali chhodo to icon nahi dikhega)</div>
      <div class="grid2">
        <div class="field"><label>Facebook page URL</label>
          <input name="social_facebook" value="<?= htmlspecialchars($SETTINGS['social_facebook']) ?>" placeholder="https://facebook.com/..."></div>
        <div class="field"><label>Instagram URL</label>
          <input name="social_instagram" value="<?= htmlspecialchars($SETTINGS['social_instagram']) ?>" placeholder="https://instagram.com/..."></div>
        <div class="field"><label>YouTube URL</label>
          <input name="social_youtube" value="<?= htmlspecialchars($SETTINGS['social_youtube']) ?>" placeholder="https://youtube.com/@..."></div>
        <div class="field"><label>Google Business / Maps URL</label>
          <input name="social_google" value="<?= htmlspecialchars($SETTINGS['social_google']) ?>" placeholder="https://g.page/..."></div>
      </div>

      <div class="section-title">Google Analytics / tracking code</div>
      <div class="field"><label>Code paste karein (Google Analytics ka pura &lt;script&gt; tag ya Tag Manager / Search Console verification) — har page ke &lt;head&gt; me lagta hai</label>
        <textarea name="analytics_code" rows="6" style="font-family:ui-monospace,monospace;font-size:0.82rem" placeholder="<!-- Google tag (gtag.js) -->&#10;<script async src=&quot;https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX&quot;></script>&#10;<script>...</script>"><?= htmlspecialchars($SETTINGS['analytics_code']) ?></textarea></div>

      <div class="section-title">Per-km rates (fare estimator)</div>
      <div class="grid">
        <div class="field"><label>Sedan (₹/km)</label>
          <input type="number" min="1" name="rate_sedan" value="<?= (int)$SETTINGS['rate_sedan'] ?>" required></div>
        <div class="field"><label>SUV (₹/km)</label>
          <input type="number" min="1" name="rate_suv" value="<?= (int)$SETTINGS['rate_suv'] ?>" required></div>
        <div class="field"><label>Innova Crysta (₹/km)</label>
          <input type="number" min="1" name="rate_innova" value="<?= (int)$SETTINGS['rate_innova'] ?>" required></div>
        <div class="field"><label>Tempo Traveller (₹/km)</label>
          <input type="number" min="1" name="rate_tempo" value="<?= (int)$SETTINGS['rate_tempo'] ?>" required></div>
        <div class="field"><label>Driver allowance (₹/day)</label>
          <input type="number" min="0" name="allowance_default" value="<?= (int)$SETTINGS['allowance_default'] ?>" required></div>
        <div class="field"><label>Tempo driver allowance (₹/day)</label>
          <input type="number" min="0" name="allowance_tempo" value="<?= (int)$SETTINGS['allowance_tempo'] ?>" required></div>
      </div>
      <div class="actions"><button class="btn btn--primary" type="submit">Save settings</button></div>
    </form>
  </div>

<?php elseif ($tab === 'password'): ?>
  <div class="card" style="max-width:420px">
    <h2>Password badlein</h2>
    <form method="post" action="<?= tab_url('password') ?>">
      <input type="hidden" name="action" value="change_password">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <div class="field"><label>Current password</label>
        <input type="password" name="current_password" required></div>
      <div class="field"><label>Naya password (min 6 characters)</label>
        <input type="password" name="new_password" minlength="6" required></div>
      <div class="actions"><button class="btn btn--primary" type="submit">Change password</button></div>
    </form>
  </div>
<?php endif; ?>

<?php if ($needs_picker): ?>
<div class="modal" id="media-modal" role="dialog" aria-modal="true">
  <div class="modal__box">
    <div class="modal__head">
      <h3>Media Library — image choose karein</h3>
      <div style="display:flex;gap:8px;align-items:center">
        <label class="btn btn--primary btn--sm" style="margin:0;cursor:pointer;color:#fff">Upload new <input type="file" accept="image/*" style="display:none" id="modal-upload"></label>
        <button type="button" class="btn btn--ghost btn--sm" data-close>✕</button>
      </div>
    </div>
    <div class="modal__body"><div class="media-grid" id="modal-grid"></div><p class="muted" id="modal-empty" style="display:none">Abhi koi image nahi hai — "Upload new" se upload karein.</p></div>
  </div>
</div>
<script>
(function () {
  var CSRF = <?= json_encode($csrf) ?>;
  var MEDIA = <?= $media_json ?>;
  var modal = document.getElementById('media-modal');
  var grid = document.getElementById('modal-grid');
  var onPick = null;

  function renderGrid() {
    grid.innerHTML = '';
    document.getElementById('modal-empty').style.display = MEDIA.length ? 'none' : '';
    MEDIA.forEach(function (m) {
      var d = document.createElement('div');
      d.className = 'media-item';
      d.innerHTML = '<img loading="lazy" alt=""><div class="media-item__body"></div>';
      d.querySelector('img').src = m.url;
      d.querySelector('.media-item__body').textContent = m.name;
      d.addEventListener('click', function () { if (onPick) onPick(m.url); closeModal(); });
      grid.appendChild(d);
    });
  }
  function openPicker(cb) { onPick = cb; renderGrid(); modal.classList.add('open'); }
  function closeModal() { modal.classList.remove('open'); onPick = null; }
  modal.addEventListener('click', function (e) { if (e.target === modal || e.target.closest('[data-close]')) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

  function uploadFile(file, done) {
    var fd = new FormData();
    fd.append('file', file);
    fd.append('csrf', CSRF);
    fetch('/admin/upload.php', { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (j.success && j.data.urls && j.data.urls.length) {
          j.data.urls.forEach(function (u) { MEDIA.unshift({ url: u, name: u.split('/').pop(), deletable: true }); });
          done(null, j.data.urls[0]);
        } else {
          done((j.data && j.data.messages && j.data.messages.join(' ')) || 'Upload fail ho gaya.');
        }
      })
      .catch(function () { done('Upload fail ho gaya (network).'); });
  }
  document.getElementById('modal-upload').addEventListener('change', function () {
    var f = this.files[0]; this.value = '';
    if (!f) return;
    uploadFile(f, function (err, url) { if (err) { alert(err); return; } if (onPick) onPick(url); closeModal(); });
  });

  // Image fields (blog thumbnail, tour image)
  document.querySelectorAll('[data-img-field]').forEach(function (box) {
    var url = box.querySelector('[data-url]'), prev = box.querySelector('[data-prev]'), file = box.querySelector('[data-file]');
    function set(u) { url.value = u; prev.src = u || 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27/%3E'; }
    url.addEventListener('change', function () { set(url.value.trim()); });
    box.querySelector('[data-pick]').addEventListener('click', function () { openPicker(set); });
    box.querySelector('[data-clear]').addEventListener('click', function () { set(''); file.value = ''; });
    file.addEventListener('change', function () {
      if (file.files[0]) { prev.src = URL.createObjectURL(file.files[0]); url.value = ''; }
    });
  });

  // Copy URL buttons (Media tab)
  document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var u = location.origin + b.getAttribute('data-copy');
      (navigator.clipboard ? navigator.clipboard.writeText(u) : Promise.reject()).then(function () { b.textContent = 'Copied!'; setTimeout(function () { b.textContent = 'Copy URL'; }, 1400); }, function () { prompt('URL copy karein:', u); });
    });
  });

  // Char counters for SEO fields
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var out = el.parentElement.querySelector('.count');
    var max = el.tagName === 'TEXTAREA' ? 160 : 60;
    function upd() { out.textContent = el.value.length + ' / ' + max + ' chars'; out.style.color = el.value.length > max ? '#b42318' : ''; }
    el.addEventListener('input', upd); upd();
  });

  // WordPress-style editor with server-side image upload + media library button
  var ta = document.getElementById('body-editor');
  if (ta && window.Jodit) {
    Jodit.defaultOptions.controls.mediaLibrary = {
      tooltip: 'Media Library se image lagayein',
      iconURL: 'data:image/svg+xml;utf8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#4c5b6e" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>'),
      exec: function (ed) { openPicker(function (u) { ed.s.insertImage(u, null, 600); }); }
    };
    var editor = Jodit.make('#body-editor', {
      height: 420,
      toolbarAdaptive: false,
      buttons: 'paragraph,bold,italic,underline,|,ul,ol,|,link,image,mediaLibrary,table,hr,|,align,|,source,fullsize,undo,redo',
      uploader: {
        url: '/admin/upload.php',
        headers: { 'X-CSRF': CSRF },
        format: 'json',
        isSuccess: function (resp) { return resp && resp.success; },
        getMessage: function (resp) { return resp && resp.data && resp.data.messages ? resp.data.messages.join(' ') : 'Upload fail ho gaya.'; },
        process: function (resp) { return resp.data; },
        defaultHandlerSuccess: function (data) {
          var ed = this.j || this.jodit || editor;
          (data.files || []).forEach(function (f) { ed.s.insertImage(data.baseurl + f, null, 600); });
          (data.urls || []).forEach(function (u) { MEDIA.unshift({ url: u, name: u.split('/').pop(), deletable: true }); });
        }
      },
      cleanHTML: { fillEmptyParagraph: false }
    });
  }
})();
</script>
<?php endif; ?>
  </main>
</div>
<?php endif; ?>
</body>
</html>
