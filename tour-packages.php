<?php
require_once __DIR__ . '/includes/config.php';
$page_title = page_field('tours', 'seo_title');
$page_desc = page_field('tours', 'seo_desc');
$canonical = '/tour-packages.php';
$wa_page = wa_link("Hello MK Cab Service, I'd like to enquire about Rajasthan Tour Packages.");
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('tours', 'hero_title') ?></h1>
    <p><?= page_field('tours', 'hero_sub') ?></p>
    <div class="hero__actions" style="margin-top:22px">
      <a class="btn btn--primary btn--lg" href="<?= $wa_page ?>" target="_blank" rel="noopener">Enquire on WhatsApp</a>
      <a class="btn btn--white btn--lg" href="<?= $wa_page ?>" target="_blank" rel="noopener">💬 <?= PHONE_DISPLAY ?></a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-3" style="margin-bottom:40px">
<?php foreach (site_tours() as $t): $t_wa = wa_link("Hello MK Cab Service, I'd like to enquire about the {$t['title']} tour package."); ?>
      <div class="card tour-card">
<?php if (!empty($t['image'])): ?>
        <div class="tour-card__img"><img src="<?= htmlspecialchars($t['image']) ?>" alt="<?= htmlspecialchars($t['title']) ?>" loading="lazy"></div>
<?php endif; ?>
        <div class="tour-card__meta">
<?php if (!empty($t['duration'])): ?><span>⏱ <?= htmlspecialchars($t['duration']) ?></span><?php endif; ?>
<?php if (!empty($t['price'])): ?><span class="tour-card__price"><?= htmlspecialchars($t['price']) ?></span><?php endif; ?>
        </div>
        <h3><?= htmlspecialchars($t['title']) ?></h3>
        <p><?= htmlspecialchars($t['desc'] ?? '') ?></p>
<?php $hl = array_filter(array_map('trim', explode("\n", (string)($t['highlights'] ?? '')))); if ($hl): ?>
        <ul class="tour-card__list">
<?php foreach ($hl as $h): ?>
          <li><?= htmlspecialchars($h) ?></li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>
        <a class="btn btn--green" href="<?= $t_wa ?>" target="_blank" rel="noopener" style="margin-top:auto">Enquire on WhatsApp</a>
      </div>
<?php endforeach; ?>
    </div>
    <div class="content-block">
<?= page_field('tours', 'body') ?>
    </div>

    
      </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Need a cab? We're one message away</h2>
    <p>Tell us your plan on WhatsApp and we'll confirm your car, driver and fare within minutes — 24x7.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="<?= $wa_page ?>" target="_blank" rel="noopener">💬 WhatsApp <?= PHONE_DISPLAY ?></a>
      <a class="btn btn--white btn--lg" href="<?= $wa_page ?>" target="_blank" rel="noopener">Enquire Now</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
