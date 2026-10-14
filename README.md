# Drive4U — Complete Website Project

A responsive, multi-page HTML/CSS/JavaScript site in Drive4U's navy and gold brand colours. Includes a PHP contact form using PHPMailer over SMTP, local vehicle SVG illustrations, the supplied logo, structured data and technical SEO assets.

## Pages

- `index.html` — Home
- `about.html` — About
- `services.html` — Services and FAQs
- `team.html` — Driver community (actual testimonial drivers, not invented executives)
- `team-details.html` — Individual driver stories (anchor-linked)
- `contact.html` — AJAX contact form
- `privacy.html`, `terms.html`, `404.html`

## Setup — required for a working contact form

1. Upload the entire folder to a **PHP 8.1+ hosting environment with HTTPS**. The HTML pages work without PHP; the contact form requires PHP.
2. Install Composer dependencies in the project root (locally or via SSH):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
   This installs the **PHPMailer library** into `vendor/`. Composer is not installed in this project build environment, so `vendor/` is not included. If your hosting has no Composer, run this command on your computer and upload the generated `vendor/` directory too.
3. Copy `config/mail.example.php` to `config/mail.php`. Fill in SMTP host, port, encryption, username, password, sender and recipient. **Never place SMTP credentials in JS or HTML.**
4. For Apache hosting, `.htaccess` protects the config and vendor directories. For Nginx or other servers, configure equivalent **deny rules** for `/config/`, `/vendor/`, `composer.json`, `.gitignore`, and `README.md` — or move private config/vendor outside the web root and update PHP paths.
5. Verify your SMTP provider allows the configured sender address; set SPF, DKIM and DMARC DNS records for email deliverability.
6. Open `contact.html`, fill in the form and test a real submission. A success message is shown only after PHPMailer reports the email was sent to the SMTP server. Delivery to the inbox can still be affected by filtering.

### Local preview

- For the front end: `python -m http.server 8000` and open `http://localhost:8000/` (the form **will not work** on Python's static server).
- For PHP testing: `php -S localhost:8000` in the project directory, after installing PHPMailer and configuring SMTP.
- For production, use Apache/Nginx with PHP-FPM and HTTPS.

## SEO checklist included

- Unique title and meta description per page; canonical URLs; `lang=en-ZA`.
- Open Graph and Twitter card metadata, local OG graphic, accessible alt text.
- Organization JSON-LD, sitemap.xml, robots.txt, clean internal linking.
- Semantic page headings, mobile viewport, responsive layouts, accessible navigation.
- Compressed supplied logo, local SVG illustration, deferred JavaScript.

## Important before publishing

1. **Confirm the preferred domain**. Current canonical links, sitemap, and JSON-LD use `https://www.drive4u.co.za/`. If the actual website resolves on the non-www hostname, update all references and redirect the other host permanently (301).
2. Update the starter privacy policy and terms with legal review, especially POPIA requirements and actual data-retention practices.
3. Confirm the three testimonials are approved for public display and wording is accurate.
4. Replace initial-style driver avatars with real, licensed/consented photos if available; no real staff biographies were invented.
5. Submit `sitemap.xml` to Google Search Console and Bing Webmaster Tools, verify indexing and performance after deployment.
6. Add real registered business details and physical address to structured data only if confirmed. Do not invent reviews, ratings, business addresses or guaranteed investment returns.
7. Set up server-side rate limits or CAPTCHA if spam volume increases. The form already includes a honeypot, time check, basic session throttle, input validation and same-origin check.
8. For stronger social sharing compatibility, the supplied OG SVG is also rendered as a PNG (`assets/img/og-cover.png`) and used in metadata.

## Libraries and dependencies

- PHPMailer 6.x via Composer (server-side SMTP).
- Google Fonts (DM Sans and Manrope, with system font fallbacks).
- Vanilla JS for mobile menu, FAQ accordions, back-to-top, form validation and AJAX; no heavyweight framework required.

## Contact

Email: info@drive4u.co.za | WhatsApp: +27 67 051 5941
