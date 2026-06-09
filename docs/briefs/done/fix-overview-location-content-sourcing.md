# Villa Detail — Overview/Location content sourcing fix

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise concerns or
> suggest alternatives — but only where there's a genuine technical reason.
> Do not modify files outside the list below.

---

## Brief type & purpose

**Content-sourcing / wiring fix.** No design change — the Overview and
Location section layouts stay exactly as they are. This only changes
*which field feeds which section*.

**The problem.**

1. **`the_content()` is not rendered anywhere on the villa template.**
   `single-villas.php` and the villa sections never output it, so the main
   villa description — the keyword-rich copy *with internal links to other
   villas and locations* (a real SEO asset) — is currently missing from
   every villa page. This is the priority to fix.
2. **`villa-overview` sources the wrong field.** It reads
   `property_summary` (which is **labelled "Property Location"** in the
   admin and holds the distances / logistics / rental-licence text), so the
   "Villa Overview" heading currently sits on top of location content.

**The fix (agreed).**

- **Overview** renders **`the_content()`** (the description) — restoring
  the copy and the internal links.
- The **"Property Location" field (`property_summary`)** moves into the
  **`villa-location`** section, under the map and distance ticks — exactly
  where its label says it belongs. Page flow becomes: Overview
  (description) → Location (map + ticks + location/logistics text).

No content is migrated or rewritten; no fields are renamed; the new
`property_more_info_*` fields are not touched (they remain a future
"More Information" block).

---

## Files to edit

```
EDIT  mu-plugins/ibv-core/includes/sections/villa-overview/villa-overview.php
EDIT  mu-plugins/ibv-core/includes/sections/villa-location/villa-location.php
EDIT  mu-plugins/ibv-core/includes/sections/villa-location/villa-location.css
```

---

## 1 — `villa-overview.php`: source `the_content()` (was `property_summary`)

Swap the summary source from the ACF field to the post content. Keep the
read-more clamp logic, the amenity ticks, and the price block exactly as
they are — only the `$summary` source changes.

Replace:

```php
$summary    = get_field( 'property_summary', $villa_id );
```

with:

```php
// Main villa description — the post content (keyword-rich, carries the
// internal links). Rendered through the_content filter so formatting,
// links, and embeds resolve exactly as core would output them.
$content_raw = get_the_content( null, false, $villa_id );
$summary     = ( '' !== trim( $content_raw ) ) ? apply_filters( 'the_content', $content_raw ) : '';
```

Then, where the summary is printed, change the escaping. It's now
the_content-filtered post output (same as core's `the_content()`), so it
is **not** run through `wp_kses_post()` (that would strip valid embeds):

Replace:

```php
<?php echo wp_kses_post( $summary ); ?>
```

with:

```php
<?php echo $summary; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- the_content filter output, rendered as core does. ?>
```

The existing `$plain_len` / `$use_clamp` logic already measures
`wp_strip_all_tags( $summary )`, so it works unchanged against the
filtered content. Leave the read-more button, the amenity ticks
(`ibv_core_amenity_ticks`), and the price block as-is.

> **Clamp must stay CSS-only.** The read-more collapse is a CSS class
> toggle (`--clamp` / `--open`), so the full content — including the
> internal links — stays in the DOM when collapsed. Keep it that way; do
> **not** switch to truncating the string in PHP/JS, or the links drop out
> of the HTML and the SEO benefit is lost.

---

## 2 — `villa-location.php`: render the "Property Location" text

Read `property_summary` here and render it after the distance ticks. Add
it to the presence check so the section can render on the strength of this
text too.

Add to the presence checks:

```php
$location_text     = get_field( 'property_summary', $villa_id );
$has_location_text = $location_text && '' !== trim( wp_strip_all_tags( (string) $location_text ) );

if ( ! $has_map && ! $has_distance && ! $has_location_text ) {
	return;
}
```

Then render it after `ibv_core_distance_ticks( $villa_id );`:

```php
ibv_core_villa_map( $villa_id );
ibv_core_distance_ticks( $villa_id );

if ( $has_location_text ) : ?>
	<div class="ibv-villa-location__detail ibv-prose">
		<?php echo wp_kses_post( $location_text ); ?>
	</div>
<?php endif;
```

(`property_summary` is an ACF WYSIWYG — bullet lists / light markup — so
`wp_kses_post()` is the right escaping here, unlike the the_content case
above.)

---

## 3 — `villa-location.css`: space the detail block

```css
.ibv-villa-location__detail {
	margin-block-start: var(--ibv-space-lg);
}
```

`ibv-prose` already handles list/paragraph/link styling — only add the
top spacing so it sits clearly below the ticks. Adjust the value to match
the section's existing rhythm if `--ibv-space-lg` reads too tight/loose.

---

## Smoke test

1. **Overview** now shows the villa **description** (the post content),
   not the distances — with the read-more clamp behaving as before, and
   the amenity ticks + price block unchanged beneath it.
2. **Internal links** in the description (e.g. to other villas / Ibiza
   Town) are present and clickable; view source confirms they're in the
   DOM even when the summary is collapsed.
3. **Location** section now shows the map + distance ticks **and** the
   "Property Location" text (distances detail, car note, **rental licence
   number**) below them.
4. Degrade: villa with empty post content → no summary block in the
   overview (heading + amenities + price still render); villa with no
   `property_summary` → location renders without the detail block.
5. No PHP/console errors; headings still read Overview … Location in order.

---

## Notes

- **The rental licence (e.g. ET1075E) lives in `property_summary`** and is
  legally required to be displayed for Balearic holiday rentals. It must
  remain visible — this brief keeps it on the page (now in the Location
  section). Don't strip or alter it.
- **Distance duplication (content task, not code):** the `property_summary`
  text lists distances that also appear as the structured distance ticks.
  That's a content-normalisation decision for Tina (e.g. trim the prose to
  the extras the ticks don't cover — car advice, licence), not something to
  fix in markup here. Flagging, not acting.
- **Don't rename the field.** `property_summary` (label "Property
  Location") keeps its internal name — renaming an ACF field name can
  orphan existing data. The label is accurate now that it renders in the
  Location section.
- **`property_more_info_*` untouched** — those new fields are for a future
  "More Information" block, not part of this change.
- If a file differs from what's described, surface it rather than reshaping
  around it.
