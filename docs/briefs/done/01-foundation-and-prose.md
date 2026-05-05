# Single article page foundation: template + ACF + body + pull quote

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield: new single-post template, new post-level ACF group,
new `pull-quote` component, plus an extension pass on the existing
`.ibv-prose` styles to cover all WordPress block types editors will
use. Body content stays in `the_content()` — no migration to ACF.
Related articles section is deferred to brief 02.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
"Ibiza Guide Article" page (frame `04b`). Working PDF reference is
provided alongside the brief.

## Scope

**In:**

1. Decide and implement the single-post template strategy: read
   `singular.php` first; create `single.php` taking over for the
   `post` post type, OR modify `singular.php` to branch by post
   type if cleaner.
2. New post-level ACF group bound to the `post` post type — pull
   quote sub-fields + related_articles picker (consumed by
   brief 02; field registered now).
3. New `components/pull-quote/` — small reusable component
   rendering centered display text + author name · title
   attribution.
4. Article header (inline in template): date · category meta,
   title, subtitle from raw excerpt.
5. Featured image: 6:4 CSS crop wrapper + rounded corners.
6. Body content: `the_content()` wrapped in `.ibv-prose`.
7. **Prose CSS extension pass** — fill in gaps so editors get
   correct styling for any WordPress block: h1, h4, h5, h6,
   inline images (rounded corners, alignment classes), figures,
   figcaptions, tables, horizontal rules.
8. Pull quote rendered via the component, sourced from ACF.
9. Related articles: **TODO placeholder** for brief 02.

**Out:**

- Related articles section build (brief 02)
- Any change to `single-villas.php` or page templates
- Comment system (no comments expected for blog posts; if WP
  defaults to showing the comment form, suppress it in the
  template)
- Any new top-level surface modifiers
- Migration of existing post content to ACF
- Subtitle as ACF field — using raw excerpt only

## Standards

- BEM naming, `ibv-` prefixes
- All rounded corners use existing radius tokens. Villa-card
  uses `--ibv-radius-lg` for its outer corners; verify the right
  token for image rounding by reading `villa-card.css` and
  checking design proportions
- Date format via WP's `get_the_date()` with the project's
  preferred format string (verify by reading any existing
  date-rendering code in `article-card` or homepage sections)
- Category from `get_the_category()` — first entry, name only
- Subtitle from `get_post_field( 'post_excerpt', $post_id )` —
  raw value, NOT `get_the_excerpt()` (which applies the auto-trim
  filter at 55 words)
- Pull quote ACF group: one ACF group field `pull_quote`
  containing sub-fields `quote_text` (textarea), `author_name`
  (text), `author_title` (text). Sub-fields keep editorial
  experience clean; group field keeps storage organised.
- The article body wraps in a single `<article>` element with the
  surface modifier — header, featured image, pull quote, and body
  prose are all children of this article. Related articles
  section sits outside the article in its own `<section>` (built
  in brief 02).

## Files

```
NEW    themes/ibv/single.php
                                  (or modify singular.php — agent decides)

NEW    mu-plugins/ibv-core/includes/acf/register-post-fields.php
                                  (post-level ACF: pull_quote group + related_articles)

NEW    mu-plugins/ibv-core/includes/components/pull-quote/pull-quote.php
NEW    mu-plugins/ibv-core/includes/components/pull-quote/pull-quote.css

EDIT   mu-plugins/ibv-core/assets/css/typography.css
                                  (extend .ibv-prose for full WP block coverage)

EDIT   mu-plugins/ibv-core/bootstrap.php       (require new ACF + component)
EDIT   mu-plugins/ibv-core/includes/shared-assets.php  (register pull-quote handle)
```

## Change 1 — Single-post template strategy

Read `themes/ibv/singular.php` first to understand what it
currently renders. Two viable paths:

- **A. Create `single.php`** that takes over for the `post` post
  type. WP template hierarchy will pick `single.php` over
  `singular.php` for posts; pages continue through whatever path
  they currently use. Cleanest separation.
- **B. Modify `singular.php`** to branch on post type — if it's
  a post, render the new article markup; otherwise fall through
  to the existing rendering. Acceptable if `singular.php` is
  already simple and a branch is trivial; avoid if it would mean
  introducing significant conditional complexity.

Recommendation: option A unless reading `singular.php` reveals
that option B is materially simpler. Explain the choice in the
commit message.

## Change 2 — Post ACF group

Create `register-post-fields.php`. Pattern after the existing
`register-page-*.php` files for naming conventions.

Field group: `group_ibv_post_fields` (or matching the project's
existing key convention).

Location rule: `post_type == post` (binds to all posts).

Fields:

```
pull_quote (group)
├── quote_text       (textarea)
├── author_name      (text)
└── author_title     (text)

related_articles    (post_object, multiple, max 3, post type filter: post,
                     return type: id, allow_null: true)
```

Field labels: "Pull Quote" / "Quote text" / "Author name" /
"Author title" / "Related Articles".

If the pull_quote group is left empty (all sub-fields blank), the
template skips rendering the pull quote entirely. No partial
rendering — empty fields produce no markup.

The `related_articles` field is registered now but not consumed
in this brief. Brief 02 reads it.

## Change 3 — Pull quote component

`components/pull-quote/pull-quote.php`:

```php
function ibv_core_pull_quote( $args = [] ) {
    $defaults = [
        'text'         => '',
        'author_name'  => '',
        'author_title' => '',
    ];
    $args = wp_parse_args( $args, $defaults );

    if ( ! $args['text'] ) {
        return;
    }

    wp_enqueue_style( 'ibv-component-pull-quote' );

    // ... markup
}
```

Markup: a `<figure>` (semantically a quote block) with:

- `<blockquote>` wrapping the quote text in display font
- `<figcaption>` containing author name and title with a
  separator dot between them

The separator dot in the design is a small teal-coloured dot.
Implement via a `::before` pseudo-element on the second span
(`__author-title`) styled as a small filled circle with the
project's teal accent token. Verify the exact token name by
reading existing components — sage, teal, or a specific accent
token. Use whatever the codebase already uses for similar accent
dots.

CSS: centered, narrow max-width (~48rem looks right against the
PDF — verify against Figma), large display font with italic
optional per design. Author line is small, regular weight,
muted-text colour with the accent dot.

Component takes explicit args (rather than reading ACF directly)
so it remains usable from other pages. Single.php reads ACF and
passes the args.

## Change 4 — Single template

Whether new `single.php` or modified `singular.php`, the post
rendering follows this shape:

```php
get_header();

while ( have_posts() ) :
    the_post();

    $post_id     = get_the_ID();
    $subtitle    = get_post_field( 'post_excerpt', $post_id );
    $cats        = get_the_category( $post_id );
    $cat_name    = ( $cats && isset( $cats[0] ) ) ? $cats[0]->name : '';
    $pull_quote  = get_field( 'pull_quote' );
    ?>

    <article class="ibv-article ibv-section ibv-section--surface-bg">
        <div class="ibv-container">

            <header class="ibv-article__header">
                <p class="ibv-article__meta">
                    <span class="ibv-article__date"><?php echo esc_html( get_the_date() ); ?></span>
                    <?php if ( $cat_name ) : ?>
                        <span class="ibv-article__cat"><?php echo esc_html( $cat_name ); ?></span>
                    <?php endif; ?>
                </p>
                <h1 class="ibv-article__title ibv-font-display"><?php the_title(); ?></h1>
                <?php if ( $subtitle ) : ?>
                    <p class="ibv-article__subtitle"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="ibv-article__featured">
                    <?php
                    ibv_core_image(
                        get_post_thumbnail_id(),
                        'full',
                        [ 'class' => 'ibv-article__featured-image', 'loading' => 'eager' ]
                    );
                    ?>
                </figure>
            <?php endif; ?>

            <?php
            if ( is_array( $pull_quote ) && ! empty( $pull_quote['quote_text'] ) ) {
                ibv_core_pull_quote( [
                    'text'         => $pull_quote['quote_text'],
                    'author_name'  => $pull_quote['author_name'] ?? '',
                    'author_title' => $pull_quote['author_title'] ?? '',
                ] );
            }
            ?>

            <div class="ibv-prose ibv-article__body">
                <?php the_content(); ?>
            </div>

        </div>
    </article>

    <?php
    /*
     * TODO brief 02: Related articles section here.
     * Reads ACF related_articles, falls back to category-related auto query,
     * renders 3-up grid via ibv_core_article_card.
     */

endwhile;

get_footer();
```

The article meta line shows `date · category` with a teal dot
separator — same pattern as the pull quote attribution. Implement
via `::before` pseudo-element on `__cat`, same colour token as
the pull quote separator.

The featured image figure uses the `--ibv-radius-lg` token (or
whichever radius the project uses for media corners) and a 6:4
aspect-ratio crop on the wrapper. The image gets `object-fit:
cover; width: 100%; height: 100%`. Article-scope CSS handles
this — there's no need for a new component.

## Change 5 — Prose CSS extension pass

Extend `.ibv-prose` in `assets/css/typography.css`. Currently it
covers `h2`, `h3`, `ul/ol`, `blockquote`, and `> * + *` spacing.
Fill in:

- **`.ibv-prose h1`** — discouraged in body content (the article
  title is already an h1 in the header), but style defensively
- **`.ibv-prose h4`, `h5`, `h6`** — match the type scale; smaller
  than h3 by sensible token steps
- **`.ibv-prose img`** — `max-width: 100%; height: auto;
  border-radius: var(--ibv-radius-lg);` (or whichever token).
  Inline images keep their natural aspect ratio (do NOT force
  6:4 here — that's featured-only).
- **`.ibv-prose figure`** — block-level, centered or left per WP
  alignment classes
- **`.ibv-prose figcaption`** — small, muted, centered (or
  inheriting figure alignment), italic if the design suggests,
  upright otherwise
- **WP alignment classes inside prose:** `.alignleft`,
  `.alignright`, `.aligncenter`, `.alignwide`, `.alignfull` —
  follow the WordPress block editor conventions. `alignwide` and
  `alignfull` need to break out of the container width if the
  container is narrower than the viewport
- **`.ibv-prose hr`** — light rule with sensible vertical spacing
- **`.ibv-prose table`** — basic table styling: borders, padding
  on cells, header row distinction
- **`.ibv-prose pre, code`** — light touch, monospace, subtle
  background. Unlikely to be used in travel-blog content but
  worth covering in case
- **`.ibv-prose a`** — link colour, underline, hover state.
  Verify the existing link styles cover this; if so, no extra
  work needed
- **`.ibv-prose strong, em`** — almost certainly inheriting from
  base; verify renders correctly

The extension pass is conservative: use existing tokens for
spacing, colour, and type sizes. Do not introduce new tokens.
If a needed value doesn't exist as a token, use the closest
existing one and surface it as an observation in the commit
message.

## Change 6 — Bootstrap + asset registration

In `bootstrap.php`, require:

- `includes/acf/register-post-fields.php`
- `includes/components/pull-quote/pull-quote.php`

In `shared-assets.php`, register the new CSS handle for the
pull-quote component, following the existing naming pattern.

## Verification

1. **Manual content setup — the prose smoke test post:**
   - Create a new post titled "Test post — all WP styling"
   - Set a featured image (any landscape photo)
   - Set the excerpt to a one-sentence subtitle
   - Set a category
   - Fill the pull_quote ACF: text + author name + author title
   - In the body content, include at minimum:
     - h2, h3, h4, h5, h6 headings
     - paragraph with **bold**, *italic*, and a [link]
     - unordered list (3 items)
     - ordered list (3 items)
     - blockquote
     - inline image with caption (centered alignment)
     - inline image left-aligned
     - inline image right-aligned
     - inline image full-width (alignfull)
     - horizontal rule
     - a small table (3 cols × 3 rows)
   - Save and view on frontend

2. **Frontend smoke test:**
   - Article header: date · category (teal dot between), title,
     subtitle render correctly. Subtitle shows the full excerpt
     with no auto-trim.
   - Featured image: 6:4 crop, rounded corners, image fills the
     frame via cover
   - Pull quote: centered narrow column, display text, "Author
     Name · Author Title" with teal dot separator
   - Body: `.ibv-prose` styles apply. All test elements above
     render with appropriate styling
   - Inline images: rounded corners, alignment classes work,
     full-width breaks out of container correctly
   - Captions render styled appropriately

3. **Empty-fields fallbacks:**
   - With excerpt empty: subtitle area doesn't render
   - With pull_quote empty: pull quote section doesn't render
   - With no featured image: article still renders without the
     image figure
   - With no category: meta line shows just the date

4. **Related articles placeholder:**
   - Below the article body, no related-articles markup yet —
     just the TODO comment for brief 02. Smoke test should
     confirm the article ends cleanly above the footer

5. **Token + system compliance:**
   - All new CSS uses existing tokens
   - No new tokens added
   - Article container uses surface system correctly
   - Prose extensions use existing typography tokens for sizes
     and spacing

6. **Regression — page rendering unchanged:**
   - Visit any standard page (About, Ibiza Guide, Special Offers)
   - Confirm those still render through their existing templates
     unchanged
   - This catches any accidental over-reach if option B
     (modifying `singular.php`) was chosen

## Notes

- **Why raw excerpt over `get_the_excerpt()`.** The default WP
  excerpt filter trims to 55 words and appends a hellip. For our
  use as a subtitle, we want exactly what the editor typed — no
  truncation, no auto-generation if empty. The raw post field
  gives us this.
- **Why pull-quote is a component.** Single.php is the only
  consumer right now, but the pattern (centered display quote +
  attribution) is the kind of thing that recurs. Concierge and
  About pages might want it later. The cost of building it as a
  component now (vs. inline) is minimal; the cost of extracting
  later is also small. Either is defensible. Going with component
  for consistency with the rest of the project's small-component
  habit (image, button, newsletter-form, article-card,
  villa-card, etc. all live as components).
- **Why prose extension is in scope.** David specifically wants
  editors to use any WP block type and have it render correctly
  via the existing `.ibv-prose` system. Currently prose is
  minimal (h2, h3, lists, blockquote). The smoke-test post
  forces the full coverage to be tested — anything broken in the
  smoke test is an in-scope fix per Step 12.
- **Featured image vs inline images.** Featured image is forced
  to 6:4 via a wrapper aspect-ratio because IV2000's photography
  quality varies and a consistent crop normalises the visual.
  Inline images come from editorial control inside the body and
  should render at their natural aspect — forcing 6:4 there would
  crop content unpredictably.
- **Step 12 reminder.** In-scope improvements (a missing escape,
  a stray hardcoded value in prose CSS, a broken link style)
  fix in the same commit and note in the message. Larger
  observations (e.g. "the prose system would benefit from a full
  redesign") surface in the summary, do not act.
- **No comments.** Blog posts don't expect a comment system.
  If `singular.php` currently renders comments, the new
  `single.php` simply omits the `comments_template()` call. If
  modifying `singular.php`, branch on post type so pages keep
  their existing behaviour.
