<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Travel Blog | MK Cab Service';
$page_desc = 'Travel guides, route tips and Rajasthan trip ideas from the MK Cab Service team — fares, routes and everything you need to plan your next cab journey.';
$canonical = '/blog/';
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('blog', 'hero_title') ?></h1>
    <p><?= page_field('blog', 'hero_sub') ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-3">
<?php foreach (blog_posts() as $post): ?>
      <a class="card blog-card" href="<?= blog_url($post['slug']) ?>">
        <span class="blog-card__date"><?= htmlspecialchars($post['date']) ?></span>
        <h3><?= htmlspecialchars($post['title']) ?></h3>
        <p><?= htmlspecialchars($post['excerpt']) ?></p>
        <span class="blog-card__more">Read more →</span>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Planning a trip?</h2>
    <p>Message us your route and dates on WhatsApp — we'll share an exact fare within minutes, 24x7.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">💬 WhatsApp <?= PHONE_DISPLAY ?></a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
