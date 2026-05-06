# Single article: related articles section

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield. New `related-articles` section that renders below
the article body on the single-post template. Reads the
`related_articles` ACF field (registered in the foundation+prose
brief) and falls back to a category-related auto query if the ACF
is empty. Reuses the existing `ibv_core_article_card()` component.

This brief replaces the `TODO brief 02` placeholder left in the
single-article template by the foundation+prose brief. After it
lands, the single-article page is feature-complete.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
"Ibiza Guide Article" page (frame `04b`). The Related Articles
section sits between the article body and the footer. Forest-
green-deep / surface-blue banded section, centered "Related
Articles" heading with a horizontal rule under it, three article
cards in a 3-up grid below.

## Scope

**In:**

1. New `sections/related-articles/` (PHP + CSS).
2. Reads ACF `related_articles` (post object, multiple, returns
   IDs) for the current post.
3. If empty or returns no valid posts, falls back to: posts in
   the same category as the current post, exclude the current
   post, ordered by date desc, limited to 3.
4. If the fallback also returns nothing (orphan post with no
   category or no other posts in the category), the section
   doesn't render.
5. Renders a banded section with heading + 3-up card grid, using
   the existing `ibv_core_article_card()` component.
6. Replaces the `TODO brief 02` placeholder in the single-article
   template with a call to the new section helper.

**Out:**

- Any change to `article-card` component (already shipped in
  Ibiza Guide brief 01)
- Any change to the foundation+prose brief's article markup
- Filtering or pagination — there are always exactly 3 cards;
  no UI to expand or filter
- Any change to the homepage `ibiza-guide-preview` section
  (similar pattern but separate concern)

## Standards

- BEM, `ibv-` prefixes
- Surface system: section root carries
  `ibv-section ibv-section--surface-blue` (the same surface used
  for the Ibiza Guide page newsletter band per memory — verify
  the exact token name in `tokens.css`)
- Section CSS does NOT redeclare `padding-block`, `background`,
  or `color` on the root — surface system handles these
- Existing tokens only — no new tokens
- Section heading uses the project's display font and matches the
  pattern used by other section headings (centered or left per
  design — verify against Figma; my read from the PDF is centered
  with a horizontal rule beneath, like the three-step section's
  heading)
- 3-up grid at desktop (≥56rem), 1-up at mobile, gap matching the
  article-grid section's gap (verify by reading
  `sections/article-grid/article-grid.css`)
- Query uses standard `WP_Query` — no FacetWP integration on this
  section

## Files

```
NEW    mu-plugins/ibv-core/includes/sections/related-articles/related-articles.php
NEW    mu-plugins/ibv-core/includes/sections/related-articles/related-articles.css

EDIT   themes/ibv/single.php   (or singular.php branch — whichever the
                                foundation brief used; replace TODO with
                                the new section call)
EDIT   mu-plugins/ibv-core/bootstrap.php           (require new file)
EDIT   mu-plugins/ibv-core/includes/shared-assets.php  (register CSS handle)
```

## Change 1 — Section helper

Create `sections/related-articles/related-articles.php` with:

```php
function ibv_core_section_related_articles( $args = [] ) {
    $defaults = [
        'post_id' => get_the_ID(),
        'limit'   => 3,
    ];
    $args = wp_parse_args( $args, $defaults );

    $post_id = (int) $args['post_id'];
    if ( ! $post_id ) {
        return;
    }

    $limit = (int) $args['limit'];

    // 1. Try ACF picker first.
    $ids = get_field( 'related_articles', $post_id );
    if ( ! is_array( $ids ) ) {
        $ids = $ids ? [ $ids ] : [];
    }
    $ids = array_slice(
        array_filter( array_map( 'intval', $ids ) ),
        0,
        $limit
    );

    // 2. Fallback: category-related auto query.
    if ( count( $ids ) < 1 ) {
        $cats = wp_get_post_categories( $post_id );
        if ( ! empty( $cats ) ) {
            $q = new WP_Query( [
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'category__in'   => array_map( 'intval', $cats ),
                'post__not_in'   => [ $post_id ],
                'fields'         => 'ids',
                'no_found_rows'  => true,
            ] );
            $ids = $q->posts;
            wp_reset_postdata();
        }
    }

    if ( count( $ids ) < 1 ) {
        return;
    }

    wp_enqueue_style( 'ibv-section-related-articles' );
    ?>
    <section class="ibv-section-related-articles ibv-section ibv-section--surface-blue">
        <div class="ibv-container">
            <header class="ibv-section-related-articles__header">
                <h2 class="ibv-section-related-articles__title ibv-font-display">
                    <?php esc_html_e( 'Related Articles', 'ibv' ); ?>
                </h2>
                <hr class="ibv-section-related-articles__rule" aria-hidden="true">
            </header>
            <div class="ibv-section-related-articles__grid">
                <?php foreach ( $ids as $pid ) : ?>
                    <?php ibv_core_article_card( [ 'post_id' => $pid ] ); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}
```

The function signature (`post_id`, `limit`) keeps it reusable for
future contexts where related-article logic might apply (e.g. a
landing page that shows related guides). The default `post_id` is
the current post in the loop, so calling without args from
`single.php` works straight away.

## Change 2 — Replace the TODO in the single template

In `themes/ibv/single.php` (or wherever the foundation brief
placed the article template), find the comment block that reads:

```php
/*
 * TODO brief 02: Related articles section here.
 * ...
 */
```

Replace with:

```php
ibv_core_section_related_articles();
```

The section call sits OUTSIDE the article container's closing
`</article>` tag — related articles is its own surface section,
not part of the article body.

## Change 3 — Bootstrap + asset registration

In `bootstrap.php`:

```php
require_once IBV_CORE_PATH . 'includes/sections/related-articles/related-articles.php';
```

In `shared-assets.php`, register the `ibv-section-related-articles`
style handle following the existing pattern.

## Change 4 — Section CSS

`related-articles.css` owns the layout and visual treatment:

- Header: centered heading + horizontal rule below, matching the
  pattern used by other sections with the gold-rule treatment
  (e.g. three-step, About story). Read those CSS files to verify
  the rule token, width, and colour
- Grid: `display: grid; grid-template-columns: 1fr;` at mobile,
  `repeat(3, 1fr)` at ≥56rem, gap matching `article-grid`'s gap
- No padding-block, background, or color declarations on the
  section root — that's the surface system

The display title may render in italic per design — verify
against Figma. The newsletter-band brief discovered the title was
upright, not italic, against the brief's initial assumption. Same
discipline here: read the design directly, don't guess.

## Verification

1. **ACF-driven path:**
   - Edit a post in WP admin, open the Related Articles ACF, pick
     three other posts
   - Save, view the post on frontend
   - Below the article body, the Related Articles section renders
     with the three picked posts, in pick order
   - Each card uses the same article-card styling as the homepage
     ibiza-guide-preview and the Ibiza Guide article-grid

2. **Category-fallback path:**
   - Pick a post that has at least one category
   - Clear (or leave empty) its Related Articles ACF
   - Ensure 3+ other posts share that category
   - View on frontend
   - Section renders with 3 most recent posts in the same
     category, current post excluded

3. **Empty path:**
   - Pick or create a post with no category and no Related
     Articles ACF set
   - View on frontend
   - The Related Articles section does NOT render — page ends
     cleanly at the article body / footer

4. **Surface + system compliance:**
   - Section renders on `--surface-blue` (matching the Ibiza Guide
     newsletter band visually)
   - Adjacent same-surface sections (if any) collapse padding
     correctly via the rhythm system
   - Mobile: 1-col grid, full-width cards, sensible vertical
     spacing
   - Desktop: 3-col grid, even gaps

5. **Token + system compliance:**
   - All CSS uses existing tokens
   - No new tokens added
   - Surface modifier name is correct (verify against
     `tokens.css`)

6. **Related-but-itself bug check:**
   - Edit the related_articles ACF and pick the current post
     itself (intentionally, just to verify the dedupe)
   - View the post — the current post should NOT appear in its
     own related list. The query / fallback should both exclude
     the current post ID

## Notes

- **Why ACF first, category fallback second.** Editorial control
  always takes precedence — if Tina (or whoever populates content)
  has explicitly picked related articles, we honour those. If they
  haven't, the auto-query gives a sensible default so every post
  has a related-articles section without manual work. This matches
  the homepage `ibiza-guide-preview` pattern.
- **Why no_found_rows on the fallback query.** Pagination is not
  needed here (always exactly 3 cards), so we skip the
  `SQL_CALC_FOUND_ROWS` cost.
- **Why `wp_get_post_categories` over `get_the_category`.** Both
  return categories for a post; `wp_get_post_categories` returns
  IDs directly, which is cleaner for `category__in`. If reading
  the existing codebase shows `get_the_category` is the preferred
  helper here, switch to it — consistency over micro-preference.
- **Why no separator between the article and this section.** The
  surface change (article on `bg`, related-articles on
  `surface-blue`) provides the visual break. No `<hr>` needed.
- **Step 12 reminder.** Small in-scope fixes (a missing escape, a
  stray hardcoded spacing in related CSS, an obviously-wrong token
  reference) — fix in the same commit and note. Larger or out-of-
  scope: surface, don't act.
