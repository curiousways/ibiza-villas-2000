# Article-card extraction + featured-article section

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Mixed:

- **Refactor (visual no-op):** the existing inline article-card
  markup inside `sections/ibiza-guide-preview/ibiza-guide-preview.php`
  is extracted into a reusable component. The homepage section
  becomes a thin caller. Visually identical to today.
- **Greenfield:** new featured-article presentation — the large
  two-column block that renders the page-picked featured post on
  the Ibiza Guide page (and potentially elsewhere).

Both pieces are prerequisites for the Ibiza Guide page foundation
(brief 02). They land together because the page consumes both.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
"Ibiza Guide" page (frame `04a`). Working PDF reference is
provided alongside the brief.

## Scope

**In:**

1. Extract the inline `<article class="ibv-article-card">` markup
   from `ibiza-guide-preview.php` into
   `components/article-card/article-card.php` as
   `ibv_core_article_card( $args )`.
2. Refactor `ibiza-guide-preview.php` to use the new component.
   Visual no-op on the homepage.
3. Build a featured-article presentation that renders an ACF-
   picked post in the two-column image-left + content-right layout
   shown in the design.

**Out:**

- Ibiza Guide page template (brief 02)
- Article grid section (brief 02)
- Filtering / pagination (brief 03 — FacetWP)
- Any post type or taxonomy work — standard `post` + WP categories,
  no changes

## Decisions deferred to the agent

For the **featured-article presentation**, two viable approaches —
read both files before choosing:

- **A. Extend `components/image-text-section/`** with optional
  `eyebrow` (category pill) and `meta` (read-time) args. Then on
  the Ibiza Guide page (in brief 02) the featured-article
  rendering is a `ibv_core_image_text_section()` call with the
  new args populated from the picked post.
- **B. Build a new `sections/featured-article/`** that internally
  composes `ibv_core_image_text_section()` (or pattern-matches its
  layout). Encapsulates the post-querying + meta-line logic.

Pick the cleaner option. In the commit message, briefly justify
the choice. The bias should be towards minimal new code — A is
preferred IF the variant doesn't pollute `image-text-section`'s
contract for its existing uses (Short Breaks, IPS, Meet the Team).
If extending `image-text-section` would require conditionals that
clutter its existing rendering, B is correct.

If a third option presents itself during execution that's clearly
better, take it — explain in the commit message.

## Standards

- Components under `mu-plugins/ibv-core/includes/components/{name}/`
  with a `.php` and a `.css`
- Sections under `mu-plugins/ibv-core/includes/sections/{name}/` if
  approach B
- BEM naming, `ibv-` prefixes, no new dependencies
- Image rendering via `ibv_core_image()` helper
- Buttons via `ibv_core_button()` helper
- Read-time via existing `ibv_estimate_reading_minutes( $post_id )`
  helper in `helpers.php`
- Category from standard WP `get_the_category()` — first category
  is the displayed pill
- All editorial copy from `get_the_title()`, `get_the_excerpt()`,
  etc. — no `the_content()`

## Files

```
NEW    mu-plugins/ibv-core/includes/components/article-card/article-card.php
NEW    mu-plugins/ibv-core/includes/components/article-card/article-card.css
EDIT   mu-plugins/ibv-core/includes/sections/ibiza-guide-preview/ibiza-guide-preview.php
                                                                    (refactor to use component)

EDIT   mu-plugins/ibv-core/includes/components/image-text-section/image-text-section.php  (if approach A)
EDIT   mu-plugins/ibv-core/includes/components/image-text-section/image-text-section.css  (if approach A)

NEW    mu-plugins/ibv-core/includes/sections/featured-article/featured-article.php   (if approach B)
NEW    mu-plugins/ibv-core/includes/sections/featured-article/featured-article.css   (if approach B)

EDIT   mu-plugins/ibv-core/bootstrap.php                          (require new file(s))
EDIT   mu-plugins/ibv-core/includes/shared-assets.php             (register new CSS handle(s))
```

## Change 1 — Extract article-card component

Create `components/article-card/article-card.php`. Move the
existing inline markup (everything from `<article
class="ibv-article-card">` to its closing `</article>`) into a
helper:

```php
function ibv_core_article_card( $args = [] ) {
    $defaults = [ 'post_id' => 0 ];
    $args = wp_parse_args( $args, $defaults );

    $post_id = (int) $args['post_id'];
    if ( ! $post_id ) {
        return;
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        return;
    }

    wp_enqueue_style( 'ibv-component-article-card' );

    setup_postdata( $post );
    $cats    = get_the_category( $post_id );
    $catname = ( $cats && isset( $cats[0] ) ) ? $cats[0]->name : '';
    $mins    = ibv_estimate_reading_minutes( $post_id );
    $excerpt = get_the_excerpt( $post );
    if ( ! $excerpt ) {
        $excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 24, '…' );
    }

    // ... existing markup, unchanged from ibiza-guide-preview.php ...

    wp_reset_postdata();
}
```

**The markup itself does not change.** This is a pure
refactor — same classes, same structure, same image size, same
button variant. If the agent spots an obvious quality-improvement
opportunity in the existing markup (semantics, accessibility, etc.)
note it in the commit message but do not act on it in this brief.

## Change 2 — Refactor `ibiza-guide-preview.php`

Replace the inline `<article>` markup inside the `foreach` loop
with a call to the new component:

```php
foreach ( $ids as $pid ) {
    ibv_core_article_card( [ 'post_id' => $pid ] );
}
```

Drop the `setup_postdata` / `wp_reset_postdata` / `$post = get_post()`
preamble inside the loop — those are now the component's
responsibility.

The CSS file `ibiza-guide-preview.css` still owns the **section-
level** layout (header, divider, grid container). The card-level
CSS (everything beginning `.ibv-article-card`) moves to the new
`article-card.css`.

If `ibiza-guide-preview.css` had any `.ibv-article-card` selectors
nested inside section-scoped selectors, port them carefully. Same
visual output afterwards.

## Change 3 — Featured article presentation

Per the design:

- Two-column block: image left, content right (or right + left at
  agent's discretion if design renders differently in mobile-first
  context — verify against PDF)
- Content column contains, in order:
  - Category pill (small rounded label, e.g. tinted background,
    short text — "Best Beaches" in the design)
  - Title (large display font)
  - Read-time meta with clock icon — "8 min read"
  - Description (excerpt)
  - Primary "Read More" button (filled, primary variant — the
    `ibv_core_button` helper with `variant => 'primary'`)
- Surface: `bg` (off-white) — verify against design before
  finalising; if it reads differently, swap to existing surface
  modifier, do not invent new ones

Implementation per agent's choice (A or B above). The function the
brief 02 page template will call is one of:

```php
// Approach A:
ibv_core_image_text_section( [
    'title'       => get_the_title( $featured_id ),
    'description' => get_the_excerpt( $featured_id ),
    'cta_url'     => get_permalink( $featured_id ),
    'cta_label'   => __( 'Read More', 'ibv' ),
    'image'       => get_post_thumbnail_id( $featured_id ),
    'image_side'  => 'left',
    'surface'     => 'bg',
    'eyebrow'     => $category_name,   // ← new arg
    'meta'        => sprintf( '%d min read', $minutes ),  // ← new arg
] );

// Approach B:
ibv_core_section_featured_article( [ 'post_id' => $featured_id ] );
```

Pick the cleaner of the two. If approach B, the section
internally handles the post lookup, category, read-time, and
delegates layout to `image_text_section` (or duplicates layout if
that's cleaner — agent's call).

The category pill is a new visual element. Style it as a small
tag with rounded corners, body-small font size, tinted background.
Reuse existing tokens for colour, padding, border-radius — do not
introduce new tokens.

## Change 4 — Bootstrap + asset registration

In `bootstrap.php`, require the new component file(s):

```php
require_once IBV_CORE_PATH . 'includes/components/article-card/article-card.php';
// + featured-article section if approach B
```

In `shared-assets.php`, register the new CSS handle(s) following
the pattern used by other components/sections.

## Verification

1. **Homepage smoke test (visual no-op):**
   - Three article cards render identically to before
   - Same image, title, category, read-time, excerpt, "Read more"
     ghost button
   - DevTools: same class names, same DOM structure inside each
     card. Outer section markup unchanged.

2. **Featured-article test (manual):**
   - Temporarily edit `front-page.php` (or any test template) to
     call the new featured-article rendering with a real post ID
   - Renders a two-column block: image one side, content the
     other — with category pill, title, read-time, excerpt,
     primary Read More button
   - Mobile: stacks vertically
   - Revert temporary edit before committing

3. **Token compliance:**
   - All new CSS uses existing tokens
   - Grep `tokens.css` to confirm any token referenced exists
   - No hardcoded colours, font sizes, spacing values
   - No new tokens added

4. **Section system compliance** (if approach B):
   - Section root carries surface modifier
   - Section CSS doesn't redeclare `padding-block`, `background`,
     `color` on root

## Notes

- **Why extract article-card now.** Brief 02 builds the Ibiza
  Guide page's article grid, which queries posts and loops them
  via the same card. Extracting to a component now means brief 02
  is a thin call rather than a copy-paste. Same component used in
  both consumers.
- **Why featured-article is a different component from
  article-card.** Card and featured share data shape (post → image
  + title + meta + excerpt + link) but their layouts are
  fundamentally different — card is a vertical stack, featured is
  a horizontal two-column with a primary button. Trying to make
  one component handle both would over-couple them.
- **No `the_content()` in the new component.** Excerpt is sourced
  from `get_the_excerpt()` with `wp_trim_words` fallback — same
  as the existing inline markup. No editorial copy from
  `the_content()` for the same reason captured in the
  agent-brief skill.
- **Read-time is computed.** Existing
  `ibv_estimate_reading_minutes( $post_id )` in `helpers.php`
  takes a post ID and returns minutes. No new ACF field, no new
  function — the helper is already in production.
- **Approach decision in commit message.** Whichever path is
  chosen (A, B, or a third option), the commit message briefly
  explains why. That's the durable artefact — future me reading
  the codebase should be able to understand the choice without
  digging into the agent-brief.
