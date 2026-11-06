# WP Rocket — first enable (2026-11)

Local first pass. Plugin is **not** in git (`wp-content/plugins/`);
licence + options are **per environment**. Code-owned guards ship in
`ibv-core` so staging/live inherit the same safety rails after deploy.

## Versions

| Item | Value |
| --- | --- |
| WP Rocket | 3.20.3 |
| Licence account | `it-invoicing@ibizavillas2000.com` (Single — ibizavillas2000.com) |
| Local | Licensed; `WP_CACHE` true |

## Admin settings (conservative)

| Area | Setting |
| --- | --- |
| Cache | Mobile cache on; separate mobile files on; logged-in cache **off** |
| Advanced Rules | Lifespan 10h; never cache `/my-booking/(.*)`, `/booking-confirmation/(.*)`; User Cache off; Cache Query Strings empty |
| File Optimization | Minify/combine/defer/delay JS **off**; minify CSS **off**; Remove Unused CSS / Optimize CSS delivery **off** |
| Media | LazyLoad images / iframes / CSS backgrounds **off**; Imagify not installed |
| Preload | Left on (Rocket default after licence) |

Do **not** turn on Delay JS, Defer JS, Combine JS, RUCSS, or CSS-background
lazyload without a dedicated regression pass (see skill `wp-rocket.md`).

## Code guards (`includes/integrations/wp-rocket.php`)

Force-off via `pre_get_rocket_option_*`: delay_js, defer_all_js, minify_js,
minify_concatenate_js, remove_unused_css, async_css, lazyload_css_bg_img.

Exclusions merged into Delay/Defer/minify JS lists for Gravity Forms,
FacetWP, and `/wp-content/mu-plugins/ibv-core/`.

Never-cache URIs also filtered: `/my-booking`, `/booking-confirmation`.

LazyLoad attribute/src exclusions for logo / hero-ish markup.

## Local verification (2026-11-06)

- Cold + warm homepage: cache files written under
  `wp-content/cache/wp-rocket/ibiza-villas-2000.test/`
- Rocket HTML comment present; footer reCAPTCHA notice present; newsletter
  honeypot not visible
- David: “site feels correct” in browser (home / listing / villa / contact)

## Promote to staging / live

1. Deploy `ibv-core` (Buddy: staging push, then PR → main).
2. On each env: install/activate same WP Rocket build; validate licence.
3. Re-apply the admin settings table above (or export/import Rocket settings).
4. Purge cache; cold + warm smoke (forms, FacetWP, enquiry dates/prices,
   cookie banner).
5. Only then consider turning on image LazyLoad (still keep CSS-background
   lazyload off).
