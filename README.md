# mkcabservice.com

PHP source for the MK Cab Service website (Jaipur taxi service). No database needed — all editable content is stored as JSON/HTML files under `data/`.

## Structure

- `includes/config.php` — default settings, route/fare data, and helpers for editable content
- `includes/page-defaults.php` — default content for the editable pages
- `includes/header.php` / `includes/footer.php` — shared layout (nav, footer)
- `index.php` — home page (route cards, rate table, fare estimator)
- `route.php` — one-way route page template; served via clean URLs like `/jaipur-to-delhi-one-way-taxi`
- `book.php` / `contact.php` — forms forward the enquiry to WhatsApp
- `blog.php`, `blog-post.php` — blog (index in `data/posts.json`, bodies in `data/posts/*.html`)
- `admin/index.php` — the admin panel (see below)
- `.htaccess` — clean URL rewrites + 301 redirects from old `route.php?slug=...-taxi` links
- `sitemap.php`, `robots.txt`

## Deploy (Hostinger)

Upload everything to `public_html/` (keep the `.htaccess` file — it powers the clean URLs and redirects). The `data/` folder must be writable by PHP (permission 755) so the admin panel can save changes.

## Admin panel (`/admin/`)

Password-protected CMS. Default password: `mkcab123` — change it from the Password tab after first login (stored hashed in `data/admin.json`). Tabs:

- **Routes & Fares** — add/edit/delete one-way routes (distance, time, Sedan/SUV/Innova/Tempo fares, tag). Saved to `data/routes.json`.
- **Pages** — WordPress-style editor for the heading, sub-heading, and body content of Home, About, Outstation, Airport, Local Rental, Tours, Fleet, Contact, and the Blog listing page. Saved to `data/pages/<page>.json`; leaving a field empty restores the default. Shortcodes `{{PHONE}}`, `{{EMAIL}}`, `{{ADDRESS}}`, `{{WHATSAPP_LINK}}`, `{{SITE_NAME}}` are replaced on the website automatically.
- **Blog** — create/edit/delete posts with the same rich-text editor (title, date, excerpt, SEO fields, content). Index in `data/posts.json`, bodies in `data/posts/<slug>.html`.
- **Settings** — business name, phone, WhatsApp number, email, address, per-km rates and driver allowances (used by the fare estimator and shown across the site). Saved to `data/settings.json`.

All admin edits go live on the website immediately. Direct browser access to `data/` is blocked by `data/.htaccess`.

The rich-text editor (Jodit) loads from the jsDelivr CDN, so the admin editing screens need internet access in the browser.

## Backups

To back up the site content, download the `data/` folder from Hostinger — it contains every admin edit (routes, settings, page content, blog posts, admin password hash).
