<?php
require_once __DIR__ . '/includes/config.php';
$page_title = page_field('about', 'seo_title');
$page_desc = page_field('about', 'seo_desc');
$canonical = '/about.php';
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('about', 'hero_title') ?></h1>
    <p><?= page_field('about', 'hero_sub') ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="content-block">
<?= page_field('about', 'body') ?>
    </div>
  </div>
</section>

<section class="section section--soft">
  <div class="container">
    <div class="stats">
      <div><div class="stats__num">10+</div><div class="stats__label">Years on Rajasthan roads</div></div>
      <div><div class="stats__num">50,000+</div><div class="stats__label">Happy trips completed</div></div>
      <div><div class="stats__num">75+</div><div class="stats__label">Well-maintained cars</div></div>
      <div><div class="stats__num">4.8<span>★</span></div><div class="stats__label">Average rider rating</div></div>
    </div>
  </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Plan your next trip with us</h2>
    <p>From a quick airport drop to a week across Rajasthan — we're ready when you are.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">💬 WhatsApp <?= PHONE_DISPLAY ?></a>
      <a class="btn btn--white btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">Message Us</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
