# mkcabservice.com

PHP source for the MK Cab Service website (Jaipur taxi service).

## Structure

- `includes/config.php` — site settings and the one-way route/fare data (edit fares here)
- `includes/header.php` / `includes/footer.php` — shared layout (nav, footer)
- `index.php` — home page (route cards, rate table, fare estimator)
- `route.php` — one-way route page template; served via clean URLs like `/jaipur-to-delhi-one-way-taxi`
- `book.php` / `contact.php` — forms forward the enquiry to WhatsApp
- `blog.php`, `blog-post.php`, `data/posts/` — blog
- `.htaccess` — clean URL rewrites + 301 redirects from old `route.php?slug=...-taxi` links
- `sitemap.php`, `robots.txt`

## Deploy (Hostinger)

Upload everything in this repo to `public_html/` (keep the `.htaccess` file — it powers the clean URLs and redirects from the old `route.php?slug=...` links).

## Editing fares

All route fares (Sedan / SUV / Innova Crysta / Tempo Traveller) are in `$ROUTES` inside `includes/config.php`. Only parking is extra; the Tempo Traveller driver allowance used by the fare estimator is ₹500 (`ALLOWANCE_TEMPO`).
