# Villa Detail — Location Section (`villa-location`)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

---

## Brief type & design source

**Greenfield section build** (new `villa-location` wrapper) against a
signed-off design, plus a **restyle** of the existing `distance-ticks`
component. The map render is **retained unchanged**.

- **Design source:** Figma `Dj7yiKWK0pNADS7OdjXRKo`, frame `02b | Villa
  Detail`, Location node **`1:5998`**. Read it before styling and
  design-match against it.
- Third brief in the staged villa-detail rebuild (after `villa-header`
  and the `villa-overview` redesign).

**Hard constraint — retain the current map rendering.** Do **not** modify
`villa-map.php` or `villa-map.css`. The map (a live Google Maps embed
reading `property_map` lat/lng) stays exactly as it renders today. This
section *calls* `ibv_core_villa_map()`; it does not restyle, reposition,
or constrain the map's internals.

---

## Scope

Wrap the currently-bare `villa-map` and `distance-ticks` calls into a
single `villa-location` section matching Figma `1:5998`:

1. New `villa-location` section: a "Location" `<h2>` (via the shared
   `section-heading` component, as the other lower sections do), then the
   retained map, then the distance pills.
2. Restyle `distance-ticks` from a vertical emoji list into the design's
   horizontal **mint** pills, each with a **map-pin** icon + text.
3. Re-wire `single-villas.php` to call the new section in place of the two
   bare calls.

No new ACF fields, no new dependencies, no new icons (`map-pin.svg` is
already vendored). `distance-ticks` is used **only** here (verified), so
the restyle is safe.

**Out of scope:** the map component itself; the Gallery section (`1:6019`,
next brief); everything else on the page. No changes outside the listed
files, including small fixes.

---

## Files to create / edit

```
CREATE  mu-plugins/ibv-core/includes/sections/villa-location/villa-location.php
CREATE  mu-plugins/ibv-core/includes/sections/villa-location/villa-location.css

EDIT    mu-plugins/ibv-core/includes/components/distance-ticks/distance-ticks.php  (emoji → map-pin icon)
EDIT    mu-plugins/ibv-core/includes/components/distance-ticks/distance-ticks.css  (mint pills, horizontal)
EDIT    mu-plugins/ibv-core/bootstrap.php                                          (require new section)
EDIT    mu-plugins/ibv-core/includes/shared-assets.php                             (register style handle)
EDIT    themes/ibv/single-villas.php                                              (call villa-location, drop bare calls)
```

---

## Standards

- Colours / spacing / radius / type via `tokens.css`. No hardcoded values;
  grep `tokens.css` and paste output if a token looks missing rather than
  guessing.
- Icons via `ibv_core_icon()` / `ibv_core_the_icon()`. BEM, `ibv-` prefix,
  all output escaped.
- **Section pattern — match the lower villa-detail group.** `villa-location`
  is one of the page's full content sections (it sits among Gallery /
  Concierge / Testimonial / Similar). Mirror their wrapper exactly:
  `<section class="ibv-villa-location ibv-section"><div class="ibv-container">`,
  heading via `ibv_core_section_heading()`. **Do not** add a
  `ibv-section--surface-*` modifier: none of the sibling villa-detail
  sections (`concierge-cross-sell`, `villa-testimonial-teaser`,
  `villa-similar`) declare one yet, and half-migrating a single section
  would create an inconsistent rhythm seam. (Surface migration for the
  whole villa-detail group is a separate foundation pass — see Notes.)
  Accordingly the section CSS does **not** set `padding-block` /
  `background` / `color` on the root; `.ibv-section` owns the rhythm.

---

## 1 — `villa-location.php`

Render the heading + retained map + distance pills. Guard against an
orphan heading: if the villa has neither map coordinates nor any distance
rows, render nothing.

```php
<?php
/**
 * Section: Villa location (heading + retained map + distance pills).
 *
 * Matches Figma node 1:5998 (02b | Villa Detail → Location).
 * The map itself is rendered by ibv_core_villa_map() and is intentionally
 * left untouched by this section.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_section_villa_location( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	// Presence check — don't render an empty section.
	$map  = get_field( 'property_map', $villa_id );
	$has_map = is_array( $map ) && isset( $map['lat'], $map['lng'] )
		&& is_numeric( $map['lat'] ) && is_numeric( $map['lng'] );

	$rows         = get_field( 'villa_distances', $villa_id );
	$has_distance = is_array( $rows ) && count( $rows ) > 0;

	if ( ! $has_map && ! $has_distance ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-detail' );
	wp_enqueue_style( 'ibv-section-villa-location' );
	?>
	<section class="ibv-villa-location ibv-section">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'Location', 'ibv' ),
					'level' => 'h2',
				]
			);

			ibv_core_villa_map( $villa_id );      // retained — do not modify the map component
			ibv_core_distance_ticks( $villa_id ); // restyled to pills below
			?>
		</div>
	</section>
	<?php
}
```

---

## 2 — `distance-ticks` restyle

### PHP — swap the emoji for the vendored icon

In `distance-ticks.php`, replace the `📍` emoji span with the icon helper
(the `map-pin` glyph the design uses, already in `assets/icons/lucide/`).
Everything else (the `villa_distances` loop, the `distance_text` guard,
the `<ul>`/`<li>` structure) stays.

```php
<li class="ibv-distance-ticks__item">
	<?php ibv_core_the_icon( 'map-pin', [ 'size' => 14, 'class' => 'ibv-distance-ticks__icon' ] ); ?>
	<span class="ibv-distance-ticks__text"><?php echo esc_html( $text ); ?></span>
</li>
```

(Wrap the text in a `<span>` so the pill flex aligns icon + label cleanly.)

### CSS — vertical emoji list → horizontal mint pills

Replace `distance-ticks.css` with the pill treatment. Pill colour is
**mint** (`--ibv-color-mint-100`) with forest-dark text — matching the
header's distance pill and the Figma. **Text is `--ibv-fs-small` (14px),
not the Figma's 10px**, per the smoke-tested pill-sizing decision applied
across the page's pills.

```css
.ibv-distance-ticks {
	display: flex;
	flex-wrap: wrap;
	gap: var(--ibv-space-2xs); /* ~5px in Figma */
	list-style: none;
	margin: 0;
	padding: 0;
}

.ibv-distance-ticks__item {
	display: flex;
	align-items: center;
	gap: var(--ibv-space-2xs); /* icon ↔ label */
	padding: var(--ibv-space-2xs) var(--ibv-space-sm); /* 4px / 12px */
	background: var(--ibv-color-mint-100);
	border-radius: var(--ibv-radius-pill);
	font-family: var(--ibv-font-body);
	font-size: var(--ibv-fs-small);
	line-height: var(--ibv-lh-cozy);
	color: var(--ibv-color-forest-green-deep);
}

.ibv-distance-ticks__icon {
	flex-shrink: 0;
	color: var(--ibv-color-forest-green-deep);
}
```

---

## 3 — `villa-location.css`

The map and distance pills need separation from the heading and each
other. `section-heading` already supplies its own bottom margin; this file
handles the gap between the map and the pills.

```css
.ibv-villa-location .ibv-villa-map {
	/* Map render retained as-is; only ensure it sits above the pills. */
	margin-block-end: var(--ibv-space-lg); /* ~32px Figma map → pills */
}
```

Keep it minimal — do not set width/height/aspect on `.ibv-villa-map`
(that's the map component's own CSS, retained). If the map already carries
its own bottom margin, drop this rule rather than doubling the gap —
verify against `villa-map.css` and design-match the map→pills spacing.

---

## 4 — Wiring

**`bootstrap.php`** — beside the other villa section requires:

```php
require_once IBV_CORE_PATH . 'includes/sections/villa-location/villa-location.php';
```

**`shared-assets.php`** — add to the `$detail_handles` map:

```php
'ibv-section-villa-location'       => 'includes/sections/villa-location/villa-location.css',
```

**`themes/ibv/single-villas.php`** — replace the two bare calls with the
section. Current:

```php
ibv_core_villa_map( $villa_id );
ibv_core_distance_ticks( $villa_id );
```

Becomes:

```php
ibv_core_section_villa_location( $villa_id );
```

Leave the surrounding calls (offers, overview, gallery, concierge, etc.)
and the `TODO villa-detail-page-rebuild` comment in place.

---

## Smoke test (compare live render to Figma `1:5998`)

1. Staging villa with `property_map` coordinates, a configured Google Maps
   API key, and several `villa_distances` rows.
2. "Location" heading renders in the same serif treatment as "Similar
   Villas" lower down (same `section-heading` output).
3. The map renders **exactly as before this brief** — same size, style,
   marker, controls. No visual change to the map.
4. Distance items render as mint pills with a map-pin icon + 14px label,
   in a horizontal wrapping row beneath the map — matching the header's
   distance pill styling.
5. Degrade: no API key → map shows its existing fallback message, pills
   still render; no distance rows → no pill row, map still renders; neither
   map coords nor distances → the whole Location section is absent (no
   orphan heading).
6. No PHP notices; section sits in rhythm with the sections above/below.

---

## Notes

- **Pill size (14px, not Figma 10px):** deliberate — carries the
  smoke-tested pill-sizing decision (micro read too small in-browser)
  across to the distance pills for consistency with the header/amenity
  pills. Flag if it looks heavy beside the map.
- **Map retained:** the brief touches nothing in `villa-map.php` /
  `villa-map.css`. If matching the Figma would *require* a map change
  (e.g. the design's map aspect differs from the current render), surface
  it — do not change the map under this brief.
- **Shared pill component (surface, don't act):** pills now appear in
  three sections with copy-pasted styling — header (gold/mint), amenities
  (gold), distance (mint). This is the natural point to consolidate into a
  shared `.ibv-tag` pill (base + `--gold` / `--mint` modifiers) and have
  all three consume it. It touches files outside this brief, so leave it —
  strong candidate for the next consolidation brief.
- **Surface migration (surface, don't act):** none of the villa-detail
  lower sections declare an `ibv-section--surface-*` modifier. Bringing the
  whole group (location, gallery, concierge, testimonial, similar) onto
  explicit surfaces is a single foundation pass worth doing once the
  sections are all built — not piecemeal here.
- If `single-villas.php` differs from the two-bare-calls state described,
  adapt the insertion rather than reshaping the template, and surface it.
