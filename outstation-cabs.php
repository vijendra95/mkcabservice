<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Outstation Cabs from Jaipur | MK Cab Service | +91 91161 71336';
$page_desc = 'One-way and round-trip taxis from Jaipur to Delhi, Udaipur, Agra, Jodhpur and every major city in Rajasthan and North India — at fair per-km rates with verified drivers.';
$canonical = '/outstation-cabs.php';
$wa_page = wa_link("Hello MK Cab Service, I'd like to enquire about Outstation Cabs from Jaipur.");
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('outstation', 'hero_title') ?></h1>
    <p><?= page_field('outstation', 'hero_sub') ?></p>
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
        <h3>One-way &amp; round trips</h3>
        <p>Pay only for what you use. One-way drops or multi-day round trips, both with clear per-km rates.</p>
      </div>
            <div class="card">
        <h3>Experienced highway drivers</h3>
        <p>Drivers who know the routes, the best food stops and the safest night-driving practices.</p>
      </div>
            <div class="card">
        <h3>All-inclusive quotes</h3>
        <p>We tell you the full estimated fare before you book — fuel and driver allowance included.</p>
      </div>
          </div>
    <div class="content-block">
<?= page_field('outstation', 'body') ?>
    </div>

        <div class="section-head" style="margin-top:40px">
      <span class="eyebrow">Transparent pricing</span>
      <h2>Per-km rates by car type</h2>
    </div>
    <table class="info-table">
      <thead><tr><th>Car type</th><th>Models</th><th>Seats</th><th>Per-km rate</th></tr></thead>
      <tbody>
                <tr><td><b>Sedan</b></td><td>Dzire, Etios, Aura</td><td>4</td><td><b>₹<?= RATE_SEDAN ?>/km</b></td></tr>
                <tr><td><b>SUV</b></td><td>Ertiga, Marazzo</td><td>6-7</td><td><b>₹<?= RATE_SUV ?>/km</b></td></tr>
                <tr><td><b>Innova Crysta</b></td><td>Innova Crysta</td><td>6-7</td><td><b>₹<?= RATE_INNOVA ?>/km</b></td></tr>
                <tr><td><b>Tempo Traveller</b></td><td>12-17 seater</td><td>12-17</td><td><b>₹<?= RATE_TEMPO ?>/km</b></td></tr>
              </tbody>
    </table>
    <p style="color:var(--ink-500);font-size:0.88rem">Minimum 250 km/day for round trips, driver allowance ₹<?= ALLOWANCE_DEFAULT ?>/day (Tempo Traveller ₹<?= ALLOWANCE_TEMPO ?>/day). Only parking extra. <a style="color:var(--orange-600);font-weight:700" href="<?= $wa_page ?>" target="_blank" rel="noopener">WhatsApp for exact quote</a>.</p>
    
        <div class="section-head" style="margin-top:40px">
      <span class="eyebrow">FAQs</span>
      <h2>Outstation Cabs from Jaipur — common questions</h2>
    </div>
    <div class="faq">
            <details open><summary>Do you charge for the return journey on one-way trips?</summary><p>No. On a one-way outstation cab you pay only for the distance you travel — there is no empty-return charge.</p></details>
            <details ><summary>What is included in the outstation fare?</summary><p>The per-km rate covers the AC car, fuel and driver. Driver allowance is shown separately in your quote. Only parking is billed extra.</p></details>
            <details ><summary>Can I make stops on the way?</summary><p>Yes — short breaks for food, tea and photos are always fine. For long detours, just tell us in advance so we can adjust the estimate.</p></details>
            <details ><summary>How do I get an exact quote?</summary><p>Message us on WhatsApp with your destination, date and preferred car — we reply with an exact all-inclusive quote within minutes, 24x7.</p></details>
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
