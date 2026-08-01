# Page Builder template — compose future pages from existing sections

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Feature — a "Page Builder" page template driven by an ACF
Flexible Content field, letting editors assemble new pages from sections
that already exist. **Strictly composition plumbing: no new section
designs, no new markup patterns, no new CSS beyond zero-or-near-zero
glue.** The one genuinely new render path is the prose layout (Change 4),
and even that reuses the existing `.ibv-prose` styles wholesale.

## Background (verified against the codebase, 2026-08-01)

- Sections are parametrised render functions in
  `mu-plugins/ibv-core/includes/sections/` — e.g.
  `ibv_core_image_entries_section( [ 'title' => …, 'entries' => …,
  'image_side' => 'right', 'surface' => 'bg' ] )` — and page templates
  are pure composition (see `themes/ibv/page-ips.php`, the cleanest
  model: hero + two sections, fields mapped to args, nothing else).
- Two kinds of section exist, and the builder leans on both:
  **content sections** taking full args (`image-entries-section`,
  `image-text-section` component, `title-band`, `hero`), and
  **global bands** that read Site Options and take zero or near-zero
  args (`ibv_core_section_trust_strip()`, `…_testimonials()`,
  `…_newsletter_cta()`, `…_meet_team_teaser()`, `…_ibiza_guide_preview()`,
  `…_why_iv2000()`, `…_fancy_different()`, `…_featured_villas()`;
  `…_three_step( $args )` takes optional overrides;
  `…_short_breaks( [ 'surface' => … ] )`;
  `…_concierge_cross_sell( $heading_override )`).
- ACF is registered in PHP in the mu-plugin (no ACF admin UI, no JSON) —
  find the existing page-template field registrations under
  `includes/acf/` and match their file/register pattern exactly.
- Surface system: sections accept a `surface` slug
  (`'bg' | 'white' | 'tint-teal' | 'tint-gold' | 'tint-blue' | 'forest-green'`)
  with per-section defaults. The rhythm system lives in the section
  wrappers — the builder inherits it for free.
- July lesson, non-negotiable: **internal link fields are `page_link`**,
  never `url`, so references survive environment migration.

## Decisions (agreed with David, 1 Aug 2026)

1. **Menu scope:** content sections + global bands (~15 layouts, table
   below). Dynamic listing machinery and page-specific shells excluded.
2. **Hero is fixed**, not a layout: the template always renders the
   compact hero from the page's standard hero fields; the builder
   controls everything below it.
3. **Editor picks the surface** per section where the section supports
   it — constrained select mirroring the section's real options, default
   matching the section's current default. Never a free-text field.
4. **Prose layout included:** one WYSIWYG layout rendered inside the
   existing `.ibv-prose` styles.

## Standards

- BEM `ibv-` prefix; plain CSS with `var(--ibv-*)` tokens; plain PHP; no
  build step; Classic editor.
- ACF field groups registered in PHP in the mu-plugin, following the
  existing registration files' structure, key conventions, and tab usage.
- Templates compose; the mu-plugin renders. The dispatch loop is
  composition — it lives in the theme template.
- Every layout maps to an existing render function **unchanged**. If an
  arg mapping doesn't line up cleanly, push back — do not modify a
  section function's signature to suit the builder.

## Scope

**In:**
1. `themes/ibv/page-builder.php` — Template Name "Page Builder";
   compact hero + flexible-content dispatch loop.
2. One ACF Flexible Content field (`builder_sections`) registered in
   PHP, located to the Page Builder template, with the ~15 layouts below.
3. Hero field group location extended to cover the Page Builder template
   (reuse the existing hero fields — do not duplicate them).
4. Prose layout: WYSIWYG → existing `.ibv-prose` treatment inside the
   standard section rhythm wrapper.
5. Editor-facing polish: plain-English layout labels, collapsed-state
   labels, per-layout instructions where a layout has a gotcha.

**Out:**
- New section designs, variants, or CSS. The builder composes what
  exists.
- Existing page templates — all untouched; the builder is for *future*
  pages only.
- Villa/listing/booking machinery, legal tabs, article shells,
  enquiry/accommodation sections — page-specific, excluded from the menu.
- Layout preview thumbnails in the ACF picker (nice-to-have; separate
  polish task if ever wanted).

## The layout menu

| # | Layout (editor label) | Renders via | Sub-fields |
|---|---|---|---|
| 1 | Image + entries | `ibv_core_image_entries_section()` | title, entries repeater (label, body), image, image side (left/right), surface |
| 2 | Image + text with CTA | `ibv_core_image_text_section()` | title, description, CTA label, CTA page (`page_link`), image, image side, surface |
| 3 | Title band | `ibv_core_title_band()` | title, meta, surface |
| 4 | Text block (prose) | new thin wrapper (Change 4) | content (WYSIWYG), surface |
| 5 | Three-step process | `ibv_core_section_three_step()` | *(none — reads Site Options; overrides not exposed in v1)* |
| 6 | Trust logos strip | `ibv_core_section_trust_strip()` | *(none)* |
| 7 | Testimonials | `ibv_core_section_testimonials()` | *(none)* |
| 8 | Newsletter sign-up | `ibv_core_section_newsletter_cta()` | *(none)* |
| 9 | Short breaks band | `ibv_core_section_short_breaks()` | surface |
| 10 | Meet the team teaser | `ibv_core_section_meet_team_teaser()` | *(none)* |
| 11 | Concierge cross-sell | `ibv_core_section_concierge_cross_sell()` | heading override (optional text) |
| 12 | Ibiza Guide preview | `ibv_core_section_ibiza_guide_preview()` | *(none)* |
| 13 | Why IV2000 | `ibv_core_section_why_iv2000()` | *(none)* |
| 14 | Fancy something different | `ibv_core_section_fancy_different()` | *(none)* |
| 15 | Featured villas | `ibv_core_section_featured_villas()` | *(none)* |

Before finalising each layout, **read the section function's docblock**
and mirror its actual args — the table above is the map, the docblocks
are the truth. Where a section's `surface` support differs from the
six-slug list, mirror what it actually accepts.

## File list

| File | Action |
|---|---|
| `themes/ibv/page-builder.php` | **Create** |
| `mu-plugins/ibv-core/includes/acf/register-builder-fields.php` (name per existing convention) | **Create** |
| `mu-plugins/ibv-core/includes/sections/prose-section/prose-section.php` (or the location the agent judges consistent) | **Create** (thin wrapper only) |
| Existing hero field-group registration | **Edit** (add Page Builder template to location rules) |
| `mu-plugins/ibv-core/ibv-core.php` | **Edit** (require new files; bump `IBV_CORE_VERSION`) |

## Changes

### 1. `page-builder.php`

Composition-only, modelled on `page-ips.php`:

```php
<?php
/**
 * Template Name: Page Builder
 *
 * Editor-composed page: fixed compact hero, then sections assembled
 * from the `builder_sections` flexible content field. Composition
 * only — every layout dispatches to an existing ibv-core section.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_hero( [ 'compact' => true ] );

	if ( have_rows( 'builder_sections' ) ) :
		while ( have_rows( 'builder_sections' ) ) :
			the_row();

			switch ( get_row_layout() ) {
				case 'image_entries':
					ibv_core_image_entries_section( [ /* map sub-fields → args */ ] );
					break;
				// … one case per layout, mapping get_sub_field() values
				// to the section's documented args. Zero-config bands
				// are bare function calls.
			}

		endwhile;
	endif;

endwhile;

get_footer();
```

An empty builder field renders hero-only — valid, no notices.

### 2. ACF registration

- Flexible content field `builder_sections`, one layout per table row,
  keys and structure following the existing `register-*-fields.php`
  conventions. Location: page template `page-builder.php`.
- Sub-fields mirror section args exactly: repeaters for entries, image
  fields returning what the section expects (check whether sections
  take IDs or ACF arrays — mirror the existing per-template field
  configs for the same sections), **`page_link` for the CTA target**,
  select fields for `image_side` and `surface` (constrained to the
  section's real options, correct per-section default).
- Editor UX: layouts labelled as in the table (plain English, no
  function names); block display; collapsed state showing the layout's
  title sub-field where one exists; `button_label` "Add section".
  Per-layout instructions only where behaviour isn't obvious (e.g. the
  zero-config bands: "Content for this band is managed in Site
  Options").

### 3. Hero fields on builder pages

Extend the existing hero field group's location rules to include the
Page Builder template so editors get the standard hero title / subtitle
/ image fields. Reuse — do not clone the group.

### 4. Prose layout wrapper

The one new render path: a minimal section function (match house
naming, e.g. `ibv_core_prose_section( [ 'content' => …, 'surface' => … ] )`)
that outputs the WYSIWYG content inside the standard section/rhythm
wrapper with the existing `.ibv-prose` class. Find where `.ibv-prose`
is applied today (article single / legal content) and reuse that exact
treatment — **no new CSS** unless a one-line width constraint is
genuinely missing, in which case flag it rather than improvising.

## Verification

Admin:
- [ ] New template appears in the template dropdown; selecting it swaps
  the editor to hero fields + the builder field, no notices.
- [ ] Every layout addable, reorderable, deletable; collapsed labels
  readable; surface/image-side selects show only real options.

Frontend (build one test page using **every layout at least once**):
- [ ] Each layout renders identically to its counterpart on the pages
  that already use that section (side-by-side spot check: IPS for
  image-entries/image-text, homepage for the bands).
- [ ] Surface choices apply; adjacent same-surface sections don't break
  the rhythm system (visual check).
- [ ] Prose layout typography matches article/legal prose exactly.
- [ ] Empty builder field → hero-only page, no notices; every layout
  with empty optional fields renders sanely or degrades silently,
  matching each section's existing empty-state behaviour.
- [ ] Existing pages (home, about, IPS, concierge, contact, legal,
  guide) completely unaffected.
- [ ] `page_link` CTA resolves on the current environment.

## Notes

- **Why these 15:** dynamic listing machinery (villa listing grid,
  article grid, special offers, related articles) and page shells
  (villa detail, booking confirmation, legal tabs, accommodation,
  enquiry sections) are wired to specific queries, templates, or page
  context — composing them arbitrarily invites broken states. The
  bands + content sections cover the actual use case: future marketing
  / landing / info pages that feel native.
- **Extension pattern** (this brief is the doc future briefs cite): a
  new layout = one `case` in the template switch + one layout in the
  registration + a row in the table above. A section must be
  parametrised and context-free before it earns a layout.
- Three-step overrides (`eyebrow` / `title` / `steps`) deliberately not
  exposed in v1 — the global band is the point. Expose later if a real
  page needs it.
- Site-Options bands render whatever is currently configured — an
  editor can't break them from the builder, which is the feature.
- Deliberately deferred: layout preview images, per-layout max
  instances, any "spacer" or raw-HTML layout (the answer to those
  requests is no).
