# Villa Detail — Testimonial Section ("What our guests say")

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise concerns or
> suggest alternatives — but only where there's a genuine technical reason.
> If a larger or out-of-scope change seems warranted, **surface it first**;
> do not modify files outside the list below.

---

## Brief type & design source

**Design-fix** of the existing `villa-testimonial-teaser` section to match
the Figma. The section currently renders a bare quote on a teal band with
emoji stars and no heading; the design is a deep-blue band with a heading,
accent rule, and an off-white card.

- **Design source:** Figma `Dj7yiKWK0pNADS7OdjXRKo`, frame `02b | Villa
  Detail`, Testimonials node **`1:6040`**. Read it before styling.
- Sixth section in the staged villa-detail rebuild. It now renders
  full-width below the two-column grid (concierge → testimonial → similar).

---

## Scope

Bring `villa-testimonial-teaser` to match `1:6040`:

1. Deep-blue full-width band via the surface system.
2. White serif "What our guests say" heading + a short accent rule (the
   same heading-plus-rule treatment as the concierge block).
3. An off-white rounded card holding: 5 gold star **icons**, the quote
   (dark text), and the attribution (forest-dark).

Same data source (Site Options `testimonials` repeater, first row). No new
ACF fields, no new dependencies, no new icons (`star.svg` was vendored in
the header brief — reuse it).

**Out of scope:** Similar Villas (next brief); the enquiry sidebar;
everything else.

---

## Files to edit

```
EDIT  mu-plugins/ibv-core/includes/sections/villa-testimonial-teaser/villa-testimonial-teaser.php
EDIT  mu-plugins/ibv-core/includes/sections/villa-testimonial-teaser/villa-testimonial-teaser.css
```

---

## Standards

- Tokens only; grep `tokens.css` and paste if one looks missing rather
  than guessing. Icons via `ibv_core_the_icon()`. BEM, `ibv-` prefix, all
  output escaped.
- It's a full-width body section at page root, so it **declares a surface**
  (`ibv-section ibv-section--surface-blue`) — see colour note below.

---

## Confirmed values / token mapping (from Figma `1:6040`)

- **Band:** Figma `blue/500 #00526a`. Use **`ibv-section--surface-blue`**
  (it sets the blue bg **and** light text via `text-on-dark`). See the
  colour caveat in Notes — `surface-blue` is `blue-400 (#1e647a)`, a touch
  lighter than the Figma; this is deliberate (matches the site's other
  blue bands).
- **Heading "What our guests say":** Newsreader Regular, **44px**, white
  (inherited from the surface), line-height 1.1. (`Desktop/H2` = 44px; no
  exact token — design-match, e.g. `clamp(2rem, 5vw, 2.75rem)`. Don't use
  `--ibv-fs-h2` (tops at 36px).)
- **Accent rule:** 100px short rule under the heading — same treatment as
  the concierge block's `__rule` (sage accent; adjust for legibility on
  the blue if needed).
- **Card:** off-white `#f9f8f4` (`--ibv-color-off-white`), radius 4px
  (`--ibv-radius-md`), padding ~33px (`--ibv-space-xl`), inner gap 24px
  (`--ibv-space-lg`), `max-width: ~53rem` (Figma card is 847px, left-
  aligned within the container — not full width).
- **Stars:** 5 gold `star` icons (reuse the header's pattern —
  `ibv_core_the_icon('star')`, `fill: currentColor; stroke: none` with
  `color: var(--ibv-color-gold-500)`), ~14px.
- **Quote:** Uncut Sans Regular, 16px (`--ibv-fs-body`), `#343434`
  (`--ibv-color-text`), line-height ~1.4.
- **Attribution:** Uncut Sans Regular, 14px (`--ibv-fs-small`), forest-dark
  (`--ibv-color-forest-green-deep`).

---

## Markup

```php
function ibv_core_section_villa_testimonial_teaser() {
	$rows = get_field( 'testimonials', 'option' );
	if ( ! is_array( $rows ) || ! count( $rows ) || empty( $rows[0]['quote'] ) ) {
		return;
	}
	$row = $rows[0];

	wp_enqueue_style( 'ibv-section-villa-testimonial-teaser' );

	$heading_id = wp_unique_id( 'ibv-testimonial-heading-' );
	?>
	<section class="ibv-villa-testimonial-teaser ibv-section ibv-section--surface-blue" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<div class="ibv-container">
			<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-testimonial-teaser__heading ibv-font-display">
				<?php esc_html_e( 'What our guests say', 'ibv' ); ?>
			</h2>
			<hr class="ibv-villa-testimonial-teaser__rule" aria-hidden="true">

			<blockquote class="ibv-villa-testimonial-teaser__card">
				<span class="ibv-villa-testimonial-teaser__stars" role="img" aria-label="<?php esc_attr_e( 'Rated 5 out of 5', 'ibv' ); ?>">
					<?php for ( $i = 0; $i < 5; $i++ ) {
						ibv_core_the_icon( 'star', [ 'size' => 14, 'class' => 'ibv-villa-testimonial-teaser__star' ] );
					} ?>
				</span>
				<p class="ibv-villa-testimonial-teaser__text"><?php echo esc_html( $row['quote'] ); ?></p>
				<?php if ( ! empty( $row['attribution'] ) ) : ?>
					<footer class="ibv-villa-testimonial-teaser__attr"><?php echo esc_html( $row['attribution'] ); ?></footer>
				<?php endif; ?>
			</blockquote>
		</div>
	</section>
	<?php
}
```

---

## CSS

Replace the current rules (drop the bespoke `background: brand-teal` /
`color: text-inverse` / `padding-block` — `.ibv-section` +
`--surface-blue` own the band, bg, light text, and rhythm):

```css
.ibv-villa-testimonial-teaser .ibv-container {
	display: flex;
	flex-direction: column;
	gap: var(--ibv-space-xl); /* ~30px heading-block → card */
	align-items: flex-start;
}

.ibv-villa-testimonial-teaser__heading {
	margin: 0;
	font-weight: var(--ibv-font-weight-regular);
	font-size: clamp(2rem, 5vw, 2.75rem); /* ~44px */
	line-height: var(--ibv-lh-tight);
	/* colour inherits white from --surface-blue */
}

.ibv-villa-testimonial-teaser__rule {
	width: 100px;
	height: 0;
	margin: calc(var(--ibv-space-xl) * -0.5) 0 0; /* tighten to the heading per Figma */
	border: 0;
	border-top: 1px solid var(--ibv-color-sage-500); /* match concierge rule; adjust if low-contrast on blue */
}

/* Off-white card — its own dark text, overriding the band's light text */
.ibv-villa-testimonial-teaser__card {
	margin: 0;
	max-width: 53rem;
	display: flex;
	flex-direction: column;
	gap: var(--ibv-space-lg); /* 24px */
	padding: var(--ibv-space-xl); /* ~33px */
	background: var(--ibv-color-off-white);
	border-radius: var(--ibv-radius-md);
	color: var(--ibv-color-text);
}

.ibv-villa-testimonial-teaser__stars {
	display: inline-flex;
	gap: 2px;
	color: var(--ibv-color-gold-500);
}
.ibv-villa-testimonial-teaser__star {
	width: 14px; height: 14px;
	fill: currentColor; stroke: none;
}

.ibv-villa-testimonial-teaser__text {
	margin: 0;
	font-family: var(--ibv-font-body);
	font-size: var(--ibv-fs-body); /* 16px */
	line-height: var(--ibv-lh-cozy);
	color: var(--ibv-color-text);
}

.ibv-villa-testimonial-teaser__attr {
	font-family: var(--ibv-font-body);
	font-size: var(--ibv-fs-small); /* 14px */
	color: var(--ibv-color-forest-green-deep);
}
```

(Check the rule's negative margin against the actual container gap — the
intent is the rule sits close under the heading, not a full gap below it.
Design-match the spacing rather than treating those values as exact.)

---

## Smoke test (compare to Figma `1:6040`)

1. Full-width deep-blue band below the concierge block.
2. White serif "What our guests say" heading + short accent rule.
3. Off-white rounded card (left-aligned, ~half-to-two-thirds width, not
   full band) holding 5 gold star icons, the quote in dark text, and the
   attribution in forest-dark.
4. Degrade: no testimonials set → section absent; no attribution → quote +
   stars only.
5. No PHP/console errors; heading announces the section; stars announce
   the rating.

---

## Notes

- **Band colour (flag):** the Figma is `blue/500 #00526a`; the palette has
  no `blue-500`, and `surface-blue` is `blue-400 (#1e647a)` — slightly
  lighter. Using `surface-blue` keeps the testimonial consistent with the
  site's other blue bands (about-stats, related-articles, ibiza-guide).
  Exact Figma match would mean either a per-section deeper blue
  (inconsistent with those bands) or repointing `surface-blue` palette-wide
  (affects all four). That's a design-system decision — left as
  `surface-blue` here; raise it if the deeper blue is wanted everywhere.
- **Data is global, not per-villa:** the quote comes from the shared Site
  Options `testimonials` repeater (first row) — the same testimonial shows
  on every villa. That matches the current build and the filler-content
  approach; flag if per-villa testimonials are wanted later (that's a
  content-model change, not this brief).
- **Heading is bespoke, not `section-heading`:** deliberate — this is a
  coloured band with a larger (44px) white heading + accent rule, matching
  the concierge pattern rather than the 32px `section-heading` used by the
  in-grid sections.
- **Quote marks:** the Figma shows the quote wrapped in quotation marks. If
  the stored `quote` doesn't include them, decide whether to add them in
  content or via CSS `::before/::after` — but don't double up if the data
  already has them.
- If a file differs from what's described, surface it rather than reshaping
  around it.
