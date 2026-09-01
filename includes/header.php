<?php
require_once __DIR__ . '/config.php';
$page_title = $page_title ?? SITE_NAME . ' | Taxi & Cab Booking in Jaipur | ' . PHONE_DISPLAY;
$page_desc = $page_desc ?? 'Book reliable cabs in Jaipur with ' . SITE_NAME . '. Outstation taxis, airport transfers, local rentals and Rajasthan tours — 24x7. WhatsApp ' . PHONE_DISPLAY . '.';
$canonical = $canonical ?? null;
?><!DOCTYPE html>
<html lang="en-IN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
<?php if ($canonical): ?><link rel="canonical" href="https://mkcabservice.com<?= htmlspecialchars($canonical) ?>">
<?php endif; ?>
<meta name="theme-color" content="#0b3d91">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%230b3d91'/%3E%3Cpath d='M6 20l2-6a3 3 0 0 1 2.8-2h10.4A3 3 0 0 1 24 14l2 6v4a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-1H9v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1z' fill='%23ffc107'/%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap">
<link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<div class="topbar">
  <div class="container">
    <div class="topbar__badges">
      <span class="hide-sm">Honest fares, no hidden charges</span>
      <span>Rated 4.8/5 by our riders</span>
    </div>
    <div><a href="<?= wa_link() ?>" target="_blank" rel="noopener">24x7 WhatsApp Booking: <?= PHONE_DISPLAY ?></a></div>
  </div>
</div>

<header class="site-header">
  <div class="container nav">
    <a class="brand" href="/" aria-label="MK Cab Service home">
      <img class="brand__logo" src="/assets/img/logo.png" alt="MK Cab Service logo">
    </a>

    <nav aria-label="Primary">
      <ul class="nav__links" id="primary-nav">
        <li><a href="/">Home</a></li>
        <li class="has-drop">
          <a href="/#services" aria-haspopup="true">Services ▾</a>
          <ul class="drop">
            <li><a href="/outstation-cabs.php">Outstation Cabs</a></li>
            <li><a href="/airport-taxi.php">Airport Taxi</a></li>
            <li><a href="/local-car-rental.php">Local Car Rental</a></li>
            <li><a href="/tour-packages.php">Tour Packages</a></li>
          </ul>
        </li>
        <li class="has-drop">
          <a href="/#routes" aria-haspopup="true">One Way Taxi ▾</a>
          <ul class="drop">
<?php foreach ($ROUTES as $nav_slug => $nav_r): ?>
            <li><a href="<?= route_url($nav_slug) ?>"><?= htmlspecialchars($nav_r['from']) ?> → <?= htmlspecialchars($nav_r['to']) ?></a></li>
<?php endforeach; ?>
          </ul>
        </li>
        <li><a href="/fleet.php">Fleet</a></li>
        <li><a href="/blog.php">Blog</a></li>
        <li><a href="/about.php">About</a></li>
        <li><a href="/contact.php">Contact</a></li>
      </ul>
    </nav>

    <div class="nav__cta">
      <a class="btn btn--green" href="<?= wa_link() ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.7.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.7.9-.1.2-.3.2-.5.1a6.6 6.6 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.8.8-.8 1.9-.8 2 0 .2.6 2.3 2.5 3.9 2.3 2 3.3 1.7 3.9 1.6.4 0 1.2-.5 1.4-1 .2-.5.2-.9.1-1z"/></svg>
        WhatsApp Us
      </a>
      <button class="nav__toggle" aria-label="Open menu" aria-controls="primary-nav" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
  </div>
</header>
<main id="main-content" tabindex="-1">
