# Refactor — generalise `about-story` into a reusable `image-entries-section`

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Refactor (rename + generalise + migrate the one consumer).
**Goal:** turn the About-specific `about-story` section into a generic,
arg-driven, page-builder-ready block — the same shape `image-text-section`
already has. **Zero visual change** anywhere.

**Why now:** these section components are heading toward a page builder that
composes them. `image-text-section` is already a good citizen (generic name,
arg-driven, `surface` / `image_side` args). `about-story` is the laggard — it's
named for one page and reads its own ACF. Generalise it before the builder and
before a second consumer (the IPS page) lands on it.

---

## The rename

`about-story` → **`image-entries-section`** (image + heading + rule + a
definition list of labelled entries — sibling to `image-text-section`).

> Name is David's to veto — it's chosen to parallel `image-text-section`
> (`image-{text|entries}-section`). If you'd prefer another, flag before
> renaming; otherwise proceed with `image-entries-section`.

Rename consistently — directory, file, function, CSS file, style handle, and
BEM classes:

| Now | After |
|---|---|
| `includes/sections/about-story/about-story.php` | `includes/sections/image-entries-section/image-entries-section.php` |
| `includes/sections/about-story/about-story.css` | `…/image-entries-section/image-entries-section.css` |
| `ibv_core_section_about_story()` | `ibv_core_image_entries_section()` |
| style handle `ibv-section-about-story` | `ibv-image-entries-section` |
| BEM `ibv-section-about-story__*` | `ibv-image-entries-section__*` |

**Grep the whole repo** (mu-plugin + theme) for every old name above and update
all references. Update the `require_once` in `bootstrap.php` and the handle
registration in `shared-assets.php`.

---

## The generalisation (make it arg-driven, like `image-text-section`)

Move the ACF reads **out** of the section and into the caller. New signature,
mirroring `image-text-section`'s conventions:

```php
/**
 * @param array $args {
 *   @type string    $title       Heading (rendered h2).
 *   @type array     $entries     List of [ 'label' => '', 'body' => '' ].
 *   @type int|array $image       Image ID or ACF array.
 *   @type string    $image_side  'left' | 'right'. Default 'right'.
 *   @type string    $surface     Surface slug. Default 'bg'.
 *   @type string    $rule_color  Optional accent for the rule.
 * }
 */
function ibv_core_image_entries_section( array $args = [] ) { … }
```

- Keep the markup **byte-identical** to today apart from the class renames: same
  `<h2>` + gold rule + `<dl>` of `<dt>`/`<dd>` (label/body) + image, same
  escaping, same empty-guards (bail if no entries and no title and no image).
- **Surface** becomes an arg (default `'bg'` to preserve About) instead of the
  hard-coded `surface-bg` — validate against the same surface list
  `image-text-section` uses.
- **`image_side`** becomes an arg (default `'right'` to preserve About's
  current copy-left/image-right). Add the side modifier + CSS order the same
  way `image-text-section` does it (`--image-left` / `--image-right`). This is
  the only net-new CSS; if it's not trivially mirror-able, flag rather than
  improvise.
- Sub-field names stay `label` / `body` — **no ACF field changes**.

---

## Migrate the one consumer (About page)

The About template currently calls `ibv_core_section_about_story()` (which read
the fields itself). After the refactor it reads the fields and passes them in:

```php
ibv_core_image_entries_section( [
    'title'   => get_field( 'about_story_title' ),
    'entries' => get_field( 'about_story_entries' ), // [label, body] rows
    'image'   => get_field( 'about_story_image' ),
    // image_side 'right', surface 'bg' = defaults → unchanged
] );
```

- The `about_story_*` ACF field group is **unchanged** (names + keys stay).
  Only the call site moves the reads.
- Find the About template in the theme (Step 0) — that's where the call lives.

---

## Step 0 — Audit first

Read and report before editing:
1. `includes/sections/about-story/about-story.php` + its `.css` (current
   markup/classes — you'll preserve them under new names).
2. `bootstrap.php` require line; `shared-assets.php` handle registration.
3. The **About page template** in the theme that calls
   `ibv_core_section_about_story()` (the migration target).
4. `includes/components/image-text-section/image-text-section.php` — the
   arg-driven sibling whose `surface` / `image_side` handling you're mirroring.
5. A repo-wide grep for `about_story`, `about-story`, `section_about_story`,
   `ibv-section-about-story` — report every hit so nothing is missed.

---

## Smoke test

1. The About page "Our Story" section is **pixel-identical** to before (heading,
   gold rule, the `<dl>` entries, image right, off-white surface).
2. No orphaned references to any old name remain (grep clean).
3. The style handle loads under `ibv-image-entries-section`; the section renders
   on About via the new arg-driven function.
4. `image_side => 'left'` flips copy/image order correctly (quick manual check),
   `image_side => 'right'` matches About.
5. Nothing else on the site changed.

---

## Notes

- **Pure generalisation — no behaviour change.** Same block, made arg-driven and
  generically named so the page builder (and the IPS page next) can compose it.
- **Name** `image-entries-section` is a recommendation; flag if you'd rather
  something else before doing the rename.
- **Next:** the IPS page brief consumes this via
  `ibv_core_image_entries_section()` with args — it depends on this landing
  first.
- **In-scope quality fixes** — small obvious single-file fixes in files you're
  touching; note in the commit. Surface anything larger.
