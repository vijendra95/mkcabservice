<?php
// MK Cab Service — admin panel for routes & fares.
session_start();
require_once __DIR__ . '/../includes/config.php';

const ADMIN_DEFAULT_PASSWORD = 'mkcab123';
$DATA_DIR = __DIR__ . '/../data';
$ROUTES_FILE = $DATA_DIR . '/routes.json';
$ADMIN_FILE = $DATA_DIR . '/admin.json';

function admin_password_hash(string $file): string {
    if (is_file($file)) {
        $j = json_decode((string)file_get_contents($file), true);
        if (!empty($j['password_hash'])) return $j['password_hash'];
    }
    return password_hash(ADMIN_DEFAULT_PASSWORD, PASSWORD_DEFAULT);
}

function save_routes(string $file, array $routes): bool {
    $dir = dirname($file);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($file, json_encode($routes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
}

function make_slug(string $from, string $to): string {
    $s = strtolower($from . '-to-' . $to . '-one-way-taxi');
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
            $slug = $orig !== '' ? $orig : make_slug($from, $to);
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
            if (save_routes($ROUTES_FILE, $ROUTES)) {
                $msg = ($orig !== '' ? 'Route update ho gaya' : 'Naya route add ho gaya') . ": $from → $to";
            } else {
                $err = 'Save nahi hua — data/ folder writable nahi hai. Hostinger File Manager me data folder ki permission 755 karein.';
            }
        }
    } elseif ($action === 'delete_route') {
        $slug = $_POST['slug'] ?? '';
        if (isset($ROUTES[$slug])) {
            $name = $ROUTES[$slug]['from'] . ' → ' . $ROUTES[$slug]['to'];
            unset($ROUTES[$slug]);
            if (save_routes($ROUTES_FILE, $ROUTES)) {
                $msg = "Route delete ho gaya: $name";
            } else {
                $err = 'Delete save nahi hua — data/ folder writable nahi hai.';
            }
        }
    } elseif ($action === 'change_password') {
        $new = $_POST['new_password'] ?? '';
        if (strlen($new) < 6) {
            $err = 'Naya password kam se kam 6 characters ka ho.';
        } elseif (!password_verify($_POST['current_password'] ?? '', admin_password_hash($ADMIN_FILE))) {
            $err = 'Current password galat hai.';
        } else {
            if (!is_dir($DATA_DIR)) mkdir($DATA_DIR, 0755, true);
            $ok = file_put_contents($ADMIN_FILE, json_encode(['password_hash' => password_hash($new, PASSWORD_DEFAULT)])) !== false;
            $msg = $ok ? 'Password badal gaya.' : '';
            if (!$ok) $err = 'Password save nahi hua — data/ folder writable nahi hai.';
        }
    }
}

$edit_slug = $_GET['edit'] ?? '';
$edit = ($edit_slug !== '' && isset($ROUTES[$edit_slug])) ? $ROUTES[$edit_slug] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin | MK Cab Service</title>
<style>
  * { box-sizing: border-box; margin: 0; }
  body { font-family: system-ui, -apple-system, sans-serif; background: #f2f4f7; color: #1c2434; }
  .wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px 60px; }
  .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
  h1 { font-size: 1.4rem; }
  h2 { font-size: 1.05rem; margin-bottom: 12px; }
  .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(16,24,40,.08); margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
  th, td { text-align: left; padding: 9px 10px; border-bottom: 1px solid #eceef2; white-space: nowrap; }
  th { background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; letter-spacing: .04em; color: #5b6472; }
  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
  label { display: block; font-size: 0.78rem; font-weight: 600; color: #5b6472; margin-bottom: 4px; }
  input { width: 100%; padding: 9px 10px; border: 1px solid #d4d9e0; border-radius: 8px; font-size: 0.92rem; }
  .btn { display: inline-block; padding: 9px 18px; border: 0; border-radius: 8px; font-weight: 700; font-size: 0.88rem; cursor: pointer; text-decoration: none; }
  .btn--primary { background: #e8590c; color: #fff; }
  .btn--ghost { background: #eef1f5; color: #1c2434; }
  .btn--danger { background: #fee2e2; color: #b91c1c; }
  .btn--sm { padding: 5px 11px; font-size: 0.8rem; }
  .msg { padding: 11px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; }
  .msg--ok { background: #dcfce7; color: #14532d; }
  .msg--err { background: #fee2e2; color: #991b1b; }
  .muted { color: #7a8494; font-size: 0.8rem; }
  .login { max-width: 380px; margin: 12vh auto 0; }
  .actions { margin-top: 16px; display: flex; gap: 10px; }
  @media (max-width: 720px) { .table-scroll { overflow-x: auto; } }
</style>
</head>
<body>
<div class="wrap">
<?php if (!$logged_in): ?>
  <div class="card login">
    <h1 style="margin-bottom:14px">MK Cab Service — Admin</h1>
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
    <h1>MK Cab Service — Admin</h1>
    <div style="display:flex;gap:10px">
      <a class="btn btn--ghost" href="/" target="_blank">Website dekhein ↗</a>
      <form method="post" style="display:inline">
        <input type="hidden" name="action" value="logout">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <button class="btn btn--ghost" type="submit">Logout</button>
      </form>
    </div>
  </div>

  <?php if ($msg): ?><div class="msg msg--ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="msg msg--err"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <div class="card">
    <h2><?= $edit ? 'Route edit karein: ' . htmlspecialchars($edit['from'] . ' → ' . $edit['to']) : 'Naya route add karein' ?></h2>
    <form method="post">
      <input type="hidden" name="action" value="save_route">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="orig_slug" value="<?= htmlspecialchars($edit_slug !== '' && $edit ? $edit_slug : '') ?>">
      <div class="grid">
        <div><label>From (city)</label><input name="from" value="<?= htmlspecialchars($edit['from'] ?? 'Jaipur') ?>" required></div>
        <div><label>To (city)</label><input name="to" value="<?= htmlspecialchars($edit['to'] ?? '') ?>" required></div>
        <div><label>Distance (km)</label><input name="km" type="number" min="1" value="<?= (int)($edit['km'] ?? '') ?: '' ?>" required></div>
        <div><label>Travel time (e.g. 5h 30m)</label><input name="time" value="<?= htmlspecialchars($edit['time'] ?? '') ?>" required></div>
        <div><label>Sedan fare (₹)</label><input name="sedan" type="number" min="0" value="<?= (int)($edit['sedan'] ?? '') ?: '' ?>" required></div>
        <div><label>SUV fare (₹)</label><input name="suv" type="number" min="0" value="<?= (int)($edit['suv'] ?? '') ?: '' ?>" required></div>
        <div><label>Innova fare (₹)</label><input name="innova" type="number" min="0" value="<?= (int)($edit['innova'] ?? '') ?: '' ?>" required></div>
        <div><label>Tempo Traveller fare (₹)</label><input name="tempo" type="number" min="0" value="<?= (int)($edit['tempo'] ?? '') ?: '' ?>" required></div>
        <div><label>Tag (optional, e.g. Most booked)</label><input name="tag" value="<?= htmlspecialchars($edit['tag'] ?? '') ?>"></div>
      </div>
      <div class="actions">
        <button class="btn btn--primary" type="submit"><?= $edit ? 'Update route' : 'Add route' ?></button>
        <?php if ($edit): ?><a class="btn btn--ghost" href="/admin/">Cancel</a><?php endif; ?>
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
          <td><b><?= htmlspecialchars($r['from']) ?> → <?= htmlspecialchars($r['to']) ?></b><br><span class="muted">/<?= htmlspecialchars($slug) ?></span></td>
          <td><?= (int)$r['km'] ?></td>
          <td><?= htmlspecialchars($r['time']) ?></td>
          <td><?= inr((int)$r['sedan']) ?></td>
          <td><?= inr((int)$r['suv']) ?></td>
          <td><?= inr((int)$r['innova']) ?></td>
          <td><?= inr((int)$r['tempo']) ?></td>
          <td><?= htmlspecialchars($r['tag'] ?? '') ?></td>
          <td style="text-align:right">
            <a class="btn btn--ghost btn--sm" href="/admin/?edit=<?= urlencode($slug) ?>">Edit</a>
            <a class="btn btn--ghost btn--sm" href="<?= route_url($slug) ?>" target="_blank">View</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Ye route delete karein?')">
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

  <div class="card" style="max-width:420px">
    <h2>Password badlein</h2>
    <form method="post">
      <input type="hidden" name="action" value="change_password">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <label>Current password</label>
      <input type="password" name="current_password" required>
      <div style="height:10px"></div>
      <label>Naya password (min 6 characters)</label>
      <input type="password" name="new_password" minlength="6" required>
      <div class="actions"><button class="btn btn--primary" type="submit">Change password</button></div>
    </form>
  </div>
<?php endif; ?>
</div>
</body>
</html>
