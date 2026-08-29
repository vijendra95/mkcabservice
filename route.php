<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';

// Old links like /route.php?slug=jaipur-to-delhi-taxi → 301 to the clean URL.
if (!isset($ROUTES[$slug])) {
    $alt = preg_replace('/-taxi$/', '-one-way-taxi', $slug);
    if (isset($ROUTES[$alt])) {
        header('Location: ' . route_url($alt), true, 301);
        exit;
    }
    http_response_code(404);
    $page_title = 'Route not found | MK Cab Service';
    include __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Route not found</h1><p>The route you are looking for is not available. <a href="/#routes" style="color:#fff;text-decoration:underline">See all routes</a>.</p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Canonical clean URL: redirect direct route.php hits.
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/route.php') === 0) {
    header('Location: ' . route_url($slug), true, 301);
    exit;
}

$r = $ROUTES[$slug];
$title = route_title($r);
$page_title = "$title Taxi | MK Cab Service | " . PHONE_DISPLAY;
$page_desc = "Book a $title one way cab ({$r['km']} km, ~{$r['time']}). Sedan " . inr($r['sedan']) . ", only parking extra. WhatsApp " . PHONE_DISPLAY . '.';
$canonical = route_url($slug);

$wa_route = wa_link("Hello MK Cab Service, I'd like to book a {$r['from']} to {$r['to']} cab.");

$cars = [
    ['name' => 'Sedan', 'models' => 'Dzire, Etios, Aura', 'seats' => '4', 'fare' => $r['sedan']],
    ['name' => 'SUV', 'models' => 'Ertiga, Marazzo', 'seats' => '6-7', 'fare' => $r['suv']],
    ['name' => 'Innova Crysta', 'models' => 'Innova Crysta', 'seats' => '6-7', 'fare' => $r['innova']],
    ['name' => 'Tempo Traveller', 'models' => '12-17 seater', 'seats' => '12-17', 'fare' => $r['tempo']],
];

// Three other routes for the "More routes" section.
$others = [];
foreach ($ROUTES as $s => $o) {
    if ($s !== $slug) { $others[$s] = $o; }
    if (count($others) >= 3) { break; }
}

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= htmlspecialchars($title) ?> Taxi</h1>
    <p><?= $r['km'] ?> km · ~<?= $r['time'] ?> · Sedan <?= inr($r['sedan']) ?> — clean cars, verified drivers and upfront pricing, 24x7.</p>
    <div class="hero__actions" style="margin-top:22px">
      <a class="btn btn--primary btn--lg" href="<?= $wa_route ?>" target="_blank" rel="noopener">Book on WhatsApp</a>
      <a class="btn btn--white btn--lg" href="<?= $wa_route ?>" target="_blank" rel="noopener">💬 <?= PHONE_DISPLAY ?></a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Fares for this route</span>
      <h2><?= htmlspecialchars($r['from']) ?> → <?= htmlspecialchars($r['to']) ?> cab fares</h2>
    </div>
    <table class="info-table">
      <thead><tr><th>Car type</th><th>Models</th><th>Seats</th><th>One-way fare</th></tr></thead>
      <tbody>
<?php foreach ($cars as $c): ?>
        <tr>
          <td><b><?= $c['name'] ?></b></td>
          <td><?= $c['models'] ?></td>
          <td><?= $c['seats'] ?></td>
          <td><b><?= inr($c['fare']) ?></b></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
    <p style="color:var(--ink-500);font-size:0.88rem">Only parking extra. <a style="color:var(--orange-600);font-weight:700" href="<?= $wa_route ?>" target="_blank" rel="noopener">WhatsApp us</a> to book this route.</p>

    <div class="content-block" style="margin-top:30px">
      <h2>Route overview — comfortable cabs from <?= htmlspecialchars($r['from']) ?> to <?= htmlspecialchars($r['to']) ?></h2>
      <p>At MK Cab Service, we make this <?= $r['km'] ?> km trip effortless with clean, well-maintained cars, experienced highway drivers and pricing you can trust. Whether you're travelling for business, a family visit or a holiday, we offer flexible one-way and round-trip options — with a one-way trip you pay only for the side you travel, with no empty return charges.</p>
      <ul>
        <li><b>Distance:</b> ~<?= $r['km'] ?> km</li>
        <li><b>Travel time:</b> approx. <?= $r['time'] ?></li>
        <li><b>Pickup:</b> anywhere in <?= htmlspecialchars($r['from']) ?>, 24x7</li>
        <li><b>Drop:</b> any address in <?= htmlspecialchars($r['to']) ?></li>
      </ul>
    </div>

    <div class="section-head" style="margin-top:40px">
      <span class="eyebrow">FAQs</span>
      <h2><?= htmlspecialchars($title) ?> taxi — common questions</h2>
    </div>
    <div class="faq">
      <details open><summary>What is the <?= htmlspecialchars($title) ?> taxi fare?</summary><p>A one-way <?= htmlspecialchars($title) ?> cab is <?= inr($r['sedan']) ?> for a sedan, <?= inr($r['suv']) ?> for an SUV and <?= inr($r['innova']) ?> for an Innova Crysta. Only parking is extra — WhatsApp us on <?= PHONE_DISPLAY ?> to book.</p></details>
      <details><summary>How long does the <?= htmlspecialchars($title) ?> cab take?</summary><p>The <?= $r['km'] ?> km journey takes about <?= $r['time'] ?>, depending on traffic and stops along the way.</p></details>
      <details><summary>Do you offer a one-way <?= htmlspecialchars($title) ?> taxi?</summary><p>Yes. We offer both one-way and round-trip taxis. One-way means you pay only for a single side with no return fare.</p></details>
      <details><summary>How do I book this route?</summary><p>Just tap "Book on WhatsApp" on this page, or message us on <?= PHONE_DISPLAY ?> with your pickup point, date and time — we confirm your cab within minutes, 24x7.</p></details>
    </div>

    <div class="section-head" style="margin-top:40px">
      <span class="eyebrow">More routes</span>
      <h2>Other popular Jaipur taxi routes</h2>
    </div>
    <div class="grid grid-3">
<?php foreach ($others as $s => $o): ?>
      <a class="route-card" href="<?= route_url($s) ?>">
        <span class="route-card__path"><?= htmlspecialchars($o['from']) ?> <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> <?= htmlspecialchars($o['to']) ?></span>
        <span class="route-card__meta"><span>📍 <b><?= $o['km'] ?> km</b></span></span>
        <span class="route-card__price">from <b><?= inr($o['sedan']) ?></b></span>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Book your <?= htmlspecialchars($title) ?> cab now</h2>
    <p>On-time pickup, clean car and a courteous driver — confirmed in minutes.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="<?= $wa_route ?>" target="_blank" rel="noopener">💬 WhatsApp <?= PHONE_DISPLAY ?></a>
      <a class="btn btn--white btn--lg" href="<?= $wa_route ?>" target="_blank" rel="noopener">Book on WhatsApp</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
