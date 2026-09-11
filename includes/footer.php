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

<a class="wa-float" href="<?= wa_link() ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.7.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.7.9-.1.2-.3.2-.5.1a6.6 6.6 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.8.8-.8 1.9-.8 2 0 .2.6 2.3 2.5 3.9 2.3 2 3.3 1.7 3.9 1.6.4 0 1.2-.5 1.4-1 .2-.5.2-.9.1-1z"/></svg>
</a>

<script src="/assets/js/main.js"></script>
</body>
</html>
