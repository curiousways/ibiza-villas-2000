# IPS Page — Ibiza Preservation Society (greenfield page, pure reuse)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Greenfield page, **composition only — no new components or
sections.** Every block already exists. If you find yourself about to write or
extend a section/component, stop — a reuse has been missed.
**Depends on:** the refactor brief *"generalise `about-story` into
`image-entries-section`"* landing first — this page consumes that generalised,
arg-driven component.
**Design source:** node **`326:14416`** (`10 | Ibiza Preservation Society`),
via David's MCP-shareable copy `3g57x18kqjjNtnJ9SVgTUI` (identical to canonical
`Dj7yiKWK0pNADS7OdjXRKo`; node IDs match).

"IPS" = **Ibiza Preservation Society**, a responsible-tourism page. Not the
homepage `ips-panel` teaser that links *to* it.

---

## The page = three existing sections

| Design block | Reuse |
|---|---|
| Hero ("Ibiza Preservation Society" + rule + subtitle) | **`hero` section** (`ibv_core_section_hero()`), reads `hero_*` from the page, compact height — same as About. |
| "What we do" (title + rule + labelled key-value list + image right, off-white) | **`image-entries-section`** (`ibv_core_image_entries_section()`) — the generalised, arg-driven block from the refactor brief. |
| "Want to find out more?" (title + rule + copy + CTA + image left, white) | **`image-text-section`** (`ibv_core_image_text_section()`) — already arg-driven. |

Only the **page template** (composing the three) and its **ACF group** are
authored. No section/component code changes.

---

## Step 0 — Audit first

1. **Page pattern** — how `about` / `concierge` / `contact` register ACF
   (`includes/acf/register-page-*.php`) and their theme templates. Mirror for
   IPS. Confirm how About calls `ibv_core_section_hero()` (compact).
2. **`image-entries-section`** — confirm the post-refactor arg signature
   (`title`, `entries` [label/body], `image`, `image_side`, `surface`,
   `rule_color`). The IPS template passes IPS fields straight in.
3. **`hero`** — `hero_*` field names + `compact` arg (About uses 480px compact;
   IPS the same).
4. **`image-text-section`** — arg signature (arg-driven; pass IPS fields in).
5. **`tokens.css` / `sections.css`** — verify tokens/surfaces (no new CSS).

Report resolved paths.

---

## ACF group (`register-page-ips.php` in `ibv-core`)

Mirror the About page group; bind to the IPS page template. All field names are
free now (every consuming section is arg-driven):

- **Hero:** `hero_image`, `hero_title` (default "Ibiza Preservation Society"),
  `hero_subtitle`.
- **"What we do":** `ips_wwd_title` (e.g. "What we do"), `ips_wwd_entries`
  (repeater: `label` + `body`), `ips_wwd_image`.
- **"Find out more":** `ips_fom_title`, `ips_fom_description`,
  `ips_fom_cta_label`, `ips_fom_cta_url` (default
  `https://ibizapreservation.org`), `ips_fom_image`.

---

## Page template (theme) — compose, in order

1. `ibv_core_section_hero( [ 'compact' => true ] )`.
2. `ibv_core_image_entries_section( [
      'title'      => get_field( 'ips_wwd_title' ),
      'entries'    => get_field( 'ips_wwd_entries' ),  // [label, body] rows
      'image'      => get_field( 'ips_wwd_image' ),
      'image_side' => 'right',
      'surface'    => 'bg',
   ] )`.
3. `ibv_core_image_text_section( [
      'title'       => get_field( 'ips_fom_title' ),
      'description' => get_field( 'ips_fom_description' ),
      'cta_label'   => get_field( 'ips_fom_cta_label' ),
      'cta_url'     => get_field( 'ips_fom_cta_url' ),
      'image'       => get_field( 'ips_fom_image' ),
      'image_side'  => 'left',
      'surface'     => 'white',
   ] )`.

That's the whole template.

---

## Create the page + wire the entry point

- Create the **"Ibiza Preservation Society"** Page, assign the template.
- Point the homepage `ips-panel` CTA at it (`ips_cta_url` — confirm where it
  lives and set, or note for content entry).

---

## Content

The Figma ships **placeholder copy** ("[Content to be supplied — …]") — real
copy is pending (Tina). Seed entries/description with the design's placeholder
text or light filler. Don't invent IPS/IV2000 specifics.

---

## Smoke test

1. Page renders Header → hero → "What we do" (`image-entries-section`) → "Want
   to find out more?" (`image-text-section`) → Footer, matching node `326:14416`.
2. Hero: page H1 + rule + subtitle, compact height.
3. "What we do": the entries `<dl>` (label bold / body), off-white, image right.
4. "Find out more": copy + CTA + image left, white; CTA → `ibizapreservation.org`.
5. **No new sections/components and no changes to the reused ones** — other
   consumers visually unchanged.

---

## Notes

- **Pure reuse.** Three existing sections + a template + an ACF group. The
  generalisation of the "What we do" block lives in its own refactor brief
  (rename `about-story` → `image-entries-section`); this page just consumes it.
- **External CTA** — confirm the button helper sets `rel="noopener"` (and target
  if new-tab) for the ibizapreservation.org link; flag if not.
- **In-scope quality fixes** — small obvious single-file fixes; note in commit.
