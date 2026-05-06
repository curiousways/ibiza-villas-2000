# Special Offers page: aggregation refactor

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Refactor with intentional visual change. The Special Offers page
currently reads offer data from a page-level `offer_table` ACF
repeater (legacy centralised model). After the previous brief
shipped the per-villa `villa_offers` repeater on the villa CPT,
this brief swaps the SO page's data source: it now aggregates
active offers across all villas and renders them as cards.

The visual change is real but bounded: the offer card adapts to
a simpler data shape (the new `villa_offers` doesn't have was/now
pricing pairs, "this week's deal" badge, or per-row footnote —
those were artefacts of the legacy model). The card retains its
existing layout primitives (image, villa name, dates, surface,
spacing) but the price-pair block becomes a single headline line
sourced from the offer's `offer_headline` field.

The featured offer block above the grid (Site Options-driven) is
untouched. The Short Breaks section below the grid is untouched.
The hero is untouched.

## Scope

**In:**

1. Replace the data source for the SO grid: query villas, expand
   to one card per active villa-offer pair, sort by
   `offer_date_from` ascending.
2. Adapt the offer card markup/CSS to the new shape:
   - Drop was-price / now-price block
   - Drop "this week's deal" badge
   - Drop footnote field
   - Add headline line (replaces the price-pair visual block)
   - Description (optional) follows headline if present
3. Remove the legacy `offer_table` ACF repeater + its sub-fields
   from `register-page-special-offers.php`.
4. Simplify the page template to call the section helper without
   passing data (the helper now handles its own aggregation).

**Out:**

- Featured offer block (Site Options-driven, untouched)
- Short Breaks section (untouched)
- Hero section (untouched)
- Empty-state component (untouched — same component triggers when
  the new aggregation finds zero active offers)
- Per-villa accordion on villa detail (already shipped, not affected)
- Villa detail page rebuild (separate future workstream)
- Any change to villa CPT, `villa_offers` schema, or how Tina
  authors offers

## Verification needed before starting

Confirm against current state of the repo. Don't proceed until
pinned down:

- [ ] **Existing offer card location.** Find the file that renders
  one offer card. Likely under
  `mu-plugins/ibv-core/includes/components/` or inside the
  `special-offers-grid` section. Read its current markup and
  data expectations carefully — the card adapts in this brief,
  doesn't get rebuilt.
- [ ] **Existing `special-offers-grid` section file** — confirm
  its function signature
  (`ibv_core_section_special_offers_grid( $offers )`) and where
  it loops over rows.
- [ ] **`villa_offers` schema reminder.** Sub-field names from
  the previous brief: `offer_name`, `offer_date_from`,
  `offer_date_to`, `offer_headline`, `offer_description`. Verify
  by reading the villa CPT ACF registration file.
- [ ] **Token names.** Verify against `tokens.css` for any
  spacing or typography tokens needed by the simplified card
  (since the price-pair block goes away, vertical rhythm in the
  card will change — confirm the existing tokens used elsewhere
  in the card before introducing anything).
- [ ] **Other callers of `ibv_core_section_special_offers_grid`.**
  Grep the codebase. If only the SO page template calls it,
  changing the signature is safe. If anything else calls it,
  surface and discuss before changing.

If any of the above are not where the brief assumes, surface
before writing code, not after.

## Standards

- BEM, `ibv-` prefixes
- Section rhythm + surface system: section root is
  `<section class="ibv-section-... ibv-section ibv-section--surface-{X}">`,
  no `padding-block`/`background`/`color` on root
- Existing tokens only — no new tokens
- All escaping/sanitisation explicit
- Standard image and button helpers
- No `the_content()` — copy comes from explicit ACF fields

Step 12 default applies: act on small in-scope quality fixes
within files this brief edits. Surface anything bigger.

## Files

```
EDIT  mu-plugins/ibv-core/includes/sections/special-offers-grid/special-offers-grid.php
                                  (own its query; new data shape per row)
EDIT  mu-plugins/ibv-core/includes/sections/special-offers-grid/special-offers-grid.css
                                  (only if grid layout needs adjustments; likely minor)

EDIT  [VERIFY path]/components/.../offer-card or wherever the card lives
                                  (drop was/now block + badge + footnote;
                                   add headline + optional description)
EDIT  [VERIFY path]/.css for the card

EDIT  themes/ibv/page-special-offers.php
                                  (remove $offers fetch and the grid/empty-state if/else)

EDIT  mu-plugins/ibv-core/includes/acf/register-page-special-offers.php
                                  (remove the offer_table tab + repeater; keep hero fields)
```

## Change 1 — Aggregation logic

The `special-offers-grid` section now owns its own data. New
function flow:

```php
function ibv_core_section_special_offers_grid() {

    // 1. Find villas with at least one offer.
    //    Most efficient path: fetch all published villas; the
    //    villa_offers repeater is small per villa, so the
    //    in-memory filter that follows is fine for the project's
    //    scale (~15 villas). Avoid meta_query gymnastics.
    $villas = get_posts( [
        'post_type'      => 'villa',  // VERIFY post_type slug
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );

    // 2. Expand each villa's active offers into a flat list.
    $today  = current_time( 'Ymd' );
    $cards  = [];

    foreach ( $villas as $villa_id ) {
        $offers = get_field( 'villa_offers', $villa_id );
        if ( ! is_array( $offers ) ) {
            continue;
        }
        foreach ( $offers as $i => $offer ) {
            if ( empty( $offer['offer_date_to'] ) ) {
                continue;
            }
            if ( $offer['offer_date_to'] < $today ) {
                continue;
            }
            $cards[] = [
                'villa_id'    => (int) $villa_id,
                'offer'       => $offer,
                'offer_index' => $i,
            ];
        }
    }

    // 3. Sort by offer_date_from ascending.
    usort( $cards, function ( $a, $b ) {
        return strcmp(
            (string) ( $a['offer']['offer_date_from'] ?? '' ),
            (string) ( $b['offer']['offer_date_from'] ?? '' )
        );
    } );

    // 4. Empty state if nothing active.
    if ( empty( $cards ) ) {
        ibv_core_section_special_offers_empty_state();
        return;
    }

    // 5. Render section + grid + cards.
    wp_enqueue_style( 'ibv-section-special-offers-grid' );
    ?>
    <section class="ibv-section-special-offers-grid ibv-section ibv-section--surface-bg">
        <div class="ibv-container">
            <div class="ibv-section-special-offers-grid__grid">
                <?php foreach ( $cards as $card ) : ?>
                    <?php
                    ibv_core_offer_card( [   // VERIFY helper name
                        'villa_id'    => $card['villa_id'],
                        'offer'       => $card['offer'],
                    ] );
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}
```

The function signature changes — no `$offers` arg. The page
template just calls it. The grid function delegates the empty
state internally so the template doesn't need an if/else.

`current_time( 'Ymd' )` is site-timezone-aware (matches the
villa-offers accordion's filter rule from the previous brief).

## Change 2 — Adapt the offer card

Read the existing card first. It currently expects keys like
`villa`, `was_price`, `now_price`, `valid_from`, `valid_to`,
`footnote`, `is_weekly_deal` (or whichever names — verify against
legacy field keys).

After this brief, the card receives:

```php
[
    'villa_id' => 42,           // for villa name + permalink + featured image
    'offer'    => [             // a row from the villa_offers repeater
        'offer_name'        => 'Spring 4-night escape',
        'offer_date_from'   => '20260527',
        'offer_date_to'     => '20260531',
        'offer_headline'    => 'From €1,800/wk',
        'offer_description' => '',  // optional
    ],
]
```

**Card markup adaptation:**

- **Image:** villa featured image (existing behaviour).
- **Villa name:** villa post title (existing behaviour, link to
  villa permalink).
- **Dates:** the offer's `offer_date_from`–`offer_date_to`,
  formatted as a short date range (`27–31 May 2026` /
  `28 May – 3 Jun 2026` — same format as the villa-offers
  accordion uses).
- **Headline:** new line replacing the was/now price block.
  Visually prominent — display font or bolder weight; sits where
  the price-pair currently sits. Reuses the typography tokens
  the legacy now-price used for visual continuity.
- **Description:** small body copy below the headline if
  `offer_description` is non-empty. Otherwise nothing.
- **CTA:** existing CTA pattern — link to villa permalink,
  labelled "View villa" (or whatever the existing card uses;
  match it). The CTA goes to the villa page, where the user
  sees the full offer accordion + enquiry form.

**Removed from the card:**

- "This week's deal" badge — drop entirely. The featured offer
  above the grid plays the highlight role.
- Was/now price block — replaced by headline.
- Footnote — drop. If important context is needed for an offer,
  Tina puts it in `offer_description`.

**Card CSS adjustments:**

- The price-pair visual block becomes a single line. Line spacing
  in the card may need to relax slightly (one fewer block of
  type). Verify visually against the existing layout.
- Badge CSS removed.
- Footnote CSS removed.
- All other card CSS (image aspect, surface, padding, hover,
  CTA styling) unchanged.

The card helper signature changes from `(villa_post_obj, $row_data)`
or similar to `(villa_id, $offer_array)`. Update callers
accordingly. If the card is ALSO rendered from somewhere else in
the codebase (Step 11 grep), surface that — it likely isn't, but
worth verifying.

## Change 3 — Remove legacy `offer_table` ACF

In `register-page-special-offers.php`:

- Remove the `field_ibv_so_tab_offer_table` tab field
- Remove the `field_ibv_so_offer_table` repeater field and ALL
  its sub-fields (villa picker, was-price, now-price, valid-from,
  valid-to, footnote, badge, etc.)
- Keep the hero tab + hero fields untouched
- Keep all other field-group infrastructure intact

The page-level "Offers" admin UI disappears entirely. That's
intentional — Tina manages offers per-villa now, not on the SO
page.

## Change 4 — Page template

In `themes/ibv/page-special-offers.php`, simplify:

```php
// Before:
$offers = get_field( 'offer_table' );
if ( ! empty( $offers ) && is_array( $offers ) ) {
    ibv_core_section_special_offers_grid( $offers );
} else {
    ibv_core_section_special_offers_empty_state();
}

// After:
ibv_core_section_special_offers_grid();
```

The grid section handles its own data lookup AND the empty-state
fallback.

The hero call, featured offer call, and short breaks call all
remain unchanged.

## Verification

1. **Admin:**
   - Edit the Special Offers page in WP admin
   - The "Offer table" tab is gone — only "Hero" remains
   - Saving the page does not produce any PHP errors

2. **Frontend — populated state:**
   - Multiple villas have active `villa_offers`. Add 2–3 across
     different villas if needed for the smoke test.
   - View the SO page on staging
   - Hero renders unchanged
   - Featured offer (Site Options) renders unchanged above the
     grid
   - The grid renders one card per active villa-offer pair
   - One villa with two active offers produces TWO cards (not
     one grouped card) — each card represents a single offer
   - Cards sorted by `offer_date_from` ascending
   - Each card shows: villa image, villa name, dates, headline,
     description (if present), CTA to villa
   - Short Breaks section renders unchanged below

3. **Frontend — empty state:**
   - Temporarily set all `villa_offers` `offer_date_to` values
     to past dates (or remove them)
   - Reload the SO page
   - Hero + featured offer + Short Breaks all render unchanged
   - Where the grid would be: the empty-state component renders
     instead

4. **Frontend — expired offers don't show:**
   - Set one offer's `offer_date_to` to yesterday on a villa
     with multiple offers
   - Confirm that offer is gone from the SO page; sibling offers
     on the same villa still render

5. **Visual regression check:**
   - Compare the rebuilt SO grid against the design:
     - Card spacing, image aspect, type hierarchy all match
     - Headline replaces was/now visual block at the same
       vertical position
     - No badge artefacts, no footnote remnants
   - If anything looks visually off vs the existing design,
     surface — the brief says headline reuses the now-price
     typography for continuity; if that's wrong against the
     current card visual, flag and ask

6. **Token + system compliance:**
   - All CSS uses existing tokens
   - No new tokens added
   - Section root carries surface modifier; grid CSS doesn't
     redeclare padding-block/background/color on root

7. **Regressions on adjacent pages:**
   - Homepage: featured offer block (if rendered there from
     globals) unchanged
   - Villa detail page: villa-offers accordion still renders
     correctly (this brief doesn't touch it but worth a glance)

## Notes

- **Why "one card per villa-offer pair", not "one card per
  villa".** Tina described 3–4 offers per villa as routine. Each
  offer is its own promotable item with its own dates and
  headline. Grouping them under one card per villa would hide
  the variety and bury time-sensitive offers.
- **Why drop the "this week's deal" badge.** The featured offer
  block above the grid is the highlight mechanism. A second
  highlight inside the grid would compete with it. The featured
  offer is also already a fully-controlled editorial choice
  (Site Options) — Tina picks the one that matters, the grid
  shows the rest evenly.
- **Why drop the footnote.** The legacy footnote was per-row
  small print on offer cards (e.g. "valid for new bookings
  only"). Under the new model, this kind of caveat goes into
  `offer_description`. Editorial workflow simplification.
- **Why visual change is acceptable.** The data model genuinely
  changed shape. Forcing the legacy card layout (was/now,
  badge, footnote) onto data that doesn't fit it would be more
  jarring than letting the card simplify to match. The card
  primitives (image, villa name, dates, surface) all stay; only
  the bottom-half content adapts.
- **Why keep the empty-state component.** It already handles
  "no offers active right now" elegantly. Reusing it means the
  refactor doesn't introduce an empty-state design decision.
- **Why no migration of legacy data.** Decided in earlier
  conversation. Tina re-creates offers under the new model. The
  legacy `offer_table` data, if any exists, becomes irrelevant
  the moment that ACF is removed.
- **Step 12 reminder.** Small in-scope improvements (a missing
  escape in the simplified card, a stray hardcoded value in the
  grid CSS, a now-unused helper that the legacy card depended
  on) — fix in the same commit and note. Larger or out-of-scope:
  surface, don't act.
- **Future workstreams this unblocks (or is unblocked by):**
  - **Bob's enquiry form work** — independent, parallel.
  - **Villa detail page rebuild** — independent, parallel. The
    accordion's inline location in `single-villas.php` will be
    repositioned during that rebuild.
  - **Featured offer mechanism** — currently Site Options
    manual. Could later be sourced from a "feature this offer"
    flag on a `villa_offers` row, but that's a separate
    enhancement decision; not in scope here.
