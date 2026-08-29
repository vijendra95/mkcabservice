<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Our Fleet & Pricing | MK Cab Service';
$page_desc = 'Sedans, SUVs, Innova Crysta and Tempo Travellers with driver — see seats, luggage capacity and per-km rates for every car in the MK Cab Service fleet.';
$canonical = '/fleet.php';
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1>Our Fleet &amp; Pricing</h1>
    <p>Every car is cleaned before your trip, serviced on schedule and comes with an experienced, verified driver.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-4">
            <div class="card fleet-card">
        <div class="fleet-card__img">
                    <img src="/assets/img/uploads/20260824-071645-bc4decfd.jpg" alt="Sedan" loading="lazy">
                  </div>
        <h3>Sedan</h3>
        <p>Dzire, Etios, Aura</p>
        <p style="margin-top:6px">AC, music system, comfortable seating</p>
        <ul><li>4 seats</li><li>3 bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹11/km</b></div>
      </div>
            <div class="card fleet-card">
        <div class="fleet-card__img">
                    <img src="/assets/img/uploads/20260824-071825-80b8dc57.jpg" alt="SUV" loading="lazy">
                  </div>
        <h3>SUV</h3>
        <p>Ertiga, Marazzo</p>
        <p style="margin-top:6px">AC, spacious, ideal for families</p>
        <ul><li>6-7 seats</li><li>4 bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹15/km</b></div>
      </div>
            <div class="card fleet-card">
        <div class="fleet-card__img">
                    <img src="/assets/img/uploads/20260824-071844-51443bed.jpg" alt="Innova Crysta" loading="lazy">
                  </div>
        <h3>Innova Crysta</h3>
        <p>Innova Crysta</p>
        <p style="margin-top:6px">Premium comfort, captain seats</p>
        <ul><li>6-7 seats</li><li>5 bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹19/km</b></div>
      </div>
            <div class="card fleet-card">
        <div class="fleet-card__img">
                    <img src="/assets/img/uploads/20260824-071858-368e5b73.jpg" alt="Tempo Traveller" loading="lazy">
                  </div>
        <h3>Tempo Traveller</h3>
        <p>12-17 seater</p>
        <p style="margin-top:6px">Group travel, push-back seats</p>
        <ul><li>12-17 seats</li><li>15+ bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹35/km</b></div>
      </div>
          </div>

    <div class="content-block" style="margin-top:44px">
      <h2>What the rate includes</h2>
      <ul>
        <li>AC car with fuel and an experienced driver</li>
        <li>Driver allowance shown separately in your quote — no hidden markups</li>
        <li>Only parking billed extra</li>
      </ul>
      <p>WhatsApp <a style="color:var(--orange-600);font-weight:700" href="https://wa.me/919116171336?text=Hello%20MK%20Cab%20Service%2C%20I%27d%20like%20to%20book%20a%20cab." target="_blank" rel="noopener">+91 91161 71336</a> for an exact quote for your trip — we confirm fares before you book, and the price never changes after.</p>
    </div>

    <div class="section-head" style="margin-top:40px">
      <span class="eyebrow">FAQs</span>
      <h2>Fleet &amp; pricing — common questions</h2>
    </div>
    <div class="faq">
      <details open><summary>Which car should I choose for my trip?</summary><p>For 1–4 travellers a sedan is the most economical. Families with luggage prefer an SUV or Innova Crysta for extra space, and groups of 9+ travel best in a Tempo Traveller. Message us on WhatsApp and we'll suggest the right car for your route and group size.</p></details>
      <details><summary>Are the per-km rates fixed?</summary><p>Yes — rates are transparent and confirmed before your trip. Your quote includes fuel and driver; tolls, state tax and parking are billed at actuals with receipts.</p></details>
      <details><summary>Are the cars clean and well-maintained?</summary><p>Every car is cleaned before your trip and serviced on schedule. All our vehicles are fuel-efficient, AC models driven by experienced, verified drivers.</p></details>
      <details><summary>Can I book a car for multiple days?</summary><p>Absolutely. For round trips and multi-day tours we bill a minimum of 250 km/day plus a driver allowance per day — WhatsApp us your plan for an exact package price.</p></details>
    </div>
  </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Not sure which car fits your plan?</h2>
    <p>Tell us your route, dates and group size on WhatsApp — we'll recommend the best car and share an exact fare in minutes.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="https://wa.me/919116171336?text=Hello%20MK%20Cab%20Service%2C%20I%27d%20like%20to%20book%20a%20cab." target="_blank" rel="noopener">💬 WhatsApp +91 91161 71336</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
