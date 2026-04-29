# Ibiza Villas 2000

Custom WordPress site: **must-use plugin** (`ibv-core`) plus a **thin theme** (`ibv`). Public site: [ibizavillas2000.com](https://ibizavillas2000.com).

This README is aimed at **API integration** (availability search, pricing, request-to-book): where the front-end shells live, what is stable contract vs your layer, and who owns what.

---

## Architecture

| Layer | Path | Role |
|--------|------|------|
| Core logic, components, ACF PHP, CPT/taxonomies | `wp-content/mu-plugins/ibv-core/` | All behaviour; loaded from `bootstrap.php` |
| Templates, global layout | `wp-content/themes/ibv/` | PHP templates only — call `ibv_core_*` helpers |

**Conventions:** `ibv_` / `ibv_core_*` functions, `ibv-` BEM CSS, text domain `ibv`. ACF is registered in PHP only (`includes/acf/`). No `acf-json` sync in this workflow. Classic editor is enforced site-wide via `includes/editor.php`. Local ACF field groups and the `villa_amenity` / `villa_poi` taxonomies set `show_in_rest => false`. The `villas` CPT does not pass `show_in_rest`, so WordPress defaults it to `false` (no core REST routes for that post type unless enabled in registration).

**Dependency:** **ACF Pro** (required).

---

## Branching

Active integration work is usually on **`staging`**. Confirm with the team before merging to production.

---

## API work: what is already fixed vs what you add

The theme/mu-plugin ship **markup shells** with `data-bob-*` hooks and short comment blocks describing fields and behaviour. **Do not change** those class names, `data-bob-*` attributes, or form field `name`s unless you agree an amendment with Steve + front-end — Bob’s scripts depend on them.

**You typically add:** script(s) that call Steve’s APIs, hydrate prices, replace listing HTML, handle RTB submit + redirect, and toggle empty states.

---

## Search: GET forms → listing page

**Header** and **homepage hero** search forms are GET. They submit to the URL returned by:

```php
ibv_get_search_villas_url(); // escaped URL; Site Options → Global → "Search / villas listing page" (Page picker)
```

**Query parameters (already named):**

| Name | Meaning |
|------|---------|
| `date_from` | `YYYY-MM-DD` |
| `date_to` | `YYYY-MM-DD` |
| `pax` | integer |

**Source files (comment blocks describe the shell):**

- `ibv-core/includes/components/header-search/header-search.php`
- `ibv-core/includes/components/hero-search/hero-search.php`

On the **Villa Listing** page template, the same param names can be read server-side for form repopulation via `ibv_get_villa_listing_search_params()` in `includes/helpers.php`.

---

## Villa listing grid (client-side replace)

**Container:** `[data-bob-listing-grid]` — inner HTML is **server-rendered fallback** (real villa cards for SEO / no-JS). Your code should replace the **contents** of that element when the search API returns.

**File:** `ibv-core/includes/sections/villa-listing-grid/villa-listing-grid.php`

**Empty state:** `[data-bob-empty-state]` starts with the `hidden` attribute; show it when there are no results.

**File:** `ibv-core/includes/sections/listing-empty-state/listing-empty-state.php`

---

## Villa cards: “from” price

Default cards expose a stable placeholder and a hook for live pricing:

- Element: `span.ibv-villa-card__price-amount` with `[data-bob-from-price]` — the attribute **value is the WordPress post ID** of the villa (string), for correlating the card with API data. The span’s **text content** is the displayed “from” EUR amount; replace that text with the live amount from the API (do not replace the `data-bob-from-price` attribute value with a price — it must stay the post ID).
- The **“/ wk”** suffix is a **sibling** `span` (`.ibv-villa-card__price-suffix`); leave it unless product copy changes.

**File:** `ibv-core/includes/components/villa-card/villa-card.php`

The indicative ACF field `villa_indicative_from_price` renders inside the span before your script runs (or a static `€420` fallback), so cards stay readable for SEO and no-JS users.

---

## Villa detail: enquiry / RTB panel

**Root:** `[data-bob-enquiry-panel]` — also `data-villa-id` with the villa post ID.

**Form fields (names are contract):**

- `villa_id` (hidden)
- `date_from`, `date_to`, `pax`, `message`

**Price / breakdown targets (text/HTML you update):**

- `[data-bob-total-eur]`, `[data-bob-total-gbp]`
- `[data-bob-base-rental]`, `[data-bob-adw]`, `[data-bob-cleaning]`
- `[data-bob-error]` — inline errors
- Submit: `[data-bob-submit]` — disabled until your availability logic enables it

**File:** `ibv-core/includes/components/enquiry-panel/enquiry-panel.php` (read the full BOB comment block at top of the shell)

**After successful RTB:** redirect to the booking confirmation page with the villa **slug**:

- Helper: `ibv_get_booking_confirmation_url( $villa_slug )` in `includes/helpers.php`
- Query param: `?villa={slug}` (used to contextualise “similar villas”)

---

## Spec and legacy reference

- **Authoritative API detail:** internal Notion **IBZ002 → API Integration Spec** (ask David / Steve for access).
- Legacy endpoint family (compatibility / background): `https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl` — treat Steve’s spec as source of truth for new work.

---

## Useful helpers (`includes/helpers.php`)

| Function | Purpose |
|----------|---------|
| `ibv_get_search_villas_url()` | Listing/search page permalink (escaped) |
| `ibv_get_booking_confirmation_url( $slug )` | Confirmation page + optional `?villa=` |
| `ibv_get_contact_page_url()` | Contact link |
| `ibv_get_villa_listing_search_params()` | Current GET search params on listing template |

---

## Where to put your JS

Your scripts live in the **theme**, not the mu-plugin. The mu-plugin owns markup shells; the theme owns presentation and Bob-side runtime behaviour.

Suggested starting path: `wp-content/themes/ibv/assets/js/bob-api.js` (create `assets/js/` if it does not exist yet).

Enqueue from `wp-content/themes/ibv/functions.php` with page-conditional logic so each script only loads where it is needed (listing template, single villa, booking confirmation).

If multiple files emerge (for example `bob-listing.js`, `bob-detail.js`, `bob-rtb.js`), keep them flat in `assets/js/` and enqueue per template. **No build step** — plain JS only. ES module syntax is acceptable if you rely on native browser module support.

For shared helpers used across more than one script, a small `bob-shared.js` registered as a dependency of the others is preferred over a build-time bundle.

---

## Local development

Repo layout expects a normal WordPress install with this theme + mu-plugin deployed under `wp-content/`. If you use [Laravel Herd](https://herd.laravel.com/) (or similar), point a local site at the project and enable whatever URL the team uses for staging snapshots.

After pulling changes that affect CPT/taxonomies or rewrites, visit **Settings → Permalinks** and save once.

---

## Who to ask

- **API contract / endpoints:** Steve (Notion IBZ002).
- **Front-end shells, WordPress data model:** Curious Ways / David (this repo).
