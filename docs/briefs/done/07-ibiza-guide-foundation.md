# Ibiza Guide page foundation: template + ACF + article grid

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield page foundation. Composes existing components (hero,
featured-article, article-card, newsletter-cta) into a new page.
One new section — `article-grid` — handles the post query and
loop. No refactoring of existing components.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
"Ibiza Guide" page (frame `04a`). Working PDF reference is
provided alongside the brief.

This brief follows the proven five-brief greenfield page pattern:
brief 01 covered shared-component prep (article-card extraction,
featured-article section); this brief is the page foundation;
brief 03 will fill the filter + pagination placeholder via
FacetWP. No filter/pagination logic in this brief.

## Scope

**In:**

1. New ACF field group for the Ibiza Guide page — hero fields and
   featured-article picker
2. New `sections/article-grid/` — queries published posts, optionally
   excludes specified IDs, loops via `ibv_core_article_card()`
3. New `page-ibiza-guide.php` template composing hero (compact),
   featured-article, article-grid, newsletter-cta
4. Bootstrap + asset registration for the new section

**Out:**

- Filtering / pagination — brief 03 (FacetWP)
- Any change to article-card or featured-article components
  (already done in brief 01)
- Any change to hero, newsletter-cta, or other reused sections

## Standards

- Page template lives at theme root: `themes/ibv/page-ibiza-guide.php`
- ACF registration at `mu-plugins/ibv-core/includes/acf/register-page-ibiza-guide.php`
- Section under `mu-plugins/ibv-core/includes/sections/article-grid/`
  with `.php` and `.css`
- BEM naming, `ibv-` prefixes
- Hero ACF field names **must match** the names the hero section
  reads (the same convention used on the About and Special Offers
  pages). Read `sections/hero/hero.php` and the existing
  `register-page-about.php` to confirm names before registering
  the new fields.
- Post query via standard `WP_Query` — no third-party libraries
  in this brief
- Section system: root `<section>` carries
  `ibv-section ibv-section--surface-{X}` modifiers; section CSS
  does not redeclare `padding-block`, `background`, or `color` on
  the root

## Files

```
NEW    themes/ibv/page-ibiza-guide.php
NEW    mu-plugins/ibv-core/includes/acf/register-page-ibiza-guide.php
NEW    mu-plugins/ibv-core/includes/sections/article-grid/article-grid.php
NEW    mu-plugins/ibv-core/includes/sections/article-grid/article-grid.css

EDIT   mu-plugins/ibv-core/bootstrap.php           (require new files)
EDIT   mu-plugins/ibv-core/includes/shared-assets.php  (register new CSS handle)
```

## Change 1 — ACF registration

Create `register-page-ibiza-guide.php` following the pattern of
`register-page-about.php`. The location rule binds the group to
the page using the `page-ibiza-guide.php` template.

Fields:

- **Hero Image** — image field; field name matches hero section's
  expected key (verify against `sections/hero/hero.php`)
- **Hero Title** — text field; same name convention
- **Hero Subtitle** — textarea or text field; same name convention
- **Featured Article** — post object field, name `ig_featured_article`,
  return type `id`, post type filter set to `post`, `allow_null`
  true (page should still render if no featured article picked,
  though the design assumes one is)

Field labels visible in admin should be human-readable: "Hero
Image", "Hero Title", "Hero Subtitle", "Featured Article".

The ACF group prefix and naming convention should mirror the
existing `register-page-about.php` exactly. If the About group
uses `key=group_about_page` and field keys like `field_about_hero_image`,
this group uses `key=group_ibiza_guide_page` and field keys like
`field_ibiza_guide_hero_image`.

## Change 2 — Article-grid section

Create `sections/article-grid/article-grid.php` with helper
`ibv_core_section_article_grid( $args )`.

Args:

```php
$defaults = [
    'exclude'        => [],     // array of post IDs to exclude
    'posts_per_page' => 9,      // 3 columns × 3 rows
    'surface'        => 'bg',   // surface modifier
];
```

Query:

```php
$q = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => (int) $args['posts_per_page'],
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post__not_in'   => array_map( 'intval', (array) $args['exclude'] ),
    'no_found_rows'  => true,
] );
```

If the query has no posts, the section returns nothing (no empty
state in this brief — content is being populated by Tina; an empty
state is a brief 03 concern at the earliest).

Loop body — minimal, the article-card component handles
presentation:

```php
foreach ( $q->posts as $post ) {
    ibv_core_article_card( [ 'post_id' => $post->ID ] );
}
wp_reset_postdata();
```

Section CSS owns the **grid layout only** — three columns at
desktop, single column at mobile, gap between cards. Match the
grid spacing visible in the Figma design. Use existing spacing
tokens.

The section root carries `ibv-section ibv-section--surface-{surface}`.
No layout-shift fixes, no card-internal styling — that's all in
`article-card.css` from brief 01.

## Change 3 — Page template

Create `themes/ibv/page-ibiza-guide.php`:

```php
<?php
/**
 * Template Name: Ibiza Guide
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $featured_id = (int) get_field( 'ig_featured_article' );

    ibv_core_section_hero( [ 'compact' => true ] );

    if ( $featured_id ) {
        ibv_core_section_featured_article( [ 'post_id' => $featured_id ] );
    }

    /*
     * TODO brief 03 (FacetWP): replace direct article-grid call
     * with FacetWP-wrapped listing for filter + pagination.
     */
    ibv_core_section_article_grid( [
        'exclude' => $featured_id ? [ $featured_id ] : [],
    ] );

    ibv_core_section_newsletter_cta();

endwhile;

get_footer();
```

The dedupe is automatic: if a featured article is picked, its ID
is passed to the grid's `exclude` list, so the grid never shows it
twice. If no featured article is picked, the grid shows all posts
including what would have been featured.

## Change 4 — Bootstrap + asset registration

In `bootstrap.php`, require:

- `includes/acf/register-page-ibiza-guide.php`
- `includes/sections/article-grid/article-grid.php`

In `shared-assets.php`, register the new CSS handle for the
article-grid section, following the existing pattern (use the same
handle prefix `ibv-section-` etc. that other sections use).

## Verification

1. **Page admin setup (manual):**
   - In WP admin, create a new page titled "Ibiza Guide"
   - Set page template to "Ibiza Guide" from the dropdown
   - Populate hero image, hero title ("Ibiza Guide"), hero
     subtitle ("Local knowledge, honest recommendations from a
     team that lives here.")
   - Create 5–10 sample posts across a few categories with
     thumbnails so the grid has content
   - Pick one as the Featured Article on the page
   - Save

2. **Frontend smoke test:**
   - Navigate to the new page
   - Hero renders compact with the picked image, title, subtitle
   - Featured article renders the picked post in two-column block
     (image + pill + title + read-time + excerpt + Read More button)
   - Article grid renders the remaining posts in 3-column layout
     at desktop
   - Featured post does **not** appear in the grid (dedupe works)
   - Newsletter CTA renders with the existing styling
   - Mobile: stacks correctly, single-column grid

3. **Token + system compliance:**
   - Article-grid CSS uses existing spacing tokens, no hardcoded
     values
   - Section root carries surface modifier
   - Grid CSS doesn't redeclare `padding-block`, `background`, or
     `color` on the section root

4. **Edge cases:**
   - With no featured article picked: page still renders, grid
     shows all posts, no PHP errors
   - With a featured article whose ID has been deleted: the page
     does not break (the helper handles missing posts gracefully —
     this was specced in brief 01 already)

## Notes

- **Why article-grid is a new generic section, not a "Ibiza
  Guide grid".** The grid is generic enough to be reusable
  (related-posts, category archive, etc.). Building it as
  `sections/article-grid/` rather than embedding the loop in the
  page template means brief 03 (FacetWP) wraps a single section
  rather than rewriting page-template logic.
- **Why `posts_per_page` defaults to 9.** Three rows of three
  columns is a balanced first-page view. Brief 03 (FacetWP) will
  control the actual paginated behaviour. The default here is the
  no-FacetWP fallback.
- **Why `no_found_rows => true` in this brief but not in
  brief 03.** This brief doesn't paginate, so we skip the
  `SQL_CALC_FOUND_ROWS` cost. Brief 03's FacetWP integration will
  need pagination metadata, so that flag will come off there.
- **Hero field names.** The hero section reads ACF directly using
  established field names. The agent must verify those names by
  reading `sections/hero/hero.php` before registering the new
  fields. If About and Special Offers pages use the same names,
  this page does too.
- **No FacetWP install in this brief.** That's brief 03 in full.
  This page renders as a non-paginated, unfiltered listing of
  recent posts. That's not the final UX — it's the foundation
  brief 03 builds on.
