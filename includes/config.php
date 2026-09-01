<?php
// Site-wide configuration and route data for MK Cab Service.

const SITE_NAME  = 'MK Cab Service';
const PHONE_DISPLAY = '+91 91161 71336';
const WHATSAPP_NUMBER = '919116171336';
const EMAIL = 'info@mkcabservice.com';
const ADDRESS = '53, Rd Number 1, Malhotra Nagar, VKI, Jaipur, Rajasthan 302039';

// Per-km rates used by the fare estimator.
const RATE_SEDAN = 11;
const RATE_SUV = 15;
const RATE_INNOVA = 19;
const RATE_TEMPO = 35;
const ALLOWANCE_DEFAULT = 300;      // driver allowance for Sedan / SUV / Innova
const ALLOWANCE_TEMPO = 500;        // driver allowance for Tempo Traveller

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
