<?php
require_once __DIR__ . '/includes/config.php';
$page_title = page_field('blog', 'seo_title');
$page_desc = page_field('blog', 'seo_desc');
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
<?php if (!empty($post['image'])): ?>
        <span class="blog-card__img"><img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy"></span>
<?php else: ?>
        <span class="blog-card__img blog-card__img--empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 16l4.6-4.6a1 1 0 0 1 1.4 0L14 15.4l1.6-1.6a1 1 0 0 1 1.4 0L20 16.8M4 6h16v12H4z"/><circle cx="15.5" cy="9.5" r="1.5"/></svg></span>
<?php endif; ?>
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
