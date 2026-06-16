# Concierge Page — Enquiry form enhancements (GF layer)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Feature layer (progressive JS enhancements + style polish)
over an already-functional Gravity Forms embed.
**Supersedes:** `done/feature-concierge-form-submission.md`. That brief
specced a custom WP submission handler — out of date once the form was
wired to Gravity Forms in the foundation pass.
**Design source:** Figma file `Dj7yiKWK0pNADS7OdjXRKo`, Contact section =
node **`1:7008`** (visual reference for date/phone fields, validation +
confirmation states).

---

## Context — what GF already handles, what's left

Submission, validation, sanitisation, server-side spam (honeypot toggle in
form settings), POST + AJAX paths, no-JS fallback, value repopulation,
field-level errors, success confirmation, email notifications — **all
Gravity Forms**. Editor configures notifications and honeypot in the form
settings; the project doesn't write or maintain a parallel handler.

Three things remain that GF doesn't do out of the box and need our layer:

1. **A coupled date range picker** on Arrival / Departure (GF date fields
   are independent — there's no built-in "departure ≥ arrival" linkage).
2. **A phone country picker** on the phone field (GF phone field is plain
   text with optional format validation; no country selector).
3. **A style audit** of GF's native validation + confirmation states under
   the `.ibv-gform` base from the previous pass — verify error tints,
   confirmation panel, and required-field markers all read in palette and
   don't fight the page rhythm.

Plus an **editorial note** capturing the GF-admin config that needs doing
once (honeypot on; notification recipient = Site Options enquiries email;
Service dropdown populated to match `concierge_services`).

---

## Step 0 — Audit first (read, then report, before writing)

1. **`date-range-picker` component.** Check
   `mu-plugins/ibv-core/includes/components/date-range-picker/`. Report:
   does it exist; is it Flatpickr-backed; how does a template attach it to
   a pair of inputs (render function? class/data-attr hook?); how is it
   enqueued today (sitewide or on-demand)? **If it exists and is reusable,
   reuse it** — the concierge form gets the same date UX as the villa
   enquiry. If it doesn't exist (or only works coupled to villa context),
   surface that before reaching for a second copy of Flatpickr.
2. **intl-tel-input.** Almost certainly not vendored. Confirm before
   adding.
3. **Asset registration.** `shared-assets.php` in `ibv-core` is the place
   for new handles — confirm the pattern (depend on `ibv-base`, version
   off `IBV_CORE_VERSION`).
4. **GF input markup.** Render the configured concierge GF form (or use
   any existing GF date / phone field on the site as a reference) and
   report the actual class names / `data-*` attributes GF emits for date
   and phone fields. The JS hooks below need to target real selectors.

Report resolved paths/selectors in the session summary.

---

## 1 — Date range picker

Target the two date inputs the GF form emits for Arrival and Departure.
Initialise them as a coupled range so Departure can't precede Arrival.

- **If `date-range-picker` exists and is reusable:** attach it. The
  concierge form's date pair behaves identically to the villa enquiry
  date pair — same picker UX, same locale, same mobile policy. Extend
  the component with a small arg if the existing API only supports a
  villa-context invocation.
- **If it doesn't exist (or isn't reusable as a standalone pair):**
  enqueue Flatpickr (vendored CSS + JS, no build) and init range mode
  across the two inputs. Enqueue only on the concierge page; do **not**
  pull Flatpickr in elsewhere.

**Mobile policy:** retain the native date input on touch / small
viewports — don't init the JS picker there. Same policy as the villa
enquiry pair. The field stays usable before and without JS.

---

## 2 — Phone country picker

Vendor **intl-tel-input** (its own JS + CSS — no build step required;
ship the dist files) and init it on the form's `tel` field. On submit,
write the full international number (`getNumber()`) into a hidden field
so GF — and the office notification — receives the complete number, not
just the digits the user typed without the country code.

Native fallback: with JS off, the plain `tel` input still submits.

Style overrides scoped to `.ibv-gform` so the picker chrome reads in
palette (flag colours stay vendor-supplied; surrounding chrome — flag
container, dropdown panel, dial-code text — uses our tokens).

---

## 3 — GF validation + confirmation style audit

The `.ibv-gform` base in `assets/css/gravity-forms.css` already covers
labels, inputs, submit, validation message colour, confirmation panel.
With a real submitted form on staging, verify on-error and on-success:

- Field-level error message: reads in error palette, sits beneath the
  input with the right spacing, doesn't break the grid.
- Inline `aria-invalid` border tint: not jarring against the focus ring.
- Confirmation panel: green tint reads on the off-white section; spacing
  matches the rest of the section's rhythm.
- Required-field asterisk: visible but not shouty.

If anything reads off, tweak the base or add a scoped variant rule —
keep `.ibv-gform` as the single source for GF treatment site-wide. The
goal is "looks right on first error / first success without thinking
about it", not a full redesign.

---

## 4 — Editorial config (no code — content guideline)

Once the page is on staging with the GF form ID populated, this needs
doing in GF admin:

1. **Honeypot:** Form Settings → "Enable anti-spam honeypot" → on.
2. **Notification:** Notifications → ensure the recipient address is the
   Site Options enquiries email (not a hardcoded GF default). Set
   Reply-To to the enquirer's email so the office can reply directly.
3. **Service dropdown options:** the dropdown's options should reflect
   the nine `concierge_services` titles. **Two paths — pick one:**
   - **Manual sync (default):** populate the dropdown by hand to match
     the repeater. Simple, but drifts if services change.
     Documented in the field's GF admin description.
   - **Dynamic population (better):** wire `gform_pre_render` /
     `gform_pre_validation` / `gform_admin_pre_render` to populate the
     dropdown choices from `get_field('concierge_services')` on the
     Concierge page. One-time code change; survives any service edit.
     Probably worth the small server-side filter — surface before
     building.

Capture this list as a comment / note in the concierge page template
(or in `register-page-concierge.php` near the form-id field) so a
future maintainer knows the admin steps without re-deriving them.

---

## Files to create / edit

```
EDIT    ibv-core: shared-assets.php — enqueue Flatpickr (if not via
        date-range-picker) and intl-tel-input only on the concierge page
CREATE  theme or ibv-core: small JS file that initialises the date range
        picker + phone picker against the GF selectors from Step 0.4
EDIT    ibv-core: assets/css/gravity-forms.css — any error/confirmation
        polish surfaced by the Step 3 audit; intl-tel-input chrome
        overrides scoped to .ibv-gform
EDIT    ibv-core: register-page-concierge.php — editorial-config note
        near the form-id field (so admin steps are discoverable)
EDIT    (optional) ibv-core: add `gform_pre_render` filter to populate
        the Service dropdown from concierge_services repeater
```

If the `date-range-picker` audit shows reuse is straightforward, the JS
file shrinks to just the phone init. Surface that branch.

---

## Smoke test

1. **Date range:** on desktop, picking an Arrival also constrains the
   Departure picker to dates ≥ Arrival. On a touch viewport, the native
   date input still works and the JS picker doesn't init.
2. **Phone picker:** country selector shows; switching the country
   changes the dial code; submitting persists the full international
   number to GF (verify by checking the GF entry, not just the placeholder).
3. **JS-disabled:** date inputs still submit, phone input still submits;
   GF's own POST handles validation + email as normal.
4. **Validation:** submit an invalid form (missing required); GF's
   inline error rendering reads in palette, sits cleanly, doesn't
   reflow the grid.
5. **Success:** submit a valid form; GF confirmation panel reads in
   palette and sits in section rhythm.
6. **Editorial config:** honeypot is on, notification goes to the Site
   Options enquiries email with Reply-To set, Service dropdown options
   match the nine `concierge_services` (manual or dynamic, per the
   decision above).

---

## Notes

- **Decision for David — Service dropdown population.** Dynamic
  (`gform_pre_render` filter pulling from the page repeater) avoids
  drift but adds a small server-side coupling; manual is dumb-simple
  but drifts. Surface before building; default to dynamic if no
  preference.
- **Decision for David — phone picker.** Default on per the foundation
  pass; skip section 2 entirely if he wants to stay lean (plain `tel`).
- **Autoresponder to the enquirer.** Out of scope; configurable in GF
  Notifications later if wanted.
- **reCAPTCHA / Akismet.** Not added by default — honeypot + GF's
  built-in checks are usually enough. Add later if volume warrants.
- **Don't double-enqueue.** If `date-range-picker` already pulls
  Flatpickr in, reuse — never register a second copy.
- **In-scope quality fixes:** act on small obvious single-file fixes in
  files you're already editing (missing `esc_*`, missing `aria` hook)
  and note them in the commit.

---

## Out of scope (handled by Gravity Forms or already done)

- Submission handler / nonce / sanitisation / validation / spam (GF).
- Email notification (GF Notifications — admin config only).
- PRG redirect / value repopulation (GF).
- Field-level error markup with `aria-*` (GF).
- No-JS fallback path (GF works natively without JS).
- Custom honeypot / time-trap (GF has a honeypot toggle).
- Form markup, base styling — already shipped in the foundation pass.
