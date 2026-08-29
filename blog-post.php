<?php
require_once __DIR__ . '/includes/config.php';

$POSTS = [
    'jaipur-to-delhi-taxi-guide' => ['title' => 'Jaipur to Delhi Taxi Guide | Fares, Route & Travel Tips | MK Cab Service', 'desc' => 'Planning a Jaipur to Delhi cab? Distance, travel time, per-km fares, best stops on NH48 and booking tips — everything you need before your trip.'],
    'best-places-to-visit-rajasthan-by-car' => ['title' => 'Best Rajasthan Road Trips from Jaipur | Taxi Routes & Distances | MK Cab Service', 'desc' => 'Udaipur, Jodhpur, Jaisalmer, Ranthambore and more — the best Rajasthan destinations you can reach by cab from Jaipur, with distances and travel times.'],
    'outstation-cab-booking-tips' => ['title' => '7 Tips for Booking Outstation Cabs from Jaipur | MK Cab Service', 'desc' => 'Save money and travel safer — 7 practical tips for booking an outstation taxi from Jaipur, from choosing the right car to understanding per-km fares.'],
];

$slug = $_GET['slug'] ?? '';
if (!isset($POSTS[$slug])) {
    http_response_code(404);
    $page_title = 'Post not found | MK Cab Service';
    include __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Post not found</h1><p><a href="/blog.php" style="color:#fff;text-decoration:underline">Back to blog</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $POSTS[$slug]['title'];
$page_desc = $POSTS[$slug]['desc'];
$canonical = '/blog-post.php?slug=' . $slug;
include __DIR__ . '/includes/header.php';
readfile(__DIR__ . '/data/posts/' . $slug . '.html');
include __DIR__ . '/includes/footer.php';
