# Brief 05 — Special Offers page rebuild

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

## Prerequisites

This brief assumes briefs 01–04 have all landed and been verified:

- **Brief 01** — `register-globals-content.php` exists with
  `featured_offer_*` and `short_breaks_*` field groups. Section
  function is `ibv_core_section_featured_offer( $args )` accepting
  `section_title` and `show_section_cta` overrides.
- **Brief 02** — `ibv_core_section_hero()` accepts an `after_copy`
  callable arg. Calling it with no args renders no search.
- **Brief 03** — `ibv_core_newsletter_form( $args )` helper exists,
  accepting a `variant` arg including `'empty-state'`.
- **Brief 04** — `ibv_core_villa_card()` supports `badge` on all
  variants, `badge_variant` (default | gold | teal | red), and
  `footnote` + `show_now_asterisk` on the offer variant.

If any of those isn't in place, stop and complete the missing brief
first.

## Standards

- ACF field groups registered in PHP under
  `mu-plugins/ibv-core/includes/acf/`
- Section files live under `mu-plugins/ibv-core/includes/sections/{name}/`
  with a `.php` and a `.css`
- Section root: `ibv-section-{name} ibv-section ibv-section--surface-{X}`,
  optional `--rhythm-sm`. Section CSS must NOT declare `padding-block`,
  `background`, or `color` on the root. Surface modifier is mandatory
  and must be declared explicitly.
- Image rendering via `ibv_core_image()` helper
- Buttons via `ibv_core_button()` helper
- BEM naming, `ibv-` prefixes, no new dependencies, no build step

## Scope

Build the Special Offers page to match the signed-off Figma design
(file 3g57x18kqjjNtnJ9SVgTUI, frame `282:3780` "03a | Special Offers"
on the UI page). Three new pieces of work, plus a template rewrite
and legacy cleanup.

Reuse: hero, featured-offer, short-breaks (all from prerequisite
briefs). Villa-card grid uses the offer variant with the new badge +
footnote support from brief 04.

## Files

```
NEW    mu-plugins/ibv-core/includes/acf/register-page-special-offers.php
NEW    mu-plugins/ibv-core/includes/sections/special-offers-grid/special-offers-grid.php
NEW    mu-plugins/ibv-core/includes/sections/special-offers-grid/special-offers-grid.css
NEW    mu-plugins/ibv-core/includes/sections/special-offers-empty-state/special-offers-empty-state.php
NEW    mu-plugins/ibv-core/includes/sections/special-offers-empty-state/special-offers-empty-state.css

EDIT   mu-plugins/ibv-core/includes/acf/register-villa-fields.php (strip legacy SO group)
EDIT   themes/ibv/page-special-offers.php (full rewrite)

DELETE mu-plugins/ibv-core/includes/sections/special-offers-page/special-offers-page.css

EDIT   wherever ACF files are required (add the new file)
EDIT   wherever section CSS is enqueued/registered (add 2 new sections, drop 1)
EDIT   newsletter-form.css (fill in the --empty-state modifier styles)
```

## Change 1 — Create `register-page-special-offers.php`

New file. Two field groups, both location-targeted to
`page_template == page-special-offers.php`.

**Group 1: "Special Offers — page content"**

Tabs:

**Tab Hero:**
- `hero_image` — image, return array
- `hero_title` — text
- `hero_subtitle` — text

(These match the homepage hero field shape exactly so the reused
hero section function reads them transparently.)

**Tab Offer table:**

Repeater field `offer_table`, button label "Add offer". Sub-fields:
- `villa` — post_object, post_type=villas, return=id
- `is_weeks_deal` — true_false, label "Show 'This week's deal' badge",
  instructions: "Adds a yellow badge to this card. Use on at most one
  row per page."
- `was_price` — number, prepend €
- `now_price` — number, prepend €
- `valid_from` — date_picker, return Y-m-d
- `valid_to` — date_picker, return Y-m-d
- `footnote` — text, instructions: "Optional footnote below this
  card's pricing"
- `show_now_asterisk` — true_false, default false, instructions:
  "Show * after the Now price (paired with footnote)"

Field key prefix: `field_ibv_so_*`. **Do not** reuse the legacy
`field_568e*` keys — those tied to the old free-text schema. Fresh
keys avoid ACF trying to graft new structures onto orphaned data.

**Group 2: "Special Offers — empty state"**

Single tab. Fields:
- `empty_state_title` — text, default "Nothing available"
- `empty_state_body` — textarea, default "No special offers for you
  right now — get offers by email, check back soon, or browse all
  villas."
- `empty_state_image` — image, return array
- `empty_state_browse_cta_url` — url

Both groups use `position: acf_after_title`.

## Change 2 — Strip legacy SO field group from `register-villa-fields.php`

Cut the entire field group containing `offer_table` from
`register-villa-fields.php`. It currently sits around lines 975–1256
and includes:

- The legacy `offer_table` repeater (with free-text `dates` and
  `special_offer_price`)
- `home_feature_this_offer` (true_false on offer rows)
- `offer_redbox` (true_false)
- `offer_red_round_box_1` (text)
- `offer_red_round_box_line_2` (text)
- `offer_red_round_box_line_3` (text)
- The location rule targeting `page-special-offers.php`

After cutting, this file should contain only fields that are genuinely
villa-scoped (the `villa_*` fields, `property_*` fields, etc.). The
`group_ibv_villa_listing_page` group at the bottom stays where it is.

Existing data in the legacy fields is orphaned. Acceptable — staging
is throwaway, content is re-entered.

## Change 3 — Create the offer grid section

`mu-plugins/ibv-core/includes/sections/special-offers-grid/special-offers-grid.php`:

```php
<?php
/**
 * Section: Special Offers — offer grid.
 *
 * Renders a 3-up grid of villa cards (offer variant) from the
 * page-level offer_table repeater.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @param array $offers ACF offer_table repeater rows.
 */
function ibv_core_section_special_offers_grid( $offers ) {
    if ( empty( $offers ) || ! is_array( $offers ) ) {
        return;
    }
    wp_enqueue_style( 'ibv-section-special-offers-grid' );
    ?>
    <section class="ibv-section-special-offers-grid ibv-section ibv-section--surface-bg">
        <div class="ibv-container">
            <div class="ibv-section-special-offers-grid__cards">
                <?php
                foreach ( $offers as $offer ) {
                    $vid = (int) ( $offer['villa'] ?? 0 );
                    if ( ! $vid ) {
                        continue;
                    }
                    $is_weeks_deal = ! empty( $offer['is_weeks_deal'] );
                    ibv_core_villa_card(
                        [
                            'villa'             => $vid,
                            'variant'           => 'offer',
                            'badge'             => $is_weeks_deal ? __( "This week's deal", 'ibv' ) : '',
                            'badge_variant'     => 'gold',
                            'was_price'         => isset( $offer['was_price'] ) ? (float) $offer['was_price'] : null,
                            'now_price'         => isset( $offer['now_price'] ) ? (float) $offer['now_price'] : null,
                            'valid_from'        => $offer['valid_from'] ?? '',
                            'valid_to'          => $offer['valid_to'] ?? '',
                            'footnote'          => $offer['footnote'] ?? '',
                            'show_now_asterisk' => ! empty( $offer['show_now_asterisk'] ),
                            'cta_label'         => __( 'Enquire now', 'ibv' ),
                        ]
                    );
                }
                ?>
            </div>
        </div>
    </section>
    <?php
}
```

CSS file: 3-up grid at desktop, 1-up on mobile. Match the breakpoint
used by the homepage `featured-villas` grid for consistency.

```css
.ibv-section-special-offers-grid__cards {
    display: grid;
    gap: var(--ibv-space-xl);
}

@media (min-width: 48rem) {
    .ibv-section-special-offers-grid__cards {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

(Adjust the breakpoint if the homepage uses a different one — match
that. The villa-card CSS handles per-card sizing.)

## Change 4 — Create the empty state section

`mu-plugins/ibv-core/includes/sections/special-offers-empty-state/special-offers-empty-state.php`:

```php
<?php
/**
 * Section: Special Offers — empty state.
 *
 * Rendered when offer_table is empty. Image-text layout with email
 * capture (newsletter helper) + 'Browse all villas' CTA.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ibv_core_section_special_offers_empty_state() {
    wp_enqueue_style( 'ibv-section-special-offers-empty-state' );

    $title   = (string) get_field( 'empty_state_title' );
    $body    = (string) get_field( 'empty_state_body' );
    $image   = get_field( 'empty_state_image' );
    $cta_url = (string) get_field( 'empty_state_browse_cta_url' );
    ?>
    <section class="ibv-section-special-offers-empty-state ibv-section ibv-section--surface-tint-teal">
        <div class="ibv-container ibv-section-special-offers-empty-state__inner">
            <div class="ibv-section-special-offers-empty-state__copy">
                <?php if ( $title ) : ?>
                    <h2 class="ibv-section-special-offers-empty-state__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <hr class="ibv-section-special-offers-empty-state__rule" aria-hidden="true">
                <?php if ( $body ) : ?>
                    <p class="ibv-section-special-offers-empty-state__body"><?php echo esc_html( $body ); ?></p>
                <?php endif; ?>
                <?php
                ibv_core_newsletter_form(
                    [
                        'variant' => 'empty-state',
                    ]
                );
                if ( $cta_url ) {
                    ibv_core_button(
                        [
                            'url'     => $cta_url,
                            'label'   => __( 'Browse all villas', 'ibv' ),
                            'variant' => 'secondary',
                        ]
                    );
                }
                ?>
            </div>
            <?php if ( $image ) : ?>
                <div class="ibv-section-special-offers-empty-state__media">
                    <?php
                    ibv_core_image(
                        $image,
                        'ibv-card',
                        [ 'class' => 'ibv-section-special-offers-empty-state__image' ]
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}
```

CSS file: two-column layout (copy left, image right) matching the
homepage `image-text-section` component's responsive treatment.
Section CSS does NOT declare `padding-block`, `background`, or `color`
on the root. Surface (`tint-teal`) is provided by the modifier class.

## Change 5 — Newsletter form `--empty-state` styling

In `newsletter-form.css` (created in brief 03), the `--empty-state`
modifier was left as a stub. Fill it in now. The empty-state context
sits on a tint-teal background, with the title and body copy provided
by the surrounding section (NOT by the helper). So in this context,
the helper renders ONLY the form embed — title and description args
are not passed.

Style the form's submit button and input field to fit the tint-teal
context: the input should match the design's email capture styling
(see Figma frame). The submit button uses the project's standard
button styling — likely `var(--ibv-color-accent-gold)` background
matching the right-arrow CTA shown in the design.

## Change 6 — Rewrite `themes/ibv/page-special-offers.php`

Replace the existing template wholesale:

```php
<?php
/**
 * Template Name: Special Offers
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    // Hero — reused section, reads page-level hero_* ACF fields.
    // No after_copy callable → no search bar (search is homepage-only).
    ibv_core_section_hero();

    // Featured offer — reused section, reads global featured_offer_* fields.
    // Override section title and suppress the section CTA on the SO page
    // (the CTA on the homepage points HERE, so it'd loop back).
    ibv_core_section_featured_offer(
        [
            'section_title'    => __( "This Week's Special Offer", 'ibv' ),
            'show_section_cta' => false,
        ]
    );

    // Offer grid OR empty state.
    $offers = get_field( 'offer_table' );
    if ( ! empty( $offers ) && is_array( $offers ) ) {
        ibv_core_section_special_offers_grid( $offers );
    } else {
        ibv_core_section_special_offers_empty_state();
    }

    // Short breaks — reused section, reads global short_breaks_* fields.
    ibv_core_section_short_breaks();

endwhile;

get_footer();
```

Drop all references to `wp_enqueue_style( 'ibv-section-special-offers-page' )`
and `wp_enqueue_style( 'ibv-section-featured-villas' )` etc. — each
section enqueues its own CSS.

## Change 7 — Delete `special-offers-page.css`

The 17-line file at
`mu-plugins/ibv-core/includes/sections/special-offers-page/special-offers-page.css`
is now redundant. Delete the file. Remove its enqueue registration
from wherever section CSS is registered (likely a sections registry
file). Leave the directory if section auto-discovery requires it,
otherwise remove that too.

## Change 8 — Register new sections + ACF file

Wherever the codebase wires up:
- ACF files (require new `register-page-special-offers.php`)
- Section CSS (register `ibv-section-special-offers-grid` and
  `ibv-section-special-offers-empty-state` handles, drop
  `ibv-section-special-offers-page`)
- Section PHP (require the two new section files)

Pattern follows however the existing sections register today.

## Surface map for the SO page

Per Step 7 of the cursor-brief skill, every body section declares its
surface explicitly:

| Section                 | Surface     | Rhythm  |
|-------------------------|-------------|---------|
| Hero (chrome — exempt)  | n/a         | n/a     |
| Featured offer          | tint-teal   | default |
| Offer grid              | bg          | default |
| Empty state (alt)       | tint-teal   | default |
| Short breaks            | bg          | default |

Adjacency notes:
- **Populated branch:** featured-offer (tint-teal) → grid (bg) — no
  collapse. Grid (bg) → short-breaks (bg) — same surface, auto-collapse
  fires. Desired.
- **Empty branch:** featured-offer (tint-teal) → empty-state
  (tint-teal) — same surface, auto-collapse fires. Then empty-state
  (tint-teal) → short-breaks (bg) — different, no collapse. Verify
  visually.

## Verification

1. **WP admin smoke test:**
   - Special Offers page edit screen: Hero / Offer table / Empty
     state tabs visible. Offer table rows have structured fields
     (was/now numeric, date pickers, footnote, is_weeks_deal toggle,
     show_now_asterisk toggle). No legacy redbox fields.
   - Villa post edit screen: legacy SO field group is gone. Only
     villa-scoped fields remain.

2. **Re-populate content:**
   - Special Offers page: hero (image + title + subtitle), offer
     rows, empty-state copy + image, browse CTA URL.
   - (Globals: featured offer + short breaks should already be set
     from brief 01 verification.)

3. **Frontend smoke test, populated state** (offer_table has rows):
   - Hero renders with image + title + subtitle. **No search bar.**
   - Featured offer renders with structured Was/Now Euro pricing on
     tint-teal, with optional asterisk + footnote.
   - Section header reads "This Week's Special Offer" with NO
     "Search all Special Offers" CTA (suppressed via
     `show_section_cta: false`).
   - Grid renders 3-up at desktop, 1-up at mobile breakpoint.
   - First row with `is_weeks_deal=true` carries the gold "This
     week's deal" badge.
   - Each card shows structured Was/Now pricing with strikethrough on
     Was, optional asterisk on Now, footnote below pricing when set.
   - Short breaks renders below.
   - Auto-collapse fires between grid and short-breaks (both
     surface-bg).

4. **Frontend smoke test, empty state** (clear the offer_table
   repeater):
   - Featured offer still renders (it's not part of the empty branch).
   - Grid replaced by empty-state section: title, body, newsletter
     email capture, "Browse all villas" CTA, image right-side.
   - Newsletter form renders inside the empty-state with the
     `--empty-state` styling variant.
   - Short breaks renders below.

5. **Mobile smoke test:**
   - Hero scales per existing responsive rules.
   - Featured offer panel stacks (image above content) per
     offer-panel responsive behaviour.
   - Grid drops from 3-up to 1-up at the same breakpoint as the
     homepage featured-villas.
   - Empty state stacks (image above copy) per image-text responsive
     behaviour.

## Notes

- **Featured offer "Search all Special Offers" CTA suppression.** On
  the homepage this CTA points to /special-offers/. On the SO page
  itself it would loop back. Suppressed via the
  `show_section_cta: false` arg added in brief 01.
- **Hero search not rendered.** SO page calls hero with no
  `after_copy`; brief 02's pattern handles this naturally.
- **Coloured location pills** (per location category — Ibiza Town,
  San Antonio, etc. in different colours) are NOT in scope. Cards use
  the existing single-style location pill. If the design intent
  is per-category colours, that's tied to the `property_location`
  taxonomy work and belongs in a separate brief.
- **Mobile design.** No dedicated mobile frame exists for the SO page.
  Implementation follows existing homepage responsive conventions.
- **Empty state newsletter.** The helper is called with no
  `title`/`description` because the surrounding section provides those.
  Only the form embed renders.
- **Content reset.** Per project policy, staging is throwaway. After
  this lands, Tina/Luke re-enter the SO page hero, offer rows, and
  empty-state content. Featured offer and short breaks should already
  be populated from brief 01 verification.
- **What's deferred to handoff or follow-up briefs:**
  - Coloured location pills (taxonomy-driven)
  - Per-card asterisk+footnote pairing UX (today the asterisk is
    presentational; if Tina wants the asterisk to auto-link to the
    footnote semantically, that's a content/UX decision)
  - Any auto-population logic for "feature on homepage" if that
    workflow comes back (legacy `home_feature_this_offer` was
    dropped — homepage now uses the global featured offer instead)
