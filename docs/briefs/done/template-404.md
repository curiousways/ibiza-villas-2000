# 404 page — hero rebuild to design

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Greenfield (design-match) for the 404 template, carried by a
small generalisation of the existing `hero` section. The hero generalisation
itself is a **visual no-op for existing consumers** — the home page hero must
render identically before and after.

**Design source:** Figma `IBZ002_Ibiza-2000_Website`, node **368-5143**
("14 | 404"). Reference regions within it: hero container `368:5145`, title
`368:5501`, rule `368:5153`, sub-line `368:5154`, button `368:5503`.

## Standards

Work entirely within the existing design system:

- All colours, spacing, type, radii, and motion come from tokens — canonical
  source `mu-plugins/ibv-core/assets/css/tokens.css`. No hardcoded values.
- Section rhythm/surface conventions live in
  `mu-plugins/ibv-core/assets/css/sections.css`. The hero is a documented
  **chrome-level exemption** — it owns its own padding model and does not take
  a `--surface-{X}` modifier. Do not "fix" that.
- Buttons only via `ibv_core_button()`
  (`mu-plugins/ibv-core/includes/components/button/button.php`) — never raw
  `<a class="ibv-button">`.
- Content imagery via `ibv_core_image()` / `wp_get_attachment_image_url()`
  patterns already in use; icons via `ibv_core_icon()` (the button helper
  already handles its own arrow).
- BEM naming with the `ibv-` prefix throughout; component CSS co-located and
  registered in `mu-plugins/ibv-core/includes/shared-assets.php` (the
  `ibv-section-hero` handle already exists — no asset wiring needed).
- Editorial copy from explicit ACF fields registered in PHP — no
  `the_content()`, no new field groups in the admin UI.
- No new dependencies, no build step.

## Scope

Rebuild the theme's placeholder `404.php` (currently a bare
`ibv_core_section_heading()` + button on a plain section) as the designed 404:
standard header, full-height image hero with title / gold rule / sub-line /
primary CTA, standard footer. Header and footer are existing site chrome and
are **out of scope** — `get_header()` / `get_footer()` only.

The hero is not a new section. `ibv_core_section_hero()`
(`mu-plugins/ibv-core/includes/sections/hero/hero.php`) is already this exact
layout (720px min-height, cover image via `--ibv-hero-image`, dark overlay,
centred copy: title → gold rule → subtitle). Its only limitation is that it
sources content from ACF page fields (`hero_image` / `hero_title` /
`hero_subtitle`) on the queried post — and a 404 has no queried post. So this
brief: (1) adds optional content-override args to the hero (same pattern as
the recent `about-story` → `image-entries-section` generalisation), (2) adds a
small "404 page (shared)" field group on Site Options for the image and copy,
(3) rewrites `404.php` as pure composition with hardcoded i18n fallbacks so
the page never renders empty.

Out of scope: search UI on the 404 (not in the design), any change to hero
CSS beyond nothing (none is expected), header/footer, other templates.

## Files to create / edit

```
EDIT  wp-content/mu-plugins/ibv-core/includes/sections/hero/hero.php        (content-override + cta args)
EDIT  wp-content/mu-plugins/ibv-core/includes/acf/register-globals-content.php  (add 404 field group)
EDIT  wp-content/themes/ibv/404.php                                          (rewrite as composition)
```

No new files. No changes to `shared-assets.php` or `bootstrap.php` — the hero
section and its style handle are already registered and required.

## Hero generalisation (`hero.php`)

Extend the `$args` contract with four optional keys. All default to `null` /
absent, and when absent behaviour is byte-for-byte what it is today — that is
the visual no-op contract for the home page.

```php
/**
 * @type int|array|null $image      Optional. Attachment ID or ACF image array.
 *                                  Overrides the `hero_image` ACF lookup.
 * @type string|null    $title      Optional. Overrides the `hero_title` ACF lookup.
 * @type string|null    $subtitle   Optional. Overrides the `hero_subtitle` ACF lookup.
 * @type array|null     $cta        Optional. Args array passed to ibv_core_button();
 *                                  rendered inside the copy block, after the subtitle.
 */
```

Implementation points:

- Resolve content as `null !== $args['title'] ? $args['title'] : get_field( 'hero_title' )`
  (same for subtitle and image). Do not call `get_field()` when an override is
  supplied — on a 404 there is no post context.
- The existing image resolution only handles an ACF array with an `ID` key.
  Extend it to also accept a bare attachment ID (mirror the ID-normalisation at
  the top of `ibv_core_image()` in
  `mu-plugins/ibv-core/includes/components/image/image.php`). Keep the
  `ibv-hero` image size and the `--ibv-hero-image` custom-property mechanism
  exactly as they are.
- Render the CTA as a direct child of `.ibv-section-hero__copy`, after the
  subtitle conditional:

```php
<?php if ( ! empty( $args['cta'] ) && is_array( $args['cta'] ) ) : ?>
	<?php ibv_core_button( $args['cta'] ); ?>
<?php endif; ?>
```

  The copy block is a column flex with the system gap; the button picks that
  up. Do not use the existing `after_copy` slot for this — that slot renders
  outside the copy block at a much larger gap (it exists for the home-page
  search panel) and would not match the design's tight title/rule/text/button
  cluster.
- No CSS changes to `hero.css` are expected. The existing title
  (`--ibv-fs-display`, display face, on-dark), gold rule (`.ibv-rule--gold`,
  defined in `mu-plugins/ibv-core/assets/css/layout.css`), subtitle
  (`--ibv-fs-h4`), overlay, and 720px min-height are the signed-off hero
  system and the 404 design uses the same treatment. Design-match against the
  Figma node, but resolve any perceived deltas at the token/system level —
  if you believe a delta is real, show the comparison rather than patching
  one-off values into the section.

## ACF fields (`register-globals-content.php`)

Append a fourth field group to `ibv_register_globals_content_fields()`,
following the file's existing style (same location array `$loc_option`, next
`menu_order`, `show_in_rest => false`):

```php
acf_add_local_field_group(
	array(
		'key'    => 'group_ibv_global_error404',
		'title'  => __( '404 page (shared)', 'ibv' ),
		'fields' => array(
			array(
				'key'           => 'field_ibv_global_error404_image',
				'label'         => __( 'Background image', 'ibv' ),
				'name'          => 'error404_image',
				'type'          => 'image',
				'return_format' => 'array',
				'instructions'  => __( 'Full-bleed photo behind the 404 message. Landscape, ideally 2560px+ wide.', 'ibv' ),
			),
			array(
				'key'          => 'field_ibv_global_error404_title',
				'label'        => __( 'Title', 'ibv' ),
				'name'         => 'error404_title',
				'type'         => 'text',
				'instructions' => __( 'Falls back to "It appears this page has gone off-season" when empty.', 'ibv' ),
			),
			array(
				'key'          => 'field_ibv_global_error404_subtitle',
				'label'        => __( 'Sub-line', 'ibv' ),
				'name'         => 'error404_subtitle',
				'type'         => 'text',
				'instructions' => __( 'Falls back to "We\'re afraid something has gone wrong with this link." when empty.', 'ibv' ),
			),
		),
		'location'              => $loc_option,
		'menu_order'            => 6,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
		'show_in_rest'          => false,
	)
);
```

Field names use the `error404_` prefix (matching WP's `error404` body class)
rather than a leading digit. No CTA fields — the button is fixed chrome, not
editorial.

## Template (`404.php`)

Full replacement:

```php
<?php
/**
 * 404 template.
 *
 * Composition only: standard chrome + the shared hero section with
 * content-overrides sourced from Site Options ("404 page (shared)"),
 * falling back to hardcoded copy so the page never renders empty.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$error404_image    = get_field( 'error404_image', 'option' );
$error404_title    = (string) get_field( 'error404_title', 'option' );
$error404_subtitle = (string) get_field( 'error404_subtitle', 'option' );

ibv_core_section_hero(
	[
		'image'    => $error404_image ? $error404_image : 0,
		'title'    => $error404_title ? $error404_title : __( 'It appears this page has gone off-season', 'ibv' ),
		'subtitle' => $error404_subtitle ? $error404_subtitle : __( 'We\'re afraid something has gone wrong with this link.', 'ibv' ),
		'cta'      => [
			'url'     => home_url( '/' ),
			'label'   => __( 'Go back', 'ibv' ),
			'variant' => 'primary',
		],
	]
);

get_footer();
```

Note the image override passes `0` (not `null`) when the option is unset —
`null` means "fall back to the page-field ACF lookup", which must not happen
on a 404. With `0` / empty image the hero renders on its forest-green fallback
background, which is the correct degraded state.

## Notes

- **"Go back" destination.** The design labels the CTA "Go back"; implement it
  as a plain link to `home_url( '/' )`. A literal `history.back()` is
  unreliable on a 404 (direct entry / external links have no useful history)
  and would need JS for no real gain. Flagging in case the label-vs-behaviour
  mismatch bothers anyone — recommended: keep the home link.
- **Line break in the title.** The Figma title wraps as "It appears this page
  has / gone off-season". Do not hardcode a `<br>` — the hero title's existing
  `max-width: 22ch` produces a near-identical wrap and stays honest for
  editor-supplied titles.
- **Overlay.** Figma draws `rgba(0,0,0,0.5)` over the photo; the hero system
  uses forest-green-deep at 0.55 (`hero.css::before`). That is the signed-off
  site-wide hero treatment — keep it. Do not add a one-off black overlay.
- **HTTP status.** WordPress already sends 404 headers for this template — do
  not add `status_header()` calls.
- **Regression check is part of the job.** The home page (front-page hero with
  search slot) and any other `ibv_core_section_hero()` callers must render
  identically after the arg changes. Grep for `ibv_core_section_hero(` and
  check each call site.
- **Content entry is manual, not seeded.** After deploy, the signpost photo
  from the Figma frame needs exporting/uploading and selecting under Site
  Options → "404 page (shared)". The template's fallbacks cover copy until
  then; the image has no hardcoded fallback by design.
- **Codebase claims.** If any token, class, or helper referenced here doesn't
  match what you find, verify against the canonical files named in Standards
  and paste the grep/read evidence before substituting.

## Smoke test

1. Visit a garbage URL (e.g. `/definitely-not-a-page/`) on staging with the
   Site Options fields populated. Compare against Figma node 368-5143: header
   chrome, 720px hero with photo + overlay, centred Newsreader title on
   off-white, 100px gold rule, sub-line, sage "Go back" button with arrow,
   footer.
2. Confirm the response status is 404 (curl -I).
3. Clear the Site Options fields and reload: fallback copy renders, hero shows
   the forest-green fallback background, nothing is empty or warns.
4. Load the home page and compare its hero to production/staging before the
   change: identical (visual no-op contract).
