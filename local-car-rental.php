<?php
require_once __DIR__ . '/includes/config.php';
$page_title = page_field('local', 'seo_title');
$page_desc = page_field('local', 'seo_desc');
$canonical = '/local-car-rental.php';
$wa_page = wa_link("Hello MK Cab Service, I'd like to enquire about Local Car Rental in Jaipur.");
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('local', 'hero_title') ?></h1>
    <p><?= page_field('local', 'hero_sub') ?></p>
    <div class="hero__actions" style="margin-top:22px">
      <a class="btn btn--primary btn--lg" href="<?= $wa_page ?>" target="_blank" rel="noopener">Enquire on WhatsApp</a>
      <a class="btn btn--white btn--lg" href="<?= $wa_page ?>" target="_blank" rel="noopener">💬 <?= PHONE_DISPLAY ?></a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-3" style="margin-bottom:40px">
            <div class="card">
        <h3>8hr / 80km package</h3>
        <p>A full day of city travel with a dedicated car and driver — perfect for sightseeing.</p>
      </div>
            <div class="card">
        <h3>4hr / 40km package</h3>
        <p>Half-day option for meetings, errands and short city plans.</p>
      </div>
            <div class="card">
        <h3>Wedding &amp; event duty</h3>
        <p>Multiple cars, coordinated pickups and decorated vehicles on request.</p>
      </div>
          </div>
    <div class="content-block">
<?= page_field('local', 'body') ?>
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
