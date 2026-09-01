<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Rajasthan Tour Packages | MK Cab Service | +91 91161 71336';
$page_desc = 'Multi-day road trips across Rajasthan with a dedicated car and driver — Golden Triangle, lakes of Udaipur, the blue city of Jodhpur and the dunes of Jaisalmer.';
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
            <div class="card">
        <h3>Golden Triangle</h3>
        <p>Jaipur – Agra – Delhi circuit, typically 3-4 days, ideal for first-time visitors.</p>
      </div>
            <div class="card">
        <h3>Royal Rajasthan</h3>
        <p>Jaipur – Pushkar – Udaipur – Jodhpur – Jaisalmer, 6-8 days of forts, lakes and desert.</p>
      </div>
            <div class="card">
        <h3>Custom itineraries</h3>
        <p>Tell us your dates and interests — we plan the route, stops and stays around you.</p>
      </div>
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
