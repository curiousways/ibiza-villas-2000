# Brief 02 — Hero: extract search composition

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

## Standards

- Section files live under `mu-plugins/ibv-core/includes/sections/{name}/`
- The hero is a chrome-level section: it owns its own padding model and
  is exempt from the standard `.ibv-section` rhythm system. Don't
  introduce surface modifiers on the hero root.
- BEM naming, `ibv-` prefixes, no new dependencies, no build step

## Scope

The hero currently renders an inline `ibv_core_hero_search()` call
inside its `__search` div. That makes it homepage-specific by accident
— other pages calling `ibv_core_section_hero()` get a search bar they
don't want.

Refactor the hero into a pure presentational component, with search
composition lifted to the homepage template. Use an `after_copy`
callable arg pattern so the hero stays a single section (one
`<section>`, one background, one overlay) but accepts arbitrary
content slotted below the copy block.

This brief touches the hero and the homepage template only. The
Special Offers page (which calls hero without search) is brief 05.

## Files

```
EDIT  mu-plugins/ibv-core/includes/sections/hero/hero.php
EDIT  mu-plugins/ibv-core/includes/sections/hero/hero.css
EDIT  themes/ibv/front-page.php
```

## Change 1 — Add `after_copy` arg to `hero.php`

`ibv_core_section_hero()` currently takes no args. Update its signature
to accept an args array:

```php
function ibv_core_section_hero( $args = [] ) {
    $defaults = [
        'after_copy' => null, // optional callable; receives no args
    ];
    $args = wp_parse_args( $args, $defaults );
    // ...
}
```

In the existing markup, replace the inlined `ibv_core_hero_search()`
call (and its `<div class="ibv-section-hero__search">…</div>` wrapper)
with a callable invocation:

```php
<?php if ( is_callable( $args['after_copy'] ) ) : ?>
    <div class="ibv-section-hero__after-copy">
        <?php call_user_func( $args['after_copy'] ); ?>
    </div>
<?php endif; ?>
```

The callable validation is `is_callable()` so any
function-name-string, closure, or array callable works.

The hero owns one `<section>` element with one background image, one
copy block, and an optional after-copy slot. No second sibling element,
no positioning hacks at the template level — the slot is part of the
section.

## Change 2 — Update `hero.css`

The `__search` modifier becomes `__after-copy`. Same width
constraints, same positioning. If the existing `.ibv-section-hero__search`
selector has rules for the search wrapper's spacing/width relative to
the copy block, port them to `.ibv-section-hero__after-copy`. If
`__search` only contained the form-specific styles (and the form
component owns its own rules via `hero-search.css`), drop the
`__search` rules entirely and don't re-add anything.

Audit the file: the old class name is `__search`, the new class name
is `__after-copy`. Don't introduce a generic class name like
`__slot` — `__after-copy` describes where in the section it sits and
matches the PHP arg name.

## Change 3 — Compose hero + search in `front-page.php`

Update the homepage template to pass the search render as the
`after_copy` callable:

```php
ibv_core_section_hero(
    [
        'after_copy' => 'ibv_core_hero_search',
    ]
);
```

That's the only homepage caller of the hero, so no other templates
need updating in this brief. Other pages that currently use the hero
(if any beyond the homepage) continue to call it with no args and now
correctly render without a search bar.

## Verification

1. **Homepage frontend** — hero section renders identically to before:
   background image, title, rule, subtitle, search form below the
   copy. No visual regression.

2. **DOM check** — the section markup now has
   `<div class="ibv-section-hero__after-copy">` instead of
   `<div class="ibv-section-hero__search">` wrapping the search form.

3. **Other pages** — confirm by visual smoke that any other template
   currently calling `ibv_core_section_hero()` (if any) renders without
   a search bar. If there are no such templates currently, this is a
   no-op confirmation; the pattern is set up for brief 05.

4. **Hero CSS regression check** — the after-copy slot positions
   identically to the previous search slot. Spacing/width relative to
   the copy block unchanged.

## Notes

- **Why a callable, not a render arg.** A render arg (string of HTML)
  would force the homepage template to capture the search render
  output via `ob_*` buffering, which is uglier than just passing the
  function name. The callable shape stays generic — any future page
  that wants something else slotted below the hero copy (a quick CTA
  row, a notice, a feature flag panel) can pass any callable.
- **Why not split into hero + after-copy as siblings.** Two siblings
  would duplicate the section background or force the homepage to
  manage positioning. Keeping the hero as one section preserves the
  invariant that hero-level chrome owns its own space.
- **`__after-copy` not `__slot`.** Slot is too generic; the next
  developer reading the markup would have to look up where it's
  rendered. `__after-copy` self-documents.
