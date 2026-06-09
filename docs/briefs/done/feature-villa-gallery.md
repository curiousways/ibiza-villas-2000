# Villa Detail — Gallery Section (`gallery` redesign: static teaser + pop-up viewer)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise concerns or
> suggest alternatives — but only where there's a genuine technical reason.
> If a larger or out-of-scope change seems warranted, **surface it first
> rather than doing it**; do not modify files outside the list below.

---

## Brief type & design source

**Design-fix + interaction build** on the existing `gallery` component.

- **Design source:** Figma `Dj7yiKWK0pNADS7OdjXRKo`, frame `02b | Villa
  Detail`, Gallery node **`1:6019`**. Read it before styling.
- Fourth brief in the staged villa-detail rebuild. The component already
  exists and renders a static hero + thumb strip + a `<dialog>` lightbox.

**UX model (decided — supersedes the earlier on-page-carousel idea).** The
on-page gallery is a **static teaser**; the pop-up is the **viewer**. The
on-page crop is purely cosmetic because **every tile opens the pop-up**,
where images show at their true shape. This is the conversion-safe
property-gallery pattern (Airbnb/Booking): tidy teaser pulls people in, the
immersive viewer is where the villa sells.

**Two conscious deviations from the signed-off Figma (approved):**
1. The on-page prev/next **chevrons are dropped** — no in-place paging.
2. The pop-up gains an **image counter** ("4 / 18"). The pop-up isn't in
   the Figma; its details are deliberate judgement calls.

Everything else matches the Figma: serif "Gallery" heading, solid-sage
"View all photos" button + arrow, wide-cropped hero, 6-up rounded thumb
strip.

---

## Scope

1. **On-page teaser (Figma `1:6019`):** serif heading; sage "View all
   photos" button + arrow top-right; a hero image cropped to a wide ratio;
   a 6-up rounded thumb strip. The hero and every thumb are buttons.
2. **Every tile opens the pop-up:** hero click → pop-up at image 0; thumb
   click → pop-up at that image; "View all photos" → pop-up at image 0.
3. **Pop-up viewer:** images at **source shape** held at a **fixed height**
   (no jump between portrait/landscape); prev/next; **X icon** close;
   image counter; Esc + arrow-key support.

No new ACF fields, no new dependencies. One new vendored icon
(`chevron-left`, used by the pop-up nav). `gallery` is used only on the
villa detail page.

**Out of scope:** every other section; the map; anything outside the files
listed.

---

## Files to create / edit

```
CREATE  mu-plugins/ibv-core/assets/icons/lucide/chevron-left.svg
EDIT    mu-plugins/ibv-core/includes/components/gallery/gallery.php
EDIT    mu-plugins/ibv-core/includes/components/gallery/gallery.css
```

(Inline lightbox JS lives in `gallery.php` via `wp_add_inline_script` —
extend it there; no separate JS file. Keep the single-image guard: one
image → render just that image, no thumbs/button/dialog.)

---

## Standards

- Tokens only (`tokens.css`); grep and paste if one looks missing rather
  than guessing. Icons via `ibv_core_icon()` / `ibv_core_the_icon()`. BEM,
  `ibv-` prefix, all output escaped, `esc_js()` for inline JS values.

---

## Confirmed values (from the repo)

- **Solid sage button** = `ibv_core_button` variant **`primary`**
  (`--ibv-button--primary` → `--ibv-color-accent` → `--ibv-color-sage-500`
  `#00897e`); arrow on by default. The current code uses `secondary`
  (transparent) — switch to `primary`.
- **Image radius** = 4px = **`--ibv-radius-md`** (apply to hero + thumbs;
  current code uses `lg`/`sm` — unify to `md`). Chevron/close square
  wrappers = 2px = `--ibv-radius-sm`.
- **Sizes:** hero uses uncropped **`large`** (NOT `ibv-hero` — that's a
  hard 16:9 crop; cover-cropping it to the on-page ratio double-crops);
  thumbs **`medium`**; pop-up **`full`** (source shape, full quality).

## On-page crop ratio

Crop hero + thumbs to a uniform **3:2** via CSS (`aspect-ratio: 3 / 2;
object-fit: cover`).

> Figma draws these at **16:9**; 3:2 is the smoke-test call. Single
> `aspect-ratio` value used by both — change `3 / 2` → `16 / 9` in one
> place if Figma-exact is wanted. (Crop is cosmetic now — the pop-up shows
> source shape regardless.)

---

## 1 — Vendor `chevron-left.svg`

Repo has `chevron-right.svg` and `x.svg` but not `chevron-left`. Add the
standard Lucide glyph (used by the pop-up's prev button):

```svg
<!-- @license lucide-static v0.469.0 - ISC -->
<svg class="lucide lucide-chevron-left" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="m15 18-6-6 6-6" />
</svg>
```

---

## 2 — Data array (lightbox only)

The on-page hero + thumbs are server-rendered, so the JSON only feeds the
pop-up. Reduce the per-image array to what the viewer needs:

```php
$images[] = [
	'id'   => $id,
	'full' => $full[0], // pop-up: source shape, full quality
	'alt'  => $alt,
];
```

`data-images` (already on the root) then carries `{full, alt}` for every
image. (Drop the `thumb`/`display` keys from the JSON — thumbs render
server-side.)

---

## 3 — On-page markup

```php
$hero   = $images[0];
$thumbs = array_slice( $images, 1, 6 ); // images 1–6 → hero + 6 thumbs, no duplication
?>
<div class="ibv-gallery" id="<?php echo esc_attr( $uid ); ?>" data-images="<?php echo $escaped_json; ?>">
	<div class="ibv-gallery__header">
		<?php ibv_core_section_heading( [ 'title' => __( 'Gallery', 'ibv' ), 'level' => 'h2' ] ); ?>
		<?php
		ibv_core_button( [
			'tag'        => 'button',
			'type'       => 'button',
			'label'      => __( 'View all photos', 'ibv' ),
			'variant'    => 'primary',
			'size'       => 'medium',
			'class'      => 'ibv-gallery__view-all',
			'attributes' => [ 'data-ibv-gallery-open' => '0' ],
		] );
		?>
	</div>

	<button type="button" class="ibv-gallery__main" data-ibv-gallery-open="0" aria-label="<?php esc_attr_e( 'Open photo gallery', 'ibv' ); ?>">
		<?php ibv_core_image( $hero['id'], 'large', [ 'class' => 'ibv-gallery__main-image', 'loading' => 'eager', 'decoding' => 'async' ] ); ?>
	</button>

	<ul class="ibv-gallery__thumbs">
		<?php foreach ( $thumbs as $i => $item ) : $global_index = $i + 1; ?>
			<li class="ibv-gallery__thumb-item">
				<button type="button" class="ibv-gallery__thumb" data-ibv-gallery-open="<?php echo esc_attr( (string) $global_index ); ?>">
					<?php ibv_core_image( $item['id'], 'medium', [ 'class' => 'ibv-gallery__thumb-image', 'loading' => 'lazy', 'decoding' => 'async' ] ); ?>
					<span class="ibv-u-visually-hidden"><?php esc_html_e( 'Open photo', 'ibv' ); ?></span>
				</button>
			</li>
		<?php endforeach; ?>
	</ul>

	<!-- dialog below -->
</div>
```

> Note `ibv_core_section_heading` here matches "Location"/"Similar Villas".
> Confirm it sits correctly inside the flex header row beside the button;
> if its default bottom margin fights the row, scope a reset on
> `.ibv-gallery__header .ibv-section-heading`.

---

## 4 — Pop-up markup

```php
<dialog class="ibv-gallery__dialog" id="<?php echo esc_attr( $dialog_id ); ?>" aria-label="<?php esc_attr_e( 'Photo gallery', 'ibv' ); ?>">
	<div class="ibv-gallery__dialog-inner">
		<button type="button" class="ibv-gallery__close" data-ibv-gallery-close aria-label="<?php esc_attr_e( 'Close', 'ibv' ); ?>">
			<?php ibv_core_the_icon( 'x', [ 'size' => 24 ] ); ?>
		</button>
		<button type="button" class="ibv-gallery__lb-arrow ibv-gallery__lb-arrow--prev" data-ibv-gallery-prev aria-label="<?php esc_attr_e( 'Previous photo', 'ibv' ); ?>">
			<?php ibv_core_the_icon( 'chevron-left', [ 'size' => 28 ] ); ?>
		</button>
		<img class="ibv-gallery__lightbox-img" src="" alt="">
		<button type="button" class="ibv-gallery__lb-arrow ibv-gallery__lb-arrow--next" data-ibv-gallery-next aria-label="<?php esc_attr_e( 'Next photo', 'ibv' ); ?>">
			<?php ibv_core_the_icon( 'chevron-right', [ 'size' => 28 ] ); ?>
		</button>
		<p class="ibv-gallery__counter" aria-live="polite"></p>
	</div>
</dialog>
```

---

## 5 — JavaScript (inline)

Simpler than the carousel version — open-at-index + viewer nav + counter:

```js
document.addEventListener("DOMContentLoaded", function () {
  var root = document.getElementById("{UID}");
  if (!root) return;
  var data = JSON.parse(root.getAttribute("data-images"));
  var dlg = document.getElementById("{DIALOG_ID}");
  if (!dlg) return;
  var img = dlg.querySelector(".ibv-gallery__lightbox-img");
  var counter = dlg.querySelector(".ibv-gallery__counter");
  var ix = 0;

  function show(i) {
    ix = (i + data.length) % data.length;
    var cur = data[ix];
    if (img) { img.src = cur.full; img.alt = cur.alt || ""; }
    if (counter) { counter.textContent = (ix + 1) + " / " + data.length; }
  }
  function open(i) {
    show(i);
    if (typeof dlg.showModal === "function") { dlg.showModal(); } else { dlg.setAttribute("open", ""); }
  }

  root.querySelectorAll("[data-ibv-gallery-open]").forEach(function (btn) {
    btn.addEventListener("click", function () { open(parseInt(btn.getAttribute("data-ibv-gallery-open"), 10) || 0); });
  });
  var prev = dlg.querySelector("[data-ibv-gallery-prev]");
  if (prev) prev.addEventListener("click", function () { show(ix - 1); });
  var next = dlg.querySelector("[data-ibv-gallery-next]");
  if (next) next.addEventListener("click", function () { show(ix + 1); });
  var close = dlg.querySelector("[data-ibv-gallery-close]");
  if (close) close.addEventListener("click", function () {
    if (typeof dlg.close === "function") { dlg.close(); } else { dlg.removeAttribute("open"); }
  });
  dlg.addEventListener("keydown", function (e) {
    if (e.key === "ArrowRight") { show(ix + 1); }
    else if (e.key === "ArrowLeft") { show(ix - 1); }
  });
});
```

---

## 6 — CSS

```css
.ibv-gallery { display: flex; flex-direction: column; gap: var(--ibv-space-md); }

.ibv-gallery__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: var(--ibv-space-md);
}

/* Hero teaser — wide crop, clickable */
.ibv-gallery__main {
	display: block;
	width: 100%;
	padding: 0;
	border: none;
	background: none;
	cursor: pointer;
	aspect-ratio: 3 / 2;          /* smoke-test call; Figma 16/9 */
	border-radius: var(--ibv-radius-md);
	overflow: hidden;
}
.ibv-gallery__main-image { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Thumb strip — 6-up, uniform crop, clickable */
.ibv-gallery__thumbs {
	display: grid;
	grid-template-columns: repeat(6, minmax(0, 1fr));
	gap: var(--ibv-space-xs);
	list-style: none; margin: 0; padding: 0;
}
.ibv-gallery__thumb {
	display: block; width: 100%; aspect-ratio: 3 / 2;
	padding: 0; border: none; background: none; cursor: pointer;
	border-radius: var(--ibv-radius-md); overflow: hidden;
	opacity: 0.85; transition: opacity var(--ibv-duration-normal) var(--ibv-ease-standard);
}
.ibv-gallery__thumb:hover, .ibv-gallery__thumb:focus-visible { opacity: 1; }
.ibv-gallery__thumb-image { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Pop-up — source shape held at fixed height (no jump) */
.ibv-gallery__dialog {
	padding: 0; border: none;
	width: auto; max-width: 94vw;          /* hug the image */
	background: var(--ibv-color-bg);
	border-radius: var(--ibv-radius-md);
}
.ibv-gallery__dialog::backdrop { background: rgb(0 0 0 / 0.7); }
.ibv-gallery__dialog-inner {
	position: relative;
	display: flex; align-items: center; justify-content: center;
}
.ibv-gallery__lightbox-img {
	display: block;
	height: min(80vh, 44rem);              /* fixed height → constant box */
	width: auto;                           /* width follows source aspect */
	max-width: 92vw;
	object-fit: contain;
}
.ibv-gallery__close {
	position: absolute; top: var(--ibv-space-sm); right: var(--ibv-space-sm); z-index: 1;
	display: flex; border: none; cursor: pointer;
	background: var(--ibv-color-off-white); border-radius: var(--ibv-radius-sm);
	padding: var(--ibv-space-2xs);
}
.ibv-gallery__lb-arrow {
	position: absolute; top: 50%; transform: translateY(-50%); z-index: 1;
	display: flex; align-items: center; justify-content: center;
	width: 40px; height: 40px; border: none; cursor: pointer;
	background: var(--ibv-color-off-white); border-radius: var(--ibv-radius-sm);
	color: var(--ibv-color-text);
}
.ibv-gallery__lb-arrow--prev { left: var(--ibv-space-sm); }
.ibv-gallery__lb-arrow--next { right: var(--ibv-space-sm); }
.ibv-gallery__counter {
	position: absolute; bottom: var(--ibv-space-sm); left: 50%; transform: translateX(-50%);
	margin: 0; padding: var(--ibv-space-2xs) var(--ibv-space-sm);
	background: var(--ibv-color-off-white); border-radius: var(--ibv-radius-pill);
	font-size: var(--ibv-fs-micro); color: var(--ibv-color-text);
}
```

Remove the old `__hero` / `__hero-image`, square `__thumb-image`, the old
text `__close`, and the bottom `__nav` row rules.

---

## Smoke test

1. Villa with several landscape **and** at least one portrait image.
2. **Teaser:** serif "Gallery" heading; solid sage "View all photos" +
   arrow top-right; hero + 6 thumbs all cropped to the same wide shape,
   4px corners; no on-page arrows.
3. **Open:** clicking the hero opens the pop-up at the first image;
   clicking a thumb opens at that image; "View all photos" opens at the
   first.
4. **Viewer:** images show at **source shape**; the box stays the **same
   height** paging between a portrait and a landscape (no jump); prev/next
   + X work; counter reads "n / total"; Esc and ← → keys work.
5. Single-image villa: just the one image, no thumbs/button/dialog.
6. No console errors; dialog focus behaves.

---

## Notes / judgement calls

- **Teaser vs viewer:** on-page crop is cosmetic; the pop-up is the real
  viewing surface, so portraits display in full there. Every tile is one
  tap from the uncropped image — the point of this redesign.
- **Deviations from Figma (approved):** on-page chevrons dropped; pop-up
  gains a counter. Crop is 3:2 (Figma 16:9) — one-value switch.
- **Fixed-height edge case:** an extreme portrait at `min(80vh,44rem)`
  could hit `max-width: 92vw` and sit slightly shorter. Rare for villas;
  flag if it bites.
- **Thumb strip caps at 6** (hero + images 1–6); the pop-up holds the full
  set via the counter/nav. Flag if the strip should show more.
- If `gallery.php` differs from what's described, surface it rather than
  reshaping around it.
