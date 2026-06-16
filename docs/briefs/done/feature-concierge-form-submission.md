# Concierge Page — Brief 3: Enquiry form — submission + field enhancements

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Feature (behaviour spanning multiple files), wiring the form
markup built in Brief 2.
**Design source:** Figma file `Dj7yiKWK0pNADS7OdjXRKo`, Contact section =
node **`1:7008`** (visual reference for field/error/button states).

This is brief **3 of the concierge sequence**. It makes the enquiry form
**functional**: server-side submission (validate, sanitise, spam-protect,
email the office), success/error states that work **without JS**, and then JS
field enhancements layered on top (date range picker, phone picker).

---

## Scope

Take Brief 2's static form markup and make it work end to end:

1. **Server-side submission** — a custom WP handler that validates, sanitises,
   spam-checks, and emails the IV2000 office. Works as a plain POST with **no
   JavaScript**.
2. **Success / error states** — post/redirect/get, value repopulation,
   field-level errors, success confirmation. No JS required.
3. **Field enhancements (progressive)** — Arrival/Departure date range picker
   (reuse existing component or enqueue Flatpickr); phone country picker. Both
   are enhancements over a form that already works without them.

**Out of scope:** anything that touches Steve's PMS/booking API — this is a
general enquiry, **not** a booking, so it does not hit availability/pricing
endpoints. The image-and-text band is a later brief.

---

## Step 0 — Audit first (read, then report, before writing)

Read these and report exactly what you'll reuse before building:

1. **Existing form-submission infrastructure.** The **newsletter form
   helper** (Special Offers brief 03) already submits a form somewhere —
   read how: the hook it registers on (`admin_post_*` / `init` / AJAX), how
   it verifies a nonce, how it sanitises, and how it sends mail (`wp_mail`
   wrapper?). **Follow the same pattern** for the concierge handler rather
   than inventing a new submission path. Report the file + the pattern.
2. **`date-range-picker` component.** Check
   `mu-plugins/ibv-core/includes/components/date-range-picker/` (it appears to
   exist as of the latest handover). Report: does it exist, is it enqueued
   site-wide, is it Flatpickr-backed, and **how does a template attach it to a
   pair of inputs** (a render function? a class/data-attribute hook the JS
   scans for?). This determines the date-picker branch below.
3. **Asset registration.** Where scripts/styles are registered and enqueued
   (Bob's assets live in `shared-assets.php` in `ibv-core`). Report the file
   so new enqueues follow the same place/pattern.
4. **Recipient address.** Confirm the IV2000 enquiries email is available in
   **Site Options** (the contact email Brief 1/2 used). The handler sends
   there — don't hardcode an address.
5. **intl-tel-input.** Almost certainly not present; confirm. (Phone picker
   branch below.)

Report resolved paths/patterns in your session summary.

---

## Submission mechanism (decision — defaulted, confirm with David)

Default, consistent with the house pattern (custom form helpers, no form
plugin in the stack): a **custom WP handler that emails the IV2000 office**.
No PMS API, no third-party form service. If David has since said he wants an
existing form plugin instead, stop and flag — that changes this brief.

---

## 1 — Wire the form (edit Brief 2 markup)

In the concierge contact partial, complete the `<form>` Brief 2 left stubbed:

- `method="post"`, `action` targeting the handler endpoint (e.g.
  `admin-post.php` with an `action` hidden field — match the newsletter
  helper's approach).
- `wp_nonce_field()` for a concierge-enquiry-specific action.
- A **honeypot** field: a visually-hidden input (e.g. `name="website"`) that
  real users leave empty; label it off-screen and `tabindex="-1"`,
  `autocomplete="off"`.
- A hidden **timestamp** field (render time) for the time-trap check.
- On re-render after a failed submit: **repopulate** every field with the
  sanitised submitted value, and render **field-level error messages**.
- A **status region** at the top of the form (`role="status"` for success,
  `role="alert"` for the error summary) for the no-JS confirmation/error path.

---

## 2 — Server-side handler

Create the handler in `ibv-core`, following the newsletter helper's pattern
(hook, nonce, mailer). It must:

1. **Verify** the nonce; bail (with a generic error) if it fails.
2. **Spam checks:** reject if the honeypot is non-empty, or if the submit
   timestamp is implausibly fast (e.g. < ~2–3s after render). Fail silently to
   the bot (generic success or no-op) — don't reveal the trap.
3. **Validate** required fields — **Name, Email, Service** (per Brief 2's
   recommended set; adjust here if David prefers a different set). Email must
   be a valid format. Collect all errors, don't stop at the first.
4. **Sanitise** every field before use and before any echo:
   `sanitize_text_field` for name/phone/villa/service, `sanitize_email` for
   email, `sanitize_textarea_field` for the message, validate
   arrival/departure as dates (expected `Y-m-d`; reject malformed).
5. **Send mail** via the house mailer to the Site Options enquiries address:
   - Clear subject (e.g. "Concierge enquiry — {name}").
   - Body with all submitted fields, labelled.
   - **Reply-To** set to the enquirer's email so the office can reply directly.
   - Escape values appropriate to the mail content type.
6. **PRG redirect** back to the concierge page with a status indicator
   (success → e.g. `?enquiry=sent#enquiry`; error → repopulate via transient
   or a short-lived store keyed to the session, matching how the newsletter
   helper carries state back). On success, clear the stored values.

Keep it dependency-free server-side. No reCAPTCHA by default (see _Notes_).

---

## 3 — Success / error states (no-JS path is the spec)

- **Success:** after redirect, show a confirmation message in the status
  region (e.g. "Thanks — your enquiry's on its way. We'll be in touch
  shortly."), and present an empty form (or hide it). Move focus to the
  confirmation.
- **Error:** show an error summary in the status region, render each field's
  error inline beneath/within the field, set `aria-invalid="true"` and
  `aria-describedby` linking the input to its error message, and move focus to
  the first invalid field. Repopulate all values.
- Style error/success to the design language (forest/sage palette; verify
  tokens against `tokens.css`). No new surface needed.

This whole path must work with JavaScript disabled.

---

## 4 — Date range picker (Arrival / Departure) — reuse or enqueue

Target the date hook Brief 2 put on the Arrival/Departure inputs
(`data-ibv-datepicker` or whatever Brief 2 used — confirm).

**Branch on the Step 0.2 audit:**

- **If the `date-range-picker` component exists and is enqueued:** reuse it.
  Attach it to the concierge form's Arrival/Departure pair using whatever
  mechanism it exposes (render helper or hook). The concierge form should get
  the **same date UX as the villa enquiry** — don't reimplement. If the
  component needs a small extension to support a standalone pair (vs the villa
  context), extend it and document the new arg.
- **If it does NOT exist (or isn't usable here):** enqueue **Flatpickr**
  (vendored asset + its own CSS — fine, the no-build standard is CSS-authoring
  only) via the asset-registration file from Step 0.3. Init range mode across
  the two inputs (Arrival = start, Departure = end, departure ≥ arrival).
  Enqueue only on the concierge page.

**Mobile:** retain native inputs on touch / small viewports — don't init the
JS picker there (same policy as the villa enquiry date range). The field must
remain usable before/without JS.

---

## 5 — Phone picker (default on — confirm if lean)

Per the Brief 2 decision (David: default to matching the design's country
chip): enqueue **intl-tel-input** (vendored JS + CSS) and init it on the `tel`
field. On submit, persist the **full international number** (intl-tel-input's
`getNumber()`), written into a hidden field the handler reads, so the office
receives a complete number. Native fallback: the plain `tel` input still
submits if JS is off.

If David opted for the lean route (plain `tel`, no picker), skip this section
entirely.

---

## Files to create / edit

```
EDIT    theme: concierge contact partial — complete the <form> (action,
        nonce, honeypot, timestamp, repopulation, status/error regions)
CREATE  ibv-core: concierge enquiry submission handler (mirror newsletter
        helper's hook/nonce/mailer pattern)
CREATE  theme/ibv-core: concierge form JS (date picker init, phone init,
        focus/error enhancement)
EDIT    ibv-core: shared-assets.php (or the audited registration file) —
        enqueue the form JS; enqueue Flatpickr / intl-tel-input only if
        needed, only on the concierge page
EDIT    theme: CSS for error/success states (+ any picker style overrides)
```

---

## Accessibility

- Errors linked to inputs via `aria-describedby`; invalid inputs get
  `aria-invalid="true"`.
- Success message in a `role="status"` region; error summary in `role="alert"`.
- Focus moves to the success confirmation (on success) or the first invalid
  field (on error).
- Pickers must remain keyboard-operable; the native-input fallback covers
  no-JS and touch.

---

## Smoke test

1. **No-JS path:** disable JavaScript. Submit empty → required-field errors
   render inline, values repopulate, focus lands on the first error. Submit
   valid → office receives the email (Reply-To = enquirer), success
   confirmation shows.
2. **Spam:** filling the honeypot, or submitting near-instantly, is rejected
   without a real email being sent.
3. **Mail:** recipient is the **Site Options** enquiries address (not a
   hardcoded one); all fields present and labelled; Service value is one of
   the nine concierge services.
4. **Date picker:** Arrival/Departure use the **existing date-range-picker
   component** if present (matching villa-enquiry UX), else Flatpickr range;
   native inputs on mobile; departure can't precede arrival.
5. **Phone:** (if enabled) country picker initialises; submitted value is the
   full international number; plain `tel` still works with JS off.
6. **Enhanced path:** with JS on, the form still submits successfully and
   states behave; no double-enqueue of any picker library.

---

## Notes

- **Decision for David — submission mechanism.** Defaulted to a custom WP
  handler emailing the Site Options enquiries address (no PMS API, no form
  plugin), matching the house pattern. Flag if you'd rather route through an
  existing plugin.
- **Decision for David — reCAPTCHA.** Default spam protection is honeypot +
  nonce + time-trap (no third-party service, no Google dependency). If volume
  later justifies it, reCAPTCHA/Turnstile can be added — left out for now to
  keep it lean and privacy-clean.
- **Autoresponder to the enquirer** (a "we've received your enquiry" email) is
  **out of scope** here — easy to add later if wanted. Flagging so it's a
  deliberate omission, not an oversight.
- **Phone picker** is default-on per the Brief 2 decision; skip section 5 if
  David chose the lean route.
- **Don't double-enqueue.** If the date-range-picker component already pulls
  Flatpickr in, reuse that — do not register a second copy. The Step 0 audit
  is what prevents this.
- **In-scope quality fixes:** act on small, obvious, single-file fixes in
  files you're already editing (a missing `esc_*`, a missing `aria` hook) and
  note them in the commit. Surface anything larger.

---

## Roadmap (for context)

1. Foundation ✓ (Brief 1)
2. Contact section markup + styling ✓ (Brief 2)
3. **Form submission + field enhancements (this brief)**
4. Image-and-Text band — feed the existing component this page's content
5. (Conditional) villa-detail concierge → `concierge_services` migration
