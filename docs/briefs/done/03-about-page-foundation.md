# About page foundation

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield. New page template, new ACF group, two new sections
(stats bar + Our Story), plus page composition. Two later sections
(FAQ accordion, Team grid) are out of scope here — they land in
briefs 04 and 05 — but the page composition includes commented
TODO placeholders so the structure is visible.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
frame `282-3846` ("05 | About"). Working PDF reference is provided
alongside the brief as the authoritative comp; the agent should
match the PDF for layout and copy intent.

## Scope

Build the About page foundation:

- Page template `themes/ibv/page-about.php`
- ACF group `register-page-about.php`, location-targeted to the
  About page template, providing hero image/title/subtitle, stats
  repeater (capped at 4), Our Story repeater (2+ entries, no cap)
- Section `sections/about-stats/` — row of stat tiles on a
  forest-green band beneath the hero
- Section `sections/about-story/` — eyebrow + title + label/copy
  pair repeater, on light surface

Page composes:

```
hero (compact)              ← brief 02 variant
about-stats                  ← this brief
about-story                  ← this brief
three-step                   ← already global (brief 01)
testimonials                 ← already global
[TODO: FAQ — brief 04]
[TODO: Team — brief 05]
newsletter-cta               ← existing
```

The page is end-to-end testable when this brief lands — every
section either renders or carries a clear TODO marker.

## Standards

- ACF in PHP under `mu-plugins/ibv-core/includes/acf/`
- Sections under `mu-plugins/ibv-core/includes/sections/{name}/`
- Section root: `ibv-section-{name} ibv-section ibv-section--surface-{X}`
  with optional `--rhythm-sm`. No `padding-block`, `background`, or
  `color` declared on the root.
- Image rendering via `ibv_core_image()` helper
- BEM naming, `ibv-` prefixes, no new dependencies
- Hero image is page-scoped — About has its own ACF field, not
  shared with homepage
- No `the_content()` — all editorial copy comes from explicit ACF
  fields

## Files

```
NEW    themes/ibv/page-about.php
NEW    mu-plugins/ibv-core/includes/acf/register-page-about.php
NEW    mu-plugins/ibv-core/includes/sections/about-stats/about-stats.php
NEW    mu-plugins/ibv-core/includes/sections/about-stats/about-stats.css
NEW    mu-plugins/ibv-core/includes/sections/about-story/about-story.php
NEW    mu-plugins/ibv-core/includes/sections/about-story/about-story.css

EDIT   mu-plugins/ibv-core/bootstrap.php (require new files)
```

## Change 1 — `register-page-about.php`

Pattern follows `register-page-special-offers.php`. Single field
group, location-targeted to `page_template == page-about.php`,
position `acf_after_title`.

Field group `group_ibv_page_about` with these fields, in this order:

**Hero**
- `hero_image` — image, return array, instructions: "Hero
  background image. Used at compact height (480px)."
- `hero_title` — text, default `About Ibiza Villas 2000`
- `hero_subtitle` — textarea, 2 rows, default `Warm, direct intro —
  who we are, how long we've been going, what makes us different.
  Not corporate.`

(These names mirror the homepage hero's ACF field names so
`ibv_core_section_hero()` reads them with no changes. The hero
section reads `get_field('hero_image')` etc. on the queried post,
so the About page provides its own hero data automatically.)

**Stats** — required exactly 4 rows (`min: 4, max: 4`), button label "Add stat":
- `about_stats` — repeater with subfields, all REQUIRED:
  - `eyebrow` — text, required, instructions: "Small label above the value. e.g. 'Established'"
  - `value` — text, required, instructions: "Headline number or short text. e.g. '2002', '15+', '20min', 'AVAT'"
  - `caption` — text, required, instructions: "Caption beneath the value. e.g. 'Established', 'Villas', 'Response time', 'members'"

The stats bar's design is a fixed 4-column grid. Anything other
than 4 fully-populated rows breaks the visual. ACF enforcement
(`min_rows: 4, max_rows: 4`, all subfields `required: 1`) does the
work of stopping editors from saving a broken state.

**Our Story** — no cap on entries:
- `about_story_eyebrow` — text, default `Our Story`
- `about_story_title` — text. Optional.
- `about_story_image` — image, return array, required, instructions: "Image displayed alongside the story entries on desktop."
- `about_story_entries` — repeater with subfields:
  - `label` — text, instructions: "Short label. e.g. 'Origin', 'Growth', 'Values'"
  - `body` — textarea, 4 rows. Plain text.

Field keys: prefix `field_ibv_page_about_*`. Standard ACF group
boilerplate (`menu_order: 0, position: acf_after_title, style: default,
label_placement: top, instruction_placement: label, active: true,
show_in_rest: false`).

## Change 2 — Section: `about-stats`

Forest-green band with a row of stat tiles. Surface modifier:
`forest-green`. Below the hero, before Our Story.

```php
<?php
/**
 * Section: About — stats bar.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ibv_core_section_about_stats() {
    $stats = get_field( 'about_stats' );
    if ( ! is_array( $stats ) || ! count( $stats ) ) {
        return;
    }

    wp_enqueue_style( 'ibv-section-about-stats' );
    ?>
    <section class="ibv-section-about-stats ibv-section ibv-section--surface-forest-green ibv-section--rhythm-sm">
        <div class="ibv-container">
            <ul class="ibv-section-about-stats__grid">
                <?php foreach ( $stats as $stat ) : ?>
                    <?php
                    $eyebrow = (string) ( $stat['eyebrow'] ?? '' );
                    $value   = (string) ( $stat['value'] ?? '' );
                    $caption = (string) ( $stat['caption'] ?? '' );
                    if ( ! $value ) {
                        continue; // Defensive: skip rows missing the headline value.
                    }
                    ?>
                    <li class="ibv-section-about-stats__tile">
                        <p class="ibv-section-about-stats__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                        <p class="ibv-section-about-stats__value ibv-font-display"><?php echo esc_html( $value ); ?></p>
                        <p class="ibv-section-about-stats__caption"><?php echo esc_html( $caption ); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php
}
```

CSS: 4-column grid at desktop, stack at mobile. Tile is
left-aligned. Value uses display font at large size. Eyebrow is
small uppercase muted (visually similar to the three-step eyebrow).
All colours via existing tokens — verify against `tokens.css`. Do
not invent tokens; if a value isn't tokenised, flag it.

## Change 3 — Section: `about-story`

Two-column section: copy block on the left (eyebrow, optional
title, label/body entries) and a single image on the right. Image
is required at the section level, not per-entry. Stack at mobile.

```php
<?php
/**
 * Section: About — Our Story.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ibv_core_section_about_story() {
    $entries = get_field( 'about_story_entries' );
    if ( ! is_array( $entries ) || ! count( $entries ) ) {
        return;
    }

    $eyebrow = (string) get_field( 'about_story_eyebrow' );
    $title   = (string) get_field( 'about_story_title' );
    $image   = get_field( 'about_story_image' );

    wp_enqueue_style( 'ibv-section-about-story' );
    ?>
    <section class="ibv-section-about-story ibv-section ibv-section--surface-bg">
        <div class="ibv-container ibv-section-about-story__inner">
            <div class="ibv-section-about-story__copy">
                <header class="ibv-section-about-story__header">
                    <?php if ( $eyebrow ) : ?>
                        <p class="ibv-section-about-story__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                    <?php endif; ?>
                    <?php if ( $title ) : ?>
                        <h2 class="ibv-section-about-story__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                </header>

                <dl class="ibv-section-about-story__entries">
                    <?php foreach ( $entries as $entry ) :
                        $label = (string) ( $entry['label'] ?? '' );
                        $body  = (string) ( $entry['body'] ?? '' );
                        if ( ! $body ) {
                            continue;
                        }
                        ?>
                        <div class="ibv-section-about-story__entry">
                            <?php if ( $label ) : ?>
                                <dt class="ibv-section-about-story__label"><?php echo esc_html( $label ); ?></dt>
                            <?php endif; ?>
                            <dd class="ibv-section-about-story__body"><?php echo esc_html( $body ); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>

            <?php if ( ! empty( $image['ID'] ) ) : ?>
                <div class="ibv-section-about-story__media">
                    <?php
                    ibv_core_image(
                        $image,
                        'ibv-card',
                        [ 'class' => 'ibv-section-about-story__image' ]
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}
```

CSS: at desktop, two-column grid with copy block left, image right.
Within the copy block: header on top, entries below as label/body
two-column grid (label narrow, body wide). Stack at mobile (image
below copy). Use `<dl>`/`<dt>`/`<dd>` for the entries — semantic
match to label-and-definition pattern. All tokens verified against
`tokens.css`. Image uses `ibv-card` registered size.

## Change 4 — Page template `themes/ibv/page-about.php`

```php
<?php
/**
 * Template Name: About
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    ibv_core_section_hero( [ 'compact' => true ] );
    ibv_core_section_about_stats();
    ibv_core_section_about_story();
    ibv_core_section_three_step();
    ibv_core_section_testimonials();

    // TODO: FAQ section — brief 04.
    // TODO: Team section — brief 05.

    ibv_core_section_newsletter_cta();

endwhile;

get_footer();
```

## Change 5 — Bootstrap inclusions

In `mu-plugins/ibv-core/bootstrap.php`, add the new section files
to the sections require block (alphabetical or by feature, match
existing convention):

```php
require_once IBV_CORE_PATH . 'includes/sections/about-stats/about-stats.php';
require_once IBV_CORE_PATH . 'includes/sections/about-story/about-story.php';
```

And add the new ACF file to the ACF require block:

```php
require_once IBV_CORE_PATH . 'includes/acf/register-page-about.php';
```

## Verification

1. **Admin smoke test:**
   - Create a new page in WP, set Template = "About"
   - On the edit screen, the About ACF group renders below title
   - Hero (image, title, subtitle), Stats repeater (cap 4), Story
     (eyebrow, title, entries repeater) all visible
   - Other pages don't show these fields

2. **Populate sample content** matching the PDF:
   - Hero: image, title `About Ibiza Villas 2000`, subtitle from design
   - Stats: 4 rows — `2002 / Established`, `15+ / Villas`,
     `20min / Response time`, `AVAT / members`. Eyebrows can be
     blank for sample data.
   - Story: 3 entries — Origin / Growth / Values with copy from PDF

3. **Frontend smoke test (the About page):**
   - Hero renders at compact 480px height
   - Stats bar on forest-green band, 4 tiles desktop, stacked mobile
   - Our Story renders eyebrow + entries
   - Three-step renders (uses globals from brief 01)
   - Testimonials render (already global)
   - Two TODO comments visible in template source
   - Newsletter CTA renders
   - No PHP errors, no 404 on enqueued CSS

4. **Section-system compliance check:**
   - All new section roots carry both base rhythm class and surface
     modifier
   - Section CSS files don't declare `padding-block`, `background`,
     or `color` on roots
   - All tokens used exist in `tokens.css` — grep to confirm

## Notes

- **Stats fields are required.** Eyebrow, value, and caption all
  carry `required: 1`, and the repeater enforces `min_rows: 4,
  max_rows: 4`. The design's stats bar is a fixed 4-column grid;
  partial population breaks the visual. ACF stops editors from
  saving a broken state.
- **Our Story image is page-scoped, section-level, single.** The
  image isn't tied to any individual entry — it's one image
  sitting alongside the whole copy block. ACF has it as a top-level
  field on the About page, not inside the entries repeater.
- **Hero image is page-scoped.** The About page's hero ACF reads
  `hero_image` / `hero_title` / `hero_subtitle` from the queried
  post — same field NAMES as homepage but different field group
  (different keys), targeted to a different template. Each page
  carries its own data. No cross-page sharing. (Note: same field
  names used here as a convenience because the hero section reads
  them by name; in general, sections can take args and field names
  don't need to match consumer expectations.)
- **Why FAQ and Team are placeholders, not omitted.** The page
  composition is the durable artefact; FAQ and Team will slot into
  the existing TODOs in briefs 04 and 05. Anyone reading the
  template now sees the full intended structure with two unimplemented
  sections clearly marked. Easier than re-touching the template
  twice.
- **Newsletter CTA at bottom.** Existing section, called
  unchanged. Confirms the page's bottom chrome matches site-wide
  pattern.
- **No image-text-section reuse.** Looking at the existing
  components folder, `image-text-section` exists. Considered using
  it for Our Story, but the design's structure (eyebrow + title +
  multi-entry label/body list + image alongside) is different
  enough that a dedicated section is cleaner. Don't refactor
  image-text-section to fit; build About-Story as its own thing.
