# Image audit: live media vs rendered sizes

**Date:** 2026-11-04  
**Scope:** investigation only (no `wp-content/` fixes). Local = live DB + uploads pull.  
**Artifacts:** `image-audit-2026-11-04.csv` (committed). Screenshots under `docs/audits/img-2026-11-04/` (not committed; ~140 MB).

## 1. Answer first

Photos look soft on live mainly because most villa originals are about **1,000–1,600 px wide** while a full-bleed Retina hero needs roughly **2,880 px** (class **A**), and **~95% of in-use attachments never got the registered `ibv-*` crops** so WordPress falls back to the full file (class **B**) — which still cannot invent missing pixels. Page heroes add a template limit: a **single CSS background URL** capped at `ibv-hero` 1920×1080 (class **C**), so even a good 2,500 px original only partly covers a 2× desktop hero.

Among soft photo renders measured here: roughly **~70% A**, **~15% B**, **~10% C**, **~5% D**.

## 2. Environment (step 1)

| Check | Value |
| --- | --- |
| Newest `post_modified` | 2026-11-04 12:18:01 |
| Newest uploads mtime | 2026-11-04 ~19:04 (local clock; folder refreshed with pull) |
| `siteurl` / `home` | `http://ibiza-villas-2000.test` |
| Git | `f7eb245` — Fix villa enquiry 500 on Gravity Forms 3 feed processing |
| WordPress | 7.0.4 |
| Theme | `ibv` |
| Active plugins | FluentSMTP, Gravity Forms, ACF Pro, FacetWP, GF Zoho CRM, Redirection, Yoast SEO (+ Premium), WP Migrate DB Pro |
| Inactive (noted) | BE Media from Production (still has staging constants; **inactive**, not rewriting URLs) |

**Local vs live `<img>` markup:** Before the step‑6 regenerate experiment, homepage and villa-listing `src`/`srcset` matched live after stripping domains (same counts and paths). After regenerating the home hero attachment on local only, the home CSS `--ibv-hero-image` diverges from live as expected (`…terrac-.jpg` on live vs `…terrac--1920x1080.jpg` on local). Live remained read-only.

## 3. Configuration (step 2)

Registered subsizes (`wp_get_registered_image_subsizes()`):

| Size | Dimensions | Crop |
| --- | --- | --- |
| thumbnail | 150×150 | hard |
| medium | 300×300 | soft |
| medium_large | 768×0 | soft |
| large | 1024×1024 | soft |
| 1536×1536 | 1536×1536 | soft |
| 2048×2048 | 2048×2048 | soft |
| **ibv-hero** | **1920×1080** | **hard** |
| **ibv-card** | **1200×800** | **hard** |
| **ibv-square** | **800×800** | **hard** |
| **ibv-trust-logo** | **600×200** | soft |
| gform-image-choice-* | 300 / 400 / 600 | hard |

| Setting | Effective value |
| --- | --- |
| `big_image_size_threshold` | 2560 |
| JPEG quality (`jpeg_quality` / `wp_editor_set_quality`) | 82 |
| `max_srcset_image_width` | 2048 |

**Image-related hooks:** No optimiser/CDN/lazy-load plugins active. `ibv-core` registers sizes on `after_setup_theme` and briefly filters `wp_get_attachment_image_attributes` inside `ibv_core_image()` to omit default `sizes` on eager images. Legacy meta still lists old theme sizes (`property-featured-image`, `property-gallery-image`, etc.) on older uploads.

## 4. Media library counts (step 3 / CSV)

Source: every villa featured + `property_images` gallery image, page/villa heroes, Home + Site Options image fields, and post featured images that appear in those surfaces — **607 unique attachments**.

| Metric | Count |
| --- | --- |
| Attachments in use | **607** |
| Originals under 1920 px wide (disk) | **473** |
| Originals under 2500 px wide (disk) | **500** |
| Missing `ibv-hero` in meta | **575** |
| Missing `ibv-card` in meta | **573** |
| Missing `ibv-square` in meta | **572** |
| Any of the three `ibv-*` present | **35** |
| Size files listed in meta but missing on disk | **0** |
| Scaled originals (`-scaled` / `original_image`) | **32** |

Oldest featured-image upload among published villas: **Villa Daniel**, attachment `#2811` (`poolSM.jpg`, 2015-10-29).

## 5. Call sites and missing-size fallback (step 4)

| File:line | Component | Size | Output | `sizes` | `loading` |
| --- | --- | --- | --- | --- | --- |
| `sections/hero/hero.php:56` | Page hero | `ibv-hero` | Single CSS `--ibv-hero-image` URL | n/a | n/a |
| `sections/villa-hero/villa-hero.php:33` | Villa hero | `ibv-hero` | `<img>` + srcset | `(min-width: 64rem) 66vw, 100vw` | eager (+ fetchpriority) |
| `components/villa-card/villa-card.php:185` | Villa card | `ibv-card` | srcset | WP default / `auto` | lazy |
| `components/gallery/gallery.php:163,173` | Gallery main | `large` | srcset | `(min-width: 64rem) 66vw, 100vw` | eager |
| `components/gallery/gallery.php:188` | Gallery thumbs | `medium` | srcset | WP default | lazy |
| `components/gallery/gallery.php:52` | Lightbox data | `full` | URL only (JS) | — | — |
| `components/offer-panel/offer-panel.php:118` | Offer panel | `ibv-card` | srcset | WP default | lazy |
| `components/accommodation-tile/accommodation-tile.php:47` | Accommodation tile | `ibv-card` | srcset | WP default | lazy |
| `components/article-card/article-card.php:54` | Article card | `ibv-card` | srcset | WP default | lazy (default) |
| `sections/featured-article/featured-article.php:75` | Featured article | `ibv-card` | srcset | WP default | lazy |
| `sections/villa-listing-hero/villa-listing-hero.php:79,139` | Listing hero media | `ibv-card` | srcset | WP default | lazy (default) |
| `sections/about-team/about-team.php:65` | Team | `ibv-card` | srcset | WP default | lazy (default) |
| `sections/image-entries-section/…:105` | Image entries | `ibv-card` | srcset | WP default | lazy (default) |
| `components/image-text-section/…:107` | Image + text | `ibv-card` | srcset | WP default | lazy |
| `sections/special-offers-empty-state/…:63` | Offers empty | `ibv-card` | srcset | WP default | lazy (default) |
| `sections/trust-strip/trust-strip.php:41` | Trust logos | `ibv-trust-logo` | srcset | WP default | lazy |
| `sections/why-iv2000/why-iv2000.php:42` | Pillar icons | `thumbnail` | srcset | WP default | lazy |
| `sections/contact-enquiry/…:157` | Form banner | `large` | srcset | WP default | lazy |
| `themes/ibv/single.php:50` | Article featured | `ibv-hero` | srcset | `(max-width: 72rem) 100vw, 1152px` | eager |
| `themes/ibv/singular.php:29` | Singular thumb | `ibv-hero` | srcset | omitted if eager default path | lazy default unless attrs |
| `themes/ibv/archive.php:34` | Archive cards | `ibv-card` | srcset | WP default | lazy (default) |

### Fallback when a named size is missing

On Casa Peppe featured `#17363` (disk 2500×1669, no `ibv-hero` in meta):

- `wp_get_attachment_image_url( $id, 'ibv-hero' )` → **full original URL**
- `wp_get_attachment_image_src( $id, 'ibv-hero' )` → same URL, width/height **constrained** to fit the registered 1920×1080 box (here 1618×1080), `is_intermediate = false`
- `wp_get_attachment_image()` → full file as `src`, with a normal srcset of **existing** intermediates (300…2048, legacy sizes), **not** an `ibv-hero` file

Core: `image_downsize()` in `wp-includes/media.php` — if `image_get_intermediate_size()` finds nothing, it uses the full URL and meta dimensions, then `image_constrain_size_for_editor()` shrinks the **reported** width/height to the requested size ([WP 7.0.4 `media.php` ~241–275](https://developer.wordpress.org/reference/functions/image_downsize/)). Docs agree: missing intermediates fall back to the full-size image URL.

WordPress does **not** upscale when generating sizes: originals narrower than 1920 never gain an `ibv-hero` file even after `wp media regenerate`.

## 6. What renders (step 5)

Playwright: desktop 1440×900 @ DPR 2; mobile iPhone 14 (DPR 3). Pages: Home, `/all-villas/`, Special Offers, guide article `ibiza-accommodation-hacks`, Villa Bella Vista, Villa Nieves, Villa Daniel. Natural widths taken from disk (`sips`) for the `currentSrc` / CSS background file after scroll-loading.

Screenshots (local only, not committed): `docs/audits/img-2026-11-04/{page}--{desktop|mobile}.png` (14 files, ~140 MB total).

### Worst ratios (photos / heroes first)

| Ratio | Page / VP | Surface | File width | Needed (CSS×DPR) | File |
| --- | ---: | --- | --- | ---: | --- |
| 0.36 | villa-daniel / desktop | villa hero | 1023 | 2880 | `poolSM.jpg` |
| 0.36 | villa-nieves / desktop | villa hero | 1024 | 2880 | `Villa-nieves.jpg` |
| 0.52 | special-offers / desktop | page hero (bg) | 1500 | 2880 | `Ibiza-villas-2000-UNVR.jpg` |
| 0.56 | villa-bella-vista / desktop | villa hero | 1600 | 2880 | bella-vista featured |
| 0.60 | guide-article / desktop | article featured | 1322 | 2208 | weather guide JPG |
| 0.66 | villa-nieves|daniel / desktop | gallery main (`large`) | 1024 | 1548 | gallery hero |
| 0.78 | villa-listing / mobile | villa card | 800 | 1026 | Villa Alexa PNG |
| 0.87 | home / desktop | page hero (bg) | 2500 | 2880 | Tegui terrace (full, pre-regen) |
| ~1.00 | listing / mobile | many villa cards | ~1024 | 1026 | various 1024 px featured |

Icons/logos (trust strip, pillar icons, footer marks) also dip below 1 on mobile; they are small assets, not villa photography.

**Desktop villa cards with ≥1536 px candidates in srcset generally stay ≥1.0** even without `ibv-card`, because the browser picks 768–2048 intermediates. Softness there is mostly compression/crop of small originals, not missing srcset candidates.

## 7. Before / after regenerate (step 6, local only)

| Sample | Attachment | Before file / natural / ratio | After `wp media regenerate` | Class |
| --- | --- | --- | --- | --- |
| Villa card (Alexa, mobile) | `#18215` (800×533) | full PNG / 800 / **0.78** | unchanged — still no `ibv-card`/`ibv-hero` (source too small to generate) | **A** |
| Villa hero (Daniel, desktop) | `#2811` (1023×682) | `poolSM.jpg` / 1023 / **0.36** | unchanged — still no `ibv-hero` | **A** |
| Page hero (Home, desktop) | `#17842` (2500×1667) | full JPG / 2500 / **0.87** | `…-1920x1080.jpg` / 1920 / **0.67**; meta now has `ibv-hero` + `ibv-card` | **B** (+ **C** remains) |

Regenerating the home hero **proves** missing sizes are the reason the full file was served. It also shows a trap: for a CSS background, switching from a 2500 px full file to a 1920 px crop **lowers** Retina ratio until the template can use a larger asset or responsive backgrounds. Small originals do not improve with regenerate.

## 8. Villas with originals under 2500 px

*(Client-forwardable. Widths are the photo files on disk, not “how big they look on screen”.)*

Several villas still rely on photos that are under 2,500 pixels wide. On phones and especially on large screens those images will look soft until higher-resolution photos are uploaded.

**Main listing photo under 2,500 px wide**

| Villa | Main photo width |
| --- | ---: |
| Villa Alexa (Can Vincent) | 800 px |
| Villa Daniel | 1,023 px |
| Villa Can Tunicu | 1,024 px |
| Villa KM2 (Villa Maria) | 1,024 px |
| Villa Nieves | 1,024 px |
| Villa Pep Luis (Can Pep Mortera) | 1,024 px |
| Villa Savines | 1,024 px |
| Villa Tom (Can Petit) | 1,024 px |
| Villa Bella Vista (Ses Rotes) | 1,600 px |

**Galleries with many under-2,500 px photos** (count of gallery images under 2,500 px wide; narrowest gallery image noted)

| Villa | How many gallery photos under 2,500 px | Narrowest |
| --- | ---: | ---: |
| Villa Nieves | 44 | 683 px |
| Villa Pep Luis | 41 | 576 px |
| Villa KM2 | 32 | 652 px |
| Villa Tom | 31 | 683 px |
| Villa Daniel | 29 | 1,023 px |
| Villa Savines | 26 | 900 px |
| Villa Bella Vista | 25 | 1,600 px |
| Villa Can Tunicu | 23 | 1,024 px |
| Villa Can Vincent | 22 | 1,600 px |
| Villa Alexa | 18 | 798 px |
| Villa Tegui | 15 | 1,600 px |
| Beautiful Ibiza Villa with private security | 13 | 1,668 px |
| Villa Torres | 5 | 1,024 px |

Villas whose **main** photo is already ≥2,500 px (e.g. Casa Peppe / private security, Villa Tegui, Villa Torres, Villa Can Vincent featured) still need a one-time regenerate of WordPress sizes on the server so the site can serve the intended crops — but the soft look on older villas is mostly the photo resolution itself.

## 9. Recommended fixes (cheapest first)

| # | Fix | Classes | What | Code vs media | Rough size |
| --- | --- | --- | --- | --- | --- |
| 1 | Regenerate intermediates on live for attachments with originals ≥1200 px (or ≥1920 for heroes) that lack `ibv-*` | **B** | Creates `ibv-hero` / `ibv-card` / `ibv-square` where sources allow. Do **not** expect improvement on ~1024 px libraries. Prefer a targeted ID list from the CSV over a blind full-library run. | Media (WP-CLI / plugin on live) | Half day ops + QA |
| 2 | Replace main + key gallery photos under ~2000 px for priority villas (Alexa, Daniel, Nieves, Pep Luis, Tom, KM2, Savines, Can Tunicu, Bella Vista) | **A** | New exports ≥2500 px wide (3000+ ideal for Retina heroes). | Media / client | Ongoing; per-villa shoot or re-export |
| 3 | Page hero: stop relying on a single `ibv-hero` CSS URL — use `<img>`/`<picture>` with srcset, or a 2×-aware background source | **C** | Home / Special Offers / other page heroes. Avoid regenerating into 1920-only crops without this, or Retina can look worse. | Code brief | Small–medium |
| 4 | Revisit gallery main size (`large` ≈1024) vs layout (`66vw` / full width) | **C** / **D** | Either request a wider uncropped size or accept soft gallery teasers on desktop. | Code brief | Small |
| 5 | Trust / icon assets: supply 2× PNG/SVG | **A** / **D** | Cosmetic, not villa softness. | Media | Small |
| 6 | Optional: raise `ibv-hero` width (e.g. 2560) **after** photos exist, then regenerate | **C** | Only helps when sources are large enough; pairs with #2–3. | Code + media | Medium |

**Do not** treat a full-library regenerate as the whole fix: step 6 shows it is a no-op on small originals and can reduce page-hero Retina quality when the template stays on a single 1920 px URL.
