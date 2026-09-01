<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Jaipur Airport Taxi | MK Cab Service | +91 91161 71336';
$page_desc = '24x7 pickup and drop for Jaipur International Airport with flight tracking and on-time guarantee — for red-eye departures and late-night landings alike.';
$canonical = '/airport-taxi.php';
$wa_page = wa_link("Hello MK Cab Service, I'd like to enquire about Jaipur Airport Taxi.");
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('airport', 'hero_title') ?></h1>
    <p><?= page_field('airport', 'hero_sub') ?></p>
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
        <h3>Flight tracking</h3>
        <p>Share your flight number and we adjust the pickup automatically if your flight is early or delayed.</p>
      </div>
            <div class="card">
        <h3>On-time, every time</h3>
        <p>Drivers reach before schedule so you never miss a flight or wait at the arrivals gate.</p>
      </div>
            <div class="card">
        <h3>Fixed airport fares</h3>
        <p>Flat, quoted-in-advance pricing for airport transfers within Jaipur — no meter anxiety.</p>
      </div>
          </div>
    <div class="content-block">
<?= page_field('airport', 'body') ?>
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
