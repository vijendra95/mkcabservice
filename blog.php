<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Travel Blog | MK Cab Service';
$page_desc = 'Travel guides, route tips and Rajasthan trip ideas from the MK Cab Service team — fares, routes and everything you need to plan your next cab journey.';
$canonical = '/blog.php';
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1>Travel Blog</h1>
    <p>Route guides, fare explainers and Rajasthan trip ideas — straight from the drivers&#039; seat.</p>
  </div>
</section>

<section class="section">
  <div class="container">
        <div class="grid grid-3">
            <a class="card blog-card" href="/blog-post.php?slug=best-places-to-visit-rajasthan-by-car">
        <span class="blog-card__date">22 Aug 2026</span>
        <h3>Best Places to Visit in Rajasthan by Car — Routes from Jaipur</h3>
        <p>From the lakes of Udaipur to the dunes of Jaisalmer — the best road-trip destinations from Jaipur, with distances and drive times.</p>
        <span class="blog-card__more">Read more →</span>
      </a>
            <a class="card blog-card" href="/blog-post.php?slug=outstation-cab-booking-tips">
        <span class="blog-card__date">22 Aug 2026</span>
        <h3>7 Smart Tips Before Booking an Outstation Cab from Jaipur</h3>
        <p>From choosing the right car to understanding what a per-km fare really includes — seven practical tips for a smooth outstation trip.</p>
        <span class="blog-card__more">Read more →</span>
      </a>
            <a class="card blog-card" href="/blog-post.php?slug=jaipur-to-delhi-taxi-guide">
        <span class="blog-card__date">22 Aug 2026</span>
        <h3>Jaipur to Delhi Taxi — Complete Travel Guide, Fare &amp; Tips</h3>
        <p>Everything you need to know before booking a Jaipur to Delhi cab — distance, fares, the NH48 route, best food stops and smart booking tips.</p>
        <span class="blog-card__more">Read more →</span>
      </a>
          </div>
      </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Planning a trip?</h2>
    <p>Message us your route and dates on WhatsApp — we'll share an exact fare within minutes, 24x7.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="https://wa.me/919116171336?text=Hello%20MK%20Cab%20Service%2C%20I%27d%20like%20to%20book%20a%20cab." target="_blank" rel="noopener">💬 WhatsApp +91 91161 71336</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
