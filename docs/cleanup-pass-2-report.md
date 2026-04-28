# Cleanup Pass 2 — Strip — completion report

Source of removals: `docs/cleanup-audit.md` §13 (David signed off). Operational pre-flight (ACF sync, plugins, DB snapshot, branch) assumed done per brief.

## Branch

Work was completed on `staging` in this workspace. Before committing, checkout or create **`cleanup-pass-2-strip`** from `staging` (`git checkout -b cleanup-pass-2-strip`) so the merge target matches the brief. **No commit** was made here; stage and review locally.

## Files deleted (§13 REMOVE)

- **97** paths removed with `git rm` and **staged** (see `git diff --cached --name-only`).
- Includes: legacy CPT/widget PHP under `functions/`, bundled vendor JS/CSS, `geoplugin/geoplugin.class.php`, `car-hire/*`, FAQ/testimonials/promotion/explore flows, Facet/search shell templates, `templates/loop-property.php`, `templates/property-results.php`, `templates/spanish_law.php`, `templates/nav-topbar.php`, and other rows from §13 as applied in this pass.

**Preserved by design (not removed):** `style.css`, `functions.php`, `index.php` — all §13 **REFACTOR**.

## Files edited (REFACTOR + follow-up fixes)

PHP and JS touched to strip branches, shortcodes, Facet/WPB leftovers, Comoyoti/geo/emergency admin, REST-disable filters, Slick inits, etc. Key paths:

| Area | Files |
|------|--------|
| Bootstrap | `functions.php`, `functions/widgets.php`, `functions/custom-admin-columns.php` |
| Villas / listing | `single-villas.php`, `page-all-villas.php`, `page-special-offers.php`, `front-page.php` |
| Headers / footer / contact | `header.php`, `header-new.php`, `footer.php`, `page-contact.php` |
| Templates | `templates/nav-topbarnew.php`, `templates/search-home-new.php`, `templates/loop-grid-*.php`, `templates/special-offers.php`, `templates/similar-properties.php`, `templates/single-property-*.php`, … |
| Assets | `js/rude-app.js` |

### Post–step-5 fixes (audit dependency gaps)

These surfaced during verification so the theme does not fatal after REMOVES:

1. **`templates/nav-topbar.php` removed** — `header.php` now loads **`templates/nav-topbarnew`** (same as `header-new.php`).
2. **`templates/home-header-offer-como.php` removed** — `get_template_part` removed from `header-new.php` (wrapper was already `display:none`).
3. **`functions/rude-brands-footer-widget.php` removed** — `the_widget( 'rude_brands_footer_widget' )` removed from `footer.php`; empty `.brands` wrapper kept for pass 3.
4. **`Walker` `top_bar_walker` removed** — `templates/nav-topbarnew.php` uses default `wp_nav_menu` (no custom walker).
5. **`property-results` URLs** — footer “capacity” links pointed at removed Facet route; now **`home_url( '/all-villas/' )`** (capacity filters deferred to pass 3).
6. **Deleted page templates in conditions** — `property-results` / villas-small / villas-big checks removed; **`templates/search-home-new`** always loaded from `header.php` / `header-new.php`.

## Branches / logic stripped (Step 2 summary)

- Spanish-law: `$spanish_law`, `_spanish` field reads, `spanish_law.php` include — removed from PHP (ACF JSON untouched).
- Sale URL / Airstream forks — removed where present.
- REST `rest_enabled` → false — removed.
- Emergency admin block — removed entirely.
- GeoPlugin require, IP/FX helpers on special offers — removed; euro-only display.
- Comoyoti hooks (login URL, login CSS, admin footer) — removed.
- `my_booking_data_att_menu_item`, FAQ JSON-LD slug map, post-ID admin columns — removed.
- `[childpages]`, `wpb_custom_new_menu` — removed.
- WPBakery `do_shortcode('[vc_…]')`, Facet shortcodes/markup, `.slick(...)` in PHP/footer — removed.
- Front page: post ID `2` / hardcoded `post__not_in` — neutralised with **TODO pass 3** comments per brief.

## Hooks / includes cleaned

- `core_theme_setup` / `require_once` chain reduced to: widgets, ACF options, properties CPT, related + featured property widgets, custom admin columns, `inc/image-dimensions.php` (per surviving §13 list).
- Dead widget/CPT `require_once` lines for removed files dropped from `functions.php`.

## Plugin / runtime expectations

- WPBakery, FacetWP, SearchWP, standalone Slick plugin — expected **deactivated** on the target install; theme no longer depends on them.
- ACF Pro, Gravity Forms, Yoast, Wordfence — remain valid targets to stay active.

## Verification

| Check | Result |
|--------|--------|
| `php -l` on all `*.php` under theme | **No syntax errors** |
| Grep `*.php` for `_spanish`, `facetwp`, `vc_`, `dd_show_calendar`, `12511`, `comoyoti`, `geoplugin` | **No matches** (docs excluded) |
| Grep `*.php` for `/sale/` URL routing | **No matches** |
| `.slick(` in theme | **No matches** (dead CSS class names may remain in `style.css` — pass 3) |

**Note (Step 7 vs `style.css`):** `style.css` is §13 **REFACTOR** and still contains legacy strings such as `facetwp-*` and `.slick-*` in CSS rules. PHP/template execution paths are clean; full CSS strip is **pass 3**.

## Empty directories

After `git rm`, `geoplugin/`, `car-hire/`, and `templates/property_availability/` are gone or not present (Git does not track empty dirs).

## What David should do next

1. `git add -u` and `git add docs/cleanup-pass-2-report.md` (and decide whether to track `docs/cleanup-audit.md`).
2. Ensure branch **`cleanup-pass-2-strip`** from `staging`, then commit with a clear message.
3. Run the **smoke test** from the brief (Permalinks save, `/`, villa single, `/special-offers`, `/contact` + GF, wp-admin villas/pages, `debug.log`).

---

*Generated for Cleanup Pass 2 — Strip; Pass 3 rebuild is out of scope for this pass.*
