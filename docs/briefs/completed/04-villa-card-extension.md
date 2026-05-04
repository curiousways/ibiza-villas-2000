# Brief 04 — Villa card extension: badge variants + offer footnote

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

## Standards

- Components live under `mu-plugins/ibv-core/includes/components/{name}/`
- BEM naming, `ibv-` prefixes, no new dependencies, no build step
- All additions must be backwards-compatible — existing callers
  (homepage featured-villas grid, similar-villas section, anywhere
  villa-card is used today) render identically without changes

## Scope

The villa card component already supports an `offer` variant and a
`badge` arg, but the badge is hardcoded to render only when
`variant === 'similar'`. The Special Offers page (brief 05) needs:

1. The badge to render on the offer variant too (for the "This week's
   deal" tag on the featured grid card).
2. Variable badge colour — the SO design uses gold; future use cases
   may want teal or red.
3. Offer-variant cards to support a per-row footnote (mirroring the
   offer-panel's existing footnote treatment).
4. Offer-variant cards to support the `*` asterisk on the Now price
   (matching the offer panel).

All four are additive. Existing callers pass none of these and get
unchanged behaviour.

## Files

```
EDIT  mu-plugins/ibv-core/includes/components/villa-card/villa-card.php
EDIT  mu-plugins/ibv-core/includes/components/villa-card/villa-card.css
EDIT  mu-plugins/ibv-core/assets/css/tokens.css (only if --ibv-color-accent-red is missing)
```

## Change 1 — Open `badge` arg to all variants

Locate the badge render block in `villa-card.php` (currently around
line 130):

```php
<?php if ( 'similar' === $variant && ! empty( $args['badge'] ) ) : ?>
    <span class="ibv-villa-card__badge"><?php echo esc_html( $args['badge'] ); ?></span>
<?php endif; ?>
```

Change to:

```php
<?php if ( ! empty( $args['badge'] ) ) : ?>
    <span class="ibv-villa-card__badge ibv-villa-card__badge--<?php echo esc_attr( $badge_variant ); ?>">
        <?php echo esc_html( $args['badge'] ); ?>
    </span>
<?php endif; ?>
```

The badge now renders for any variant when `$args['badge']` is set.
`$badge_variant` is resolved earlier in the function (see Change 2).

## Change 2 — Add `badge_variant` arg

Add to defaults:

```php
'badge_variant' => 'default',
```

Validate and resolve before render:

```php
$valid_badge_variants = [ 'default', 'gold', 'teal', 'red' ];
$badge_variant        = in_array( $args['badge_variant'], $valid_badge_variants, true )
    ? $args['badge_variant']
    : 'default';
```

`default` is allowed (and renders as gold-on-teal, matching today's
similar-variant badge). Any other value → falls back to `default`.

## Change 3 — Add badge modifier classes in `villa-card.css`

The existing `.ibv-villa-card__badge` selector currently sets
`background: var(--ibv-color-accent-gold); color: var(--ibv-color-brand-teal);`
(based on what was on disk previously). Keep that selector as-is — it
provides the base layout and acts as the `--default` styling.

Add modifier rules:

```css
.ibv-villa-card__badge--gold {
    background: var(--ibv-color-accent-gold);
    color: var(--ibv-color-brand-teal);
}

.ibv-villa-card__badge--teal {
    background: var(--ibv-color-brand-teal);
    color: var(--ibv-color-text-inverse);
}

.ibv-villa-card__badge--red {
    background: var(--ibv-color-accent-red);
    color: var(--ibv-color-text-inverse);
}
```

Note that `--default` is intentionally NOT a separate selector — it
inherits the base rule. So the existing `similar` callers (which pass
no `badge_variant` and now get `default`) render exactly as before.

## Change 4 — Add `--ibv-color-accent-red` token if missing

Check `mu-plugins/ibv-core/assets/css/tokens.css` for an existing
`--ibv-color-accent-red`. If present: skip this change.

If missing: add it alongside the other accent tokens. Pick a value
that matches the project's brand palette — check the design library
in Figma if you have access to it. If not, use a placeholder like
`#c8102e` and flag in Notes that the value needs design verification.

The red variant ships unused on the SO page (which uses gold). It's
included for API completeness and to give the design system a
documented red badge. Brief 05 doesn't reference red.

## Change 5 — Add `footnote` and `show_now_asterisk` args to offer variant

Add to defaults:

```php
'footnote'          => '',
'show_now_asterisk' => false,
```

In the offer variant render block (currently around lines 213–265 in
`villa-card.php`), make two additions.

**A — Asterisk on the Now amount.**

Find the `__now-amount` span:

```php
<span class="ibv-villa-card__now-amount">
    <?php
    printf(
        esc_html__( 'From €%s / wk', 'ibv' ),
        esc_html( number_format_i18n( (float) $args['now_price'] ) )
    );
    ?>
</span>
```

Append an asterisk when `show_now_asterisk` is truthy:

```php
<span class="ibv-villa-card__now-amount">
    <?php
    printf(
        esc_html__( 'From €%s / wk', 'ibv' ),
        esc_html( number_format_i18n( (float) $args['now_price'] ) )
    );
    if ( ! empty( $args['show_now_asterisk'] ) ) {
        echo '<span class="ibv-villa-card__now-asterisk" aria-hidden="true">*</span>';
    }
    ?>
</span>
```

**B — Footnote rendering below pricing.**

Inside the offer variant block, after the `__offer-pricing` div
closes, add the footnote render:

```php
<?php if ( $args['footnote'] ) : ?>
    <p class="ibv-villa-card__footnote"><?php echo esc_html( $args['footnote'] ); ?></p>
<?php endif; ?>
```

Match the visual treatment used by the offer-panel's existing
footnote — small text, muted colour. If those rules don't already
exist in `villa-card.css`, add them:

```css
.ibv-villa-card__footnote {
    margin-block-start: var(--ibv-space-xs);
    font-size: var(--ibv-fs-small);
    color: var(--ibv-color-text-muted);
}

.ibv-villa-card__now-asterisk {
    margin-inline-start: 0.125em;
}
```

## Verification

1. **Existing callers — visual no-op check:**
   - Homepage featured-villas grid: cards render identically to today.
   - Anywhere using `variant=similar` with a `badge`: still renders
     the badge, same gold-on-teal styling.
   - Anywhere using `variant=offer`: cards render identically to
     today (no `badge`, no `footnote`, no asterisk passed → no
     additions appear).

2. **New API smoke (call manually in a test template or via
   `wp shell`):**
   - `villa-card([villa, variant=offer, badge="This week's deal",
     badge_variant="gold"])` renders gold badge top-left.
   - Same with `badge_variant="teal"` renders teal badge.
   - Same with `badge_variant="red"` renders red badge (assumes red
     token exists or was added).
   - Same with `footnote="There may be additional costs"` renders the
     footnote below pricing in muted small text.
   - Same with `show_now_asterisk=true` renders `*` after the Now
     amount.

3. **Validation guard:**
   - Calling with an invalid `badge_variant` string ("orange") falls
     back to `default` rather than crashing or rendering an undefined
     class.

## Notes

- **Why `default` is synonymous with gold.** The existing `similar`
  badge ships gold-on-teal styling. Existing callers don't pass a
  `badge_variant`, so they default to `default`. Making `default`
  identical to today's behaviour means zero regression risk for
  current consumers.
- **Red token.** If `--ibv-color-accent-red` doesn't exist yet, ship
  the badge--red modifier with the placeholder value and flag for
  design review. Don't block the brief on the design system gap; the
  red variant isn't used by brief 05.
- **Footnote placement matches offer-panel.** The offer-panel
  component (used by featured-offer section) already renders a
  footnote below its pricing. Mirroring that treatment on the offer
  card means visual consistency between the two components when the
  same villa is featured + listed.
- **Asterisk is presentational only.** The `*` doesn't auto-link to
  any specific footnote text on the card itself — it pairs with the
  footnote when both are set. If the footnote describes what the
  asterisk means, that's a content-editor responsibility.
