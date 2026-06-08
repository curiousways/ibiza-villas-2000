# Booking confirmation page

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield page. Composes one new tiny inline section
("confirmation panel") plus the existing three-step section
(with a small variant-prep change to accept page-scoped content)
plus the existing `image-text-section` component.

This is the page Bob's enquiry form redirects to on successful
submission. Tina manages the editorial content via ACF.

**Design reference:** Figma frame 02c "Booking Confirmation"
(node-id 301-8647), file `3g57x18kqjjNtnJ9SVgTUI`.

**Departure from Figma:** the design has a "Similar villas"
section at the bottom; David's calling that out — it doesn't
make sense to upsell more villas to someone who has just booked
one. **Drop it.** Page ends after the concierge image-text
section.

## Scope

**In:**

1. New page template `themes/ibv/page-booking-confirmation.php`
2. New ACF group on the page template (confirmation heading,
   three-step content, concierge content)
3. **Variant-prep on `three-step` section** — add an optional
   args path so the page can supply its own content instead of
   reading globals. Default behaviour (no args → read globals)
   unchanged.
4. Confirmation panel — small inline markup at the top of the
   template: centered checkmark icon in a teal circle + display
   heading. Inline in template, not a new component (one consumer
   only).
5. Composition: confirmation panel → optional booking-details
   panel (URL-driven, see below) → three-step → image-text
6. **Bonus — booking details from URL.** Read query params Bob's
   form will pass on redirect; render a small "Your request"
   panel between the confirmation heading and the three-step
   section. Strict whitelisting and escaping. If no params,
   panel doesn't render. See "Bonus" section below.

**Out:**

- "Similar villas" section from the Figma — explicitly dropped
- Form handling, redirect behaviour from the enquiry form —
  Bob's territory
- Any change to the existing three-step rendering (adding an
  args path is additive only)
- Any change to image-text-section
- Email confirmation copy / template (Bob's email handler)
- Any new icon assets unless one is genuinely missing

## Verification needed before starting

- [ ] **Three-step section** — confirmed reads globals only, no
  args. Adding an optional args path is the variant-prep step.
- [ ] **`ibv_core_image_text_section()`** — confirmed takes args
  cleanly (`title`, `description`, `cta_url`, `cta_label`,
  `image`, `image_side`, `surface`). Reuse as-is.
- [ ] **Lucide icon location** — earlier briefs vendored icons
  at `assets/icons/lucide/` somewhere. Brief 01 of Ibiza Guide
  vendored `clock.svg`. Locate the canonical path; the booking
  confirmation page needs `check.svg` (or `check-circle.svg`)
  for the success indicator. Vendor from Lucide v0.460.0 if
  not present.
- [ ] **Surface tokens** — confirm the surface modifier name for
  the off-white background visible in the Figma. The
  image-text-section args list `'bg'` as a valid surface so
  that's likely the right one.
- [ ] **Existing page template patterns** — read
  `register-page-about.php` and `register-page-ibiza-guide.php`
  to match field-key naming convention.

If any of the above are not where the brief assumes, surface
before writing code, not after.

## Standards

- BEM, `ibv-` prefixes
- Section rhythm + surface system: confirmation panel section
  is `<section class="ibv-section-... ibv-section ibv-section--surface-{X}">`
- Existing tokens only — no new tokens
- All escaping/sanitisation explicit
- Standard image and button helpers
- No `the_content()` — copy comes from explicit ACF fields
- Field-key naming follows `register-page-*.php` pattern

Step 12 default applies: act on small in-scope quality fixes
within files this brief edits. Surface anything bigger.

## Files

```
NEW   themes/ibv/page-booking-confirmation.php
NEW   mu-plugins/ibv-core/includes/acf/register-page-booking-confirmation.php

EDIT  mu-plugins/ibv-core/includes/sections/three-step/three-step.php
                                  (add optional args path; defaults preserve existing
                                   global-reading behaviour)

VERIFY/VENDOR  themes/ibv/assets/icons/lucide/check-circle.svg
                                  (or wherever the project canonically vendors Lucide)

EDIT  mu-plugins/ibv-core/bootstrap.php
EDIT  mu-plugins/ibv-core/includes/shared-assets.php
                                  (only if a new style handle is registered for the
                                   confirmation panel CSS — see below)
```

If the confirmation panel's CSS is small enough (a handful of
rules), the agent can either inline the styles into the page
template's existing CSS handle (if the page template enqueues a
small page-scoped stylesheet) OR put them in a tiny CSS file at
`themes/ibv/assets/css/page-booking-confirmation.css`. Pick
whatever matches existing precedent. If neither precedent
exists, inline the rules into a tiny page-scoped CSS file.

## Change 1 — ACF schema

Create `register-page-booking-confirmation.php` following the
naming and structure of existing `register-page-*.php` files.

Field group key: `group_ibv_page_booking_confirmation` (match
project convention).

Location rule: `page_template == page-booking-confirmation.php`.

Fields:

```
Confirmation panel (tab)
├── confirmation_heading           (text, required)
│   default: "Booking request received"
└── confirmation_subheading        (text, optional)
    helper: "Optional supporting line below the heading."

Three-step (tab)
├── three_step_eyebrow             (text)
│   default: "What happens next"
├── three_step_title               (text)
│   default: "Three steps to confirmation"
└── three_step_steps               (repeater, min 3 / max 3)
    sub-fields:
    ├── step_title  (text)         e.g. "Call or email"
    └── step_body   (textarea)     2-3 sentences

Concierge (tab)
├── concierge_image                (image)
├── concierge_title                (text)
│   default: "Explore concierge services"
├── concierge_body                 (textarea)
├── concierge_cta_label            (text)   default: "Enquire about a villa"
└── concierge_cta_url              (URL or page picker — match existing pattern)
```

Field labels in admin should be human-readable. Copy of Figma
content provided as defaults so the page renders sensibly out of
the box; Tina edits as needed.

## Change 2 — Variant-prep on three-step

Read `sections/three-step/three-step.php`. The current function
signature is `ibv_core_section_three_step()` — no args, reads
globals exclusively.

**Add an optional args path that does NOT change default
behaviour:**

```php
function ibv_core_section_three_step( $args = [] ) {
    $defaults = [
        'eyebrow'   => null,    // null = read from globals
        'title'     => null,    // null = read from globals
        'steps'     => null,    // null = read from globals
    ];
    $args = wp_parse_args( $args, $defaults );

    wp_enqueue_style( 'ibv-section-three-step' );

    // Preserve existing global-reading behaviour when arg is null.
    $eyebrow = $args['eyebrow'] !== null
        ? $args['eyebrow']
        : get_field( 'three_step_intro_eyebrow', 'option' );

    $title   = $args['title'] !== null
        ? $args['title']
        : get_field( 'three_step_intro_title', 'option' );

    $steps   = $args['steps'] !== null
        ? $args['steps']
        : get_field( 'three_step_steps', 'option' );

    // Existing fallback defaults + render unchanged from here.
    // ...
}
```

The semantics: passing `null` (or omitting the key) means
"use the global". Passing an explicit value (string, array, or
empty string) means "use this". This way the page-scoped
content overrides cleanly without touching the global config.

The page-scoped `steps` array shape must match what the existing
loop expects. Read the existing loop body to confirm sub-field
keys (likely `step_title`, `step_body`, possibly an icon).
Match the page-scoped repeater sub-field names to those keys
exactly so no mapping is needed.

The agent verifies the existing loop and adjusts the booking-
confirmation ACF repeater sub-field names if needed for parity.

This is a backwards-compatible addition. About + Homepage callers
are unaffected.

## Change 3 — Confirmation panel

Inline in the page template (no new component). Markup:

```html
<section class="ibv-page-booking-confirmation__panel ibv-section ibv-section--surface-bg">
    <div class="ibv-container">
        <div class="ibv-page-booking-confirmation__icon" aria-hidden="true">
            <!-- inlined SVG check-circle from Lucide, or img referencing the vendored asset -->
        </div>
        <h1 class="ibv-page-booking-confirmation__title ibv-font-display">
            <?php echo esc_html( $confirmation_heading ); ?>
        </h1>
        <?php if ( $confirmation_subheading ) : ?>
            <p class="ibv-page-booking-confirmation__subtitle">
                <?php echo esc_html( $confirmation_subheading ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>
```

Visual:

- Centered alignment
- Icon: white check on filled teal-circle background; size ~80px
  square. The Figma uses a deep teal (likely
  `--ibv-color-forest-green-deep` or `--ibv-color-link` —
  verify against tokens). Pad the icon's vertical rhythm
  generously.
- Title: large display, centered
- Subtitle (if present): body copy, muted, centered, narrow
  max-width

Surface: `bg` (off-white) per Figma reading.

CSS lives wherever the agent chose per the Files section. Keep
the styles compact — the panel is visually simple.

## Change 4 — Page template

```php
<?php
/**
 * Template Name: Booking Confirmation
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $confirmation_heading    = (string) get_field( 'confirmation_heading' );
    $confirmation_subheading = (string) get_field( 'confirmation_subheading' );

    // 1. Confirmation panel
    // [inline markup per Change 3]

    // 2. Booking details panel — optional, URL-driven
    ibv_render_booking_details_panel();   // helper; see Change 5

    // 3. Three-step (page-scoped content)
    ibv_core_section_three_step( [
        'eyebrow' => (string) get_field( 'three_step_eyebrow' ),
        'title'   => (string) get_field( 'three_step_title' ),
        'steps'   => get_field( 'three_step_steps' ),
    ] );

    // 4. Concierge image-text section
    $concierge_image = get_field( 'concierge_image' );
    if ( $concierge_image ) {
        ibv_core_image_text_section( [
            'title'       => (string) get_field( 'concierge_title' ),
            'description' => (string) get_field( 'concierge_body' ),
            'cta_label'   => (string) get_field( 'concierge_cta_label' ),
            'cta_url'     => (string) get_field( 'concierge_cta_url' ),
            'image'       => $concierge_image,
            'image_side'  => 'right',
            'surface'     => 'white',
        ] );
    }

endwhile;

get_footer();
```

## Change 5 — Bonus: booking details from URL

Bob's enquiry form will redirect to this page after submission.
He may pass booking details via query parameters. This brief
defines the URL contract he'll work to.

**Proposed URL parameters** (Bob to confirm):

- `villa` — villa post ID (integer) OR villa slug (string)
- `arrival` — `YYYY-MM-DD`
- `departure` — `YYYY-MM-DD`
- `guests` — integer
- `offer` — offer name (string, ≤80 chars)

Sensible URL shape:

```
/booking-request-received/?villa=42&arrival=2026-05-27&departure=2026-05-31&guests=4
```

**Helper function** in the page template (or in a small new
helper file `themes/ibv/inc/booking-confirmation-helpers.php` —
agent's call):

```php
function ibv_render_booking_details_panel() {

    // Strict whitelisting and sanitisation.
    $villa_id  = isset( $_GET['villa'] )     ? absint( wp_unslash( $_GET['villa'] ) )                       : 0;
    $arrival   = isset( $_GET['arrival'] )   ? sanitize_text_field( wp_unslash( $_GET['arrival'] ) )        : '';
    $departure = isset( $_GET['departure'] ) ? sanitize_text_field( wp_unslash( $_GET['departure'] ) )      : '';
    $guests    = isset( $_GET['guests'] )    ? absint( wp_unslash( $_GET['guests'] ) )                      : 0;
    $offer     = isset( $_GET['offer'] )     ? sanitize_text_field( wp_unslash( $_GET['offer'] ) )          : '';

    // Validate dates — strict YYYY-MM-DD only.
    $arrival   = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $arrival )   ? $arrival   : '';
    $departure = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $departure ) ? $departure : '';

    // Validate villa exists if ID supplied.
    $villa_name = '';
    $villa_url  = '';
    if ( $villa_id ) {
        $villa = get_post( $villa_id );
        if ( $villa && 'villa' === $villa->post_type ) {  // VERIFY post type slug
            $villa_name = get_the_title( $villa );
            $villa_url  = get_permalink( $villa );
        }
    }

    // Truncate offer if absurdly long.
    if ( strlen( $offer ) > 80 ) {
        $offer = substr( $offer, 0, 80 );
    }

    // If nothing valid, render nothing.
    if ( ! $villa_name && ! $arrival && ! $departure && ! $guests && ! $offer ) {
        return;
    }

    // Render a small panel.
    ?>
    <section class="ibv-page-booking-confirmation__details ibv-section ibv-section--surface-bg ibv-section--rhythm-sm">
        <div class="ibv-container ibv-container--narrow">
            <h2 class="ibv-page-booking-confirmation__details-title">
                <?php esc_html_e( 'Your request', 'ibv' ); ?>
            </h2>
            <ul class="ibv-page-booking-confirmation__details-list">
                <?php if ( $villa_name ) : ?>
                    <li>
                        <strong><?php esc_html_e( 'Villa', 'ibv' ); ?></strong>
                        <?php
                        if ( $villa_url ) {
                            printf(
                                '<a href="%s">%s</a>',
                                esc_url( $villa_url ),
                                esc_html( $villa_name )
                            );
                        } else {
                            echo esc_html( $villa_name );
                        }
                        ?>
                    </li>
                <?php endif; ?>
                <?php if ( $arrival && $departure ) : ?>
                    <li>
                        <strong><?php esc_html_e( 'Dates', 'ibv' ); ?></strong>
                        <?php
                        // Format using existing date-format helper if present;
                        // otherwise format inline as "27 May – 31 May 2026".
                        echo esc_html( ibv_format_date_range( $arrival, $departure ) );
                        ?>
                    </li>
                <?php endif; ?>
                <?php if ( $guests ) : ?>
                    <li>
                        <strong><?php esc_html_e( 'Guests', 'ibv' ); ?></strong>
                        <?php echo esc_html( (string) $guests ); ?>
                    </li>
                <?php endif; ?>
                <?php if ( $offer ) : ?>
                    <li>
                        <strong><?php esc_html_e( 'Offer', 'ibv' ); ?></strong>
                        <?php echo esc_html( $offer ); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </section>
    <?php
}
```

**Important details:**

- Every input is whitelisted/sanitised. Even though the form
  submission is server-controlled, the URL is user-mutable —
  someone could land on this page directly with crafted params.
  Defensive sanitisation throughout.
- If no params arrive, function returns early — nothing renders.
  The page still works as a generic confirmation page.
- Villa lookup verifies the ID resolves to a real villa post. If
  it doesn't (typo, malicious URL, deleted villa), the villa
  field silently doesn't render rather than showing a broken
  state.
- Offer string is shown as-is (escaped). It's an editorial
  string Bob's pulled from `villa_offers`; if multiple were
  active, his concatenation logic decides what to pass — outside
  this brief's concern.
- Date formatting reuses any existing date-range helper in the
  codebase. If none exists, format inline as
  `27 May – 31 May 2026` matching the villa-offers accordion's
  pattern from earlier briefs.

**If `ibv_format_date_range()` doesn't exist:** the agent inlines
the formatting in this helper or extracts it as a small new
helper in `helpers.php`. The villa-offers accordion built in an
earlier brief already does similar formatting — if it has a
shared helper, reuse it.

## Verification

1. **Page admin setup:**
   - Create a page in WP admin titled "Booking Request
     Received" (or any title — Tina decides), set the template
     to "Booking Confirmation"
   - Populate ACF: confirmation heading, three-step content
     (3 rows), concierge fields
   - Save

2. **Frontend smoke test — no URL params:**
   - Visit the page directly (no query string)
   - Confirmation panel renders: checkmark + title (+ optional
     subtitle)
   - Booking details panel does NOT render (no params)
   - Three-step section renders with the page's content (NOT
     the homepage's globals)
   - Concierge image-text section renders with the page's
     content
   - Footer renders below

3. **Frontend smoke test — with URL params:**
   - Visit with
     `?villa={real_villa_id}&arrival=2026-05-27&departure=2026-05-31&guests=4&offer=Spring%204-night%20escape`
   - Booking details panel renders between confirmation panel
     and three-step
   - Villa name links to the villa page
   - Dates formatted as a range
   - Guests + offer shown
   - All values escaped

4. **Frontend smoke test — partial params:**
   - With only `?villa={id}` — only villa row renders
   - With only `?arrival=2026-05-27&departure=2026-05-31` —
     only dates row renders
   - With invalid villa ID (e.g. 99999) — villa row does NOT
     render, other valid params still do
   - With invalid date format (e.g. `arrival=banana`) — dates
     row does NOT render

5. **Three-step regression:**
   - Visit homepage — three-step renders with global content
     unchanged
   - Visit About page — three-step renders with global content
     unchanged
   - The args path is purely additive

6. **Token + system compliance:**
   - All CSS uses existing tokens
   - No new tokens added
   - Section roots carry surface modifiers; section CSS doesn't
     redeclare padding-block/background/color on root

7. **Responsive:**
   - Mobile: confirmation icon + heading remain centered;
     three-step grid stacks 1-col; concierge image-text stacks
   - Booking details list (if present): legible at narrow widths

8. **Accessibility:**
   - Confirmation icon has `aria-hidden="true"` (purely
     decorative — the heading communicates the same meaning)
   - The page has a meaningful `<h1>` (the confirmation title)
   - Tab order is sensible

## Notes

- **Why drop "Similar villas" from the Figma.** David's call —
  upselling more villas immediately after a successful booking
  request feels off. Confirmation pages should reassure, not
  re-sell. If usage data later shows people want to keep
  browsing, easy to add as a future brief.
- **Why three-step gets variant-prep, not a new section.** The
  homepage and About already use three-step with global content.
  The booking confirmation page wants the same component with
  different content. Adding an args path keeps one section
  with two consumption modes; a parallel "three-step-confirmation"
  section would duplicate code for one different content set.
- **Why the confirmation panel is inline in the template.** It's
  small (icon + heading + optional subtitle), there's exactly
  one consumer, and it doesn't have the editorial reuse profile
  that justifies a component. If a second consumer ever appears
  (a "thank you" or "subscription confirmed" page), extracting
  a `confirmation-panel` component is cheap.
- **Why URL params over session storage.** Stateless, no PHP
  session machinery, refresh-friendly, debuggable, and Bob's form
  redirect just constructs a URL. The URL is technically
  shareable — an arguably mild privacy concern (someone shares
  their confirmation link, recipient sees the booking details).
  Acceptable: villa rentals aren't sensitive in the way medical
  or financial data would be.
- **What Bob needs to know.** He gets the URL contract:
  `?villa={id}&arrival=YYYY-MM-DD&departure=YYYY-MM-DD&guests={n}&offer={string}`.
  All params optional — page degrades gracefully. He doesn't
  need to coordinate with this page in any other way; we just
  need to brief him on the param names and confirm `villa` is
  ID (not slug).
- **The brief's `villa` post-type slug guess is "villa".** The
  villa-offers brief from earlier in the day used "villa" too;
  the agent should verify against the actual CPT registration
  and use the correct slug.
- **Step 12 reminder.** Small in-scope improvements (a missing
  escape in the helper, a stray hardcoded value in the
  confirmation panel CSS) — fix in the same commit and note.
  Larger or out-of-scope: surface, don't act.
