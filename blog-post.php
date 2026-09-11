<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
$post = null;
foreach (blog_posts() as $p) {
    if ($p['slug'] === $slug) { $post = $p; break; }
}

if (!$post || blog_post_body($slug) === '') {
    http_response_code(404);
    $page_title = 'Post not found | MK Cab Service';
    include __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Post not found</h1><p><a href="/blog/" style="color:#fff;text-decoration:underline">Back to blog</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = ($post['seo_title'] ?? '') !== '' ? $post['seo_title'] : $post['title'] . ' | ' . SITE_NAME;
$page_desc = ($post['seo_desc'] ?? '') !== '' ? $post['seo_desc'] : ($post['excerpt'] ?? '');
$canonical = blog_url($slug);
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><?= htmlspecialchars($post['date']) ?> · <?= SITE_NAME ?></p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:820px">
    <div class="content-block blog-content">
<?= blog_post_body($slug) ?>
    </div>
    <p style="margin-top:30px"><a style="color:var(--orange-600);font-weight:700" href="/blog/">← Back to all posts</a></p>
  </div>
</section>

<section class="section section--green">
  <div class="container cta-band">
    <h2>Ready to book your cab?</h2>
    <p>Message us your route and dates on WhatsApp — we confirm your car and fare within minutes, 24x7.</p>
    <div class="hero__actions">
      <a class="btn btn--green btn--lg" href="<?= wa_link() ?>" target="_blank" rel="noopener">💬 WhatsApp <?= PHONE_DISPLAY ?></a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
