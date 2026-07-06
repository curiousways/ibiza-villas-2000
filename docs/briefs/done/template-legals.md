# Legals template — four legal pages, one template

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Greenfield (new shared template + four Pages, against a
signed-off design).
**Design source:** node **`301:9442`** (`10-13 | Legals`), via David's
MCP-shareable copy `3g57x18kqjjNtnJ9SVgTUI` (identical to canonical
`Dj7yiKWK0pNADS7OdjXRKo`). Sub-nodes: title band `301:9445`, tabs `301:10879`,
content column `301:9492`. Node IDs convert hyphen→colon.
**Heads-up:** the hero node contains a **hidden** leftover template-kit frame
(`301:9449`, payroll/CSV mock, `hidden=true`). Ignore it entirely — it is not
part of the design.

---

## Architecture (decided — the Hotel/Airstream pattern)

The frame name "10-13" = **four site-map pages**: Website T&Cs, Privacy
Policy, Cookie Policy, Accessibility Statement. Build them as **four WP Pages
sharing one theme template** (`page-legal.php`), with the design's "tabs" as
**real links between the four pages** (current page's tab active). Not one
page with JS tabs — the footer already links Privacy Policy and T&Cs as
separate destinations, and cookie notices / Gravity Forms privacy links need
direct, individually addressable URLs.

**Body copy = `the_content()`.** Long-form legal text is exactly what the
Classic editor is for — no ACF repeater for the content. The only ACF on this
template is one small field (tab label, below).

---

## Step 0 — Audit first (read, then report)

1. **Page-template pattern** — how `page-ips.php` / `page-contact.php` are
   structured and how their ACF registers. Mirror the lightest of them.
2. **Footer links** — where the footer's "Privacy Policy" and "Terms &
   Conditions" links live (Site Options link repeater per the registrations).
   Report how they're stored; pointing them at the new pages is an editorial
   step, not code — but confirm nothing structural blocks it.
3. **`the_content()` prose styling** — check how villa-overview or the guide
   single post styles rendered editor content (headings, paragraphs, lists,
   links). Reuse/extend that approach for the legal column rather than
   authoring a parallel prose ruleset.
4. **`tokens.css` / `sections.css`** — verify tokens/surfaces. New token check:
   the active-tab underline is **Blue/500 `#00526a`** — confirm it exists in
   `tokens.css` (it's in the design token set); if genuinely absent, surface
   rather than hardcoding.

---

## Standards

As ever: plain CSS custom-property tokens, BEM `ibv-` prefix, no CSS build
step; Classic editor; ACF in PHP; sections declare surface modifiers and never
set their own `padding-block`/`background`/`color`; escape all output.

---

## 1 — Title band (node `301:9445`) — new small section: `title-band`

A text-only page header (no image) on `surface-bg`:

- **Title** — "Legals", **Newsreader 44** (`Desktop/H2` scale), `--black`.
  (The Figma frame is named "Hero Sans" but the title is Newsreader — follows
  the serif-headings convention; no override.)
- **Meta line** — "Last updated: {date}", Uncut Sans 10 (`Paragraph/XXSmall`),
  `--black`, beneath the title (16px gap).

Build it as a small **generic, arg-driven section** (page-builder-ready, like
`image-entries-section`):

```php
ibv_core_title_band( [ 'title' => …, 'meta' => …, 'surface' => 'bg' ] );
```

**Semantics:** the band's "Legals" is a shared label across four pages — render
it as a styled non-heading element; the **document title in the content column
is the page `<h1>`** (see §3). Flagged as a decision in _Notes_.

**Last updated** comes from the page's **modified date**
(`get_the_modified_date( 'F Y' )` → "April 2026") — always true, nothing to
forget to update. No ACF field for it.

---

## 2 — Legal tabs (node `301:10879`) — new section: `legal-tabs`

A horizontal nav of the four legal pages, on `surface-bg`, sitting between the
band and the content:

- Each tab: **Uncut Sans Bold 14**, `--black`, padding 16px × 8px, 8px gap.
- **Active tab** (current page): 2px bottom border, **Blue/500** token.
- Semantics: `<nav aria-label="Legal pages">` + a `<ul>` of links;
  `aria-current="page"` on the active item.
- Mobile: allow horizontal scroll (no wrap-cramping) — simple
  `overflow-x: auto` on the list.

**Tab source — self-maintaining:** query Pages assigned the `page-legal.php`
template (`meta: _wp_page_template`), ordered by `menu_order`. Add a legal
page → assign the template → it appears in the tabs. No hardcoded list, no
Site Options config.

**Tab label:** the design's labels are shorter than the document titles
("Website T&Cs" vs "Website Terms & Conditions"). Add **one ACF field** on the
template — `legal_tab_label` (text) — used for the tab, falling back to the
page title when empty. That's the template's entire ACF group.

Cache note: it's a four-row page query per load; fine as-is. Don't add
transients unless you have a concrete reason.

---

## 3 — Content column (node `301:9492`)

- A single centred reading column, **~618px** max-width, on `surface-bg`
  (rhythm system owns vertical padding).
- Top of column: the **page title as `<h1>`** (Newsreader, sized per the
  design's document headings — verify against the type styles; likely the H2
  scale used as the page h1 here).
- Then **`the_content()`**, with prose styling scoped to the column
  (`.ibv-legal-content` or similar): Newsreader for h2/h3 within the copy,
  Uncut Sans body, sensible list/link/spacing rules. Reuse the existing
  prose approach found in Step 0.3.

---

## 4 — Create the four Pages + wire links

- Create Pages: **Website Terms & Conditions** (`/terms-conditions/`),
  **Privacy Policy** (`/privacy-policy/`), **Cookie Policy**
  (`/cookie-policy/`), **Accessibility Statement** (`/accessibility/`) — all
  on `page-legal.php`, `menu_order` 1–4 in that order, `legal_tab_label` set
  to the design's short labels (Website T&Cs / Privacy Policy / Cookies /
  Accessibility).
- Seed each with the design's placeholder copy ("[…copy to be supplied…]") —
  real legal copy is pending; don't draft actual legal text.
- **Footer links** (editorial, flag in summary): point the footer "Privacy
  Policy" and "Terms & Conditions" entries at the new pages.

---

## Smoke test

1. All four pages render band → tabs → content → footer, matching node
   `301:9442`; title band shows "Legals" + "Last updated: {modified date}".
2. Tabs list all four pages in `menu_order`, short labels correct, active page
   underlined (Blue/500) with `aria-current="page"`.
3. Each page's `<h1>` is its document title; `the_content()` renders with the
   prose styling in a ~618px column.
4. Editing a page in the Classic editor updates the content and bumps the
   "Last updated" date.
5. Adding a hypothetical fifth page on the template makes it appear in the
   tabs without code changes (then delete it).
6. Mobile: tabs scroll horizontally; column is full-width with sane padding.

---

## Notes

- **Decision — band label vs H1.** The shared "Legals" band label is rendered
  as a non-heading; each page's `<h1>` is its document title in the content
  column. Semantically strongest across four URLs while matching the design
  pixel-for-pixel. Flag if you disagree.
- **Decision — modified date as "Last updated".** Automatic and truthful; no
  editorial field to go stale. If David ever wants a manual override, that's a
  one-field addition later.
- **`title-band` is generic on purpose** — new sections are named for what
  they are, not the page that first needed them (per the
  `image-entries-section` precedent), so they slot into the page builder.
- **Slugs** feed the pre-launch redirect map (SEO protection pass 3.15) — the
  four above are sensible defaults; final slugs get confirmed in the go-live
  checklist, and `page_link`-based references follow renames automatically.
- **`listing_note_link_url` tie-in:** the villa-listing hero note link very
  likely targets one of these legal pages once they exist. If David confirms,
  convert that field to `page_link` in a follow-up `fix(acf)` (it was held
  pending exactly this).
- **In-scope quality fixes** — small obvious single-file fixes in files you're
  editing; note in the commit. Surface anything larger.
