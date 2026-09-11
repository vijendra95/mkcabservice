<?php
require_once __DIR__ . '/includes/config.php';
header('Content-Type: application/xml; charset=UTF-8');
$base = 'https://mkcabservice.com';
$urls = ['/', '/outstation-cabs.php', '/airport-taxi.php', '/local-car-rental.php', '/tour-packages.php', '/fleet.php', '/blog/', '/about.php', '/contact.php'];
foreach ($ROUTES as $slug => $r) {
    $urls[] = route_url($slug);
}
foreach (blog_posts() as $post) {
    $urls[] = blog_url($post['slug']);
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . htmlspecialchars($base . $u) . "</loc></url>\n";
}
echo "</urlset>\n";
