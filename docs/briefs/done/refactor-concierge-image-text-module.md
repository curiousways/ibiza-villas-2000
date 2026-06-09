# Villa Detail — Concierge block → reusable `image-text-section` module

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise concerns or
> suggest alternatives — but only where there's a genuine technical reason.
> If a larger or out-of-scope change seems warranted, **surface it first**;
> do not modify files outside the list below.

---

## Brief type

**Refactor.** Replace the one-off `concierge-cross-sell` markup with a
direct use of the reusable `image-text-section` module, and give that
module a small backwards-compatible option so it can live inside the
villa-detail shell without double-wrapping.

- **Design reference:** Figma `Dj7yiKWK0pNADS7OdjXRKo` node `1:6037`
  (Image and Text Section). No restyle needed — the module's existing
  output already matches this design (serif h2, accent rule, body, sage
  primary button + arrow, image on the right). This brief is wiring, not
  styling.
- Fifth section in the staged villa-detail rebuild.

**Why:** `concierge-cross-sell` is bespoke markup built before the shared
`ibv_core_image_text_section()` module existed. The module is now the
canonical pattern (homepage Short Breaks / IPS / Meet the Team, and the
booking-confirmation page all use it). The concierge content is plain
image-text, so it should use the module too.

**The catch (already decided):** the module hardcodes `.ibv-section` +
an inner `.ibv-container`. The villa-detail main column is already inside
`.ibv-container`, and the sibling sections had exactly that nesting
removed in the Location round. So the module gets a new **`embedded`**
flag that omits the inner container and the section rhythm when it's
rendered inside a shell. Default off — the four existing consumers are
untouched.

---

## Files to edit

```
EDIT    mu-plugins/ibv-core/includes/components/image-text-section/image-text-section.php   (add `embedded` flag)
EDIT    mu-plugins/ibv-core/includes/sections/concierge-cross-sell/concierge-cross-sell.php  (→ thin wrapper)
DELETE  mu-plugins/ibv-core/includes/sections/concierge-cross-sell/concierge-cross-sell.css  (now dead)
EDIT    mu-plugins/ibv-core/includes/shared-assets.php                                       (drop the dead style handle)
```

`themes/ibv/single-villas.php` is **unchanged** — it still calls
`ibv_core_section_concierge_cross_sell( __( 'Make the most of your stay', 'ibv' ) )`.

---

## 1 — Add the `embedded` flag to the module

In `image-text-section.php`:

- Add `'embedded' => false` to `$defaults` and document it (`@type bool
  $embedded` — "When true, omit the `.ibv-section` rhythm and the inner
  `.ibv-container` for use inside an already-contained shell. Default
  false.").
- Build the root classes without `.ibv-section` when embedded:

```php
$root_classes = [
	'ibv-image-text-section',
	'ibv-image-text-section--image-' . $image_side,
];
if ( ! $args['embedded'] ) {
	$root_classes[] = 'ibv-section';
}

$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
	$root_classes[] = 'ibv-section--surface-' . $args['surface'];
}
```

- Build the inner wrapper class without `.ibv-container` when embedded:

```php
$inner_classes = $args['embedded']
	? 'ibv-image-text-section__inner'
	: 'ibv-container ibv-image-text-section__inner';
```

…and use `<div class="<?php echo esc_attr( $inner_classes ); ?>">`.

> The surface modifier is deliberately kept available in embedded mode —
> `.ibv-section--surface-*` sets only background (and text colour for
> forest-green), independent of `.ibv-section`'s padding, so it still
> works with `.ibv-section` absent.
>
> **No CSS change expected.** The module's layout rules are keyed on
> `.ibv-image-text-section__*` and the `--image-left/right` modifiers, not
> on `.ibv-section`/`.ibv-container`. After the change, verify the
> embedded render is full-width within the main column (no indent) and the
> image-right row layout still holds on desktop. Add a CSS rule only if the
> embedded layout genuinely needs one — and if so, scope it to the module,
> not the villa page.

---

## 2 — Convert `concierge-cross-sell` to a thin wrapper

Replace the body of `ibv_core_section_concierge_cross_sell()` so it reads
the same Site Options fields and delegates to the module (mirroring how
`short-breaks` / `ips-panel` / `meet-team-teaser` wrap it). Keep the
function name, the `$heading_override` param, the early-return guard, and
the CTA-label fallback. Drop the bespoke markup and the manual style
enqueues (the module enqueues its own style + the button):

```php
function ibv_core_section_concierge_cross_sell( $heading_override = null ) {
	$image   = get_field( 'concierge_image', 'option' );
	$heading = $heading_override ? (string) $heading_override : (string) get_field( 'concierge_heading', 'option' );
	$body    = (string) get_field( 'concierge_body', 'option' );
	$cta     = (string) get_field( 'concierge_cta_label', 'option' );
	$url     = (string) get_field( 'concierge_cta_url', 'option' );

	if ( ! $heading && ! $body && empty( $image['ID'] ) ) {
		return;
	}
	if ( ! $cta ) {
		$cta = __( 'View concierge services', 'ibv' );
	}

	ibv_core_image_text_section(
		[
			'title'       => $heading,
			'description' => $body,
			'cta_url'     => $url,
			'cta_label'   => $cta,
			'image'       => $image,
			'image_side'  => 'right',
			'surface'     => 'bg',
			'embedded'    => true,
		]
	);
}
```

(The module shows the button only when both `cta_url` and `cta_label` are
present — same as the current behaviour, which gated the button on `$url`.
So a villa with no concierge CTA URL still renders heading + body + image,
no button.)

---

## 3 — Cleanup

- **Delete** `concierge-cross-sell.css` — its `.ibv-concierge-cross-sell__*`
  rules are now unused (the wrapper renders `.ibv-image-text-section__*`).
- In `shared-assets.php`, remove the registration of the now-unused
  `ibv-section-concierge-cross-sell` style handle. Leave the
  `ibv-image-text-section` handle (the module enqueues it).
- Leave the `bootstrap.php` require for `concierge-cross-sell.php` — the
  wrapper function still lives there.

---

## Smoke test

1. **Homepage unchanged:** Short Breaks, IPS, and Meet the Team render
   exactly as before (the `embedded` default is off). Booking-confirmation
   image-text section unchanged too.
2. **Villa detail concierge block:** renders via the module — serif "Make
   the most of your stay", accent rule, body, sage "View concierge
   services" button + arrow, image on the right.
3. **No double-wrap:** the concierge content sits at the **same width** as
   the gallery/overview above it (not indented), and its spacing reads
   consistently with the neighbouring sections (no oversized `.ibv-section`
   gap).
4. Degrade: no concierge fields set → block absent; no CTA URL → no button.
5. No console/PHP errors; one `<h2>` per the section, correct heading text
   from the template override.

---

## Notes

- **CTA button size:** the module renders the CTA at `size => 'small'`
  (its existing behaviour for all consumers). If the Figma's concierge
  button should match the larger gallery "View all photos" button, that's
  a module-wide change affecting every consumer — surface it separately,
  don't change it here.
- **surface-bg vs Figma white:** the block uses `surface: 'bg'` per the
  agreed call, which is the off-white page colour (a visual no-op against
  the page — the block blends rather than sitting on a white panel). The
  Figma draws it on white; switching to `surface: 'white'` is a one-word
  change if a distinct panel is wanted.
- **Rhythm:** embedded drops `.ibv-section`, so the block now spaces via
  the main column's flex gap, consistent with the sibling sections — and
  folds into the parked full-page rhythm review rather than carrying its
  own 72px padding.
- If any file differs from what's described, surface it rather than
  reshaping around it.
