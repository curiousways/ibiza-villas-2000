# Hero compact variant

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Refactor (visual no-op for the homepage). Adds a `compact` arg to
`ibv_core_section_hero()` that swaps the section's min-height from
720px to 480px when set. Homepage usage is unchanged — no `compact`
arg passed, default behaviour preserved. About page (later brief)
will pass `compact: true`.

## Standards

- Sections under `mu-plugins/ibv-core/includes/sections/{name}/`
- BEM naming, modifier on the section root: `ibv-section-hero--compact`
- No new tokens; reuse the existing rem/space scale
- Hero remains chrome-exempt from the surface system (full-bleed
  background, owns its own background and color)
- The `compact` arg changes ONLY min-height. Title, rule, subtitle,
  after_copy slot, internal padding, and all other styles unchanged.

## Files

```
EDIT  mu-plugins/ibv-core/includes/sections/hero/hero.php
EDIT  mu-plugins/ibv-core/includes/sections/hero/hero.css
```

## Change 1 — `hero.php`

Add a `compact` boolean to the args, defaulting to false. When true,
add `ibv-section-hero--compact` to the section's class list.

```php
function ibv_core_section_hero( $args = [] ) {
    $defaults = [
        'after_copy' => null,
        'compact'    => false,
    ];
    $args = wp_parse_args( $args, $defaults );

    // ... existing image / title / subtitle reads unchanged ...

    $classes = [ 'ibv-section-hero', 'ibv-section' ];
    if ( ! empty( $args['compact'] ) ) {
        $classes[] = 'ibv-section-hero--compact';
    }
    ?>
    <section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
```

The rest of the function body (inner markup, after_copy slot) stays
exactly as is.

Update the docblock to document the new arg:

```php
/**
 * @param array $args {
 *     @type callable|null $after_copy Optional. Invoked with no arguments; output appears below the copy block.
 *     @type bool          $compact    Optional. When true, the hero uses a reduced min-height (480px instead of 720px). All other styling unchanged. Default false.
 * }
 */
```

## Change 2 — `hero.css`

Add ONE rule at the end of the file:

```css
.ibv-section-hero--compact {
    min-height: 30rem; /* 480px — About / inner-page hero */
}
```

Comment matches the existing inline comment style for `min-height`
on the base rule (`min-height: 45rem; /* 720px — Figma desktop hero */`).

Do NOT modify the existing `.ibv-section-hero` rule. The base height
stays 720px.

## Verification

1. **Homepage frontend smoke test:**
   - Hero renders identically to before
   - DevTools: section root class is `ibv-section-hero ibv-section`
     (no `--compact` modifier present)
   - Computed `min-height` reads 720px (45rem)

2. **Compact variant manual test:**
   - Temporarily edit `front-page.php` to pass `compact: true`:
     ```php
     ibv_core_section_hero( [ 'compact' => true, 'after_copy' => '...' ] );
     ```
   - Refresh homepage
   - Hero should render at 480px min-height; everything else
     identical (title, rule, subtitle, search bar, image, overlay)
   - Revert the temporary edit before committing

3. **Final commit on staging branch shows hero unchanged from a
   user-facing perspective.** The compact variant exists in code but
   isn't called yet — that's expected; the About page brief will be
   the first consumer.

## Notes

- **Why a boolean rather than a numeric or named-size arg.** Two
  hero heights exist today (default + compact). A boolean is the
  simplest expression. If a third height appears (tall, full-bleed-narrow,
  etc.), refactor to a `size` arg or similar at that point. Premature
  generalisation costs more than the eventual refactor will.
- **Why `--compact` not `--small` or `--short`.** "Compact" reads
  closer to the design language ("page header hero" vs "hero hero").
  Naming consistency matters less than the modifier doing only what
  it says.
- **Padding-block stays as is.** The existing `padding-block: var(--ibv-space-4xl)` keeps the copy vertically
  centered within whatever min-height is in effect. Reducing min-height
  to 480px and keeping the same padding works because the flex centering
  in `__inner` collapses spacing automatically.
- **Background image and overlay unchanged.** The compact hero on
  About uses the same dark forest-green-deep background + 0.55 opacity
  overlay as the homepage. Image will be ACF-supplied per page.
