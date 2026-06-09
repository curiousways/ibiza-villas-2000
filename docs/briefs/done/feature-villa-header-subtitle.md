# Villa Detail — Header subtitle (restore on-page heading keyword for SEO)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise concerns or
> suggest alternatives — but only where there's a genuine technical reason.
> Do not modify files outside the list below.

---

## Brief type & purpose

**SEO-protection enhancement** to the `villa-header` section. Small,
surgical: add a quiet descriptive **subtitle** under the H1.

**Why.** The legacy villa page carried two keyword-rich headings:

- `<h1>` = villa pretty name **+ `additional_title_keyword`** → e.g.
  "Casa Marta Villas near Playa d'en Bossa"
- `<h2>` = the WP **post title** → e.g. "Stunning Villa in Playa d'en Bossa"

The rebuilt `villa-header` renders only `<h1>` = pretty name ("Casa
Marta") and no subtitle, so the page lost its on-page location-keyword
coverage in the headings. (The Yoast `<title>`, meta description,
canonical, and URL slug all carried over unchanged — the core SEO is
safe — but the H1 is the strongest *on-page* signal and the page now has
none of that keyword in its headings.)

This restores the descriptive subheading (the WP post title — exactly the
string the legacy page used as its `<h2>`) as a **visually secondary** line
under the H1, keeping the H1 itself clean per the Figma.

**Not in the Figma — deliberate.** The signed-off header has no subtitle;
this is a conscious SEO-protection addition, so it must be **understated**
and not disrupt the clean header design.

---

## Files to edit

```
EDIT  mu-plugins/ibv-core/includes/sections/villa-header/villa-header.php
EDIT  mu-plugins/ibv-core/includes/sections/villa-header/villa-header.css
```

---

## Title-field mapping (for reference)

This is how the three title-related fields now map to the template:

| Field | Role |
|---|---|
| `villa_pretty_name` (ACF) | `<h1>` — the real villa name, displayed clean ("Casa Marta"). Unchanged. |
| WP **post title** (`get_the_title`) | **`<h2>` subtitle** — the descriptive, keyword-bearing headline ("Stunning Villa in Playa d'en Bossa"). This brief adds it. |
| `additional_title_keyword` (ACF) | **Not rendered on the new page.** Its keyword ("Villas near Playa d'en Bossa") overlaps the post title and the Yoast title, and the H1 is kept clean. See Notes — it's now effectively unused; a decision for David. |

---

## Markup change (`villa-header.php`)

Replace the title read with pretty-name + post-title + a guarded subtitle,
and render the subtitle inside the title-cluster, between the H1 and the
rating:

```php
	$pretty   = get_field( 'villa_pretty_name', $villa_id );
	$wp_title = get_the_title( $villa_id );
	$title    = $pretty ? $pretty : $wp_title;

	// Descriptive subhead — restores the keyword-bearing <h2> the legacy
	// page carried (the WP post title). Suppressed when it would just
	// duplicate the H1 (e.g. no pretty name set, or they're identical).
	$subtitle = ( $pretty && $wp_title && $wp_title !== $pretty ) ? $wp_title : '';
```

Then in the title-cluster:

```php
<div class="ibv-villa-header__title-cluster">
	<h1 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-header__title">
		<?php echo esc_html( $title ); ?>
	</h1>

	<?php if ( $subtitle ) : ?>
		<p class="ibv-villa-header__subtitle"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>

	<?php if ( $rating || $rv_ct ) : ?>
		<div class="ibv-villa-header__rating"> ... unchanged ... </div>
	<?php endif; ?>
</div>
```

**Element choice — `<p>` vs `<h2>`:** the legacy markup used an `<h2>`,
which carries a little more heading weight. Use a **`<p>`** here (not an
`<h2>`) unless you have reason to prefer the heading: the page already has
a clear outline (H1 villa name → H2 "Villa Overview" → H2 section
titles), and slotting an H2 *above* "Villa Overview" muddies that order.
A styled `<p>` keeps the keyword visible on-page (the SEO point) without
disrupting the heading hierarchy. If you think the `<h2>` weight is worth
the outline trade-off, flag it rather than deciding silently. **Do not
hide it** (no visually-hidden / off-screen text — keep it genuinely
visible).

Leave the H1 content exactly as the pretty name — do not append anything
to it.

---

## CSS (`villa-header.css`)

Add a quiet, secondary subtitle — clearly subordinate to the 32px serif
H1, so the header still reads as the Figma intends:

```css
.ibv-villa-header__subtitle {
	margin: 0;
	font-family: var(--ibv-font-body);
	font-weight: var(--ibv-font-weight-regular);
	font-size: var(--ibv-fs-body); /* ~16px — secondary to the H1 */
	line-height: var(--ibv-lh-cozy);
	color: var(--ibv-color-text-muted); /* muted; verify the muted-text token in tokens.css */
}
```

- Confirm the muted-text token name against `tokens.css` (use the
  project's existing muted/secondary text colour; don't invent one). If
  none exists, use a clearly lighter shade of `--ibv-color-text` rather
  than full-strength black.
- Check the `__title-cluster` gap: the subtitle sits between the H1 and
  the rating row. Ensure a small gap H1 → subtitle and that the rating
  still sits comfortably below — design-match an understated rhythm; the
  subtitle should feel attached to the name, not floating.
- Body/sans is a deliberate choice here (the convention is serif titles;
  this is a quiet supporting subhead, so sans keeps it visually
  subordinate to the serif name — a per-case override, not the default).

---

## Smoke test

1. Villa with `villa_pretty_name` "Casa Marta" and post title "Stunning
   Villa in Playa d'en Bossa": H1 reads "Casa Marta" (clean), with
   "Stunning Villa in Playa d'en Bossa" as a quiet line directly beneath,
   then the rating row.
2. The subtitle is visibly subordinate to the H1 (smaller, muted) — the
   header still matches the clean Figma feel, not two competing titles.
3. Guard: a villa where pretty name == post title (or no pretty name) →
   **no** subtitle (no duplicated line).
4. View source: the post-title keyword phrase is present on-page beneath
   the H1; the H1 itself is unchanged; still exactly one `<h1>`.
5. No layout regression to the rating/tags/facts below.

---

## Notes

- **`additional_title_keyword` is now unused on the page** (the H1 is kept
  clean; its keyword overlaps the post title and the Yoast title). It's a
  mapping decision for David: retire the field, or swap it in as the
  subtitle instead of the post title (a one-line change — `$subtitle =
  get_field( 'additional_title_keyword', $villa_id )`). Left on the post
  title here because that exactly restores the legacy `<h2>`. Don't render
  both — they overlap and would read redundantly.
- **Core SEO is unaffected by this change** — the Yoast title, meta, and
  canonical are independent of the header markup. This only restores
  on-page heading keyword coverage; it's sensible insurance for a site in
  protection mode, not a fix for a confirmed ranking loss.
- **This subtitle is not in the Figma** — keep it understated; if it ever
  reads as fighting the H1, dial it back further rather than up.
- If `villa-header.php` differs from what's described, surface it rather
  than reshaping around it.
