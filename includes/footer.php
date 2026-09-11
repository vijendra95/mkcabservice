<?php require_once __DIR__ . '/config.php'; ?>
</main>
<footer class="site-footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <a class="brand" href="/">
          <img class="brand__logo brand__logo--footer" src="/assets/img/logo.png" alt="MK Cab Service logo">
        </a>
        <p>Dependable taxi service in Jaipur for outstation trips, airport transfers, local rentals and Rajasthan tours — on the road for you 24x7.</p>
<?php $socials = social_links(); if ($socials): ?>
        <div class="footer__social">
<?php foreach ($socials as $sk => $s): ?>
          <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= $s['label'] ?>" title="<?= $s['label'] ?>">
<?php if ($sk === 'facebook'): ?>
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 22v-8h2.7l.4-3.2h-3.1V8.8c0-.9.3-1.6 1.6-1.6h1.7V4.4c-.3 0-1.3-.1-2.4-.1-2.4 0-4.1 1.5-4.1 4.2v2.3H7.6V14h2.7v8z"/></svg>
<?php elseif ($sk === 'instagram'): ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
<?php elseif ($sk === 'youtube'): ?>
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12a31 31 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8zM9.7 15.1V8.9l6 3.1z"/></svg>
<?php else: ?>
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
<?php endif; ?>
          </a>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </div>
      <div>
        <h3>Services</h3>
        <ul class="footer__links">
          <li><a href="/outstation-cabs.php">Outstation Cabs</a></li>
          <li><a href="/airport-taxi.php">Airport Taxi</a></li>
          <li><a href="/local-car-rental.php">Local Car Rental</a></li>
          <li><a href="/tour-packages.php">Tour Packages</a></li>
          <li><a href="/fleet.php">Our Fleet</a></li>
          <li><a href="/blog/">Blog</a></li>
        </ul>
      </div>
      <div>
        <h3>Popular Routes</h3>
        <ul class="footer__links">
<?php foreach (array_slice($ROUTES, 0, 6, true) as $foot_slug => $foot_r): ?>
          <li><a href="<?= route_url($foot_slug) ?>"><?= htmlspecialchars(route_title($foot_r)) ?></a></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h3>Contact</h3>
        <ul class="footer__links">
          <li><a href="<?= wa_link() ?>" target="_blank" rel="noopener">WhatsApp: <?= PHONE_DISPLAY ?></a></li>
          <li><a href="mailto:<?= EMAIL ?>"><?= EMAIL ?></a></li>
          <li><?= ADDRESS ?></li>
        </ul>
      </div>
    </div>
    <div class="footer__bottom">
      <span>© <?= date('Y') ?> MK Cab Service. All rights reserved.</span>
      <span><a href="/contact.php">Contact us</a></span>
    </div>
  </div>
</footer>

<a class="wa-float" href="<?= wa_link() ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars(setting('wa_button_text') ?: 'Chat on WhatsApp') ?>" title="<?= htmlspecialchars(setting('wa_button_text') ?: 'Chat on WhatsApp') ?>">
  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.7.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.7.9-.1.2-.3.2-.5.1a6.6 6.6 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.8.8-.8 1.9-.8 2 0 .2.6 2.3 2.5 3.9 2.3 2 3.3 1.7 3.9 1.6.4 0 1.2-.5 1.4-1 .2-.5.2-.9.1-1z"/></svg>
</a>

<script src="/assets/js/main.js"></script>
</body>
</html>
