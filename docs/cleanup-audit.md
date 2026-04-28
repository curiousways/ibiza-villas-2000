# Rudeibiza Theme — Cleanup Audit

> Pass 1 of 3. Read-only analysis.
> Output of this pass becomes the spec for pass 2 (strip) and pass 3 (boilerplate).
> **Note:** Repo theme directory is **`ibiza-villas-2000`** (`style.css`: *Ibiza Villas 2000*). Legacy URIs often still mention **`rudeibiza`**.

Generated: April 28, 2026
Theme: Ibiza Villas 2000 · version **1.0** (`style.css` header)
Total files in theme: **249** (excluding `.git`)
Code files inventoried (`*.php`, `*.css`, `*.js`, `*.scss`, `*.json`): **154**

Classified (file-level, section 13 exhaustive alphabetical list): REMOVE **97** · REFACTOR **57**
*(Logical CPT/ACF KEEP items are enumerated in §1 and §5 — pass 3 rewrites almost every theme file despite many rows reading **REFACTOR** versus **REMOVE** here.)*

---

## 0. Client Sanity-Check

Items below are visible to end-users or content editors and are slated for REMOVE in pass 2. David clears these with the client (Luke) before pass 2 runs.

| Item | Surface (where the user/editor sees it) | Removal reason | Replacement (if any) |
|---|---|---|---|
| FAQ post type | WP admin “FAQs”; widgets on legacy sidebars | New About page uses repeater FAQs (pass 3) | ACF repeater on About |
| Testimonials CPT + TripAdvisor-era reviews widget / DB-fed reviews UI | Villas “reviews” tabs; testimonial CPT | External API / new content model per rebuild | Bob’s narrative + legal review |
| `rude_brand` CPT + Brands nav/footer/menu-property widgets | Old multi-brand IA | One brand IV2000 | Primary nav content only |
| `custom_weeks` CPT + widget | Editorial “custom week” placements | Removed from roadmap | Editorial pages or specials |
| Property wishlist (template + Gravity Forms emails + cookie flow) | “Wishlist” page and buttons | Confirmed REMOVE | Villa shortlist via API enquiry later if needed |
| FacetWP search UI (`facetwp-*`, template shortcodes) | All villas / property results grids | REPLACE with villa_search API | Bob’s injected results |
| Visual Composer `[vc_*]` shortcodes stored in pages | Accordions/layout in e.g. all-villas | Plugin + shortcodes REMOVE | Plain blocks / editor |
| Geographic listing pages (“villas in San Antonio”, etc.) | Old SEO landing URLs | Outside 15-page scope | Consolidated listing + Ibiza Guide |
| Apartments/sale artefacts | Sidebar “apartments”, `/sale/` branches | Rental-only rebuild | Static cross-link copy |
| For-sale villas page (`page-villas-for-sale.php`) | Sale funnel | REMOVE per brief | Static / legal redirects if needed |
| Custom weeks page, transfers, testimonials page, FAQ page template(s) | Dedicated legacy routes | REMOVE | Sections absorbed into roadmap pages |
| Slick sliders on property/feature rows | Homepage / similar properties | REPLACE with native gallery (pass 3) | Same |
| Footer Comoyoti / admin “comoyoti.agency@gmail.com” | WP admin footer | Agency branding REMOVE | Neutral support line |
| Yoast FAQ JSON-LDR hardcoded slug map (`add_faq_json_schema`) | Specific FAQ page head output | Depends on migrated FAQ UX | Yoast/block schema or repeal |

---

## 1. Custom Post Types

| CPT | Registration file | Post count | Classification | Evidence |
|---|---|---|---|---|
| `villas` | `functions/properties-cpt.php` | unknown — DB query needed | **KEEP** (refactor placement pass 3) | Core villa inventory matches wireframes 2–3 |
| `testimonials` | `functions/tripadvisor-cpt.php` | unknown — DB query needed | **REMOVE** | TripAdvisor-era; reviews DB coupling removed |
| `faq` | `functions/faq-cpt.php` | unknown — DB query needed | **REMOVE** | FAQs → About repeater (pass 3) |
| `rude_brand` | `functions/rude-brand-cpt.php` | unknown — DB query needed | **REMOVE** | Legacy brands; confirmed REMOVE |
| `custom_weeks` | `functions/custom-week-cpt.php` | unknown — DB query needed | **REMOVE** | Confirmed REMOVE |
| `promotion_slider` | `functions/promotion-slider-cpt.php` | unknown — DB query likely zero if unused | **REMOVE** (dead registration) | File **not** `require_once` in `functions.php` `core_theme_setup`; CPT effectively not loaded from theme bootstrap |

---

## 2. Custom Taxonomies

| Taxonomy | Registration file | Term count | Classification | Evidence |
|---|---|---|---|---|
| `property_location` | `functions/properties-cpt.php` | unknown — DB query needed | **REFACTOR** | Villa listing filters; survives with taxonomy vs API-driven labels TBD |
| `villa_type` | `functions.php` (`add_custom_villa_taxonomies`) | unknown — DB query needed | **REFACTOR** | Same |
| `faq-category` | `functions/faq-cpt.php` | unknown — DB query needed | **REMOVE** | Tied to FAQ CPT REMOVAL |

`apartments` **CPT**: **not registered in theme** (no `register_post_type('apartments')` in theme grep). Listing templates/widgets still assume it — likely **plugin or DB content** · flag pass 2 to confirm source.

---

## 3. Custom Widgets

| Widget class | Registration file | Used in widget area? | Classification | Evidence |
|---|---|---|---|---|
| `custom_week_widget` | `functions/custom-week-widget.php` | Pluggable via Appearance (no DB check) | **REMOVE** | Confirmed REMOVE |
| `faq_categories_widget` | `functions/faq-categories-widget.php` | Pluggable | **REMOVE** | Confirmed REMOVE |
| `rude_brands_nav_widget` | `functions/rude-brands-nav-widget.php` | Pluggable | **REMOVE** | Confirmed REMOVE |
| `rude_brands_footer_widget` | `functions/rude-brands-footer-widget.php` | Pluggable | **REMOVE** | Confirmed REMOVE |
| `menu_property_widget` | `functions/menu-property-widget.php` | Pluggable | **REMOVE** | Confirmed REMOVE |
| `featured_properties_widget` | `functions/featured-properties-widget.php` | Pluggable (`apartment-sidebar-1`) | **REFACTOR then REMOVE** obsolete patterns | Depends on CPT queries + sale exclusions |
| `similar_property_widget` | `functions/related-properties-widget.php` | Pluggable (`villa-sidebar-1`) | **REFACTOR** | Relationship logic may become API-derived |
| `related_pages_widget` | `functions/related-pages-widget.php` | Pluggable | **REMOVE** | Legacy Related Pages UX |
| `testimonials_widget` | `functions/testimonials-widget.php` | Pluggable | **REMOVE** | TripAdvisor CPT |
| `explore_widget` | `functions/explore-widget.php` | Pluggable | **REMOVE** | Explore carousel duplicate of marketing |
| `promotion_slide_widget` | `functions/promotion-slider-widget.php` | N/A — file not required | **REMOVE** | Widget registration file never required from `functions.php` |

---

## 4. Template Files

### 4a. Theme root templates

| File | Classification | Evidence |
|---|---|---|
| `404.php` | REFACTOR | Keep route; strip builder classes / dead assets |
| `archive.php` | REFACTOR | Post archive for Ibiza Guide (wireframe 7) — trim legacy |
| `content-search.php` | REMOVE | WP search superseded by API-driven listing |
| `featured-services.php` | REMOVE | Duplicate of partial; orphan risk |
| `featured-testimonials.php` | REMOVE | Testimonial surface |
| `front-page.php` | REFACTOR | Homepage wireframe 1; hardcoded URLs + Foundation grid |
| `full-width.php` | REMOVE | Legacy page shell |
| `full-width-balance.php` | REMOVE | Legacy |
| `full-width-como.php` | REMOVE | Legacy |
| `full-width-nomads.php` | REMOVE | Legacy “boat” marketing |
| `home.php` | REFACTOR | Blog home if blog used for guide |
| `index.php` | REFACTOR | Fallback |
| `loop.php` | REFACTOR | Post loop fallback |
| `page-all-villas.php` | REFACTOR | Maps to wireframe 2 but Facet + VC shortcode |
| `page-all-villas-2.php` | REMOVE | Duplicate experiment |
| `page-contact.php` | REFACTOR | Wireframe 9; Gravity Forms KEEP |
| `page-custom-weeks.php` | REMOVE | Confirmed CPT REMOVE |
| `page-frequently-asked-questions.php` | REMOVE | FAQs → About |
| `page-homenew.php` | REMOVE | Legacy duplicate home |
| `page-how-to-book.php` | REMOVE | Outside scoped 15 pages unless merged into About |
| `page-special-offers.php` | REFACTOR | Wireframe 4; strip IP/geo/FX hacks per REMOVE list |
| `page-testimonials.php` | REMOVE | Testimonials CPT |
| `page-transfers.php` | REMOVE | Outside 15-page list unless Concierge absorbs |
| `page-villas-for-sale.php` | REMOVE | Sale REMOVE |
| `page-villas-in-ibiza-town.php` | REMOVE | Geo landing |
| `page-villas-in-north-island.php` | REMOVE | Geo landing |
| `page-villas-in-playa-den-bossa.php` | REMOVE | Geo landing |
| `page-villas-in-san-antonio.php` | REMOVE | Geo landing |
| `page-villas-in-san-josep.php` | REMOVE | Geo landing |
| `page-villas-in-san-rafel.php` | REMOVE | Geo landing |
| `ppage.php` | REMOVE | “Power Page” legacy |
| `search.php` | REMOVE | SearchWP/API replaces |
| `searchform.php` | REFACTOR | Bob header search shell |
| `sidebar.php` | REFACTOR | Primary sidebar scaffold |
| `single-villas.php` | REFACTOR | Wireframe 3; ~1000+ lines Facet/maps/sale/12511 |

**Headers / footers**

| File | Classification | Evidence |
|---|---|---|
| `header.php` | REFACTOR | Legacy header alternate |
| `header-new.php` | REFACTOR | Primary front-end shell for Bob search |
| `header-balance.php` | REMOVE | Alternate balance header |
| `header-power.php` | REMOVE | Alternate |
| `header-2020.php` | REMOVE | Legacy |
| `header-new old.php` | REMOVE | Stale duplicate filename |
| `footer.php` | REFACTOR | KEEP legal block content; MOVE to site options pass 3 |
| `footercomo.php` | REMOVE | Alt footer variant |
| `footer-old.php` | REMOVE | Alt footer |

### 4b. Partials in `templates/`

| File | Classification | Evidence |
|---|---|---|
| `templates/banner-home.php` | REMOVE | Heroes replaced in new designs |
| `templates/breadcrumbs.php` | REFACTOR | If retained in pass 3 |
| `templates/content.php` | REFACTOR | Single post/content |
| `templates/explore-carousel.php` | REMOVE | Marketing carousel |
| `templates/explore-other.php` | REMOVE | Marketing |
| `templates/explore.php` | REMOVE | Marketing explorer |
| `templates/fb-like.php` | REMOVE | Legacy social |
| `templates/featured-boxes.php` | REFACTOR | Homepage modules until replaced |
| `templates/featured-properties.php` | REFACTOR → REMOVE sliders | Featured strip |
| `templates/featured-services.php` | REFACTOR | Homepage “services” strip |
| `templates/featured-testimonials.php` | REMOVE | Legacy testimonials strip |
| `templates/head-tracking.php` | REFACTOR | Tracking partial — review with marketing |
| `templates/home-header-offer-como.php` | REMOVE | Legacy |
| `templates/home-header-offer.php` | REMOVE | Legacy |
| `templates/latest-news.php` | REFACTOR | Blog/news for guide |
| `templates/loop-grid.php` | REFACTOR | Grid shell |
| `templates/loop-grid-part-list.php` | REFACTOR | Listing card |
| `templates/loop-grid-part.php` | REFACTOR | Card variant |
| `templates/loop-grid-part-list-sale.php` | REMOVE | Sale branching |
| `templates/loop-grid-special-offers-part.php` | REFACTOR | Special offers grids |
| `templates/loop-grid-special-offers-home-part.php` | REFACTOR | Home specials teaser |
| `templates/loop-property.php` | REMOVE | Per-villa `dd_show_calendar`, sale, 12511, heavy legacy |
| `templates/loop-property_boat.php` | REMOVE | Nomads/boat CPT surface |
| `templates/nav-topbar.php` | REMOVE | Foundation top bar |
| `templates/nav-topbarnew.php` | REFACTOR | Nav partial |
| `templates/nav-topbarbalance.php` | REMOVE | Alternate |
| `templates/property-price-from-to*.php` | REFACTOR | Price teaser — Bob replaces |
| `templates/property-price-sale.php` | REMOVE | Sale |
| `templates/property-results.php` | REMOVE | Facet “Property Results” page template shell |
| `templates/property-search-facet.php` | REMOVE | FacetWP |
| `templates/property-view-list.php` | REMOVE | Wishlist list UI |
| `templates/property-wishlist.php` | REMOVE | Confirmed REMOVE |
| `templates/property_availability/availability.php` | REMOVE | Calendar path Option D REMOVE |
| `templates/rude-team.php` | REFACTOR | Meet the team → About modular |
| `templates/search-home-new.php` | REFACTOR | Home search partial |
| `templates/search-home.php` | REMOVE | Legacy search |
| `templates/search-horizontal.php` | REMOVE | Facet horizontal |
| `templates/search-vertical.php` | REMOVE | Facet vertical |
| `templates/similar-properties.php` | REFACTOR | Strip Slick; API relation later |
| `templates/single-apartment-availability.php` | REMOVE | Apartments |
| `templates/single-page-*.php` | REMOVE | Testimonial specials |
| `templates/single-property-features.php` | REFACTOR | Villa detail facets |
| `templates/single-property-map.php` | REFACTOR | Maps key rotation |
| `templates/single-property-more-info.php` | REFACTOR | Airstream fork 12511 REMOVE |
| `templates/single-property-price.php` | REFACTOR | Bob pricing panel |
| `templates/single-property-reviews.php` | REMOVE | Second DB credentials; Trip aggregate |
| `templates/single-property-special-offer.php` | REFACTOR | Offer ribbons |
| `templates/single-property-summary.php` | REFACTOR | Detail header |
| `templates/single-property-testimonials.php` | REMOVE | CPT testimonial embedding |
| `templates/single-property-testimonial.php` | REMOVE | Legacy typo filename |
| `templates/single-property-video.php` | REFACTOR | Media |
| `templates/social-buttons.php` | REFACTOR | Social icons |
| `templates/social-share.php` | REFACTOR | Share |
| `templates/spanish_law.php` | REMOVE | Dual-field branching collapsed pass 3 |
| `templates/special-offer-properties.php` | REFACTOR | Marketing carousel |
| `templates/special-offers.php` | REFACTOR | Offer listing partial |
| `templates/testimonials.php` | REMOVE | Old testimonial carousel |
| `templates/villas-big.php` | REMOVE | Alternate listing template experiments |
| `templates/villas-small.php` | REMOVE | Alternate listing template experiments |

### 4c. Page templates (files with `Template Name:` header)

| File | Used by (template hierarchy / get_template_part / Template Name) | Classification | Evidence |
|---|---|---|---|
| `page-special-offers.php` | `Template Name: Special Offers`; assigned in WP editor | REFACTOR | Wireframe 4 + `offer_table` |
| `templates/property-wishlist.php` | Assignable page template | REMOVE | Wishlist REMOVE |
| `full-width*.php`, `templates/villas-*.php`, `templates/rude-team.php`, `templates/property-results.php` | Explicit Template Name assignments | MIX | See subsection rows |

---

## 5. ACF Field Groups (relating to villas)

Field groups JSON is not versioned in this theme snapshot; registrations live in WP DB (`acf-options.php`/`acf_add_local_field_group` absent for villas here). Classification is by **`get_field` / `the_field` usage** across PHP.

| Field name | Field key | Type | Classification | Notes |
|---|---|---|---|---|
| `property_id` | _unknown — sync from WP admin JSON export if needed | text | KEEP | API integration key (`single-property-reviews.php`, maps) |
| `villa_pretty_name` | _unknown — | text | KEEP | Labels |
| `property_sleeps` | unknown | number/text | KEEP | Canonical English-facing |
| `property_bedrooms` | unknown | number | KEEP |
| `property_bathrooms` | unknown | number | KEEP |
| `property_description` | unknown | textarea/wysiwyg | KEEP |
| `property_for_sale` | unknown | boolean | REMOVE | Sale logic |
| **`property_*_spanish`** (`property_sleeps_spanish`, `property_bedrooms_spanish`, `property_bathrooms_spanish`, `property_description_spanish`) | unknown | duplicates | REMOVE (after migrate) | **See §12** merge before stripping `_spanish` |
| Booking / date UI fields referenced in old availability templates | assorted | assorted | REMOVE/REFACTOR | Availability calendar REMOVE |
| `offer_table` (repeater on Special Offers — `villa`, `dates`, `special_offer_price`) | unknown | repeater | REFACTOR | Until API-backed specials |

**Spanish-law migration:** code branches on **`$spanish_law`** (global toggled elsewhere) populate either canonical or `_spanish` fields **`single-villas.php`**, **`templates/loop-grid-part-list.php`**, **`templates/property-results.php`**, etc. Confirm per-post population in DB export before collapsing.

---

## 6. Functions in `functions.php`

### 6a. Asset enqueue / dequeue

| Function | Purpose | Classification | Evidence |
|---|---|---|---|
| `add_the_goodies` | Defines constants; hooks `load_scripts` | REFACTOR | Enqueues **`rude-vendors-feb.min.js`**, **`rude-style.min.css`** (Foundation + VC leftovers in CSS) |

| `load_scripts` | jQuery Migrate, vendors bundle, new-style CSS, conditional Google Maps + cluster + **`map_scripts.js`** | REFACTOR | Hardcoded **`maps.googleapis.com` API key** in source (see §9); **`rude-style.min.css`** REMOVE stack pass 3 |

### 6b. Admin tweaks (login, dashboard, menu hiding)

| Function | Purpose | Classification | Evidence |
|---|---|---|---|
| `remove_screen_options_tab` | Editors lose screen opts | REMOVE | Opinionated UX |
| `hide_menu_appearance` | Restrict theme UI | REMOVE | Heavy-handed |
| `remove_wp_logo`, `my_admin_bar_edit`, `remove_menu_items` | Slim admin chrome | REMOVE | Review if editors need tools |
| `pr_disable_admin_notices` | Kills notices | REMOVE | Risk masking real issues |
| `remove_footer_admin`, `put_my_url`, `my_login_head` | Comoyoti admin footer + WP login styling | REMOVE | **`comoyoti.agency@gmail.com`**, **`rudeibiza`** logo paths |
| `add_grav_forms` | Editor capability **`gform_full_access`** | KEEP | Matches Gravity KEEP |
| Embedded commented block `add_emergency_admin` **with plaintext credential** lines | dormant backdoor scaffolding | REMOVE | ⚠ **`functions.php:~208`** never ship (see §9) |

### 6c. Navigation walker classes

| Class `top_bar_walker` extends `Walker_Nav_Menu` | Foundation top-bar patterns | REMOVE | **`has-dropdown`** / Foundation |

### 6d. Excerpt / content helpers

| Function | Classification | Evidence |
|---|---|---|
| `add_excerpt_to_pages` | KEEP/REFACTOR | Normal WP feature |
| `new_excerpt_more`, `excerpt()`, `content()` | REFACTOR | Utility |

### 6e. Image sizes and thumbnail support

| Item | Classification | Evidence |
|---|---|---|
| `add_theme_support('post-thumbnails')` | KEEP |
| `add_image_size('iv2000_*', …)` | REFACTOR | Renamed sizes; regen thumbs after pass 2 |

### 6f. Custom shortcodes

| Function | Classification | Evidence |
|---|---|---|
| `wpb_list_child_pages` + shortcode **`[childpages]`** | REMOVE | Legacy IA |

### 6g. Other

| Function | Purpose | Classification | Evidence |
|---|---|---|---|
| `core_theme_setup` | `require_once` chain for widgets, CPTs, GeoPlugin, ACF options, image dimensions | REFACTOR | Central bootstrap for pass 3 split |
| `wpb_custom_new_menu` | Registers unused menu location `my-custom-menu` | REMOVE |
| `my_acf_google_map_api` | Uses **`GOOGLE_MAPS_API_KEY`** from `wp-config` when defined | REFACTOR | Good pattern; aligns with **`load_scripts`** duplication issue |
| `hide_wp_version` | Strips query version | REFACTOR | Optional hardening |
| `numeric_posts_nav` | Pagination markup | REFACTOR | |
| **`my_booking_data_att_menu_item`** | Adds **`data-reveal-id`** to menu ID **2523** | REMOVE | Modal booking pattern deprecated |
| `add_column`, `column_content` | Shows post IDs in Posts list | REMOVE | Noise for editors |
| **`add_faq_json_schema`** | Hard-coded FAQ **`$faq_schemas` map keyed by slug** | REMOVE | Bypasses CPT; fragile SEO |
| **`add_custom_villa_taxonomies`** | **`villa_type`** on villas | REFACTOR | |
| `rest_*` filters `__return_false` + strips oEmbed/REST hooks | BREAKS headless integrations | REMOVE/REFACTOR | **Conflicts with future JSON/API use** — open item §12 |

---

## 7. Plugin Dependencies in Code

| Plugin | Where used (function / shortcode / markup class / file:line) | Classification | Replacement |
|---|---|---|---|
| WPBakery Visual Composer | `do_shortcode('[vc_row]…')` `page-all-villas.php:~83`; `vc_*` classes in `header-balance.php`, min CSS | REMOVE | Plain editor |
| FacetWP | `facetwp_display`, **`facetwp-facet`** in `templates/search-horizontal.php`, **`templates/search-home.php`**, **`templates/search-vertical.php`**, **`templates/property-results.php`**; classes in **`css/rude-style.min*.css`** | REMOVE | **`villa_search`** API |
| SearchWP | *No `searchwp_*` grep hits in theme* | N/A theme | REMOVE in stack if inactive |
| Gravity Forms | `do_shortcode('[gravityform …]')` `page-contact.php` (id **3**), wishlist (**4**/ **5**) `templates/property-wishlist.php`; `gf_menu` selectors in CSS | KEEP | Forms until Bob flow |
| Slick Carousel | **`rude-vendors*.min.js`**, **`rude-app.js`** (`.slider-property` bindings), **`css/rude-style.min*.css`**, usage in **`templates/similar-properties.php`**, **`templates/featured-properties.php`**, **`templates/special-offer-properties.php`** | REMOVE | Native carousel |
| DD Show Calendar (`[dd_show_calendar]`) | `templates/loop-property.php` (long **`do_shortcode`** chain naming villas) | REMOVE | Calendar Option D REMOVE |
| Advanced Custom Fields PRO | pervasive `get_field` / `have_rows`; `acf-options.php`; map filter **`acf/fields/google_map/api`** | KEEP | Villa data KEEP |
| Yoast SEO | Commented **`yoast_is_toast`**, theme otherwise assumes Yoast runtime | KEEP | Confirmed |
| Wordfence / Mailchimp / etc. | Not directly referenced except Mailchimp script includes in footers | REFACTOR | Scripts via **`get_template_directory_uri`** in fixed footers |
| MarkerCluster + Google Maps JS | **`functions.php:load_scripts`**, **`js/map_scripts.js`**, **`js/markerclusterer.js`** | REMOVE/REFACTOR | Listing map strategy TBD |
| GeoLite / GeoPHP | **`geoplugin/geoplugin.class.php`** loaded `functions.php:20` — class unused in audited PHP outside bundle | REMOVE | Aligns with **`page-special-offers.php`** IP/geo (ipinfo/exchangerate), not GeoPlugin |

---

## 8. Asset Files (CSS / JS / images / fonts)

### 8a. CSS

| File | Size | Used by (grep references) | Classification | Evidence |
|---|---|---|---|---|
| `css/rude-style.min.css` | large | `load_scripts` | REMOVE | Foundation + Slick + Facet + VC styles |
| `css/rude-style.min (31:1:23 14:58).css` | duplicate | likely none direct enqueue | REMOVE | Versioning artefact |
| `css/new-style.css` | medium | `load_scripts` | REFACTOR | Partial overrides until replaced |
| `css/new-style-como.css` | medium | theme-specific pages | REMOVE | Legacy variant |
| `custom-admin.css` | small | not enqueued in live `functions.php` (commented) | REMOVE | Dead |
| `car-hire/cargestion.api.css` | car-hire module | REMOVE | Feature outside scope |
| `fonts/email-font.css` | email | REMOVE | Legacy |
| `style.css` | **~15k lines** | WP theme header + massive embedded legacy rules | REFACTOR | Split/clean pass 3 |

### 8b. JavaScript

| File | Classification | Evidence |
|---|---|---|
| `js/rude-vendors-feb.min.js` | REMOVE | Bundles jQuery UI + Slick etc. |
| `js/rude-vendors.min.js`, `js/rude-vendors-new.min.js` | REMOVE | Vendor duplicates |
| `js/rude-app.js` | REFACTOR | Site behaviours; Slick initialisers |
| `js/map_scripts.js` | REMOVE/REFACTOR | Maps |
| `js/markerclusterer.js` | REMOVE | Google Maps |
| `js/mailchimp.js` | REFACTOR | If newsletter kept |
| `js/jquery.tooltipster*.js` | REMOVE | Tooltip plugin |
| `js/jquery.fitvid.js` | REMOVE | Embeds helper |
| `js/custom-admin.js` | REMOVE | Admin custom (no enqueue active) |
| `car-hire/jquery*.js`, `proxy.php` | REMOVE | Legacy car hire |

### 8c. Images / SVGs / fonts

| Assets | Classification | Evidence |
|---|---|---|
| `images/` (logos, backgrounds) | REFACTOR | Many paths broken if still pointing to **`…/themes/rudeibiza/`** in inline `<style>` (**`front-page.php`**, **`header` partials**) |
| `fonts/*` icomoon | REFACTOR | If icon set reused |
| Root `featured-*.jpg` assets | REVIEW | Hero/background binaries — keep if referenced |

Flag **dead assets** when no PHP/ CSS cross-reference beyond minified bundles.

---

## 9. Hardcoded Values to Surface

| Location | Value (description) | What it should become |
|---|---|---|
| `functions.php` `load_scripts` | Google Maps **`maps.googleapis.com` key in query string** | env / `wp-config` single source; rotate after launch |
| `page-special-offers.php` lines **16–34** | **ipinfo.io** token + **exchangerate-api.com** key inline | REMOVE IP/FX hacks or move to server-side env (not client theme) |
| `functions.php` **~209–217** commented `add_emergency_admin` | **Plaintext username/password/email** | delete block entirely — never publish |
| `templates/single-property-reviews.php` **6–11** | **MySQL credentials** for `iv2000_live` via `wpdb` | REMOVE file path; use API or env **(do not paste secrets in repo)** |
| `single-villas.php`, `templates/loop-property.php`, `templates/single-property-more-info.php` | **`is_single(12511)`** Airstream fork | static page ID from site options / slug |
| `single-villas.php` | **`stripos($_SERVER['REQUEST_URI'], '/sale/')`** | DELETE branches |
| `front-page.php`, `page-all-villas.php`, geo pages, etc. | **`post__not_in` arrays** (`5610`, `2928`, etc.) | editorial rules / API filter |
| `templates/special-offers.php` | Extended exclusion list `3091`… | same |
| `functions.php` `my_booking_data_att_menu_item` | Hard menu item ID **2523** | REMOVE or options |
| `front-page.php` inline styles + `header` files | Absolute URLs to **`ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/`** | theme-relative / CDN options pass 3 |
| `footer.php` / `front-page.php` | Phone **+43 203 700 1364**, **+34 666 934 060**, email **bookings@ibizavillas2000.com** | ACF site options |
| `functions.php` `remove_footer_admin` | Email **comoyoti.agency@gmail.com** | REMOVE |
| `put_my_url` | **https://www.ibizavillas2000.com/** | site home_url |

---

## 10. Bob's Integration Surface — Current State

### Header search form

| | |
|---|---|
| **(a) Templates** | `header-new.php` (primary), `header.php`, `header-balance.php`, `searchform.php`, nav partials `templates/nav-topbarnew.php` |
| **(b) Injection regions** | Collapsed search row, date range placeholders, “Search villas” button (UI-only per brief) |
| **(c) Current implementation** | Mix of static markup + FacetWP template includes on listing pages; **`property-search-facet.php`** references |
| **(d) Pass 3 cleanup** | Strip Facet hooks; single BEM shell; wire props for **`villa_search?start_date&end_date&pax`** |

### Homepage hero search

| | |
|---|---|
| **(a)** | `front-page.php` pulls **`templates/search-home-new.php`** / featured partials (**not** Facet-front-page in audited `front-page.php` excerpt — grid uses `WP_Query` loop) |
| **(b)** | Hero banners `templates/banner-home.php`; property loop listing |
| **(c)** | Static **`WP_Query` villas** excludes IDs; **`the_field('front_page_intro', 2)` hard-coded page ID **2**** |
| **(d)** | Replace **`post__not_in`/hard-coded homepage options page ID `(2)`** with Options API; expose Bob mount points |

### Villa listing — search + grid

| | |
|---|---|
| **(a)** | **`page-all-villas.php`**, **`templates/search-horizontal.php`**, **`templates/property-results.php`**, grid partials **`loop-grid*.php`** |
| **(b)** | Filters + pager + counts map facet UI |
| **(c)** | Facet shortcodes **`echo do_shortcode( '[facetwp' … ] )`** patterns |
| **(d)** | DELETE Facet enqueue + markup shells; AJAX results container for JSON mapping |

### Villa detail — date picker + total price + Request to Book

| | |
|---|---|
| **(a)** | **`single-villas.php`**, **`templates/property_availability/availability.php`**, **`templates/single-property-price.php`**, pricing partials |
| **(b)** | Date range UI, GBP/EUR calculators, enquire CTAs |
| **(c)** | Legacy JS in **`rude-app.js`/vendors** + ACF villa meta + **`[dd_show_calendar]`** |
| **(d)** | Strip calendar + FX; placeholders for nightly total + **`Request to Book`** → Bob endpoint |

### Special offers — accordion / page

| | |
|---|---|
| **(a)** | **`page-special-offers.php`** Template Name assignment |
| **(b)** | Repeater **`offer_table`**, GBP display with **`$exchange_rate`**, accordion-style marketing |
| **(c)** | PHP functions **`get_user_location_ipinfo`**, **`get_exchange_rate_exchangerate_api`** inline file |
| **(d)** | REMOVE geo/FX; keep repeater as interim content until API‑driven specials |

---

## 11. Plugin List (deactivation in pass 2)

| Plugin | Active in code? | Classification | Notes |
|---|---|---|---|
| WPBakery Page Builder (Visual Composer) | Yes (`vc_*` shortcodes, CSS hooks) | REMOVE | Matches David list |
| FacetWP | Yes | REMOVE | |
| Gravity Forms | Yes | KEEP | Until API enquiry |
| Yoast SEO | Assumed active | KEEP | |
| Wordfence | Not referenced in PHP | KEEP | Operational |
| ACF PRO | pervasive | KEEP | |
| SearchWP | No theme refs | REMOVE if installed | Saves DB load |
| Slick Carousel (bundled) | Yes (JS/CSS vendors) | REMOVE | Bundled vendor not separate plugin |

---

## 12. Open Questions

1. **Spanish-law merge:** Decide merge script merging **`property_*` vs `property_*_spanish`** prioritising whichever is populated post-by-post (`live villas use both` per stakeholder note). Acceptance check on front-end snippets in **`single-villas.php`** and **`templates/loop-grid-part-list.php`** loops.
2. **`rest_enabled` filters false** in **`functions.php`**: Removing may be prerequisite for REST consumers (blocks API development if unintended). Decide before exposing internal REST tools.
3. **`apartments`** post type sourcing (plugin?). Confirm deactivate vs migrate content references in **`templates/property-results.php`** and widgets referencing `'post_type'=>'apartments'`.
4. **Emergency admin dormant code** (**`functions.php`**) containing plaintext credentials — delete outright + rotate any historical creds leaked in VC.
5. **TripAdvisor CPT content volume** (`testimonials`): confirm export/regression before CPT delete.
6. **`promotion_slider` CPT orphaned files**: confirm absence in DB (`require_once` never called) vs historic DB rows from older deploys — safe purge?
7. **SQL injection posture:** `templates/single-property-reviews.php` interpolates **`$villa_id`** into raw SQL **`WHERE villa ='$villa_id'`** — security hardening/removal bundled with REMOVE path.

---

## 13. Files Inventoried

Each path · classification (**REMOVE** or **REFACTOR**; logical **KEEP** CPT/fields in §1/§5) — aligns with preceding sections.

`404.php` · REFACTOR | `archive.php` · REFACTOR | `content-search.php` · REMOVE | `featured-services.php` · REMOVE | `featured-testimonials.php` · REMOVE | `front-page.php` · REFACTOR | `full-width.php` · REMOVE | `full-width-balance.php` · REMOVE | `full-width-como.php` · REMOVE | `full-width-nomads.php` · REMOVE | `home.php` · REFACTOR | `index.php` · REFACTOR | `loop.php` · REFACTOR | `page-all-villas.php` · REFACTOR | `page-all-villas-2.php` · REMOVE | `page-contact.php` · REFACTOR | `page-custom-weeks.php` · REMOVE | `page-frequently-asked-questions.php` · REMOVE | `page-homenew.php` · REMOVE | `page-how-to-book.php` · REMOVE | `page-special-offers.php` · REFACTOR | `page-testimonials.php` · REMOVE | `page-transfers.php` · REMOVE | `page-villas-for-sale.php` · REMOVE | `page-villas-in-ibiza-town.php` · REMOVE | `page-villas-in-north-island.php` · REMOVE | `page-villas-in-playa-den-bossa.php` · REMOVE | `page-villas-in-san-antonio.php` · REMOVE | `page-villas-in-san-josep.php` · REMOVE | `page-villas-in-san-rafel.php` · REMOVE | `ppage.php` · REMOVE | `search.php` · REMOVE | `searchform.php` · REFACTOR | `sidebar.php` · REFACTOR | `single-villas.php` · REFACTOR | `footer.php` · REFACTOR | `footercomo.php` · REMOVE | `footer-old.php` · REMOVE | `header.php` · REFACTOR | `header-new.php` · REFACTOR | `header-balance.php` · REMOVE | `header-power.php` · REMOVE | `header-2020.php` · REMOVE | `header-new old.php` · REMOVE | `functions.php` · REFACTOR  

`functions/acf-options.php` · REFACTOR | `functions/custom-admin-columns.php` · REFACTOR | `functions/custom-week-cpt.php` · REMOVE | `functions/custom-week-widget.php` · REMOVE | `functions/explore-widget.php` · REMOVE | `functions/faq-categories-widget.php` · REMOVE | `functions/faq-cpt.php` · REMOVE | `functions/featured-properties-widget.php` · REFACTOR | `functions/menu-property-widget.php` · REMOVE | `functions/promotion-slider-cpt.php` · REMOVE | `functions/promotion-slider-widget.php` · REMOVE | `functions/properties-cpt.php` · REFACTOR | `functions/related-pages-widget.php` · REMOVE | `functions/related-properties-widget.php` · REFACTOR | `functions/rude-brand-cpt.php` · REMOVE | `functions/rude-brands-footer-widget.php` · REMOVE | `functions/rude-brands-nav-widget.php` · REMOVE | `functions/testimonials-widget.php` · REMOVE | `functions/tripadvisor-cpt.php` · REMOVE | `functions/widgets.php` · REFACTOR  

`geoplugin/geoplugin.class.php` · REMOVE  

`inc/image-dimensions.php` · REFACTOR  

`templates/*` *(all listed §4b)* · mix REMOVE / REFACTOR  

`templates/property-results.php` · REMOVE | `templates/property-search-facet.php` · REMOVE | `templates/property-wishlist.php` · REMOVE  

`templates/single-property-reviews.php` · REMOVE  

`car-hire/*` `{css,js,php}` · REMOVE  

`css/new-style-como.css` · REMOVE | `css/new-style.css` · REFACTOR | `css/rude-style.min*.css` · REMOVE (+ artefact file)  

`custom-admin.css` · REMOVE  

`faq-schema.json` · REMOVE  

`fonts/email-font.css` · REMOVE | `fonts/selection.json` · REFACTOR  

`js/map_scripts.js` · REMOVE | `js/markerclusterer.js` · REMOVE | `js/mailchimp.js` · REFACTOR | `js/jquery.fitvid.js` · REMOVE | `js/jquery.tooltipster.js` · REMOVE | `js/jquery.tooltipster.min.js` · REMOVE | `js/custom-admin.js` · REMOVE  

`js/rude-app.js` · REFACTOR | `js/rude-vendors-feb.min.js` · REMOVE | `js/rude-vendors-new.min.js` · REMOVE | `js/rude-vendors.min.js` · REMOVE  

`style.css` · REFACTOR  

*Full explicit list (alphabetical, 154 paths):*

`404.php` · REFACTOR · `archive.php` · REFACTOR · `car-hire/cargestion.api.css` · REMOVE · `car-hire/jquery-1.4.4.min.js` · REMOVE · `car-hire/jquery-easing.js` · REMOVE · `car-hire/jquery-ui.min.js` · REMOVE · `car-hire/proxy.php` · REMOVE · `content-search.php` · REMOVE · `css/new-style-como.css` · REMOVE · `css/new-style.css` · REFACTOR · `css/rude-style.min (31:1:23 14:58).css` · REMOVE · `css/rude-style.min.css` · REMOVE · `custom-admin.css` · REMOVE · `faq-schema.json` · REMOVE · `featured-services.php` · REMOVE · `featured-testimonials.php` · REMOVE · `fonts/email-font.css` · REMOVE · `fonts/selection.json` · REFACTOR · `footer-old.php` · REMOVE · `footer.php` · REFACTOR · `footercomo.php` · REMOVE · `front-page.php` · REFACTOR · `full-width-balance.php` · REMOVE · `full-width-como.php` · REMOVE · `full-width-nomads.php` · REMOVE · `full-width.php` · REMOVE · `functions.php` · REFACTOR · `functions/acf-options.php` · REFACTOR · `functions/custom-admin-columns.php` · REFACTOR · `functions/custom-week-cpt.php` · REMOVE · `functions/custom-week-widget.php` · REMOVE · `functions/explore-widget.php` · REMOVE · `functions/faq-categories-widget.php` · REMOVE · `functions/faq-cpt.php` · REMOVE · `functions/featured-properties-widget.php` · REFACTOR · `functions/menu-property-widget.php` · REMOVE · `functions/promotion-slider-cpt.php` · REMOVE · `functions/promotion-slider-widget.php` · REMOVE · `functions/properties-cpt.php` · REFACTOR · `functions/related-pages-widget.php` · REMOVE · `functions/related-properties-widget.php` · REFACTOR · `functions/rude-brand-cpt.php` · REMOVE · `functions/rude-brands-footer-widget.php` · REMOVE · `functions/rude-brands-nav-widget.php` · REMOVE · `functions/testimonials-widget.php` · REMOVE · `functions/tripadvisor-cpt.php` · REMOVE · `functions/widgets.php` · REFACTOR · `geoplugin/geoplugin.class.php` · REMOVE · `header-2020.php` · REMOVE · `header-balance.php` · REMOVE · `header-new old.php` · REMOVE · `header-new.php` · REFACTOR · `header-power.php` · REMOVE · `header.php` · REFACTOR · `home.php` · REFACTOR · `inc/image-dimensions.php` · REFACTOR · `index.php` · REFACTOR · `js/custom-admin.js` · REMOVE · `js/jquery.fitvid.js` · REMOVE · `js/jquery.tooltipster.js` · REMOVE · `js/jquery.tooltipster.min.js` · REMOVE · `js/mailchimp.js` · REFACTOR · `js/map_scripts.js` · REMOVE · `js/markerclusterer.js` · REMOVE · `js/rude-app.js` · REFACTOR · `js/rude-vendors-feb.min.js` · REMOVE · `js/rude-vendors-new.min.js` · REMOVE · `js/rude-vendors.min.js` · REMOVE · `loop.php` · REFACTOR · `page-all-villas-2.php` · REMOVE · `page-all-villas.php` · REFACTOR · `page-contact.php` · REFACTOR · `page-custom-weeks.php` · REMOVE · `page-frequently-asked-questions.php` · REMOVE · `page-homenew.php` · REMOVE · `page-how-to-book.php` · REMOVE · `page-special-offers.php` · REFACTOR · `page-testimonials.php` · REMOVE · `page-transfers.php` · REMOVE · `page-villas-for-sale.php` · REMOVE · `page-villas-in-ibiza-town.php` · REMOVE · `page-villas-in-north-island.php` · REMOVE · `page-villas-in-playa-den-bossa.php` · REMOVE · `page-villas-in-san-antonio.php` · REMOVE · `page-villas-in-san-josep.php` · REMOVE · `page-villas-in-san-rafel.php` · REMOVE · `ppage.php` · REMOVE · `search.php` · REMOVE · `searchform.php` · REFACTOR · `sidebar.php` · REFACTOR · `single-villas.php` · REFACTOR · `style.css` · REFACTOR · `templates/banner-home.php` · REMOVE · `templates/breadcrumbs.php` · REFACTOR · `templates/content.php` · REFACTOR · `templates/explore-carousel.php` · REMOVE · `templates/explore-other.php` · REMOVE · `templates/explore.php` · REMOVE · `templates/fb-like.php` · REMOVE · `templates/featured-boxes.php` · REFACTOR · `templates/featured-properties.php` · REFACTOR · `templates/featured-services.php` · REFACTOR · `templates/featured-testimonials.php` · REMOVE · `templates/head-tracking.php` · REFACTOR · `templates/home-header-offer-como.php` · REMOVE · `templates/home-header-offer.php` · REMOVE · `templates/latest-news.php` · REFACTOR · `templates/loop-grid-part-list-sale.php` · REMOVE · `templates/loop-grid-part-list.php` · REFACTOR · `templates/loop-grid-part.php` · REFACTOR · `templates/loop-grid-special-offers-home-part.php` · REFACTOR · `templates/loop-grid-special-offers-part.php` · REFACTOR · `templates/loop-grid.php` · REFACTOR · `templates/loop-property_boat.php` · REMOVE · `templates/loop-property.php` · REMOVE · `templates/nav-topbar.php` · REMOVE · `templates/nav-topbarbalance.php` · REMOVE · `templates/nav-topbarnew.php` · REFACTOR · `templates/property-price-from-to-list.php` · REFACTOR · `templates/property-price-from-to.php` · REFACTOR · `templates/property-price-sale.php` · REMOVE · `templates/property-results.php` · REMOVE · `templates/property-search-facet.php` · REMOVE · `templates/property-view-list.php` · REMOVE · `templates/property-wishlist.php` · REMOVE · `templates/property_availability/availability.php` · REMOVE · `templates/rude-team.php` · REFACTOR · `templates/search-home-new.php` · REFACTOR · `templates/search-home.php` · REMOVE · `templates/search-horizontal.php` · REMOVE · `templates/search-vertical.php` · REMOVE · `templates/similar-properties.php` · REFACTOR · `templates/single-apartment-availability.php` · REMOVE · `templates/single-page-tesimonial.php` · REMOVE · `templates/single-page-testimonial.php` · REMOVE · `templates/single-page-video.php` · REMOVE · `templates/single-property-features.php` · REFACTOR · `templates/single-property-map.php` · REFACTOR · `templates/single-property-more-info.php` · REFACTOR · `templates/single-property-price.php` · REFACTOR · `templates/single-property-reviews.php` · REMOVE · `templates/single-property-special-offer.php` · REFACTOR · `templates/single-property-summary.php` · REFACTOR · `templates/single-property-testimonial.php` · REMOVE · `templates/single-property-testimonials.php` · REMOVE · `templates/single-property-video.php` · REFACTOR · `templates/social-buttons.php` · REFACTOR · `templates/social-share.php` · REFACTOR · `templates/spanish_law.php` · REMOVE · `templates/special-offer-properties.php` · REFACTOR · `templates/special-offers.php` · REFACTOR · `templates/testimonials.php` · REMOVE · `templates/villas-big.php` · REMOVE · `templates/villas-small.php` · REMOVE

**Alphabetical list tally:** REMOVE **97** · REFACTOR **57** (total **154** code files). Word **REMOVE** = strip in pass 2 unless overruled; **REFACTOR** = rewrite shells for CW/Bob in pass 3.
