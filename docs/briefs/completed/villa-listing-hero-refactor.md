# Refactor villa listing page hero into a section component

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

## Standards

- ACF field groups registered in PHP under
  `mu-plugins/ibv-core/includes/acf/`
- Section files live under `mu-plugins/ibv-core/includes/sections/{name}/`
  with a `.php` and a `.css`
- Section root: `ibv-section-{name} ibv-section ibv-section--surface-{X}`,
  optional `--rhythm-sm`. Section CSS must NOT declare `padding-block`,
  `background`, or `color` on the root. Surface modifier is mandatory and
  must be declared explicitly.
- Image rendering via `ibv_core_image()` helper
- Buttons via `ibv_core_button()` helper
- BEM naming, `ibv-` prefixes, no new dependencies, no build step

## Scope

The villa listing page hero (the split image-right + copy-left header
above the grid) is currently rendered inline in
`themes/ibv/page-villa-listing.php`, with bespoke
`ibv-villa-listing__hero*` BEM classes and pre-system CSS in
`mu-plugins/ibv-core/includes/sections/villa-listing-page/villa-listing-page.css`
that declares `padding-block` and `background` directly on the root.

Refactor so the hero is a proper section component:

- New section `villa-listing-hero/` consuming the project's section
  conventions (rhythm + surface system, BEM, image helper, button
  helper).
- The "Looking for 12 or more guests? Contact us" line becomes a
  generic "supporting note" with three editable ACF fields (text,
  link URL, link label) — Tina can change the threshold copy or
  remove the note entirely without code changes.
- ACF field group moves out of `register-villa-fields.php` (where
  it doesn't structurally belong, same smell as the legacy SO group)
  into its own `register-page-villa-listing.php`.
- Old `villa-listing-page/` section folder is removed — its CSS
  migrates out, its PHP file is empty.
- Page template is reduced to composing sections.

This is listing-page-only — the hero pattern isn't reused elsewhere.

Out of scope:
- Hero image cropping / responsive image sizes (`ibv-card` size is fine)
- The villa grid, empty state, or alternative accommodation sections
  (already exist as their own section calls below the header)
- Hero search component (used as-is via `ibv_core_hero_search()`)
- Any new ACF fields beyond the three notice fields below

## Files

```
NEW    mu-plugins/ibv-core/includes/acf/register-page-villa-listing.php
NEW    mu-plugins/ibv-core/includes/sections/villa-listing-hero/villa-listing-hero.php
NEW    mu-plugins/ibv-core/includes/sections/villa-listing-hero/villa-listing-hero.css

EDIT   mu-plugins/ibv-core/includes/acf/register-villa-fields.php (strip listing group)
EDIT   themes/ibv/page-villa-listing.php (compose, don't render inline)
EDIT   wherever ACF files are required (load the new file)
EDIT   wherever section CSS is enqueued/registered (register new handle, drop old)

DELETE mu-plugins/ibv-core/includes/sections/villa-listing-page/villa-listing-page.css
DELETE mu-plugins/ibv-core/includes/sections/villa-listing-page/  (empty folder if section auto-discovery doesn't require it)
```

## Change 1 — Create `register-page-villa-listing.php`

New file. Pattern follows `register-page-special-offers.php` from the
recent SO work: a single field group, location-targeted to
`page_template == page-villa-listing.php`, position `acf_after_title`.

Field group `group_ibv_page_villa_listing` with these fields:

- `listing_hero_image` — image, return array
- `listing_contact_page` — page_link (existing field, kept)
- `listing_note_text` — text, label "Note text", instructions:
  "Optional supporting note shown below the description (e.g.
  'Looking for 12 or more guests?'). Leave blank to hide the note."
- `listing_note_link_url` — url, label "Note link URL", instructions:
  "URL the note's link points to. Required if note text is set."
- `listing_note_link_label` — text, label "Note link label",
  default "Contact us", instructions: "The clickable text. Required
  if note text is set."

Existing field keys preserved exactly:
- `field_ibv_listing_hero_image` for `listing_hero_image`
- `field_ibv_listing_contact_page` for `listing_contact_page`

Use fresh `field_ibv_listing_note_*` keys for the three new note
fields.

The `listing_contact_page` field can be kept for backwards
compatibility but is no longer read by the section — the new
`listing_note_link_url` replaces its role. Recommend marking it
`'instructions' => 'Deprecated — use the Note link URL field instead.'`
rather than removing it, in case any other code path references it.
Verify with a grep before deciding; if nothing references it, remove
it cleanly.

## Change 2 — Strip the listing group from `register-villa-fields.php`

Lines 968–1004 (the `acf_add_local_field_group` call for
`group_ibv_villa_listing_page`) cut entirely. After cutting, the
file should contain only fields that are genuinely villa-scoped.

## Change 3 — Hook the new ACF file into the bootstrap

Locate where the existing ACF files are required (likely a bootstrap
or `acf/index.php`). Add the new `register-page-villa-listing.php`
alongside `register-page-special-offers.php`.

## Change 4 — Create the section: `villa-listing-hero.php`

```php
<?php
/**
 * Section: Villa Listing Hero.
 *
 * Split layout: copy left (title, intro, optional note, search),
 * image right. Used only on the villa listing page template.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ibv_core_section_villa_listing_hero() {
    wp_enqueue_style( 'ibv-section-villa-listing-hero' );

    $page_id = get_the_ID();
    if ( ! $page_id ) {
        return;
    }

    $title           = get_the_title( $page_id );
    $hero_image      = get_field( 'listing_hero_image', $page_id );
    $note_text       = (string) get_field( 'listing_note_text', $page_id );
    $note_link_url   = (string) get_field( 'listing_note_link_url', $page_id );
    $note_link_label = (string) get_field( 'listing_note_link_label', $page_id );

    // Note renders only when text + URL + label are all set. Partial
    // configurations are silently ignored — no half-rendered notice.
    $show_note = $note_text && $note_link_url && $note_link_label;
    ?>
    <section class="ibv-section-villa-listing-hero ibv-section ibv-section--surface-bg">
        <div class="ibv-container ibv-section-villa-listing-hero__inner">
            <div class="ibv-section-villa-listing-hero__copy">
                <?php if ( $title ) : ?>
                    <h1 class="ibv-section-villa-listing-hero__title ibv-font-display">
                        <?php echo esc_html( $title ); ?>
                    </h1>
                <?php endif; ?>

                <span class="ibv-section-villa-listing-hero__rule" aria-hidden="true"></span>

                <div class="ibv-section-villa-listing-hero__intro ibv-prose">
                    <?php the_content(); ?>
                </div>

                <?php if ( $show_note ) : ?>
                    <p class="ibv-section-villa-listing-hero__note">
                        <?php echo esc_html( $note_text ); ?>
                        <a class="ibv-section-villa-listing-hero__note-link" href="<?php echo esc_url( $note_link_url ); ?>">
                            <?php echo esc_html( $note_link_label ); ?>
                        </a>
                    </p>
                <?php endif; ?>

                <div class="ibv-section-villa-listing-hero__search">
                    <?php ibv_core_hero_search(); ?>
                </div>
            </div>

            <?php if ( ! empty( $hero_image['ID'] ) ) : ?>
                <div class="ibv-section-villa-listing-hero__media">
                    <?php
                    ibv_core_image(
                        $hero_image,
                        'ibv-card',
                        [ 'class' => 'ibv-section-villa-listing-hero__image' ]
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}
```

## Change 5 — Create `villa-listing-hero.css`

Two-column grid at desktop, stack at mobile. Uses surface system —
NO `padding-block`, `background`, or `color` on the root.

Match the existing visual treatment from
`villa-listing-page.css` (the migration target), but updated:
- Replace `--ibv-color-bg-alt` references — surface modifier
  `ibv-section--surface-bg` provides `#f9f8f4`. Don't re-declare.
- Remove `padding-block: var(--ibv-section-pad-y)` — system
  provides it.
- Remove the `.ibv-villa-listing` ancestor selector for hero-search
  spacing; that selector won't exist anymore. Move the search top
  margin into the new section's own search wrapper.

```css
.ibv-section-villa-listing-hero__inner {
    display: grid;
    gap: var(--ibv-space-xl);
    align-items: center;
}

@media (min-width: 64rem) {
    .ibv-section-villa-listing-hero__inner {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    }
}

.ibv-section-villa-listing-hero__copy {
    display: flex;
    flex-direction: column;
    gap: var(--ibv-space-md);
}

.ibv-section-villa-listing-hero__title {
    font-family: var(--ibv-font-display);
    font-size: var(--ibv-fs-display);
    font-weight: var(--ibv-font-weight-regular);
    line-height: var(--ibv-lh-tight);
    color: var(--ibv-color-brand-teal);
    margin: 0;
}

.ibv-section-villa-listing-hero__rule {
    display: block;
    width: 6.25rem;
    height: 1px;
    background: var(--ibv-color-gold-500);
}

.ibv-section-villa-listing-hero__intro {
    color: var(--ibv-color-text);
}

.ibv-section-villa-listing-hero__note {
    margin: 0;
    font-size: var(--ibv-fs-small);
    color: var(--ibv-color-text-muted);
}

.ibv-section-villa-listing-hero__note-link {
    color: inherit;
    text-decoration: underline;
}

.ibv-section-villa-listing-hero__search {
    margin-top: var(--ibv-space-md);
}

.ibv-section-villa-listing-hero__media {
    border-radius: var(--ibv-radius-lg);
    overflow: hidden;
    border: 1px solid var(--ibv-color-border);
}

.ibv-section-villa-listing-hero__image {
    width: 100%;
    height: auto;
    display: block;
}
```

Verify token names against the project's actual `tokens.css` —
substitute any that don't exist (e.g. if `--ibv-color-brand-teal`
is named differently, use the project's actual name). Do NOT
introduce new tokens.

## Change 6 — Rewrite `themes/ibv/page-villa-listing.php`

Replace wholesale:

```php
<?php
/**
 * Template Name: Villa Listing
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    ibv_core_section_villa_listing_hero();
    ibv_core_section_villa_listing_grid();
    ibv_core_section_listing_empty_state();
    ibv_core_alternative_accommodation();

endwhile;

get_footer();
```

The `<article class="ibv-villa-listing">` wrapper goes away —
sections handle their own root markup. If any global CSS targeted
`.ibv-villa-listing` (search the codebase to confirm), port those
rules to whichever section needs them. From the audit so far the
only reference outside the template was the
`.ibv-villa-listing .ibv-hero-search` selector that we already
moved to `__search` inside the new section.

## Change 7 — Delete the old section folder

`mu-plugins/ibv-core/includes/sections/villa-listing-page/villa-listing-page.css` 
contained the bespoke CSS — now redundant. Delete the file. If the
folder is empty after that, delete the folder too. Drop the
`wp_enqueue_style( 'ibv-section-villa-listing-page' )` registration
from wherever section CSS is registered.

## Verification

1. **Admin smoke test:**
   - Villa listing page edit screen: ACF field group "Villa listing
     page" still visible (now sourced from
     `register-page-villa-listing.php`). Hero image + Contact page
     fields present. Three new note fields present.
   - Other pages: no spurious appearance of the listing fields.
   - Villa post edit screen: legacy listing fields are gone.

2. **Re-populate content** if needed:
   - Listing page hero image (existing data should survive — keys
     preserved).
   - Note: text "Looking for 12 or more guests?", link URL set to
     the contact page, link label "Contact us".

3. **Frontend smoke test:**
   - Listing page renders: hero with image right, copy left, title,
     gold rule, intro paragraph from `the_content()`, optional note,
     search bar.
   - Section root class reads
     `ibv-section-villa-listing-hero ibv-section ibv-section--surface-bg`.
   - Surface is `#f9f8f4` (from `surface-bg` modifier) — not declared
     on the root.
   - Padding is the standard 72px from the system, not the legacy
     `--ibv-section-pad-y` declared inline.
   - Below the hero: villa grid (or empty state), then alternative
     accommodation section, all rendering as before.

4. **Note edge cases:**
   - Clear the `listing_note_text` field → note doesn't render.
   - Set text but clear URL → note doesn't render (defensive
     all-or-nothing).
   - Set all three → renders with proper link.

5. **Mobile smoke test:**
   - Below 64rem: copy and image stack vertically. Image below copy.
   - Note and search bar fit comfortably in the stacked layout.

## Notes

- **Why a new section, not a hero variant.** The split image-right
  pattern is fundamentally different from the homepage's full-bleed
  overlay hero (different layout, different surface, different text
  colour, different min-height, plus a notice slot). A `layout` arg
  on the existing hero would force six conditional branches in PHP
  and CSS; cleaner to ship a separate component. If a similar
  pattern shows up on Concierge / About / Contact later, the
  decision flips — at that point extract the shared bits to a
  generic `page-hero-split` component.
- **Why the note is three fields, not one rich text.** Three rigid
  fields stop Tina from accidentally pasting heavy formatting into
  a small caption. Defensive against editorial drift.
- **Note's all-or-nothing render rule.** Partial configurations
  (text without URL, etc.) silently hide the whole note rather than
  rendering broken markup. The ACF instructions tell editors all
  three are needed; the code enforces it defensively.
- **`listing_contact_page` field deprecation.** The new
  `listing_note_link_url` replaces it functionally. Confirm by grep
  whether the field is referenced anywhere else (a global helper, a
  shared CTA, etc.). If only the listing template referenced it,
  remove it cleanly. If anything else does, keep it with a
  deprecation note in instructions.
- **The `<article class="ibv-villa-listing">` wrapper is dropped.**
  Section components own their own root elements. If a global rule
  was hanging off `.ibv-villa-listing` ancestor, port it. Audit
  before deleting.
- **No new tokens.** All styling uses existing tokens. If a token
  referenced here doesn't exist (verify against `tokens.css`),
  substitute with the project's actual name. Never invent.
- **Image cropping.** Hero image uses `ibv-card` size (1200×800).
  At desktop the column is roughly 600px wide so this is 2x for
  retina. If the design wants a different aspect, that's a separate
  decision (probably introduces a new registered size).
