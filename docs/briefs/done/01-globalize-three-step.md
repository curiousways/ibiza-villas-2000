# Globalize the three-step process section

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Refactor (visual no-op). The three-step section currently registers
its ACF fields on the homepage and reads them from the queried post.
After this brief, the same fields live in Site Options and the
section reads from there. Homepage rendering is unchanged.

This brief is a prerequisite for the About page sequence — the
About page will call the same section function and pick up the
globally-sourced fields.

The companion change for testimonials is already done: testimonials
ACF lives in `register-site-options-content.php` and the section
reads `get_field( 'testimonials', 'option' )`. This brief brings the
three-step section into the same shape.

## Standards

- ACF field groups registered in PHP under
  `mu-plugins/ibv-core/includes/acf/`
- Sections under `mu-plugins/ibv-core/includes/sections/{name}/`
- Field keys preserved across moves where data should survive; when
  changing keys, document the rationale
- Site Options is the canonical home for content that's reused
  across pages (testimonials, three-step, accreditations, etc.)

## Files

```
EDIT  mu-plugins/ibv-core/includes/acf/register-site-options-content.php
EDIT  mu-plugins/ibv-core/includes/acf/register-page-home.php
EDIT  mu-plugins/ibv-core/includes/sections/three-step/three-step.php
```

## Change 1 — Add three-step fields to Site Options

In `register-site-options-content.php`, locate the existing
"Shared content (testimonials, cross-sell)" tab where testimonials
already lives. Add three new fields **before** testimonials inside
the same tab:

- `three_step_intro_eyebrow` — text. Default value `Simple & Swift`.
- `three_step_intro_title` — text. Default value `Our 3-Step Process`.
- `three_step_steps` — repeater with subfields:
  - `title` — text
  - `text` — wysiwyg (basic toolbar — match what the existing
    homepage registration uses; do not silently widen)

Field keys: use a `field_ibv_globals_three_step_*` prefix. The
existing homepage keys are `field_ibv_page_home_three_step_*`; new
keys signal these are now-global fields, not homepage-scoped. Keep
field NAMES (`three_step_intro_eyebrow`, `three_step_intro_title`,
`three_step_steps` and the subfield names) identical to current —
the section's `get_field()` calls reference names, not keys, so the
section continues to work after the move.

Default values on eyebrow + title preserve the existing fallback
behaviour for free, allowing the explicit `if ( ! $eyebrow )`
defaults inside the section function to be left in place as
defensive rendering.

Verify the Shared content tab structure matches existing patterns
for the testimonials block — placement, spacing, no other fields
disturbed.

## Change 2 — Section reads from Site Options

In `sections/three-step/three-step.php`, change the three
`get_field()` calls to read from Site Options:

```php
$eyebrow = get_field( 'three_step_intro_eyebrow', 'option' );
$title   = get_field( 'three_step_intro_title', 'option' );
$steps   = get_field( 'three_step_steps', 'option' );
```

Nothing else in the file changes.

## Change 3 — Strip three-step fields from homepage ACF

In `register-page-home.php`, remove the entire `three_step` tab and
its child fields (the tab field plus the three field definitions
that live under it — `three_step_intro_eyebrow`,
`three_step_intro_title`, `three_step_steps` and its subfields).

If the tab structure breaks gracefully when one tab is removed, fine.
If the surrounding tabs need re-keyed or re-ordered to avoid empty
sections, do so — but don't change other fields' content.

## Verification

1. **Admin smoke test:**
   - Site Options screen, "Shared content" tab now shows
     three-step fields above the existing testimonials repeater
   - Homepage edit screen no longer shows three-step fields
   - Existing testimonials data is untouched

2. **Re-populate three-step data in Site Options** (existing
   homepage data does NOT auto-migrate — staging is throwaway, just
   re-enter):
   - Eyebrow: `Simple & Swift`
   - Title: `Our 3-Step Process`
   - Steps: three rows matching the design (Search & Find, Request
     to Book, Confirmation — copy from existing homepage if it's
     still there or from the About design PDF).

3. **Frontend smoke test (homepage):**
   - Three-step section renders identically to before — gold-tint
     surface, three numbered cards, intro header
   - No content drift, no broken fields, no console errors
   - Source class on root is unchanged:
     `ibv-section-three-step ibv-section ibv-section--surface-tint-gold`

4. **No About page work in this brief.** The About page will
   compose this section in a later brief.

## Notes

- **Why move three-step but not testimonials.** Testimonials was
  already global in the codebase (reading from `'option'`) before
  this brief. This brief only addresses the section that wasn't.
  Both being in Site Options after this lands.
- **Why new field keys.** Field names stay the same so the
  section's `get_field()` calls work without modification. Field
  keys change to signal scope — these are now-global definitions,
  not homepage-scoped. ACF stores values keyed by name on Site
  Options anyway, so old data on the homepage post wouldn't be
  reachable from `'option'` regardless of key.
- **Default values on eyebrow + title.** Adding ACF defaults
  (`Simple & Swift` / `Our 3-Step Process`) means new installs
  render correct copy without an admin needing to populate fields.
  The defensive `if ( ! $eyebrow )` fallbacks in the section
  function stay — belt and braces, no harm.
- **Testimonials wasn't touched.** Confirmed by grep: testimonials
  ACF is in `register-site-options-content.php` and the section
  reads from `'option'`. Brief explicitly does not edit either.
