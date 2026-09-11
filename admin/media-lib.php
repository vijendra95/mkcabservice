<?php
// Shared image-upload helper for the admin panel (Media tab, blog thumbnails,
// tour images and the Jodit editor uploader in admin/upload.php).

const MEDIA_MAX_BYTES = 5 * 1024 * 1024;
const MEDIA_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];

function media_dir(): string {
    return dirname(__DIR__) . UPLOAD_DIR;
}

/**
 * Validate and store one uploaded image from $_FILES.
 * Returns ['ok' => true, 'url' => '/assets/uploads/xxx.jpg'] or ['ok' => false, 'error' => '...'].
 */
function media_store(array $file): array {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => $file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
            ? 'Image bahut badi hai (max 5 MB).' : 'Upload fail ho gaya.'];
    }
    if (($file['size'] ?? 0) > MEDIA_MAX_BYTES) {
        return ['ok' => false, 'error' => 'Image bahut badi hai (max 5 MB).'];
    }
    $tmp = $file['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return ['ok' => false, 'error' => 'Upload fail ho gaya.'];
    }
    $info = @getimagesize($tmp);
    $mime = $info['mime'] ?? '';
    if (!$info || !isset(MEDIA_TYPES[$mime])) {
        return ['ok' => false, 'error' => 'Sirf JPG, PNG, GIF ya WebP image upload karein.'];
    }
    $ext = MEDIA_TYPES[$mime];
    $base = pathinfo($file['name'] ?? 'image', PATHINFO_FILENAME);
    $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $base), '-'));
    $base = substr($base !== '' ? $base : 'image', 0, 40);
    $name = $base . '-' . date('Ymd') . '-' . substr(bin2hex(random_bytes(4)), 0, 6) . '.' . $ext;

    $dir = media_dir();
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return ['ok' => false, 'error' => 'assets/uploads folder nahi ban paya — permission 755 karein.'];
    }
    if (!move_uploaded_file($tmp, $dir . '/' . $name)) {
        return ['ok' => false, 'error' => 'File save nahi hui — assets/uploads folder writable nahi hai.'];
    }
    @chmod($dir . '/' . $name, 0644);
    return ['ok' => true, 'url' => UPLOAD_DIR . '/' . $name, 'name' => $name];
}

/** Delete an uploaded image by filename (only from the uploads dir). */
function media_delete(string $name): bool {
    $name = basename($name);
    if ($name === '' || !preg_match('/^[a-z0-9._-]+\.(jpe?g|png|gif|webp)$/i', $name)) return false;
    $path = media_dir() . '/' . $name;
    return is_file($path) && unlink($path);
}

/** Accept either a pasted URL/path or an uploaded file for an image field. */
function media_from_form(string $url_field, string $file_field, ?string &$error = null): string {
    if (!empty($_FILES[$file_field]['name'])) {
        $res = media_store($_FILES[$file_field]);
        if ($res['ok']) return $res['url'];
        $error = $res['error'];
    }
    $url = trim($_POST[$url_field] ?? '');
    if ($url !== '' && !preg_match('#^(https?://|/)[^\s"\'<>]+$#i', $url)) {
        $error = 'Image URL galat hai — /assets/uploads/... ya https://... hona chahiye.';
        return '';
    }
    return $url;
}
