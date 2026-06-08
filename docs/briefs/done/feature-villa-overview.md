# Villa Detail — Overview Section (`villa-overview` redesign)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

---

## Brief type & design source

**Design-fix** of the existing `villa-overview` section (its summary,
amenity ticks, and from-price render off-design), plus a **small
greenfield addition** (the "Villa Overview" heading and the top-border
divider, which don't exist yet).

- **Design source:** Figma `Dj7yiKWK0pNADS7OdjXRKo`, frame `02b | Villa
  Detail`, overview container node **`1:5973`**. Read it before styling
  and design-match against it — the Figma is the authority where this
  brief's prose is ambiguous.
- This is the second brief in the staged villa-detail-page rebuild,
  following the `villa-header` brief.

**Precondition — depends on the `villa-header` brief.** That brief moved
the villa name / rating / location-distance pills / facts out of
`villa-overview` into a new `villa-header` section. This brief assumes
that landed: `villa-overview` should currently render only the summary
block, amenity ticks, and the from-price (plus its `<section>` wrapper).
If the title/rating/location markup is still present in
`villa-overview.php`, **stop and flag it** — the header brief hasn't run
and the ordering is wrong.

---

## Scope

Bring `villa-overview` to match Figma `1:5973`:

1. Add a top border on the section (the divider beneath the header).
2. Add a "Villa Overview" `<h2>` heading.
3. Restyle the description body + "Read more" (keep the existing clamp /
   read-more mechanism — only its presentation changes).
4. Restyle the amenity pills (`amenity-ticks` component): inline
   green-tick text → gold pills matching the header's tag pills.
5. Restructure the from-price into the design's price block ("From" +
   large serif price + "/ wk", with "Price varies by season" beneath).

No new ACF fields. No new dependencies. No new section file. The amenity
restyle is CSS-only on the shared `amenity-ticks` component (used **only**
by this section — verified).

**Out of scope:** anything in `villa-header`, the location / gallery /
image-text / testimonials / similar-villas sections, and the enquiry
sidebar. Do not touch them, including small fixes outside the files
listed.

---

## Files to edit

```
EDIT  mu-plugins/ibv-core/includes/sections/villa-overview/villa-overview.php
EDIT  mu-plugins/ibv-core/includes/sections/villa-overview/villa-overview.css
EDIT  mu-plugins/ibv-core/includes/components/amenity-ticks/amenity-ticks.css
```

(If `amenity-ticks.php` markup needs adjusting for the pill, that's
allowed — but the current `<li>` → `<span class="__tick">✓</span> name`
structure should restyle to a pill via CSS alone. Confirm before editing
the PHP.)

---

## Standards

- All colours / spacing / radius / type via `tokens.css`. No hardcoded
  values. If a token looks missing, **grep `tokens.css` and paste the
  result** rather than inventing one.
- BEM, `ibv-` prefix, all output escaped.
- **Section/surface system does not apply** — `villa-overview` lives
  inside `.ibv-villa-detail__main` (flex column, shell owns rhythm). Its
  root stays a plain `<section class="ibv-villa-overview">`; no
  `.ibv-section` classes, no `padding-block` / `background` / `color` on
  the root. The one exception this brief introduces is the **top border**
  + its `padding-block-start` (the divider) — that's section-specific
  chrome from the design, not the rhythm system.

---

## Design deltas (current → target)

Token mapping from the Figma variables on `1:5973` — verify each against
`tokens.css`:

- `Light Grey #bfc8c8` → `--ibv-color-border` (top divider)
- `Black #343434` → `--ibv-color-black` / `--ibv-color-text`
- `Gold/100 #ffe4ac` → `--ibv-color-gold-100` (amenity pill bg)
- `Forest/Dark #055353` → `--ibv-color-forest-green-deep` (pill text, price number)

### 1. Top border / divider

The section starts with a `border-top` (`--ibv-color-border`) and
`padding-block-start: var(--ibv-space-lg)` (24px in Figma). This is the
hairline rule that separates the header above from the overview.

```css
.ibv-villa-overview {
	border-top: 1px solid var(--ibv-color-border);
	padding-block-start: var(--ibv-space-lg);
}
```

### 2. "Villa Overview" heading (new)

Add an `<h2>` as the first child of the section. **Visually it is small
and bold-sans, not a serif section heading** — Figma `Paragraph/Large (B)`:
Uncut Sans **Bold**, 18px, line-height 1.4, `#343434`. Do **not** route it
through the `section-heading` component (that's for the larger serif
section titles like "Location"/"Gallery") and do **not** give it a serif
display token. Plain styled `<h2>`.

```php
$heading_id = wp_unique_id( 'ibv-vo-heading-' );
// ...
<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-overview__heading">
	<?php esc_html_e( 'Villa Overview', 'ibv' ); ?>
</h2>
```

```css
.ibv-villa-overview__heading {
	margin: 0 0 var(--ibv-space-md); /* 16px to description */
	font-family: var(--ibv-font-body);
	font-size: 1.125rem; /* 18px — matches --ibv-fs-h4 */
	font-weight: var(--ibv-font-weight-bold);
	line-height: var(--ibv-lh-cozy);
	color: var(--ibv-color-text);
}
```

Wire the section's accessible name to this heading: set
`aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"` on the
`<section>`. (The header brief left this for the overview redesign to
reintroduce — this resolves it.)

### 3. Description + Read more

Keep the existing summary mechanism intact: the `property_summary` field,
the `ibv-prose` wrapper, the clamp logic, the `[data-ibv-readmore]`
button, and its inline toggle script. Only the presentation changes.

- Figma body is Uncut Sans Medium **12px**. **Render the description at
  `--ibv-fs-small` (14px), not 12px.** `tokens.css` explicitly reserves
  sub-14px (`--ibv-fs-micro`, 10px) for microcopy and warns against using
  it for body the user must read — and this paragraph *is* the section's
  reading content. 14px is the smallest size that respects that rule
  while staying close to the design. (Flagged for Ian/David in Notes — if
  strict 12px parity is wanted, it's a one-line change.)
- "Read more" sits on its own line below the paragraph, underlined
  (Figma shows the paragraph, a blank line, then the underlined link).
  The existing `__read-more` button is already an underlined link-styled
  button — keep it; just confirm its size matches (`--ibv-fs-small`) and
  it sits below the text, not inline.

```css
.ibv-villa-overview__summary {
	font-family: var(--ibv-font-body);
	font-size: var(--ibv-fs-small);
	line-height: var(--ibv-lh-cozy);
	color: var(--ibv-color-text);
}
```

Confirm `.ibv-prose` doesn't override the size; if it does, scope the
rule so the overview summary wins (e.g. `.ibv-villa-overview__summary.ibv-prose`).

### 4. Amenity pills (restyle `amenity-ticks`)

Currently: flex row of `✓ name` with a green tick and body-size text.
Target (Figma `1:5982`–`1:5988`): gold pills, forest-dark text, identical
to the header's location/distance pills.

- Pill: `background: var(--ibv-color-gold-100)`; `color:
  var(--ibv-color-forest-green-deep)`; `border-radius:
  var(--ibv-radius-pill)`; `padding: var(--ibv-space-2xs)
  var(--ibv-space-sm)` (4px / 12px); Uncut Sans Regular;
  `font-size: var(--ibv-fs-micro)` (10px); `line-height:
  var(--ibv-lh-cozy)`.
- The ✓ stays as the leading glyph inside the pill (it's part of the
  Figma label "✓ Energy Efficient"). Inherit the pill text colour — drop
  the separate green `--ibv-color-brand-panel-green` tick colour.
- Row gap ≈ 10px (Figma); `--ibv-space-xs` (8px) is the nearest token —
  design-match.

```css
.ibv-amenity-ticks {
	display: flex;
	flex-wrap: wrap;
	gap: var(--ibv-space-xs);
	list-style: none;
	margin: 0;
	padding: 0;
}

.ibv-amenity-ticks__item {
	display: flex;
	align-items: center;
	gap: var(--ibv-space-2xs);
	padding: var(--ibv-space-2xs) var(--ibv-space-sm);
	background: var(--ibv-color-gold-100);
	border-radius: var(--ibv-radius-pill);
	font-family: var(--ibv-font-body);
	font-size: var(--ibv-fs-micro);
	line-height: var(--ibv-lh-cozy);
	color: var(--ibv-color-forest-green-deep);
}

.ibv-amenity-ticks__tick {
	color: inherit;
	font-weight: var(--ibv-font-weight-regular);
}
```

### 5. Price block (restructure `__from-price`)

Currently one inline string: "From €X / wk · Price varies by season".
Target (Figma `1:5989`):

- Row: "From" (Uncut Sans Regular, 10px, `#343434`) + price (Newsreader
  Regular, **32px**, `--ibv-color-forest-green-deep`) + "/ wk" (Uncut Sans
  Regular, 10px, `#343434`); 4px gap between price and "/ wk".
- Below the row (12px gap): "Price varies by season" (Uncut Sans Regular,
  10px, `#343434`).

Replace the existing `__from-price` markup with:

```php
<?php if ( $indicative ) : ?>
	<div class="ibv-villa-overview__price">
		<p class="ibv-villa-overview__price-row">
			<span class="ibv-villa-overview__price-from"><?php esc_html_e( 'From', 'ibv' ); ?></span>
			<span class="ibv-villa-overview__price-amount">
				<?php
				printf(
					/* translators: %s: formatted EUR amount */
					esc_html__( '€%s', 'ibv' ),
					esc_html( number_format_i18n( (float) $indicative ) )
				);
				?>
			</span>
			<span class="ibv-villa-overview__price-unit"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
		</p>
		<p class="ibv-villa-overview__price-note"><?php esc_html_e( 'Price varies by season', 'ibv' ); ?></p>
	</div>
<?php endif; ?>
```

```css
.ibv-villa-overview__price {
	display: flex;
	flex-direction: column;
	gap: var(--ibv-space-sm); /* 12px */
}

.ibv-villa-overview__price-row {
	display: flex;
	align-items: baseline;
	gap: var(--ibv-space-2xs);
	margin: 0;
}

.ibv-villa-overview__price-from,
.ibv-villa-overview__price-unit,
.ibv-villa-overview__price-note {
	font-family: var(--ibv-font-body);
	font-size: var(--ibv-fs-micro); /* 10px */
	color: var(--ibv-color-text);
}

.ibv-villa-overview__price-note {
	margin: 0;
}

.ibv-villa-overview__price-amount {
	font-family: var(--ibv-font-display);
	font-weight: var(--ibv-font-weight-regular);
	font-size: 2rem; /* 32px — matches Figma; do NOT use --ibv-fs-h1 (clamps larger) */
	line-height: var(--ibv-lh-snug);
	color: var(--ibv-color-forest-green-deep);
}
```

---

## Vertical rhythm within the section

Figma stacking (outer container `gap-[31px]`, inner `gap-[40px]`,
text-block `gap-[16px]`): heading→description 16px; description-block →
amenities ≈40px; amenities → price ≈31px. Map to the nearest tokens and
design-match — exact pixels aren't required, but the three groups
(text / pills / price) need clear separation. Suggested:

```css
.ibv-villa-overview {
	display: flex;
	flex-direction: column;
}
.ibv-villa-overview__summary-block { margin: 0; }
.ibv-villa-overview .ibv-amenity-ticks { margin-block-start: var(--ibv-space-2xl); } /* ~40px */
.ibv-villa-overview__price            { margin-block-start: var(--ibv-space-xl); }   /* ~31px */
```

Remove any now-dead margin rules left over from the old layout (the old
`__from-price`, `.ibv-villa-overview .ibv-facts-strip`, etc. — the
facts-strip rule should already be gone from the header brief; confirm).

---

## Smoke test (compare live render to Figma `1:5973`)

1. Staging villa with a `property_summary` long enough to clamp, several
   `villa_amenity` terms, and a `villa_indicative_from_price`.
2. Hairline divider sits above the block; "Villa Overview" renders as
   small bold sans (18px), not serif.
3. Description reads at 14px; "Read more" is an underlined link below the
   paragraph; clicking it expands/collapses as before.
4. Amenities render as gold pills with forest-dark text and a ✓ prefix —
   visually matching the header's location/distance pills.
5. Price: "From" + large teal serif "€1,420" + "/ wk" on one row, "Price
   varies by season" beneath.
6. Degrade: no summary → no description block, no error; no amenity terms
   → no pill row; no indicative price → no price block.
7. A11y: section labelled by the "Villa Overview" h2; still exactly one
   `<h1>` on the page (the villa name, from `villa-header`).

---

## Notes

- **Description size (12px → 14px):** the one deliberate deviation from
  Figma, for live readability against the design system's no-micro-body
  rule. Flag at review; trivially revertible if strict parity is wanted.
- **Shared pill component (surface, don't act):** the amenity pills here
  and the location/distance pills in `villa-header` are the same Figma
  TAG component (gold / mint pill, forest-dark text, 10px). They're
  currently styled in three places. A shared `.ibv-tag` pill component
  (base + `--gold` / `--mint` modifiers) would consolidate them — but
  that touches the `villa-header` files (out of scope here) and is more
  than a few lines. Leave it; raise as a candidate follow-up brief.
- **"Villa Overview" semantics:** rendered as `<h2>` (correct document
  outline under the `<h1>` villa name) despite its small visual weight.
  Intentional — don't downgrade it to a styled `<p>` to match the visual.
- If `villa-overview.php` differs from the post-header-brief state
  described in the precondition, surface the discrepancy rather than
  reshaping around it.
