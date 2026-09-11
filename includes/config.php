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
    'wa_button_text' => 'WhatsApp Us',
    'wa_default_msg' => "Hello MK Cab Service, I'd like to book a cab.",
    'social_facebook' => '',
    'social_instagram' => '',
    'social_youtube' => '',
    'social_google' => '',
    'analytics_code' => '',       // raw <script> snippet (Google Analytics / Tag Manager), printed in <head>
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

function wa_link(?string $text = null): string {
    global $SETTINGS;
    $text = $text ?? ($SETTINGS['wa_default_msg'] ?: "Hello MK Cab Service, I'd like to book a cab.");
    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($text);
}

function setting(string $key): string {
    global $SETTINGS;
    return (string)($SETTINGS[$key] ?? '');
}

function social_links(): array {
    $out = [];
    foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'google' => 'Google'] as $k => $label) {
        $url = trim(setting('social_' . $k));
        if ($url !== '') $out[$k] = ['label' => $label, 'url' => $url];
    }
    return $out;
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

// Per-page SEO meta (browser title + meta description), editable from /admin → Pages.
$__seo_defaults = [
    'home' => ['{{SITE_NAME}} | Taxi & Cab Booking in Jaipur | {{PHONE}}', 'Book reliable cabs in Jaipur with {{SITE_NAME}}. Outstation taxis, airport transfers, local rentals and Rajasthan tours — 24x7. WhatsApp {{PHONE}}.'],
    'about' => ['About Us | {{SITE_NAME}}', '{{SITE_NAME}} is a Jaipur-based taxi company offering outstation cabs, airport transfers, local rentals and Rajasthan tours with verified drivers and honest fares.'],
    'outstation' => ['Outstation Cabs from Jaipur | {{SITE_NAME}} | {{PHONE}}', 'One-way and round-trip taxis from Jaipur to Delhi, Udaipur, Agra, Jodhpur and every major city in Rajasthan and North India — at fair per-km rates with verified drivers.'],
    'airport' => ['Jaipur Airport Taxi | {{SITE_NAME}} | {{PHONE}}', '24x7 pickup and drop for Jaipur International Airport with flight tracking and on-time guarantee — for red-eye departures and late-night landings alike.'],
    'local' => ['Local Car Rental in Jaipur | {{SITE_NAME}} | {{PHONE}}', 'Hourly car rentals with driver for Jaipur sightseeing, shopping runs, weddings and business meetings — flexible 8hr/80km and half-day packages.'],
    'tours' => ['Rajasthan Tour Packages | {{SITE_NAME}} | {{PHONE}}', 'Multi-day road trips across Rajasthan with a dedicated car and driver — Golden Triangle, lakes of Udaipur, the blue city of Jodhpur and the dunes of Jaisalmer.'],
    'fleet' => ['Our Fleet & Pricing | {{SITE_NAME}}', 'Sedans, SUVs, Innova Crysta and Tempo Travellers with driver — see seats, luggage capacity and per-km rates for every car in the {{SITE_NAME}} fleet.'],
    'contact' => ['Contact Us | {{SITE_NAME}}', 'Contact {{SITE_NAME}}, Jaipur — WhatsApp or send us a message for bookings and quotes. {{ADDRESS}}'],
    'blog' => ['Travel Blog | {{SITE_NAME}}', 'Travel guides, route tips and Rajasthan trip ideas from the {{SITE_NAME}} team — fares, routes and everything you need to plan your next cab journey.'],
];
foreach ($__seo_defaults as $__p => $__seo) {
    if (isset($PAGE_DEFAULTS[$__p])) {
        $PAGE_DEFAULTS[$__p]['fields']['seo_title'] = $__seo[0];
        $PAGE_DEFAULTS[$__p]['fields']['seo_desc'] = $__seo[1];
    }
}

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
// Generic JSON lists (reviews, FAQs, tour packages) managed from /admin.
// Stored in data/<name>.json; defaults below are used until the first save.
// ---------------------------------------------------------------------------
$DATA_LISTS = [];
function data_list(string $name, array $default = []): array {
    global $DATA_LISTS;
    if (!isset($DATA_LISTS[$name])) {
        $file = __DIR__ . '/../data/' . basename($name) . '.json';
        $list = is_file($file) ? json_decode((string)file_get_contents($file), true) : null;
        $DATA_LISTS[$name] = is_array($list) ? array_values($list) : $default;
    }
    return $DATA_LISTS[$name];
}

$REVIEWS_DEFAULT = [
    ['id' => 'r1', 'name' => 'Ramesh Gupta', 'trip' => 'Jaipur → Delhi', 'stars' => 5, 'text' => 'Booked a sedan for Delhi drop. Car was neat, driver reached before time and the price was exactly what was quoted. Very satisfied with MK Cab Service.'],
    ['id' => 'r2', 'name' => 'Sunita Agarwal', 'trip' => 'Airport Transfer', 'stars' => 5, 'text' => 'Needed an early morning airport pickup. Driver called the night before to confirm and arrived on the dot. Smooth and safe ride.'],
    ['id' => 'r3', 'name' => 'Vikas Jain', 'trip' => 'Rajasthan Tour', 'stars' => 5, 'text' => 'Took an Innova for a 5-day Rajasthan trip with family. Driver knew all the good stops and never rushed us. Fair rates, no surprises at the end.'],
];

$FAQS_DEFAULT = [
    ['id' => 'f1', 'q' => 'How do I book a cab with MK Cab Service?', 'a' => 'WhatsApp us on {{PHONE}}, or fill the booking form on this page. We confirm your booking within minutes, any time of day.'],
    ['id' => 'f2', 'q' => 'What are your outstation charges from Jaipur?', 'a' => 'Every route has a fixed one-way fare — see the rate table above. Only parking is extra. WhatsApp us for a quote for your route — what we quote is what you pay.'],
    ['id' => 'f3', 'q' => 'Do you provide Jaipur airport pickup and drop?', 'a' => 'Yes, we run 24x7 airport transfers with on-time pickup. Share your flight details and we track the arrival so the driver is ready when you land.'],
    ['id' => 'f4', 'q' => 'Which payment methods do you accept?', 'a' => 'Cash, UPI, cards and bank transfer. For most local and airport trips you can pay after the ride.'],
    ['id' => 'f5', 'q' => 'Can I book a car for a multi-day tour?', 'a' => 'Yes. We arrange multi-day Rajasthan tours with experienced drivers — Golden Triangle, Udaipur-Jodhpur circuits or a fully custom plan.'],
    ['id' => 'f6', 'q' => 'Are your drivers verified?', 'a' => 'All our drivers are police-verified, experienced on Rajasthan routes and trained to be courteous and helpful throughout your trip.'],
];

$TOURS_DEFAULT = [
    ['id' => 't1', 'title' => 'Golden Triangle', 'duration' => '3-4 days', 'price' => '', 'image' => '', 'desc' => 'Jaipur – Agra – Delhi circuit, typically 3-4 days, ideal for first-time visitors.', 'highlights' => "Amber Fort & Hawa Mahal\nTaj Mahal at sunrise\nDelhi city tour"],
    ['id' => 't2', 'title' => 'Royal Rajasthan', 'duration' => '6-8 days', 'price' => '', 'image' => '', 'desc' => 'Jaipur – Pushkar – Udaipur – Jodhpur – Jaisalmer, 6-8 days of forts, lakes and desert.', 'highlights' => "Pushkar lake & Brahma temple\nUdaipur lake palaces\nMehrangarh Fort\nSam sand dunes"],
    ['id' => 't3', 'title' => 'Custom itineraries', 'duration' => 'Your dates', 'price' => '', 'image' => '', 'desc' => 'Tell us your dates and interests — we plan the route, stops and stays around you.', 'highlights' => ''],
];

function site_reviews(): array { global $REVIEWS_DEFAULT; return data_list('reviews', $REVIEWS_DEFAULT); }
function site_faqs(): array { global $FAQS_DEFAULT; return data_list('faqs', $FAQS_DEFAULT); }
function site_tours(): array { global $TOURS_DEFAULT; return data_list('tours', $TOURS_DEFAULT); }

// Uploaded images (admin Media tab) live here; existing site images in assets/img/uploads.
const UPLOAD_DIR = '/assets/uploads';

function media_files(): array {
    $out = [];
    foreach ([UPLOAD_DIR, '/assets/img/uploads'] as $dir) {
        foreach (glob(dirname(__DIR__) . $dir . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [] as $f) {
            $out[] = ['url' => $dir . '/' . basename($f), 'name' => basename($f), 'size' => filesize($f), 'mtime' => filemtime($f), 'deletable' => $dir === UPLOAD_DIR];
        }
    }
    usort($out, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
    return $out;
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
