<?php
// Site-wide configuration and route data for MK Cab Service.
date_default_timezone_set('Asia/Kolkata');

// Default settings; overridden by data/settings.json (edited from /admin).
$SETTINGS = [
    'site_name' => 'MK Cab Service',
    'phone_display' => '+91 91161 71336',
    'whatsapp_number' => '919116171336',
    'email' => 'info@mkcabservice.com',
    'address' => '53, Rd Number 1, Malhotra Nagar, VKI, Jaipur, Rajasthan 302039',
    'rate_sedan' => 11,
    'rate_suv' => 15,
    'rate_innova' => 19,
    'rate_tempo' => 35,
    'allowance_default' => 300,   // driver allowance for Sedan / SUV / Innova
    'allowance_tempo' => 500,     // driver allowance for Tempo Traveller
];
$__settings_file = __DIR__ . '/../data/settings.json';
if (is_file($__settings_file)) {
    $__saved_settings = json_decode((string)file_get_contents($__settings_file), true);
    if (is_array($__saved_settings)) {
        $SETTINGS = array_merge($SETTINGS, $__saved_settings);
    }
}

define('SITE_NAME', $SETTINGS['site_name']);
define('PHONE_DISPLAY', $SETTINGS['phone_display']);
define('WHATSAPP_NUMBER', $SETTINGS['whatsapp_number']);
define('EMAIL', $SETTINGS['email']);
define('ADDRESS', $SETTINGS['address']);
define('RATE_SEDAN', (int)$SETTINGS['rate_sedan']);
define('RATE_SUV', (int)$SETTINGS['rate_suv']);
define('RATE_INNOVA', (int)$SETTINGS['rate_innova']);
define('RATE_TEMPO', (int)$SETTINGS['rate_tempo']);
define('ALLOWANCE_DEFAULT', (int)$SETTINGS['allowance_default']);
define('ALLOWANCE_TEMPO', (int)$SETTINGS['allowance_tempo']);

function wa_link(string $text = "Hello MK Cab Service, I'd like to book a cab."): string {
    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($text);
}

function inr(int $n): string {
    $s = number_format($n);
    return '₹' . $s;
}

/**
 * One-way taxi routes. Keyed by the clean URL slug
 * (e.g. mkcabservice.com/jaipur-to-delhi-one-way-taxi).
 * Fares are flat one-way fares; only parking is extra.
 */
$ROUTES = [
    'jaipur-to-delhi-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Delhi', 'km' => 268, 'time' => '5h 30m',
        'sedan' => 3899, 'suv' => 4500, 'innova' => 6000, 'tempo' => 9880,
        'tag' => 'Most booked',
    ],
    'jaipur-to-udaipur-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Udaipur', 'km' => 393, 'time' => '7h',
        'sedan' => 5200, 'suv' => 6000, 'innova' => 8000, 'tempo' => 14255,
        'tag' => '',
    ],
    'jaipur-to-agra-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Agra', 'km' => 240, 'time' => '4h 30m',
        'sedan' => 3510, 'suv' => 4050, 'innova' => 5400, 'tempo' => 8900,
        'tag' => 'Taj day-trip',
    ],
    'jaipur-to-jodhpur-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Jodhpur', 'km' => 337, 'time' => '6h',
        'sedan' => 4550, 'suv' => 5250, 'innova' => 7000, 'tempo' => 12295,
        'tag' => '',
    ],
    'jaipur-to-ajmer-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Ajmer', 'km' => 132, 'time' => '2h 30m',
        'sedan' => 2500, 'suv' => 3000, 'innova' => 5000, 'tempo' => 5120,
        'tag' => 'Pushkar & Dargah',
    ],
    'jaipur-to-jaisalmer-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Jaisalmer', 'km' => 558, 'time' => '9h',
        'sedan' => 7800, 'suv' => 9000, 'innova' => 12000, 'tempo' => 20030,
        'tag' => 'Desert trip',
    ],
    'jaipur-to-ranthambore-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Ranthambore', 'km' => 160, 'time' => '3h',
        'sedan' => 3250, 'suv' => 3750, 'innova' => 5000, 'tempo' => 6100,
        'tag' => 'Tiger safari',
    ],
    'jaipur-to-mount-abu-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Mount Abu', 'km' => 485, 'time' => '8h',
        'sedan' => 7150, 'suv' => 8250, 'innova' => 11000, 'tempo' => 17475,
        'tag' => 'Hill station',
    ],
    'jaipur-to-bikaner-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Bikaner', 'km' => 335, 'time' => '6h',
        'sedan' => 4810, 'suv' => 5550, 'innova' => 7400, 'tempo' => 12225,
        'tag' => '',
    ],
    'jaipur-to-kota-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Kota', 'km' => 245, 'time' => '4h 30m',
        'sedan' => 3900, 'suv' => 4500, 'innova' => 6000, 'tempo' => 9075,
        'tag' => '',
    ],
    'delhi-to-jaipur-one-way-taxi' => [
        'from' => 'Delhi', 'to' => 'Jaipur', 'km' => 268, 'time' => '5h 30m',
        'sedan' => 3900, 'suv' => 4500, 'innova' => 6000, 'tempo' => 9880,
        'tag' => 'Airport pickup',
    ],
    'jaipur-to-khatu-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Khatu Shyam Ji', 'km' => 95, 'time' => '2h',
        'sedan' => 2000, 'suv' => 3000, 'innova' => 4500, 'tempo' => 3825,
        'tag' => 'Temple darshan',
    ],
    'jaipur-to-salasar-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Salasar Balaji', 'km' => 170, 'time' => '3h 30m',
        'sedan' => 3000, 'suv' => 4000, 'innova' => 5000, 'tempo' => 6450,
        'tag' => 'Temple darshan',
    ],
    'jaipur-to-mehandipur-one-way-taxi' => [
        'from' => 'Jaipur', 'to' => 'Mehandipur Balaji', 'km' => 110, 'time' => '2h 30m',
        'sedan' => 3000, 'suv' => 4000, 'innova' => 5000, 'tempo' => 4350,
        'tag' => 'Temple darshan',
    ],
];

// Routes edited from the admin panel are stored in data/routes.json and
// override the defaults above.
$__routes_file = __DIR__ . '/../data/routes.json';
if (is_file($__routes_file)) {
    $__saved = json_decode((string)file_get_contents($__routes_file), true);
    if (is_array($__saved) && count($__saved) > 0) {
        $ROUTES = $__saved;
    }
}

function route_url(string $slug): string {
    return '/' . $slug;
}

function route_title(array $r): string {
    return $r['from'] . ' to ' . $r['to'];
}

// ---------------------------------------------------------------------------
// Editable page content (WordPress-style, via /admin).
// Defaults live in includes/page-defaults.php; admin edits are stored in
// data/pages/<page>.json and override the defaults field by field.
// ---------------------------------------------------------------------------
$PAGE_DEFAULTS = require __DIR__ . '/page-defaults.php';

function render_tokens(string $html): string {
    return strtr($html, [
        '{{PHONE}}' => PHONE_DISPLAY,
        '{{EMAIL}}' => EMAIL,
        '{{ADDRESS}}' => ADDRESS,
        '{{WHATSAPP_LINK}}' => wa_link(),
        '{{SITE_NAME}}' => SITE_NAME,
    ]);
}

function page_raw_field(string $page, string $key): string {
    global $PAGE_DEFAULTS;
    static $cache = [];
    if (!isset($cache[$page])) {
        $file = __DIR__ . '/../data/pages/' . $page . '.json';
        $cache[$page] = is_file($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];
    }
    $val = $cache[$page][$key] ?? null;
    if ($val === null || $val === '') {
        $val = $PAGE_DEFAULTS[$page]['fields'][$key] ?? '';
    }
    return (string)$val;
}

function page_field(string $page, string $key): string {
    return render_tokens(page_raw_field($page, $key));
}

// ---------------------------------------------------------------------------
// Blog posts. Index in data/posts.json, bodies in data/posts/<slug>.html.
// ---------------------------------------------------------------------------
function blog_posts(): array {
    static $posts = null;
    if ($posts === null) {
        $file = __DIR__ . '/../data/posts.json';
        $posts = is_file($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];
    }
    return $posts;
}

function blog_post_body(string $slug): string {
    $file = __DIR__ . '/../data/posts/' . basename($slug) . '.html';
    return is_file($file) ? render_tokens((string)file_get_contents($file)) : '';
}

function blog_url(string $slug): string {
    return '/blog/' . rawurlencode($slug);
}

// ---------------------------------------------------------------------------
// Booking / contact enquiries — appended to data/bookings.json for the admin
// "Bookings" tab. Failure to write never blocks the WhatsApp handoff.
// ---------------------------------------------------------------------------
function save_enquiry(string $type, array $fields): void {
    $file = __DIR__ . '/../data/bookings.json';
    $dir = dirname($file);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $fh = @fopen($file, 'c+');
    if (!$fh) return;
    if (flock($fh, LOCK_EX)) {
        $raw = stream_get_contents($fh);
        $list = $raw !== '' ? (json_decode($raw, true) ?: []) : [];
        $clean = [];
        foreach ($fields as $k => $v) {
            $v = trim(strip_tags((string)$v));
            if ($v !== '') $clean[$k] = function_exists('mb_substr') ? mb_substr($v, 0, 1000) : substr($v, 0, 1000);
        }
        array_unshift($list, [
            'id' => bin2hex(random_bytes(6)),
            'type' => $type,
            'status' => 'new',
            'created_at' => date('c'),
            'fields' => $clean,
        ]);
        $list = array_slice($list, 0, 2000);
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fflush($fh);
        flock($fh, LOCK_UN);
    }
    fclose($fh);
}
