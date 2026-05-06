# Brief 01 — ACF globals: featured offer & short breaks

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

## Standards

- ACF field groups registered in PHP under
  `mu-plugins/ibv-core/includes/acf/`
- Section files live under `mu-plugins/ibv-core/includes/sections/{name}/`
- Section root: `ibv-section-{name} ibv-section ibv-section--surface-{X}`,
  optional `--rhythm-sm`. Section CSS must NOT declare `padding-block`,
  `background`, or `color` on the root.
- BEM naming, `ibv-` prefixes, no new dependencies, no build step

## Scope

Move two pieces of editorial content out of the homepage's ACF group
and into a new globals file under Site Options:

1. Featured offer (currently `weekly_offer_*` on the homepage)
2. Short breaks (currently `short_breaks_*` on the homepage)

Both are reused on the upcoming Special Offers page, so the data needs
a single source of truth. While we're touching the section files,
rename `weekly-offer/` → `featured-offer/` (the component is no longer
homepage-specific) and make the section function accept args (so the
SO page can override the section title and CTA visibility).

This brief does NOT touch the page-special-offers.php template, the
hero, the villa card, or the newsletter helper. Each of those is a
separate brief.

## Files

```
NEW    mu-plugins/ibv-core/includes/acf/register-globals-content.php
EDIT   mu-plugins/ibv-core/includes/acf/register-page-home.php
EDIT   mu-plugins/ibv-core/includes/sections/short-breaks/short-breaks.php
RENAME mu-plugins/ibv-core/includes/sections/weekly-offer/ → featured-offer/
EDIT   (renamed) featured-offer.php  — read from 'option', accept args
EDIT   (renamed) featured-offer.css  — class-name updates only
EDIT   wherever the ACF files are required (likely an acf bootstrap include)
EDIT   wherever section CSS is enqueued/registered (handle rename)
EDIT   themes/ibv/front-page.php (homepage caller — function name change)
```

## Change 1 — Create `register-globals-content.php`

New file. Pattern follows `register-site-options-content.php` —
register field groups against the `ibv-site-options` options page.

Two field groups, each on its own ACF tab for editor clarity.

**Group: "Featured offer (shared)"**

Fields:
- `featured_offer_villa` — post_object, post_type=villas, return=id
- `featured_offer_was_price` — number, prepend €
- `featured_offer_now_price` — number, prepend €
- `featured_offer_valid_from` — date_picker, return Y-m-d
- `featured_offer_valid_to` — date_picker, return Y-m-d
- `featured_offer_show_now_asterisk` — true_false, default false,
  instructions: "Show * after the Now price (footnote below)"
- `featured_offer_footnote` — text, instructions: "Optional footnote
  below the panel (e.g. 'There may be additional costs')"

Field key prefix: `field_ibv_global_featured_offer_*`.

**Group: "Short breaks (shared)"**

Fields:
- `short_breaks_image` — image, return array
- `short_breaks_title` — text
- `short_breaks_text` — textarea
- `short_breaks_cta_url` — url

Field key prefix: `field_ibv_global_short_breaks_*`.

## Change 2 — Hook the new file into the bootstrap

Locate wherever `register-site-options-content.php` is required (likely
the mu-plugin entrypoint or an `acf/index.php`). Require the new file
alongside it.

## Change 3 — Drop two tabs from `register-page-home.php`

Remove `weekly_offer` and `short_breaks` tabs entirely. Keep all other
tabs (hero, featured villas, trust strip, why iv2000, fancy different,
IPS panel, three steps, ibiza guide, meet team) untouched.

After this lands, the homepage edit screen tabs read:
Hero → Featured villas → Trust strip → Why IV2000 → Fancy different →
IPS panel → Three steps → Ibiza guide → Meet team.

## Change 4 — Rename `weekly-offer/` → `featured-offer/`

Rename the directory and both files inside:
- `weekly-offer.php` → `featured-offer.php`
- `weekly-offer.css` → `featured-offer.css`

Function rename: `ibv_core_section_weekly_offer()` →
`ibv_core_section_featured_offer($args = [])`.

CSS handle rename: `ibv-section-weekly-offer` →
`ibv-section-featured-offer` (in both the `wp_enqueue_style` registration
and the BEM class on the `<section>` root).

The function body changes in three ways:

1. **Accepts an args array.** Default values:
   ```php
   $defaults = [
       'section_title'    => __( "This Week's Special Offer", 'ibv' ),
       'show_section_cta' => true,
   ];
   $args = wp_parse_args( $args, $defaults );
   ```

2. **Reads from globals.** All `get_field('weekly_offer_*')` calls
   become `get_field('featured_offer_*', 'option')`.

3. **Threads args through to `ibv_core_offer_panel()`.** Section title
   uses `$args['section_title']`. Section CTA renders only when
   `$args['show_section_cta']` is truthy. The new
   `featured_offer_show_now_asterisk` and `featured_offer_footnote`
   global fields wire through to the offer panel's existing
   `show_now_asterisk` and `footnote` args.

## Change 5 — Update `short-breaks.php`

Change all four `get_field('short_breaks_*')` calls to
`get_field('short_breaks_*', 'option')`. No other changes.

## Change 6 — Update homepage caller

In `themes/ibv/front-page.php`, change the function call:
- `ibv_core_section_weekly_offer();` →
  `ibv_core_section_featured_offer();`

The homepage passes no args — defaults apply (homepage gets the
"Search all Special Offers" CTA and the standard title).

## Verification

1. **WP admin smoke test:**
   - Site Options page: new "Featured offer" and "Short breaks" tabs
     visible.
   - Homepage edit screen: those two tabs are gone. All other tabs
     still present and functional.

2. **Re-populate content** under Site Options (existing data does not
   migrate — staging is throwaway). Set:
   - Featured offer: villa, was, now, valid_from/to, optional asterisk
     + footnote
   - Short breaks: image, title, text, CTA URL

3. **Frontend smoke test:**
   - Homepage renders weekly offer section identically to before
     (now sourced from globals, but visually unchanged).
   - Homepage renders short breaks identically.
   - Section root class on the homepage now reads
     `ibv-section-featured-offer` (was `ibv-section-weekly-offer`).
   - Stylesheet enqueue handle is `ibv-section-featured-offer`.
   - "This Week's Special Offer" section header still renders, with
     "Search all Special Offers" CTA still present.

## Notes

- **Why rename now.** "Weekly offer" describes a homepage-specific
  cadence; the section component is reused on the SO page in brief 05.
  Renaming once now is cheaper than touching the same files twice.
- **Newsletter Gravity Form ID is NOT in this brief.** It also belongs
  on globals but is the subject of brief 03 (newsletter helper). Don't
  add it here — keeping the briefs orthogonal makes them safer to land
  in any order.
- **Args defaults match current behaviour.** The homepage caller passes
  no args, so the defaults preserve today's section title and CTA.
  Brief 05 will pass overrides for the SO page.
