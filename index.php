<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'MK Cab Service | Taxi & Cab Booking in Jaipur | ' . PHONE_DISPLAY;
$page_desc = 'Book reliable cabs in Jaipur with MK Cab Service. Outstation taxis, airport transfers, local rentals and Rajasthan tours — 24x7. WhatsApp ' . PHONE_DISPLAY . '.';
$canonical = '/';
include __DIR__ . '/includes/header.php';
?>

<!-- HERO + BOOKING -->
<section class="hero">
  <div class="container">
    <div class="hero__grid">
      <div>
        <h1><?= page_field('home', 'hero_title') ?></h1>
        <p class="lead"><?= page_field('home', 'hero_sub') ?></p>
        <ul class="hero__usps">
          <li><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg> Transparent pricing, no surprises</li>
          <li><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg> Police-verified drivers</li>
          <li><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg> Clean, well-maintained cars</li>
          <li><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg> 24x7 pickup, on time</li>
        </ul>
        <div class="hero__actions">
          <a class="btn btn--primary btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.7.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.7.9-.1.2-.3.2-.5.1a6.6 6.6 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.8.8-.8 1.9-.8 2 0 .2.6 2.3 2.5 3.9 2.3 2 3.3 1.7 3.9 1.6.4 0 1.2-.5 1.4-1 .2-.5.2-.9.1-1z"/></svg>
            Book on WhatsApp
          </a>
          <a class="btn btn--white btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">💬 <?= PHONE_DISPLAY ?></a>
        </div>
        <div class="hero__trust">
          <span class="stars">★★★★★</span>
          <span>4.8/5 rating from riders across Rajasthan</span>
        </div>
      </div>

      <!-- Booking card -->
      <div class="booking">
        <h2>Get an instant fare quote</h2>
        <p>Share your trip details and we&#039;ll get back to you within minutes.</p>
        <div class="booking__tabs" role="tablist" aria-label="Trip type">
          <button role="tab" aria-selected="true" data-trip="Outstation One-Way">One-Way</button>
          <button role="tab" aria-selected="false" data-trip="Outstation Round-Trip">Round Trip</button>
          <button role="tab" aria-selected="false" data-trip="Airport Transfer">Airport</button>
          <button role="tab" aria-selected="false" data-trip="Local / Hourly Rental">Local</button>
        </div>
        <form id="booking-form" method="post" action="/book.php">
          <input type="hidden" name="trip" id="trip-type" value="Outstation One-Way">
          <div class="field">
            <label for="pickup">Pickup location</label>
            <input type="text" id="pickup" name="pickup" placeholder="e.g. Jaipur Railway Station" required>
          </div>
          <div class="field" id="drop-field">
            <label for="drop">Drop location</label>
            <input type="text" id="drop" name="drop" placeholder="e.g. Delhi Airport">
          </div>
          <div class="field-row">
            <div class="field">
              <label for="pickup-date">Pickup date</label>
              <input type="date" id="pickup-date" name="date" required>
            </div>
            <div class="field">
              <label for="pickup-time">Time</label>
              <input type="time" id="pickup-time" name="time" value="09:00">
            </div>
          </div>
          <div class="field-row">
            <div class="field">
              <label for="cust-name">Your name</label>
              <input type="text" id="cust-name" name="name" placeholder="Full name" required>
            </div>
            <div class="field">
              <label for="cust-phone">Mobile</label>
              <input type="tel" id="cust-phone" name="phone" placeholder="10-digit mobile" pattern="[0-9 +]{10,14}" required>
            </div>
          </div>
          <button type="submit" class="btn btn--green btn--block btn--lg">Request Booking →</button>
          <p class="booking__note">Prefer to chat? WhatsApp us on <a href="<?= wa_link() ?>" target="_blank" rel="noopener"><?= PHONE_DISPLAY ?></a> — 24x7</p>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="section" id="why">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Why choose MK Cab Service</span>
      <h2>Safe, reliable &amp; genuinely comfortable</h2>
      <p class="lead">The promises we keep on every single trip — for your peace of mind, comfort and budget.</p>
    </div>
    <div class="why">
      <div class="why__item">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M16 3l10 4v7c0 7-4.5 12-10 15C10.5 26 6 21 6 14V7z" fill="#1a2456"/><path d="M11.5 16l3 3 6-6.5" stroke="#ffc107" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <h3>Safety first</h3>
        <p>Police-verified drivers, well-serviced cars and live trip sharing so your family always knows you're safe.</p>
      </div>
      <div class="why__item">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="15" cy="17" r="11" fill="#f47b20"/><path d="M11 11h8M11 14.5h8M18 11c0 4-3 5.5-7 5.5l7 6" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M27 5l1 2.2 2.2 1-2.2 1-1 2.2-1-2.2-2.2-1 2.2-1z" fill="#ffc107"/></svg></div>
        <h3>Honest fares</h3>
        <p>Clear pricing quoted upfront. No surge, no hidden tolls, no last-minute additions.</p>
      </div>
      <div class="why__item">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M9 7h6a5 5 0 0 1 5 5v8H9z" fill="#1a2456"/><path d="M20 13h3a3 3 0 0 1 3 3v4" stroke="#1a2456" stroke-width="2.2" stroke-linecap="round"/><rect x="7" y="20" width="16" height="6" rx="2.5" fill="#f47b20"/></svg></div>
        <h3>Comfort guaranteed</h3>
        <p>Neat, sanitised interiors, AC, good legroom and polite, professionally trained chauffeurs.</p>
      </div>
      <div class="why__item">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="11" fill="#1a2456"/><path d="M16 9v7l5 3" stroke="#ffc107" stroke-width="2.4" stroke-linecap="round"/><circle cx="16" cy="16" r="1.7" fill="#fff"/></svg></div>
        <h3>24x7 on time</h3>
        <p>Round-the-clock bookings and punctual pickups — early flights and late-night arrivals covered.</p>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="section section--soft" id="services">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Our services</span>
      <h2>One trusted cab partner for every journey</h2>
    </div>
    <div class="grid grid-4">
      <a class="card service-card" href="/outstation-cabs.php">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M9 25c-3 0-3-5 0-5h9c4 0 4-7 0-7h-7" stroke="#1a2456" stroke-width="2.4" stroke-linecap="round" stroke-dasharray="0.5 4.5"/><path d="M7 3a4.5 4.5 0 0 1 4.5 4.5C11.5 11 7 14 7 14S2.5 11 2.5 7.5A4.5 4.5 0 0 1 7 3z" fill="#ffc107"/><circle cx="7" cy="7.5" r="1.6" fill="#1a2456"/><path d="M25 18a4.5 4.5 0 0 1 4.5 4.5C29.5 26 25 29 25 29s-4.5-3-4.5-6.5A4.5 4.5 0 0 1 25 18z" fill="#f47b20"/></svg></div>
        <h3>Outstation Cabs</h3>
        <p>One-way and round-trip intercity travel across Rajasthan and North India at fair rates.</p>
        <span class="card__link">Explore <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </a>
      <a class="card service-card" href="/airport-taxi.php">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M4 20l24-9.5-3.5 11.5-7.5-3-4 5.5-1.5-6.5z" fill="#1a2456"/><path d="M17 21l7.5-10" stroke="#ffc107" stroke-width="2" stroke-linecap="round"/><circle cx="26.5" cy="25.5" r="2.2" fill="#f47b20"/></svg></div>
        <h3>Airport Taxi</h3>
        <p>Punctual Jaipur airport pickup and drop with flight tracking, day or night.</p>
        <span class="card__link">Explore <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </a>
      <a class="card service-card" href="/local-car-rental.php">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M5 20l2-6.2A4 4 0 0 1 10.8 11h8.4A4 4 0 0 1 23 13.8L25 20z" fill="#1a2456"/><rect x="3.5" y="19.5" width="23" height="4.5" rx="2.2" fill="#1a2456"/><circle cx="9.5" cy="24.5" r="3.2" fill="#f47b20"/><circle cx="20.5" cy="24.5" r="3.2" fill="#f47b20"/></svg></div>
        <h3>Local Car Rental</h3>
        <p>Hourly 8hr/80km packages for city sightseeing, shopping, weddings and business meetings.</p>
        <span class="card__link">Explore <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </a>
      <a class="card service-card" href="/tour-packages.php">
        <div class="card__icon"><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M6 26V15l5-3.5L16 8l5 3.5 5 3.5v11z" fill="#1a2456"/><path d="M16 4.5c1.6 1 1.6 3 0 4-1.6-1-1.6-3 0-4z" fill="#f47b20"/><circle cx="10.5" cy="13" r="2" fill="#ffc107"/><circle cx="21.5" cy="13" r="2" fill="#ffc107"/><rect x="14" y="19" width="4" height="7" rx="1" fill="#dde3f5"/></svg></div>
        <h3>Tour Packages</h3>
        <p>Curated Rajasthan tours — Golden Triangle, Jaipur sightseeing and multi-day road trips.</p>
        <span class="card__link">Explore <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      </a>
    </div>
  </div>
</section>

<!-- POPULAR ROUTES -->
<section class="section" id="routes">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">One way taxi routes</span>
      <h2>Top Jaipur taxi routes at transparent fares</h2>
      <p class="lead">Fixed prices for the trips our riders book most — only parking extra. Tap any route for details.</p>
    </div>
    <div class="grid grid-3">
<?php foreach ($ROUTES as $slug => $r): ?>
      <a class="route-card" href="<?= route_url($slug) ?>">
        <?php if ($r['tag']): ?><span class="tag"><?= htmlspecialchars($r['tag']) ?></span><?php endif; ?>
        <span class="route-card__path"><?= htmlspecialchars($r['from']) ?> <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> <?= htmlspecialchars($r['to']) ?></span>
        <span class="route-card__meta"><span>📍 <b><?= $r['km'] ?> km</b></span><span>⏱ ~<?= $r['time'] ?></span></span>
        <span class="route-card__price">Sedan <b><?= inr($r['sedan']) ?></b></span>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- RATE TABLE -->
<section class="section section--soft" id="rate-table">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Jaipur Transport Rate Table</span>
      <h2>One-way taxi fares at a glance</h2>
      <p class="lead">Fixed one-way fares for every route — only parking extra.</p>
    </div>
    <table class="info-table">
      <thead><tr><th>Route</th><th>Sedan</th><th>SUV</th><th>Innova Crysta</th><th>Tempo Traveller</th></tr></thead>
      <tbody>
<?php foreach ($ROUTES as $slug => $r): ?>
        <tr>
          <td><a href="<?= route_url($slug) ?>" style="font-weight:700;color:var(--ink-900)"><?= htmlspecialchars(route_title($r)) ?></a></td>
          <td><b><?= inr($r['sedan']) ?></b></td>
          <td><?= inr($r['suv']) ?></td>
          <td><?= inr($r['innova']) ?></td>
          <td>On Demand</td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
    <p style="color:var(--ink-500);font-size:0.88rem;text-align:center">Only parking extra. <a style="color:var(--orange-600);font-weight:700" href="<?= wa_link() ?>" target="_blank" rel="noopener">WhatsApp us</a> to book any route.</p>
  </div>
</section>

<!-- FARE ESTIMATOR -->
<section class="section" id="fare-estimator">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Instant fare estimator</span>
      <h2>Estimate your cab fare in seconds</h2>
      <p class="lead">Pick a route or enter the distance and car type for a clear estimate — then book it on WhatsApp.</p>
    </div>
    <div class="booking" style="max-width:780px;margin-inline:auto">
      <div class="field-row">
        <div class="field">
          <label for="fe-route">Popular route (optional)</label>
          <select id="fe-route">
            <option value="">— Custom distance —</option>
<?php foreach ($ROUTES as $slug => $r): ?>
            <option value="<?= $r['km'] ?>"><?= htmlspecialchars($r['from']) ?> → <?= htmlspecialchars($r['to']) ?> (<?= $r['km'] ?> km)</option>
<?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="fe-distance">Distance (km)</label>
          <input type="number" id="fe-distance" min="1" max="2000" value="268" inputmode="numeric">
        </div>
      </div>
      <div class="field-row">
        <div class="field">
          <label for="fe-car">Car type</label>
          <select id="fe-car">
            <option value="<?= RATE_SEDAN ?>" data-allowance="<?= ALLOWANCE_DEFAULT ?>" selected>Sedan · ₹<?= RATE_SEDAN ?>/km</option>
            <option value="<?= RATE_SUV ?>" data-allowance="<?= ALLOWANCE_DEFAULT ?>">SUV · ₹<?= RATE_SUV ?>/km</option>
            <option value="<?= RATE_INNOVA ?>" data-allowance="<?= ALLOWANCE_DEFAULT ?>">Innova Crysta · ₹<?= RATE_INNOVA ?>/km</option>
            <option value="0">Tempo Traveller · On Demand</option>
          </select>
        </div>
        <div class="field">
          <label for="fe-trip">Trip type</label>
          <select id="fe-trip">
            <option value="1">One-way</option>
            <option value="2">Round trip</option>
          </select>
        </div>
      </div>
      <div id="fe-result" style="background:var(--orange-50);border:1px solid var(--orange-100);border-radius:var(--radius-sm);padding:18px 20px;text-align:center;margin:6px 0 14px">
        <div style="font-size:0.82rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--orange-600)">Estimated fare</div>
        <div id="fe-amount" data-allowance="<?= ALLOWANCE_DEFAULT ?>" style="font-size:2rem;font-weight:800;color:var(--ink-900);letter-spacing:-0.02em">₹—</div>
        <div id="fe-breakdown" style="font-size:0.85rem;color:var(--ink-500)"></div>
      </div>
      <button type="button" id="fe-book" data-wa="<?= WHATSAPP_NUMBER ?>" class="btn btn--green btn--block btn--lg">Book this estimate on WhatsApp →</button>
      <p class="booking__note">Indicative estimate. Only parking extra. <a href="<?= wa_link() ?>" target="_blank" rel="noopener">WhatsApp for exact quote</a>.</p>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Booking in 4 simple steps</span>
      <h2>Booking a cab has never been this easy</h2>
    </div>
    <div class="steps">
      <div class="step"><div class="step__num"></div><h3>Tell us your trip</h3><p>Share pickup, drop, date &amp; time via the form or WhatsApp.</p></div>
      <div class="step"><div class="step__num"></div><h3>Get an instant quote</h3><p>We confirm an upfront fare within minutes.</p></div>
      <div class="step"><div class="step__num"></div><h3>Confirm your cab</h3><p>Receive driver &amp; car details. Pay cash, UPI or online — your choice.</p></div>
      <div class="step"><div class="step__num"></div><h3>Sit back &amp; relax</h3><p>A clean, comfortable cab arrives at your door, right on time.</p></div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section section--green">
  <div class="container">
    <div class="stats">
      <div><div class="stats__num">10+</div><div class="stats__label">Years on Rajasthan roads</div></div>
      <div><div class="stats__num">50,000+</div><div class="stats__label">Happy trips completed</div></div>
      <div><div class="stats__num">75+</div><div class="stats__label">Well-maintained cars</div></div>
      <div><div class="stats__num">4.8<span>★</span></div><div class="stats__label">Average rider rating</div></div>
    </div>
  </div>
</section>

<!-- FLEET PREVIEW -->
<section class="section">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Our fleet</span>
      <h2>Choose the right cab for your journey</h2>
      <p class="lead">From economical sedans to spacious tempo travellers — all clean, AC and driver-included.</p>
    </div>
    <div class="grid grid-4">
      <div class="card fleet-card">
        <div class="fleet-card__img">
          <img src="/assets/img/uploads/20260824-071645-bc4decfd.jpg" alt="Sedan" loading="lazy">
        </div>
        <h3>Sedan</h3>
        <p>Dzire, Etios, Aura · AC, music system, comfortable seating</p>
        <ul><li>4 seats</li><li>3 bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹<?= RATE_SEDAN ?>/km</b></div>
      </div>
      <div class="card fleet-card">
        <div class="fleet-card__img">
          <img src="/assets/img/uploads/20260824-071825-80b8dc57.jpg" alt="SUV" loading="lazy">
        </div>
        <h3>SUV</h3>
        <p>Ertiga, Marazzo · AC, spacious, ideal for families</p>
        <ul><li>6-7 seats</li><li>4 bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹<?= RATE_SUV ?>/km</b></div>
      </div>
      <div class="card fleet-card">
        <div class="fleet-card__img">
          <img src="/assets/img/uploads/20260824-071844-51443bed.jpg" alt="Innova Crysta" loading="lazy">
        </div>
        <h3>Innova Crysta</h3>
        <p>Innova Crysta · Premium comfort, captain seats</p>
        <ul><li>6-7 seats</li><li>5 bags</li><li>AC</li></ul>
        <div class="fleet-card__rate">From <b>₹<?= RATE_INNOVA ?>/km</b></div>
      </div>
      <div class="card fleet-card">
        <div class="fleet-card__img">
          <img src="/assets/img/uploads/20260824-071858-368e5b73.jpg" alt="Tempo Traveller" loading="lazy">
        </div>
        <h3>Tempo Traveller</h3>
        <p>12-17 seater · Group travel, push-back seats</p>
        <ul><li>12-17 seats</li><li>15+ bags</li><li>AC</li></ul>
        <div class="fleet-card__rate"><b>On Demand</b></div>
      </div>
    </div>
    <div class="text-center mt-3"><a class="btn btn--ghost btn--lg" href="/fleet.php">View full fleet &amp; pricing →</a></div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section section--soft">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Rider reviews</span>
      <h2>Trusted by travellers across Rajasthan</h2>
    </div>
    <div class="grid grid-3">
      <figure class="testimonial">
        <div class="stars">★★★★★</div>
        <p>"Booked a sedan for Delhi drop. Car was neat, driver reached before time and the price was exactly what was quoted. Very satisfied with MK Cab Service."</p>
        <figcaption class="testimonial__who"><span class="testimonial__avatar">R</span><span><b>Ramesh Gupta</b><span>Jaipur → Delhi</span></span></figcaption>
      </figure>
      <figure class="testimonial">
        <div class="stars">★★★★★</div>
        <p>"Needed an early morning airport pickup. Driver called the night before to confirm and arrived on the dot. Smooth and safe ride."</p>
        <figcaption class="testimonial__who"><span class="testimonial__avatar">S</span><span><b>Sunita Agarwal</b><span>Airport Transfer</span></span></figcaption>
      </figure>
      <figure class="testimonial">
        <div class="stars">★★★★★</div>
        <p>"Took an Innova for a 5-day Rajasthan trip with family. Driver knew all the good stops and never rushed us. Fair rates, no surprises at the end."</p>
        <figcaption class="testimonial__who"><span class="testimonial__avatar">V</span><span><b>Vikas Jain</b><span>Rajasthan Tour</span></span></figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">FAQs</span>
      <h2>MK Cab Service — your questions answered</h2>
    </div>
    <div class="faq">
      <details open><summary>How do I book a cab with MK Cab Service?</summary><p>WhatsApp us on <?= PHONE_DISPLAY ?>, or fill the booking form on this page. We confirm your booking within minutes, any time of day.</p></details>
      <details><summary>What are your outstation charges from Jaipur?</summary><p>Every route has a fixed one-way fare — see the rate table above. Only parking is extra. WhatsApp us for a quote for your route — what we quote is what you pay.</p></details>
      <details><summary>Do you provide Jaipur airport pickup and drop?</summary><p>Yes, we run 24x7 airport transfers with on-time pickup. Share your flight details and we track the arrival so the driver is ready when you land.</p></details>
      <details><summary>Which payment methods do you accept?</summary><p>Cash, UPI, cards and bank transfer. For most local and airport trips you can pay after the ride.</p></details>
      <details><summary>Can I book a car for a multi-day tour?</summary><p>Yes. We arrange multi-day Rajasthan tours with experienced drivers — Golden Triangle, Udaipur-Jodhpur circuits or a fully custom plan.</p></details>
      <details><summary>Are your drivers verified?</summary><p>All our drivers are police-verified, experienced on Rajasthan routes and trained to be courteous and helpful throughout your trip.</p></details>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="section section--green">
  <div class="container cta-band">
    <h2>Ready to ride? Your cab is a tap away</h2>
    <p>Book in under a minute. Honest fares, clean cars, courteous drivers — 24x7 across Jaipur and all of Rajasthan.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">💬 WhatsApp <?= PHONE_DISPLAY ?></a>
      <a class="btn btn--white btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">Book on WhatsApp</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
