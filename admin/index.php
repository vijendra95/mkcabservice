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
    'routes' => ['Routes & Fares', 'One-way routes aur fares manage karein'],
    'pages' => ['Pages', 'Website ke pages ka content edit karein'],
    'blog' => ['Blog', 'Blog posts likhein aur manage karein'],
    'settings' => ['Settings', 'Business details aur per-km rates'],
    'password' => ['Password', 'Admin password badlein'],
];
$ICONS = [
    'routes' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="19" r="2"/><circle cx="18" cy="5" r="2"/><path d="M8 19h8.5a3.5 3.5 0 0 0 0-7h-9a3.5 3.5 0 0 1 0-7H16"/></svg>',
    'pages' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>',
    'blog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    'password' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
];
?>
<div class="layout">
  <aside class="side">
    <div class="brand"><b><?= htmlspecialchars(SITE_NAME) ?></b><span>Admin Panel</span></div>
    <nav class="nav">
      <?php foreach ($TAB_TITLES as $t => $info): ?>
      <a href="<?= tab_url($t) ?>" class="<?= $tab === $t ? 'on' : '' ?>"><?= $ICONS[$t] ?><?= htmlspecialchars($info[0]) ?></a>
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
      <?php
      $field_labels = $PAGE_DEFAULTS[$edit_page]['field_labels'] ?? [];
      $default_labels = [
          'hero_title' => 'Heading (page ke top ka title)',
          'hero_sub' => 'Sub-heading (heading ke niche ki line)',
          'body' => 'Page content',
      ];
      foreach (array_keys($fields) as $fkey):
          $flabel = $field_labels[$fkey] ?? $default_labels[$fkey] ?? ucwords(str_replace('_', ' ', $fkey));
          if ($fkey === 'body'): ?>
      <div class="field"><label><?= htmlspecialchars($flabel) ?></label>
        <textarea id="body-editor" name="field_body" rows="16"><?= htmlspecialchars(page_raw_field($edit_page, 'body')) ?></textarea></div>
      <?php else: ?>
      <div class="field"><label><?= htmlspecialchars($flabel) ?></label>
        <input name="field_<?= htmlspecialchars($fkey) ?>" value="<?= htmlspecialchars(page_raw_field($edit_page, $fkey)) ?>"></div>
      <?php endif; endforeach; ?>
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
  </main>
</div>
<?php endif; ?>
</body>
</html>
