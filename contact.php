<?php
require_once __DIR__ . '/includes/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $msg = "Hello MK Cab Service, message from website contact form.\n"
         . "Name: $name\nMobile: $phone\n"
         . ($email !== '' ? "Email: $email\n" : '')
         . "Message: $message";
    header('Location: ' . wa_link($msg));
    exit;
}
$page_title = 'Contact Us | MK Cab Service';
$page_desc = 'Contact MK Cab Service, Jaipur — WhatsApp or send us a message for bookings and quotes. 53, Rd Number 1, Malhotra Nagar, VKI, Jaipur, Rajasthan 302039';
$canonical = '/contact.php';
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= page_field('contact', 'hero_title') ?></h1>
    <p><?= page_field('contact', 'hero_sub') ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="hero__grid" style="align-items:start">
      <div class="content-block">
<?= page_field('contact', 'body') ?>
        <div class="hero__actions">
          <a class="btn btn--primary btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">💬 Chat on WhatsApp</a>
        </div>
      </div>

      <div class="booking">
        <h2>Send us a message</h2>
        <p>We'll get back to you as soon as possible.</p>
                <form method="post" action="/contact.php">
          <div class="field">
            <label for="c-name">Your name</label>
            <input type="text" id="c-name" name="name" placeholder="Full name" required>
          </div>
          <div class="field-row">
            <div class="field">
              <label for="c-phone">Mobile</label>
              <input type="tel" id="c-phone" name="phone" placeholder="10-digit mobile">
            </div>
            <div class="field">
              <label for="c-email">Email (optional)</label>
              <input type="email" id="c-email" name="email" placeholder="you@example.com">
            </div>
          </div>
          <div class="field">
            <label for="c-message">Message</label>
            <textarea id="c-message" name="message" rows="4" placeholder="Tell us about your trip..." required></textarea>
          </div>
          <button type="submit" class="btn btn--green btn--block btn--lg">Send Message →</button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
