# Contact Page — Brief 1: Foundation + contact-enquiry section

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Greenfield (new page, against a signed-off design).
**Design source:** node **`282:4057`** (`07 | Contact`); contact section =
node **`282:4059`**. Pulled via David's **MCP-shareable copy** of the canonical
design — file key **`3g57x18kqjjNtnJ9SVgTUI`**, the same design as the
canonical `Dj7yiKWK0pNADS7OdjXRKo` (David owns the copy so it can be shared to
MCP; node IDs are identical). Node IDs from the URL convert hyphen→colon.

This is brief **1 of the contact-page sequence**. It stands up the page and
its primary contact-enquiry section, **maximally reusing the concierge contact
work**. The image-and-text band and the form submission wiring are stubbed/
deferred (see _Roadmap_).

---

## The reuse story (read this first)

The contact page's enquiry section is the **same component** as the concierge
contact section (Brief 2 of the concierge sequence): same two-column layout,
same left-hand contact-details block, same input styling, same submit button.
The differences are parametric:

| | Concierge contact section | Contact page |
|---|---|---|
| Heading | H2 (Newsreader 44) | **H1 (Newsreader 64)** — page title |
| Banner image above form | none | **yes** (rounded 12px, ~248px tall) |
| Form-card title | none | **"Send an enquiry"** (Newsreader 24) |
| Fields | …, Arrival, Departure, **Select Service**, Message | …, **Dates of Stay** (single range), **Number of Guests**, Villa preference, Message — **no Service** |

**Do not build a second bespoke copy.** Treat the enquiry section's *chrome*
(layout, left content + contact details, optional banner, form card + optional
title, submit button, input styling, input partials) as a **shared, canonical
component** both pages consume, with per-page **field composition** inside the
form. Build the contact page on that shared component.

---

## Scope

1. **Page foundation** — Contact page template + ACF field group, mirroring
   the About / concierge-foundation pattern.
2. **Contact-enquiry section** — the two-column section (node `282:4059`):
   left content (H1, subtitle, contact details from Site Options) + right
   column (banner image + form card with title + fields), fully marked up and
   styled, reusing/generalising the concierge contact component.

**Stubbed / deferred:**
- **Image-and-Text band** (node `282:4093`) — stub a placeholder section
  (Brief 3).
- **Form submission** — render the form markup with accessible labels and
  field `name`s; leave `action`/handler/validation/date-picker/phone-picker
  for Brief 2 (same pattern as concierge Brief 2 → 3).

---

## Step 0 — Audit first (read, then report, before writing)

1. **Concierge contact section** (concierge Brief 2 output) — does the partial
   exist yet? Report its structure and how parametrisable it is.
   - If it exists and is reasonably generic → **extend it** into the shared
     component (add: heading-level/size param, optional banner image, optional
     form-card title, per-page field composition) and have the contact page
     consume it.
   - If it exists but is concierge-bespoke → build/extract the **shared
     enquiry-section partial** here, have the contact page consume it, and
     **flag migrating the concierge page onto it as a follow-up** (don't
     refactor concierge inline from this brief).
   - If it doesn't exist yet → build the shared partial here as the canonical
     home; the concierge briefs will consume it.
2. **Shared input partials / form-field styling** — reuse whatever the
   concierge form used (text input, textarea, phone field). Report names.
3. **`date-range-picker` component** —
   `mu-plugins/ibv-core/includes/components/date-range-picker/`. The "Dates of
   Stay" field is a **single range input**, which this component fits directly
   (more cleanly than concierge's two-input Arrival/Departure). Report whether
   it exists / is enqueued / how to attach it. (Wiring is Brief 2 — just
   confirm the hook now.)
4. **Image-and-Text component** — confirm it exists + its expected args (Brief
   3 feeds it this page's content).
5. **About / concierge-foundation page template + registration** — mirror for
   the Contact page template + ACF group.
6. **Site Options** — confirm Phone / Email / WhatsApp global fields exist
   (added in concierge Brief 1). The contact details render from them.
7. **`tokens.css` / `sections.css`** — verify tokens + surface modifiers.

Report resolved paths/patterns in your session summary.

---

## Standards

Same as the concierge briefs:
- Plain CSS, custom-property tokens, BEM, `ibv-` prefix; the **no-build
  standard is CSS-authoring only** — enqueued JS libraries are fine.
- Classic editor; ACF registered in PHP; icons via the icon helper; content
  images via the image helper.
- Every body section root declares base rhythm + a surface modifier matching
  its Figma fill; section CSS never sets `padding-block`/`background`/`color`
  on the root.
- Escape/sanitise all ACF output.

---

## Token / type reference (verify against `tokens.css`)

| Figma var | Hex | Use |
|---|---|---|
| Off White | `#f9f8f4` | section + form surface |
| Forest/Dark | `#055353` | heading, subtitle, contact details |
| Black | `#343434` | form-card title; phone value |
| Sage/500 | `#00897e` | submit button |
| Light Grey | `#bfc8c8` | input borders |
| Grey | `#939292` | placeholder text |

Type: H1 = Newsreader 64 (`Desktop/H1`); form-card title = Newsreader 24
(`Desktop/H4`); body = Uncut Sans 16 (`Paragraph/Regular`); contact-detail
labels = Uncut Sans Bold 16 (`Paragraph/Bold`).

---

## Files to create / edit

```
CREATE  theme: Contact page template (mirror About / concierge foundation)
EDIT    theme/ibv-core: shared enquiry-section partial — generalise per
        Step 0.1 (heading level, optional banner, optional form title,
        per-page field composition). OR create it if not present.
CREATE  theme: Contact page section markup that consumes the shared partial
        with this page's content + field set
EDIT    ibv-core: ACF field-group registration — Contact page fields
EDIT    theme: CSS — only what's net-new (banner image; any H1-size variant);
        reuse concierge form/input CSS
```

---

## Contact-enquiry section (`282:4059`)

Section root: `class="ibv-section-contact ibv-section ibv-section--surface-{X}"`
— `{X}` matches the Figma fill (off-white → likely `surface-bg`; verify).
Two columns: left **481px** fixed, right **flex-1**, **20px** gap; stack to one
column below ~900px. (Vertical padding from the rhythm system — don't hardcode
the Figma 48px.)

### Left column — content (`282:4061`)

- **H1** — Newsreader 64, `--forest-dark`: "Dive into Ibiza Villas 2000". This
  is the **page `<h1>`** (no hero on this page). ACF: `page_heading`.
- **Subtitle** — Uncut Sans 20, `--forest-dark`: "We respond within 20
  minutes." ACF: `page_subheading`.
- **Contact details** (`282:4065`, ~24px gap) — **reuse the concierge
  contact-details block verbatim**, from Site Options: Phone (`tel:`), Email
  (`mailto:`), "Chat with us" → WhatsApp (`https://wa.me/…`, underline
  treatment). Omit any row whose Site Options value is empty.

### Right column — banner + form (`282:4073`)

- **Banner image** (`282:4074`) — full-width, ~248px tall, `border-radius:
  12px`, `object-fit: cover`, via the image helper. ACF: `form_banner_image`.
  Decorative → empty/role-appropriate alt. ~8px gap below it before the form
  card.
- **Form card** (`282:4076`) — off-white bg, `border-radius: 12px`, padding
  ≈48px top / 24px sides+bottom, ~32px gap. Contains:
  - **Form title** (`282:4077`) — Newsreader 24, `--black`: "Send an enquiry".
    ACF: `form_title`. (This is the optional form-card title param.)
  - **`<form>`** — fields below; `action`/nonce/honeypot stubbed for Brief 2:
    ```html
    <form class="ibv-enquiry-form ibv-enquiry-form--contact"
          <!-- TODO Brief 2: action, method, nonce, honeypot, timestamp -->>
    ```

### Fields (`282:4079`, ~16px gap) — contact-page composition

Every field gets a **visually-hidden `<label for>`**. Order, `name`s, types:

| # | Field | Element | `name` | Label / placeholder | Required (rec.) |
|---|---|---|---|---|---|
| 1 | Name | `input[type=text]` | `name` | Your Name | **yes** |
| 2 | Email | `input[type=email]` | `email` | Email | **yes** |
| 3 | Phone | `input[type=tel]` + picker | `phone` | Phone Number | no |
| 4 | Dates of Stay | `input` + date-range hook | `dates` | Dates of Stay | no |
| 5 | Number of Guests | `input[type=number]` min=1 | `guests` | Number of Guests | no |
| 6 | Villa preference | `input[type=text]` | `villa` | Villa preference (optional) | no |
| 7 | Message | `textarea` | `message` | Message — any questions or requirements | no |

- **Rows:** fields **1 & 2** share a row; fields **4 & 5** share a row
  (collapse to single column below ~600px). Fields 3, 6, 7 full-width.
- **Phone (3):** `tel` input enhanced by the country picker (intl-tel-input)
  per the concierge phone decision — wired in Brief 2; native fallback here.
- **Dates of Stay (4):** a **single** input carrying the date-range hook (e.g.
  `data-ibv-datepicker`), enhanced by the `date-range-picker` component in
  Brief 2. Single range field — don't split into two inputs.
- **Number of Guests (5):** `type="number"`, `min="1"`, `inputmode="numeric"`.
  (A `<select>` of 1–N is the alternative — flagged in _Notes_.)
- **Message (7):** ~210px textarea. **Fix the design typo** → "Message — any
  questions or requirements".
- **No Service dropdown** on this page.
- **Submit:** primary button helper — Sage/500, "Send Enquiry", trailing arrow.

**Input styling:** reuse the concierge form's input treatment (1px
`--light-grey` border, 8px radius, ≈16/24px padding, 16px text, `--grey`
placeholder, visible focus ring). Don't author a parallel input style.

---

## Image-and-Text band (`282:4093`) — STUB (Brief 3)

```html
<section class="ibv-section-contact-image-text ibv-section ibv-section--surface-white">
  <!-- TODO Brief 3: reuse the existing Image and Text component (node 282:4093). -->
</section>
```

White surface per design. Brief 3 reads node `282:4093` and feeds the existing
component this page's content.

---

## Smoke test

1. Contact page renders Header → contact section → (stubbed) image-text →
   Footer, and the contact section **matches node `282:4059`** — H1, subtitle,
   contact details, banner image, form card + title, fields, button.
2. The section is built on the **shared enquiry component**, not a copied
   concierge partial (report how you reused it).
3. Contact details render **from Site Options**; empty values omit their row.
4. Banner image renders from ACF (`form_banner_image`) at 12px radius, cover.
5. Field set matches the contact-page composition (Dates of Stay + Number of
   Guests; **no** Service dropdown). Every field has a label; form is keyboard-
   navigable with a visible focus state.
6. Responsive: columns stack < ~900px; the paired row collapses < ~600px.
7. Form does **not** submit yet (Brief 2).

---

## Notes

- **Figma source.** The canonical design is the usual file
  (`Dj7yiKWK0pNADS7OdjXRKo`); `3g57x18kqjjNtnJ9SVgTUI` is David's own copy of
  it, used purely so the design can be shared to MCP. Same design, same node
  IDs — pull the contact-page nodes from the copy. Not a migration.
- **Canonical enquiry component.** This brief consumes (and, if needed,
  creates) a shared enquiry-section component. If the concierge page already
  shipped a bespoke copy, migrating it onto the shared component is a flagged
  **follow-up**, not done here — keeps this brief surgical.
- **Number of Guests** built as a `number` input. A constrained `<select>`
  (1–16, say) is the alternative if David wants to cap party size — flag.
- **Required fields:** Name + Email marked recommended-required. Validation is
  Brief 2; adjust there.
- **Carries into Brief 2:** `<form>` action/nonce/honeypot, the **shared
  submission handler generalised** for the contact field set (no Service;
  Dates of Stay + Guests), `date-range-picker` on Dates of Stay, phone picker,
  success/error states. The concierge handler should generalise to serve both
  forms (validate required per-form, sanitise, email the office) — flag if
  it's currently concierge-specific.
- **In-scope quality fixes:** small obvious single-file fixes (the message
  typo, a missing `esc_*`/label) in files you're editing — note in the commit.

---

## Roadmap (contact page)

1. **Foundation + contact-enquiry section (this brief)**
2. Form submission — generalise the shared handler for the contact field set;
   wire Dates-of-Stay (date-range-picker), Number of Guests, phone picker;
   success/error states
3. Image-and-Text band — reuse the existing component
