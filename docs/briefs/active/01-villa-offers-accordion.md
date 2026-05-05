# Villa offers: ACF schema + accordion component

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield. New ACF repeater on the villa CPT plus a new
`villa-offers` accordion component that renders active offers on
the villa detail template. Single architectural shift: special
offers move from being a centrally-authored list (legacy SO page
admin UI) to per-villa data. This brief lays the data + display
foundation for that shift.

**Explicitly scoped to one component.** Two related pieces of work
are deferred to future briefs:

- **Villa detail page rebuild.** The full designed villa detail
  page (per wireframe v0.4 — hero, sticky pricing bar, overview,
  pricing block, location, enquiry form) hasn't been built yet.
  This brief drops the offers accordion into the existing
  `single-villas.php` so it appears on the live villa pages
  immediately, with a TODO for repositioning when the full
  villa-detail page sequence runs.
- **Special Offers page aggregation refactor.** Currently the SO
  page reads from a centrally-authored offer table on the SO
  page itself (legacy admin model). After Tina migrates content
  to the new per-villa schema, the SO page will be refactored to
  aggregate offers across all villas. Separate later brief.

The accordion is **purely informational** — no per-offer CTA. The
villa enquiry form (Bob's territory) is the single enquiry path on
the page; offers exist to make the value visible, not to spawn a
parallel funnel.

**Reference materials:**
- Wireframe v0.4 — villa detail page structure (the offers
  accordion sits at the top of the SECTIONS 3.1–3.2 OVERVIEW
  block, where the placeholder text "[ SPECIAL OFFER active —
  shown here. Resolves discovery issue: offers disappear on
  villa page ]" appears).
- Design system: BEM, surface system, section rhythm, existing
  accordion pattern from About FAQ (brief 04 of the About
  sequence).

## Verification needed before starting

Confirm against the current state of the repo. Don't proceed
until pinned down:

- [ ] **Villa CPT ACF registration file.** Locate the file that
  registers ACF for the villa CPT — likely under
  `mu-plugins/ibv-core/includes/acf/`. Pattern after the existing
  `register-page-*.php` files for naming and key conventions.
- [ ] **`single-villas.php` current structure.** Read what's
  there now. Specifically: where is the main content area the
  accordion will drop into without disturbing existing layout?
- [ ] **About FAQ accordion location.** Find the FAQ component
  built in About brief 04. Read its markup and CSS for the
  `<details>`/`<summary>` pattern. The villa-offers accordion is
  similar in mechanism but different in visual treatment — DO
  NOT extract a shared accordion component in this brief
  (out of scope; flag as a Step 12 surface-only observation if
  the duplication becomes obvious).
- [ ] **Token names.** Verify against `tokens.css` — accent
  colour for the `::before` dot pattern (used on article-card
  meta line, pull-quote attribution, related-articles header
  per recent briefs), and the radius / spacing tokens used by
  About FAQ.

If any of the above are not where the brief assumes, surface
before writing code, not after.

## Standards

- BEM, `ibv-` prefixes
- ACF registered in PHP (project convention — no JSON sync, no
  block editor)
- Section rhythm + surface system: this is a component within an
  existing section (overview), not a standalone section. Don't
  add `ibv-section-*` modifiers to the accordion root.
- Existing tokens only — no new tokens
- All escaping/sanitisation explicit (`esc_html`, `esc_attr`,
  `esc_url`, `wp_kses_post`)
- Standard image and button helpers if needed
- No `the_content()` — editorial copy comes from explicit ACF
  fields
- `<details>` / `<summary>` for the accordion mechanism (native
  HTML, free keyboard + screen-reader handling, matches About
  FAQ pattern)

Step 12 default applies: act on small in-scope quality fixes
within files this brief edits. Surface anything bigger.

## Files

```
EDIT  mu-plugins/ibv-core/includes/acf/[verify path]/register-villa.php
                                  (add villa_offers repeater to villa CPT ACF group)

NEW   mu-plugins/ibv-core/includes/components/villa-offers/villa-offers.php
NEW   mu-plugins/ibv-core/includes/components/villa-offers/villa-offers.css

EDIT  themes/ibv/single-villas.php
                                  (render the accordion at top of main content)

EDIT  mu-plugins/ibv-core/bootstrap.php           (require new component file)
EDIT  mu-plugins/ibv-core/includes/shared-assets.php  (register CSS handle)
```

## Change 1 — ACF schema

Add a `villa_offers` repeater to the villa CPT ACF group.

**Repeater key:** `villa_offers`

**Min/max:** 0 / unlimited

**Layout:** block layout (matches the verbose-but-readable style
in similar repeaters; if the codebase consistently uses table
layout elsewhere, match that — read existing repeaters first).

**Sub-fields:**

| Sub-field | Key | Type | Required | Notes |
|---|---|---|---|---|
| Offer name | `offer_name` | Text | Yes | Marketing label, used in URL slug. e.g. "Spring 4-night escape" |
| Date from | `offer_date_from` | Date Picker | Yes | Storage `Ymd`, display `j M Y` |
| Date to | `offer_date_to` | Date Picker | Yes | Storage `Ymd`, used to filter active offers |
| Headline | `offer_headline` | Text | Yes | Short marketing line, ≤30 chars. e.g. "20% off", "From €1,800/wk", "Mon–Fri only" |
| Description | `offer_description` | Textarea (3 rows, no rich text) | No | Optional supporting copy, ~2 sentences |

Field labels visible in admin: "Offer name" / "Valid from" /
"Valid to" / "Headline" / "Description".

The repeater label in admin: "Special Offers". Description:
"Add active special offers for this villa. Offers automatically
disappear from the site after their 'Valid to' date passes."

## Change 2 — Component

Create `components/villa-offers/villa-offers.php` with helper
`ibv_core_villa_offers( $args )`.

**Args:**

```php
$defaults = [
    'post_id' => 0,   // villa post ID; required
];
```

**Function flow:**

1. Read `villa_offers` repeater for `$post_id`
2. Filter to active offers: `$offer['offer_date_to'] >= today_Ymd()`
3. Sort active offers by `offer_date_from` ascending
4. If no active offers → return nothing (component does not
   render at all — no empty state, no "no offers" message)
5. Otherwise enqueue style + render markup

**Today comparison:** use `current_time( 'Ymd' )` (site-timezone
aware) — not `date( 'Ymd' )` which uses server timezone.

**Markup shape:**

```html
<details class="ibv-villa-offers" [open if 1–2 offers]>
  <summary class="ibv-villa-offers__summary">
    <span class="ibv-villa-offers__label">Special offers available</span>
    <span class="ibv-villa-offers__count">3 offers</span>
    <span class="ibv-villa-offers__chevron" aria-hidden="true"></span>
  </summary>

  <ul class="ibv-villa-offers__list">
    <li class="ibv-villa-offers__item">
      <header class="ibv-villa-offers__item-header">
        <p class="ibv-villa-offers__dates">15–22 May 2026</p>
        <p class="ibv-villa-offers__headline">20% off</p>
      </header>
      <p class="ibv-villa-offers__desc">Spring escape — 4 nights from Monday.</p>
    </li>
    <!-- additional items -->
  </ul>
</details>
```

**Open-by-default rule:** add `open` attribute to `<details>` if
the active-offer count is 1 or 2. Closed if 3+. (Reasoning: 1–2
offers are visually contained when expanded; 3+ pushes the page
down too far.)

**Count text:**
- 1 active offer → "1 offer"
- 2+ → "{n} offers"

**Date formatting:** Use the project's existing date-format helper
if one exists. Otherwise format inline:
- Same month: `15–22 May 2026`
- Different months: `28 May – 3 Jun 2026`
- Different years: `28 Dec 2026 – 3 Jan 2027`

**Pluralisation: use `_n()` for the count text** so "1 offer" /
"3 offers" follows i18n.

**Accessibility:**
- The accordion is native HTML — keyboard and screen reader work
  for free
- The `__chevron` has `aria-hidden="true"` (decorative)

## Change 3 — CSS

`villa-offers.css` owns the visual treatment. BEM, tokens only,
no new tokens.

Visual spec:

- **Outer `<details>`:** subtle bordered or tinted container
  matching the design system. Read the About FAQ accordion CSS
  for surface/border conventions and follow the same approach.
- **`<summary>`:** clickable bar, label left + count right +
  chevron rotates on open. Use existing motion tokens for the
  rotation transition. Cursor pointer.
- **List items:** divider between items using existing divider
  token. Last item no divider (or divider applied as `border-top`
  on `:not(:first-child)` to make this trivial).
- **Item header:** dates and headline on one row at desktop;
  stacked at mobile. Headline is the visually prominent piece —
  larger or bolder than dates.
- **Chevron:** SVG-based or pseudo-element triangle. If using SVG,
  vendor a Lucide chevron-down (precedent: `assets/icons/lucide/clock.svg`
  was added in Ibiza Guide brief 01).

Mobile: full-width stacking; item headers stack dates above
headline.

## Change 4 — Wire into single-villas.php

Render the accordion at the top of the villa detail main content
area. After reading the existing `single-villas.php` structure,
the agent picks the right insertion point — likely just inside
the main content container, before the villa name / overview
content.

```php
ibv_core_villa_offers( [ 'post_id' => get_the_ID() ] );
```

**TODO comment** above the call:

```php
/*
 * TODO villa-detail-page-rebuild:
 * The villa detail page is due a full rebuild against wireframe
 * v0.4 (hero / sticky bar / overview / pricing / location /
 * enquiry form). When that work runs, reposition this accordion
 * to live at the top of the overview block as designed. For now,
 * it renders inline at the top of the existing template so the
 * data + component are live for Tina to start using.
 */
```

If `single-villas.php` doesn't already have a clear "main content
area" that the accordion can drop into without disturbing existing
layout, surface that — don't refactor the template's structure
in this brief.

## Change 5 — Bootstrap + asset registration

In `bootstrap.php`:

```php
require_once IBV_CORE_PATH . 'includes/components/villa-offers/villa-offers.php';
```

In `shared-assets.php`, register the `ibv-component-villa-offers`
(or matching naming convention used by other components — verify)
style handle.

## Verification

1. **Admin: ACF schema works.**
   - Edit a villa in WP admin
   - The "Special Offers" repeater appears in the villa edit
     screen with the correct sub-fields
   - Add 3 offers with varied dates and content; save; refresh —
     data persists

2. **Frontend: active offers render.**
   - On the test villa, ensure 3 offers exist with `offer_date_to`
     in the future
   - View the villa page on staging
   - Accordion renders at top of content area
   - Closed by default (3 offers ≥ 3)
   - Click summary → opens, lists three offers in date-ascending
     order
   - Each offer shows: dates row, headline, description

3. **Frontend: expired offers don't render.**
   - Edit one of the three offers; set `offer_date_to` to
     yesterday
   - Save; reload the villa page
   - That offer is gone; accordion shows 2 offers; now opens by
     default (1–2 rule)

4. **Frontend: zero active offers → component absent.**
   - Set all offers' `offer_date_to` to past dates
   - Reload — accordion does NOT render at all (not collapsed-
     empty; just absent)

5. **Accessibility:**
   - Tab through the page — `<summary>` receives focus and Enter
     toggles open/closed
   - Screen reader (VoiceOver / NVDA quick check): announces
     "details, collapsed" / "details, expanded"

6. **Mobile:**
   - Single-column layout, item headers stack dates above headline

7. **Token + system compliance:**
   - All CSS uses existing tokens
   - No new tokens introduced
   - Component CSS does not declare `padding-block`, `background`,
     or `color` on a section root (it's a component, not a
     section)

## Notes

- **Why per-villa not central.** The legacy SO page admin UI
  centralised offer authoring — Tina would create an offer record
  and pick which villa it applied to. That model has two problems:
  it duplicates editorial workflow ("manage this villa" + "manage
  which offers point at this villa"), and offers don't surface on
  the villa detail page itself, hurting discovery. The new model
  collapses both: editing a villa shows that villa's offers; the
  villa detail page surfaces them; the SO page (future refactor)
  becomes a *view* across villas with active offers rather than
  an authored list.
- **Why no migration of existing data.** Tina will re-create
  offers under the new model. The legacy data is small,
  unstructured (offers don't currently have names), and Tina is
  the natural owner. No migration script needed.
- **Why no per-offer CTA.** The villa enquiry form is the single
  enquiry path on the page. A per-offer CTA would either spawn a
  parallel funnel or duplicate the page-level form, both of which
  hurt the conversion model. Awareness is the job of this
  component; conversion belongs to the form.
- **Why open-by-default for 1–2.** With one or two offers, the
  expanded view is short and visibility is more valuable than
  compactness. With three or more, expansion pushes important
  page content (villa name, pricing) below the fold.
- **No auto-extraction of shared accordion.** About FAQ uses the
  same `<details>`/`<summary>` mechanism. Extracting a shared
  accordion component would be a sensible refactor LATER (third
  use case is the inflection point — same pattern as the recent
  meta-dot DRY pass), but doing it now means refactoring About
  FAQ during a brief that's supposed to be focused on one
  component. If the agent spots the duplication during work,
  surface as Step 12 observation, do not act.
- **Step 12 reminder.** Small in-scope improvements (a missing
  escape, a stray hardcoded value in the new component CSS)
  fix in the same commit and note. Larger or out-of-scope:
  surface, don't act.
- **Future briefs that consume this work:**
  - **Villa detail page rebuild** (multi-brief sequence, five-
    brief greenfield pattern). The accordion gets repositioned
    into the overview block.
  - **SO page aggregation refactor.** Single later brief —
    swaps the SO page's data source from the page's own offer
    table to a query across `villa_offers` from all villas.
    Visual output unchanged; data layer changes. Tina will
    have re-created offers under the new model by then.
