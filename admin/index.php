<?php
// MK Cab Service — admin panel (routes, pages, blog, settings).
session_start();
require_once __DIR__ . '/../includes/config.php';

const ADMIN_DEFAULT_PASSWORD = 'mkcab123';
$DATA_DIR = dirname(__DIR__) . '/data';
$ROUTES_FILE = $DATA_DIR . '/routes.json';
$ADMIN_FILE = $DATA_DIR . '/admin.json';
$SETTINGS_FILE = $DATA_DIR . '/settings.json';
$POSTS_FILE = $DATA_DIR . '/posts.json';

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

function make_slug_from(string $text, string $suffix = ''): string {
    $s = strtolower($text . $suffix);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim(preg_replace('/-+/', '-', $s), '-');
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
            $entry = [
                'slug' => $slug,
                'title' => $title,
                'date' => trim($_POST['date'] ?? '') ?: date('j M Y'),
                'excerpt' => trim($_POST['excerpt'] ?? ''),
                'seo_title' => trim($_POST['seo_title'] ?? ''),
                'seo_desc' => trim($_POST['seo_desc'] ?? ''),
            ];
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
        ];
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

$tab = $_GET['tab'] ?? 'routes';
if (!in_array($tab, ['routes', 'pages', 'blog', 'settings', 'password'], true)) $tab = 'routes';

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

function tab_url(string $t): string { return '/admin/?tab=' . $t; }
$needs_editor = ($edit_page !== '') || $edit_post || $new_post;
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
  * { box-sizing: border-box; margin: 0; }
  body { font-family: system-ui, -apple-system, sans-serif; background: #f2f4f7; color: #1c2434; }
  .wrap { max-width: 1150px; margin: 0 auto; padding: 24px 16px 60px; }
  .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
  h1 { font-size: 1.35rem; }
  h2 { font-size: 1.05rem; margin-bottom: 12px; }
  .tabs { display: flex; gap: 6px; margin-bottom: 18px; flex-wrap: wrap; }
  .tabs a { padding: 9px 18px; border-radius: 999px; text-decoration: none; font-weight: 700; font-size: 0.88rem; color: #3c4657; background: #e7ebf0; }
  .tabs a.on { background: #e8590c; color: #fff; }
  .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(16,24,40,.08); margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
  th, td { text-align: left; padding: 9px 10px; border-bottom: 1px solid #eceef2; }
  th { background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; letter-spacing: .04em; color: #5b6472; white-space: nowrap; }
  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
  .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  label { display: block; font-size: 0.78rem; font-weight: 600; color: #5b6472; margin-bottom: 4px; }
  input, textarea { width: 100%; padding: 9px 10px; border: 1px solid #d4d9e0; border-radius: 8px; font-size: 0.92rem; font-family: inherit; }
  .btn { display: inline-block; padding: 9px 18px; border: 0; border-radius: 8px; font-weight: 700; font-size: 0.88rem; cursor: pointer; text-decoration: none; }
  .btn--primary { background: #e8590c; color: #fff; }
  .btn--ghost { background: #eef1f5; color: #1c2434; }
  .btn--danger { background: #fee2e2; color: #b91c1c; }
  .btn--sm { padding: 5px 11px; font-size: 0.8rem; white-space: nowrap; }
  .msg { padding: 11px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; }
  .msg--ok { background: #dcfce7; color: #14532d; }
  .msg--err { background: #fee2e2; color: #991b1b; }
  .muted { color: #7a8494; font-size: 0.8rem; }
  .login { max-width: 380px; margin: 12vh auto 0; }
  .actions { margin-top: 16px; display: flex; gap: 10px; align-items: center; }
  .field { margin-bottom: 12px; }
  .hint { background: #fff8f1; border: 1px solid #ffd9b3; color: #7c3b00; padding: 10px 14px; border-radius: 8px; font-size: 0.82rem; margin-bottom: 14px; }
  @media (max-width: 780px) { .table-scroll { overflow-x: auto; } .grid2 { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="wrap">
<?php if (!$logged_in): ?>
  <div class="card login">
    <h1 style="margin-bottom:14px"><?= htmlspecialchars(SITE_NAME) ?> — Admin</h1>
    <?php if ($err): ?><div class="msg msg--err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="action" value="login">
      <label>Password</label>
      <input type="password" name="password" autofocus required>
      <div class="actions"><button class="btn btn--primary" type="submit">Login</button></div>
    </form>
  </div>
<?php else: ?>
  <div class="top">
    <h1><?= htmlspecialchars(SITE_NAME) ?> — Admin</h1>
    <div style="display:flex;gap:10px">
      <a class="btn btn--ghost" href="/" target="_blank">Website dekhein ↗</a>
      <form method="post" style="display:inline">
        <input type="hidden" name="action" value="logout">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <button class="btn btn--ghost" type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div class="tabs">
    <a href="<?= tab_url('routes') ?>" class="<?= $tab === 'routes' ? 'on' : '' ?>">Routes &amp; Fares</a>
    <a href="<?= tab_url('pages') ?>" class="<?= $tab === 'pages' ? 'on' : '' ?>">Pages</a>
    <a href="<?= tab_url('blog') ?>" class="<?= $tab === 'blog' ? 'on' : '' ?>">Blog</a>
    <a href="<?= tab_url('settings') ?>" class="<?= $tab === 'settings' ? 'on' : '' ?>">Settings</a>
    <a href="<?= tab_url('password') ?>" class="<?= $tab === 'password' ? 'on' : '' ?>">Password</a>
  </div>

  <?php if ($msg): ?><div class="msg msg--ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="msg msg--err"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<?php if ($tab === 'routes'): ?>
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
      <?php if (isset($fields['hero_title'])): ?>
      <div class="field"><label>Heading (page ke top ka title)</label>
        <input name="field_hero_title" value="<?= htmlspecialchars(page_raw_field($edit_page, 'hero_title')) ?>"></div>
      <?php endif; ?>
      <?php if (isset($fields['hero_sub'])): ?>
      <div class="field"><label>Sub-heading (heading ke niche ki line)</label>
        <input name="field_hero_sub" value="<?= htmlspecialchars(page_raw_field($edit_page, 'hero_sub')) ?>"></div>
      <?php endif; ?>
      <?php if (isset($fields['body'])): ?>
      <div class="field"><label>Page content</label>
        <textarea id="body-editor" name="field_body" rows="16"><?= htmlspecialchars(page_raw_field($edit_page, 'body')) ?></textarea></div>
      <?php endif; ?>
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
          <td><?= htmlspecialchars(strip_tags(page_raw_field($key, 'hero_title'))) ?></td>
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
    <form method="post" action="<?= tab_url('blog') ?>">
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
      <div class="field"><label>Content</label>
        <textarea id="body-editor" name="body" rows="18"><?= $edit_post ? htmlspecialchars((string)file_get_contents(dirname(__DIR__) . '/data/posts/' . basename($edit_post['slug']) . '.html')) : '' ?></textarea></div>
      <div class="grid2">
        <div class="field"><label>SEO title (optional)</label>
          <input name="seo_title" value="<?= htmlspecialchars($edit_post['seo_title'] ?? '') ?>"></div>
        <div class="field"><label>SEO description (optional)</label>
          <input name="seo_desc" value="<?= htmlspecialchars($edit_post['seo_desc'] ?? '') ?>"></div>
      </div>
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
      <thead><tr><th>Title</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach (admin_posts() as $p): ?>
        <tr>
          <td><b><?= htmlspecialchars($p['title']) ?></b><br><span class="muted">/blog-post.php?slug=<?= htmlspecialchars($p['slug']) ?></span></td>
          <td style="white-space:nowrap"><?= htmlspecialchars($p['date']) ?></td>
          <td style="text-align:right;white-space:nowrap">
            <a class="btn btn--ghost btn--sm" href="<?= tab_url('blog') ?>&edit=<?= urlencode($p['slug']) ?>">Edit</a>
            <a class="btn btn--ghost btn--sm" href="/blog-post.php?slug=<?= urlencode($p['slug']) ?>" target="_blank">View</a>
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
      <h2 style="margin-top:20px">Per-km rates (fare estimator)</h2>
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

<?php if ($needs_editor): ?>
<script>
  var ta = document.getElementById('body-editor');
  if (ta && window.Jodit) {
    Jodit.make('#body-editor', {
      height: 420,
      toolbarAdaptive: false,
      buttons: 'paragraph,bold,italic,underline,|,ul,ol,|,link,image,table,hr,|,align,|,source,fullsize,undo,redo',
      uploader: { insertImageAsBase64URI: true },
      cleanHTML: { fillEmptyParagraph: false }
    });
  }
</script>
<?php endif; ?>
<?php endif; ?>
</div>
</body>
</html>
