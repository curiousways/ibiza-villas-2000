# Concierge Page — Brief 1: Foundation

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Greenfield (new page, against a signed-off Figma design).
**Design source:** Figma file `Dj7yiKWK0pNADS7OdjXRKo`, page frame
`06 | Concierge` = node **`1:6981`**. Sub-nodes referenced inline below.
Node IDs from the URL convert hyphen→colon (`1-6981` → `1:6981`).

This is brief **1 of a planned sequence** (see _Roadmap_ at the end). It
stands up the page on correct architecture and gets it end-to-end testable.
It deliberately does **not** build the contact form or the image-and-text
band — those are stubbed as placeholders and filled in later briefs.

---

## Scope

Create the Concierge page: WP page template, its ACF field group, and the
two **content-only** sections — the **hero** and the **concierge-services
cards grid** — both matched to the Figma design. The cards render from a
**Site Options global repeater** (single source of truth, see _Concierge
services source_ below). The **contact section** and the **image-and-text
band** are stubbed as empty placeholder sections so the page is complete
top-to-bottom and testable; they are filled in briefs 2–4.

Header and footer are existing site chrome — do not touch them.

**No rework constraint:** the cards are built on the global-repeater
architecture from the start, so nothing here gets ripped out by the later
globalisation of the villa-detail concierge section (flagged in _Notes_).

---

## Step 0 — Audit first (read, then report, before writing)

I can't read the repo from where this brief is written, so the canonical
paths below are described by role, not asserted by path. **Before building,
read these and report back the exact paths/names you'll use.** If any
assumption here is wrong, surface it before proceeding.

Read and report:

1. **About page** — its template file, how it's registered, and its ACF
   field group registration. The About page used this same greenfield
   pattern; **mirror it** for the Concierge page (file location, naming,
   registration approach). This is the precedent — match it rather than
   inventing a new pattern.
2. **Site Options** registration (the ACF Options/Global registration
   file) — where sitewide fields live. The concierge-services repeater and
   the contact details (phone/email/WhatsApp) belong here. Report whether
   contact details already exist as global fields.
3. **Villa-detail concierge section** (shipped) — read how it sources its
   service content (inline markup? a partial? ACF?). Report what it does.
   Do **not** modify it in this brief; this read informs the migration
   flagged in _Notes_.
4. **Existing page-hero** partial/section (About / Special Offers / villa
   listing heroes) — report its markup and whether it already supports a
   centred title → hairline divider → subtitle stack. The concierge hero
   reuses it if it does; if not, note the delta (a small variant, handled
   here only if trivial — otherwise surface).
5. **Image-and-Text component** (used elsewhere, e.g. homepage) — confirm
   it exists and report its partial name + the args/ACF it expects. Brief 4
   will feed it this page's content; just confirm it's there now.
6. **Icon helper** — the project's icon function for vendored Lucide glyphs.
   Report its exact signature. Cards use it for the 80px service icon — no
   inline `<svg>`, no CDN.
7. **Image helper** — the responsive-image function. Hero background and any
   content imagery use it (raw `<img>` only for fixed brand assets).
8. **`assets/css/tokens.css`** and **`assets/css/sections.css`** — confirm
   the colour tokens and the surface modifier set. Do not copy values into
   this brief; verify against these files as you build.

Report the resolved paths in your session summary so they're captured.

---

## Standards

Work entirely within the existing design system:

- All colours use design tokens from **`tokens.css`** — no hardcoded hex.
  Map each Figma variable to its token and **verify the token name against
  `tokens.css`** before using it (don't substitute from memory — if a token
  looks missing, grep the source file and paste the result rather than
  inventing a name).
- Every body section root declares **both** the base rhythm class and a
  surface modifier: `class="ibv-section-{name} ibv-section ibv-section--surface-{X}"`.
  Surfaces and rhythm belong to the system (`sections.css`), not to the
  section. Section CSS must not set `padding-block`, `background`, or
  `color` on the root. Match each section's surface to its Figma fill.
- Hero and footer are chrome — exempt from the rhythm system; the hero owns
  its own padding/height.
- Typography: titles/headings default to **Newsreader** (serif) per the
  signed-off design. Body copy is **Uncut Sans**.
- BEM class names, `ibv-` prefix, plain CSS, **no build step**, Classic
  editor, ACF registered in PHP.
- Icons via the icon helper; content images via the image helper.
- No new dependencies.
- Escape/sanitise all ACF output (`esc_html`, `esc_url`, `esc_attr`,
  `wp_kses_post` for any rich text).

---

## Figma → token mapping (verify names against `tokens.css`)

| Figma variable | Hex | Use |
|---|---|---|
| Off White | `#f9f8f4` | hero text; page/contact surface |
| Black | `#343434` | card title |
| Forest/Dark | `#055353` | card description; contact heading/details |
| Sage/500 | `#00897e` | primary button fill |
| White | `#ffffff` | image-text surface; card bg |
| Grey | `#939292` | form placeholder text (later brief) |
| Light Grey | `#bfc8c8` | form input border (later brief) |

Type styles in play: Desktop/H1 (Newsreader 64/1.0), Desktop/H2 (Newsreader
44/1.1), Desktop/H5 + Mobile/H4 (Newsreader 20/1.2), Paragraph/Large (Uncut
Sans 18/1.4), Paragraph/Small (Uncut Sans 14/1.2). Use the project's
existing type system — don't inline font declarations if a type utility/token
exists.

---

## Files to create / edit

Exact paths to be confirmed in Step 0 by mirroring the About page. Expected
shape:

```
CREATE  theme: page template for Concierge (mirror About page template)
CREATE  theme: section partial — hero (or reuse existing page-hero partial)
CREATE  theme: section partial — concierge services cards grid
CREATE  theme: CSS for the cards grid + card (assets/css/…)
EDIT    ibv-core: ACF field-group registration — Concierge page fields
EDIT    ibv-core: Site Options registration — add `concierge_services`
        repeater (+ contact details fields if not already global)
```

Hero CSS may already exist if reusing the page-hero partial. Add only what's new.

---

## Concierge services source (architectural decision — built in)

The nine service cards are the same concept of "concierge services" that the
villa-detail concierge section also presents. To avoid two divergent copies,
**the services live once in Site Options** and every surface renders from
there.

Register on Site Options a repeater **`concierge_services`** with sub-fields:

- `icon` — text or select: the **Lucide glyph name** for the icon helper
  (e.g. `luggage`, `car`, `utensils`). Not a raw SVG.
- `title` — text (e.g. "Pre-Arrival Shopping").
- `description` — textarea, plain text, ~2–3 rows.

Seed it with the **nine** services from the Figma cards. Read each card node
for its title/description (icon glyph is your pick — see below). Card nodes:
`1:6996`, `1:6997`, `1:6998`, `1:7000`, `1:7002`, `1:7003`, `1:7005`,
`1:7006`, `1:7007`. Known sample (node `1:6996`): **Pre-Arrival Shopping** —
"Arrive to a stocked villa. We'll have everything ready before you land."
The Figma copy is placeholder-grade; real copy lands over summer (Tina).
Seeding real-ish villa-accurate filler is fine; lorem is not.

**Icon mapping is your call but not silent:** pick the closest Lucide glyph
per service via the icon helper and **list your nine picks in the session
summary for review**. Don't guess and bury it.

The Concierge page cards grid loops this repeater. (The villa-detail
concierge section is **not** migrated to read from it in this brief — see
_Notes_.)

---

## Section 1 — Hero (`1:6983`)

Full-bleed image hero, **400px** tall, chrome-exempt (owns its padding/
height — not part of the `ibv-section` rhythm system; it may still carry the
`ibv-section` class if the existing hero pattern does, but its CSS overrides
`padding-block` directly).

Structure, centred vertically and horizontally:

- Background image (full-bleed, `object-fit: cover`) via the image helper.
- Dark overlay: `rgba(0,0,0,0.2)`.
- Title — **H1, Newsreader 64**, off-white, centred: "Concierge Services".
- A centred **~100px hairline divider** beneath the title.
- Subtitle — **Paragraph/Large, Uncut Sans 18**, off-white, centred: "From
  airport transfers to pre-arrival shopping — we'll take care of it."

Content from the page's ACF: `hero_image`, `hero_title`, `hero_subtitle`.

**Reuse the existing page-hero partial** if it supports the
title→divider→subtitle stack (Step 0.4). The hairline divider is a recurring
motif (also on the cards and the image-text band) — if a shared divider
treatment exists, reuse it; if not, a thin `<hr>`/`<span>` rule styled to the
design is fine. Don't introduce a one-off where a shared treatment exists.

---

## Section 2 — Concierge services cards grid (`1:6993`)

> Note: this frame is **mislabelled "Our Ibiza Guide Section"** in Figma. It
> is the concierge-services grid. Ignore the label.

Section root: `class="ibv-section-concierge-services ibv-section ibv-section--surface-{X}"`
— set `{X}` to match the Figma fill (cards read as white tiles on the page's
off-white background → likely `surface-bg`; **verify the fill and pick the
matching modifier from `sections.css`**).

Layout:
- **3-column** grid on desktop, collapsing to 2 → 1 on smaller viewports.
- Inter-card gap per Figma (~16px). Cards equal height.

Card markup (BEM, e.g. `.ibv-concierge-card`), per `1:6996`:
- White background, 1px hairline border (Figma `#f3f4f6` — map to the
  nearest border token; verify), `border-radius: 4px`, subtle shadow
  (`0 1px 2px rgba(0,0,0,0.05)`), **32px** padding.
- **Icon** — 80px, via the icon helper (Lucide glyph from the repeater's
  `icon` field). Give it an accessible treatment (decorative → `aria-hidden`,
  since the title carries meaning).
- **Title** — Newsreader 20 (`Mobile/H4`/`Desktop/H5`), token `--black`.
- **Hairline divider** — ~50px, beneath the title (same motif as hero).
- **Description** — Uncut Sans 14 (`Paragraph/Small`), token `--forest-dark`.

Each card maps one `concierge_services` row. Escape all output.

**Card reuse:** if Step 0.3 finds the villa-detail concierge already has a
reusable card partial, use it (extend with an arg if it's close but not
exact). If the villa-detail markup is inline/bespoke, build the card here as
a clean partial and **flag in your summary** whether it should become the
shared canonical concierge card (likely yes — but that consolidation is a
follow-up, not this brief).

---

## Section 3 — Contact (STUB — do not build the form)

Render an empty placeholder section so the page is structurally complete:

```html
<section class="ibv-section-concierge-contact ibv-section ibv-section--surface-{X}">
  <!-- TODO Brief 2: contact block + concierge enquiry form. Spec in brief Notes. -->
</section>
```

Set `{X}` to the Figma fill of node `1:7008` (off-white → likely
`surface-bg`; verify). Do not build the form. Field spec is captured in
_Notes_ for the next brief.

---

## Section 4 — Image-and-Text band (STUB — do not build)

Render an empty placeholder section:

```html
<section class="ibv-section-concierge-image-text ibv-section ibv-section--surface-white">
  <!-- TODO Brief 4: reuse the existing Image-and-Text component (node 1:7042). -->
</section>
```

White surface per the design (node `1:7042`). Brief 4 feeds the existing
Image-and-Text component this page's content; nothing to build here now.

---

## Smoke test

1. Hero and cards grid render and **match Figma node `1:6981`** (hero
   `1:6983`, cards `1:6993`, card `1:6996`) — typography, colours, divider
   motif, card border/shadow/padding, grid columns and responsive collapse.
2. The nine cards render from the `concierge_services` Site Options repeater.
3. Contact and image-text sections render as empty placeholders — page is
   complete top-to-bottom with no layout breakage and correct section rhythm
   (check same-surface adjacency collapse looks intentional).
4. Section surfaces match the Figma fills; every body section declares its
   modifier explicitly.

---

## Notes

- **Decision for David — form submission mechanism (Brief 2/3).** The
  concierge enquiry form is a general enquiry, not a booking, so it should
  **not** hit Steve's PMS/booking API. Recommended default: a custom WP
  handler that validates + emails the IV2000 office, with spam protection;
  phone/email/WhatsApp shown statically from Site Options. Confirm this (vs
  an existing form plugin, if one is in the stack) before the contact-form
  brief.
- **Follow-up — globalise villa-detail concierge to `concierge_services`.**
  This brief makes Site Options the single source of truth and builds the
  Concierge page against it. The shipped villa-detail concierge section is
  **not** migrated here (kept surgical). Once Step 0.3 reports how that
  section sources its content, a small follow-up brief points it at the same
  repeater so there's one source. Flag this in your summary with what you
  found.
- **Contact form field spec (for Brief 2/3), from node `1:7008`:**
  left column = H2 "Enquire Concierge Services" + Paragraph subtitle +
  Phone / Email / "Chat with us → WhatsApp" (all from Site Options). Right
  column form fields, in order: Name, Email (one row), Phone + country-code
  selector, Arrival + Departure (one row), Villa (optional), Select Service
  (dropdown of the nine `concierge_services`), Message (textarea), Send
  Enquiry button (Sage/500, arrow icon). Inputs: light-grey border
  `#bfc8c8`, 8px radius, grey placeholder `#939292`.
- **Date pickers reuse.** Arrival/Departure in the form should use the **same
  Flatpickr approach** being briefed for Bob's villa-enquiry date range
  (Flatpickr, native inputs retained on mobile) — don't introduce a second
  date-picker pattern. Wire in the form brief.
- **Icon picks** for the nine services must be listed for review (see
  _Concierge services source_).
- **In-scope quality fixes:** act on small, obvious, single-file fixes in the
  files you're already editing (missing `esc_*`, missing `aria-hidden` on a
  decorative icon, a typo) and note them in the commit. Surface anything
  larger.

---

## Roadmap (for context — not part of this brief)

1. **Foundation (this brief)** — template, ACF, hero, cards grid, stubs.
2. **Contact section** — left content block + enquiry form markup + styling.
3. **Form submission** — handler/validation/spam + Flatpickr date pickers +
   success/error states (mechanism per the decision above).
4. **Image-and-Text band** — feed the existing component this page's content.
5. **(Conditional)** villa-detail concierge → `concierge_services` migration,
   if Step 0.3 warrants it.
