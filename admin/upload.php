<?php
// JSON image-upload endpoint used by the Jodit editor and the media picker.
// Response follows Jodit's uploader format:
//   { success: true,  data: { files: [name], baseurl: '/assets/uploads/', path: '', error: 0, msg: '' } }
//   { success: false, data: { messages: ['...'] } }
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/media-lib.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function upload_fail(string $m, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'data' => ['messages' => [$m]]], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_SESSION['mk_admin'])) upload_fail('Login expire ho gaya — admin me dobara login karein.', 401);
$token = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? '');
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)$token)) upload_fail('Form expire ho gaya — page refresh karein.', 403);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') upload_fail('POST only', 405);

// Jodit sends files[0], files[1]...; the media picker sends a single "file".
$incoming = [];
if (!empty($_FILES['files']) && is_array($_FILES['files']['name'])) {
    foreach ($_FILES['files']['name'] as $i => $n) {
        $incoming[] = [
            'name' => $n,
            'type' => $_FILES['files']['type'][$i],
            'tmp_name' => $_FILES['files']['tmp_name'][$i],
            'error' => $_FILES['files']['error'][$i],
            'size' => $_FILES['files']['size'][$i],
        ];
    }
} elseif (!empty($_FILES['file'])) {
    $incoming[] = $_FILES['file'];
}
if (!$incoming) upload_fail('Koi file nahi mili.');

$saved = [];
$errors = [];
foreach ($incoming as $f) {
    $res = media_store($f);
    if ($res['ok']) $saved[] = $res['name']; else $errors[] = $res['error'];
}
if (!$saved) upload_fail(implode(' ', $errors));

echo json_encode([
    'success' => true,
    'data' => [
        'files' => $saved,
        'urls' => array_map(fn($n) => UPLOAD_DIR . '/' . $n, $saved),
        'baseurl' => UPLOAD_DIR . '/',
        'path' => '',
        'error' => 0,
        'msg' => $errors ? implode(' ', $errors) : '',
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
