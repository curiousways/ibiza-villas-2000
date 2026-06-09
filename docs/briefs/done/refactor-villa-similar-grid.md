# Villa Detail — Similar Villas (align to the canonical villa-card grid)

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

**Refactor** — replace the bespoke parts of `villa-similar` with the exact
villa-card + grid pattern already used site-wide.

- **Design source:** Figma `Dj7yiKWK0pNADS7OdjXRKo`, frame `02b | Villa
  Detail`, Similar Villas node **`1:6062`** (header container `1:6064`).
  The cards there are the standard villa-card seen across the site (the
  same component on the homepage, listing, and offers grids).
- **Canonical pattern to mirror:** `featured-villas`
  (`includes/sections/featured-villas/featured-villas.php`) — header
  (title + button) inside `.ibv-section .ibv-container`, then
  `.ibv-grid .ibv-grid--N` of `ibv_core_villa_card()` calls. Read it; this
  brief makes `villa-similar` the 3-up sibling of it.
- Final section on the villa-detail main band (concierge → testimonial →
  similar).

---

## What's bespoke today (and goes)

`villa-similar` currently:

1. Passes `variant => 'similar'` to the card — a stripped variant that
   **removes the `__rule` divider** the Figma shows. The design uses the
   standard (default) card.
2. Has **no header button** — the Figma header has a sage "View all
   villas" button beside the title (matching `featured-villas`' header).
3. Uses `ibv_core_section_heading()` for the title, where the canonical
   pattern uses a bespoke `<header>` (title + button).

The villa-similar **data logic stays** (the `villa_similar` ACF field with
a recent-villas fallback, excluding the current villa, capped at 3) — that's
legitimate section logic, not bespoke styling. The `.ibv-grid--3` grid and
the `ibv_core_villa_card()` call are already the right pattern.

---

## Files to edit

```
EDIT  mu-plugins/ibv-core/includes/sections/villa-similar/villa-similar.php
EDIT  mu-plugins/ibv-core/includes/sections/villa-similar/villa-similar.css
```

Optional, guarded cleanup (see step 3):

```
EDIT  mu-plugins/ibv-core/includes/components/villa-card/villa-card.php   (retire the now-dead 'similar' variant)
EDIT  mu-plugins/ibv-core/includes/components/villa-card/villa-card.css   (its 'similar' rules, if any)
```

---

## 1 — Restructure `villa-similar.php` to mirror `featured-villas`

Keep the query block as-is. Replace the render block so it matches the
featured-villas structure — bespoke header (title + button) + grid of
**default** cards:

```php
	wp_enqueue_style( 'ibv-section-villa-similar' );
	wp_enqueue_style( 'ibv-villa-card' );
	wp_enqueue_style( 'ibv-button' );

	// ... existing $query logic unchanged ...

	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return;
	}
	?>
	<section class="ibv-villa-similar ibv-section ibv-section--surface-bg" aria-labelledby="ibv-villa-similar-title">
		<div class="ibv-container">
			<header class="ibv-villa-similar__header">
				<h2 id="ibv-villa-similar-title" class="ibv-villa-similar__title ibv-font-display"><?php esc_html_e( 'Similar Villas', 'ibv' ); ?></h2>
				<?php
				ibv_core_button(
					[
						'url'     => ibv_get_search_villas_url(),
						'label'   => __( 'View all villas', 'ibv' ),
						'variant' => 'primary',
						'size'    => 'small',
					]
				);
				?>
			</header>
			<div class="ibv-villa-similar__grid ibv-grid ibv-grid--3">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					ibv_core_villa_card( [ 'villa' => get_the_ID() ] ); // default card — same as everywhere else
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
```

Changes from current: drop the `wp_enqueue_style( 'ibv-section-heading' )`
and the `ibv_core_section_heading()` call (replaced by the bespoke header);
drop `'variant' => 'similar'` from the card; add the header + button.
Confirm `ibv_get_search_villas_url()` is the right "all villas"
destination (it's what `featured-villas` uses); if a dedicated villa-listing
page URL is more correct, use that instead — flag if unsure.

---

## 2 — `villa-similar.css` — header row only

The grid and cards are styled by `ibv-grid` + `villa-card`. This file only
needs the header row (mirror `featured-villas`' `__header`). Remove any
old bespoke `__grid` rules that duplicate `.ibv-grid--3`.

```css
.ibv-villa-similar__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: var(--ibv-space-md);
	margin-block-end: var(--ibv-space-2xl); /* match featured-villas header→grid spacing */
}

.ibv-villa-similar__title {
	margin: 0;
	font-weight: var(--ibv-font-weight-regular);
	font-size: var(--ibv-fs-h3); /* 32px serif, matches the in-grid section titles */
	line-height: var(--ibv-lh-snug);
	color: var(--ibv-color-text);
}
```

Mirror `featured-villas.css`'s `__header` / `__title` values so the two
sections read identically — if they already define these, match them
rather than inventing new spacing.

---

## 3 — (Guarded) retire the dead `similar` card variant

After this change, the `'similar'` variant is no longer called from the
villa-similar section. **Only if** a grep confirms nothing else in
`includes` or `themes/ibv` passes `'variant' => 'similar'` (or
`'similar'` to `ibv_core_villa_card`), remove the dead branch to keep the
card tidy:

- the `'similar'` entry in the allowed-variants `in_array()` list,
- any `'similar'`-specific conditionals (it currently only differs from
  `default` by suppressing the `__rule`),
- any `.ibv-villa-card--similar` CSS rules.

If anything still uses `'similar'`, **leave it and flag** — don't break
another consumer.

---

## Smoke test (compare to Figma `1:6062`)

1. Off-white section below the testimonial band: "Similar Villas" serif
   title (32px) on the left, sage "View all villas" button + arrow on the
   right.
2. 3-up grid of the **standard** villa cards — identical to the cards on
   the homepage/listing/offers grids, including the `__rule` divider under
   the title/facts (which the old `similar` variant was hiding).
3. Cards reflect each villa's own state (from-price, facts, any
   offer/badge) via the default card — no card-level differences from the
   other grids.
4. Degrade: no `villa_similar` set → falls back to recent villas excluding
   the current one; fewer than 3 available → grid shows what exists.
5. No PHP/console errors; section labelled by its title; cards link
   through to each villa.

---

## Notes

- **The existing villa-card is canonical — do NOT alter it to match the
  Figma.** Where the Figma cards differ from the live card (e.g. the Figma
  shows a sage *primary* "Enquire now" button; the card renders a
  `secondary` CTA), the **Figma is wrong**. Render the existing card
  exactly as it appears on the other grids. Make no changes to
  `villa-card.php` / `villa-card.css` for visual parity with the Figma
  card. (The only permitted card touch is the guarded removal of the dead
  `similar` variant in step 3.)
- **"View all villas" destination:** points at the villas listing/search
  via the same helper featured-villas uses. Confirm that's the intended
  target.
- **Why no `section-heading`:** the canonical villa-grid sections use a
  bespoke header (title + button) so the button can sit on the title row —
  matching `featured-villas`, not the `section-heading` used by the
  in-grid Location/Gallery sections.
- If a file differs from what's described, surface it rather than reshaping
  around it.
