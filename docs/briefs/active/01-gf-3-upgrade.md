# Gravity Forms 2.10.3 → 3.0.2 — upgrade plan

**Status:** local upgrade in progress. Staging is already on **3.0.2**
and has been smoke-tested there. Local is also **3.0.2**. Code
compat (submit rewrite, phone, hidden datepicker disarm, concierge
datepicker skin) is in `ibv-core` 0.1.95. Remaining: Playwright + a
manual pass of forms 32–36, then **code-only** git ship.

Do not treat this as a casual plugin click. Gravity Forms treats `X.0`
as breaking.

---

## Live forms (the only ones that matter)

| ID | Title | Where | AJAX | Custom JS / PHP |
|---|---|---|---|---|
| 32 | Newsletter | Footer / empty states (`newsletter_gravity_form_id`) | yes | Styled embed only |
| 33 | Concierge Services | Concierge page | yes | Service dropdown via `gform_pre_render` + cousins; intl-tel-input |
| 34 | Villa Enquiry | Villa detail rail | yes | Date-range graft, pricing gate, phone, submit rewrite, `gform_pre_submission`, `gform_field_value_*` |
| 35 | Accommodation Enquiry | Apartments page | yes | Date-range graft, phone, submit rewrite, `gform_field_value_ibv_accommodation` |
| 36 | Contact | Contact page | yes | intl-tel-input |

Forms 1–31 are legacy. Leave them inactive in behaviour; do not spend
upgrade time on them.

---

## Findings on 3.0.2 (local markup, 16 Aug 2026)

Inspected `/villas/villa-savines/`, `/hotel/`, `/contact/`, `/concierge/`.

| Surface | Result |
|---|---|
| Submit rewrite | Single `<button>` (no nest). Keeps `onclick='gform.submission.handleButtonClick(this)'` and `data-submission-type='submit'`. Villa/accommodation still get the arrow helper. |
| Phone | `phoneFormat=international` is still a plain `<input type="tel">` (`gfield--phone-format-international`). GF’s new formatted/imask widget did **not** appear. Keep intl-tel-input. Form 33 phone is `standard` — also a plain tel input. |
| Hidden dates (34/35) | 3.0 still emits `.gform-datepicker` + `.gform-datepicker-toggle` on `ibv-drp-hidden` fields. `display:none` hides them, but WhatSock would still init. **Disarmed** via `ibv_gf_disarm_hidden_range_datepicker()` + JS class strip. Inputs stay plain text for VanillaCalendarPro. |
| Visible dates (33) | Concierge fields 13/17 use the new WhatSock picker. jQuery UI `#ui-datepicker-div` CSS is dead; replaced with `.gform-datepicker-*` skin. |
| `gform_post_render` | 3.0 still fires the jQuery event. Dual-bind (jQuery + `gform.addAction`) stays; inits are idempotent. |
| Footer | 3.0 emits both `gform-footer` and `gform_footer`. CSS now targets both. |
| Confirmations | 32/33/36 message; 34/35 page 18542 + `ref={entry_id}&type=`. |

---

## Our GF surface (code)

### PHP hooks

| Hook | File | Risk in 3.0 |
|---|---|---|
| `gform_submit_button` | `enquiry-panel.php` | **Verified.** Helper already handled `<button>`; 3.0 markup does not nest or drop AJAX attrs. |
| `gform_submit_button_{id}` | `accommodation-enquiry.php` | Same helper. |
| `gform_field_content` | `helpers/gravity-form.php` (pax placeholder + hidden datepicker disarm) | Medium. Date disarm is the 3.0 collision fix. |
| `gform_field_value_ibv_*` | enquiry-panel, accommodation-enquiry | Low. Dynamic population API is stable. |
| `gform_pre_submission` | enquiry-panel (Property Name, Active Offers) | Low–medium. Confirm hidden fields still write. |
| `gform_pre_render` / `pre_validation` / `admin_pre_render` / `pre_submission_filter` | `concierge-service-dropdown.php` | Low. Choice injection. |
| `gform_display_add_form_button` | `editor.php` | Low. Admin-only. |
| `GFAPI::get_form` | live forms 32–36 | Low. Forms are edited in admin; seeders have been removed. |
| `ibv_build_gf_booking_confirmation()` | `helpers.php` | Medium. Page confirmation + merge-tag QS. |

### JavaScript

| Script | Binds on | Risk in 3.0 |
|---|---|---|
| `enquiry-panel.js` | `gform_post_render` (jQuery **and** `gform.addAction`) | Dual-bind stays. Disarms hidden GF datepickers before the custom picker writes. |
| `accommodation-enquiry.js` | same dual-bind | Same. |
| `contact-enquiry.js` | same dual-bind | Phone only; `__ibvItiBound` is idempotent. |
| Date graft | `.ibv-drp-from input` / `.ibv-drp-to input` | Hidden fields must stay plain inputs. PHP + JS strip `.gform-datepicker`. |
| Phone | intl-tel-input on GF phone fields | Keep ours. GF 3.0 formatted widget did not appear on `international`. |
| Submit gate | `button[type="submit"], input[type="submit"]` | Still finds the rewritten button. |

### CSS

`assets/css/gravity-forms.css` now skins `.gform-datepicker-toggle` and
`.gform-datepicker-calendar` (WhatSock). The old `#ui-datepicker-div` /
`.ui-datepicker-*` block is gone. Footer targets `.gform_footer` and
`.gform-footer`.

---

## Breaking changes that hit us

1. **Submit is a `<button>`.** Helper already handled this. Verified on 34/35.
2. **`gform_submit_button` is a known footgun in 3.0.** Leave the helper;
   do not convert via a second wrap. If AJAX ever breaks, add the arrow
   with CSS/`::after` instead.
3. **Datepicker library swap (jQuery UI → WhatSock).** Hidden range
   fields disarmed. Concierge keeps the new picker, now skinned.
4. **Phone: imaskjs + new international format.** Not active on our
   `international` fields. Keep intl-tel-input.
5. **Removed** `gform_ajax_spinner_url` / `gform_spinner_url` (unused).
6. **3.0.2 AJAX / Orbital / `gformShowSpinner` fixes.** Watch
   confirmation redirect after villa and accommodation submit.
7. **Internal save API** — we do not call the old functions. Low risk.

---

## Upgrade procedure

1. ~~Git branch from current `staging`.~~ Working on `staging`.
2. Local only. Herd snapshot is the DB fallback (`mysqldump` was not
   in PATH). Plugin rollback copy: `/tmp/ibv-gf-3-upgrade/gravityforms-2.10.3`.
3. ~~Update Gravity Forms to 3.0.2.~~ Done locally. Staging already 3.0.2.
   Do **not** update add-ons or Yoast in the same window.
4. Hard-refresh every live form. Markup/JS/CSS patched in 0.1.95.
5. Do **not** recreate or overwrite forms 32–36. They are live content.
6. Run Playwright: `enquiry-panel.spec.ts`, booking-confirmation,
   plus a manual pass of 32 / 33 / 35 / 36.
7. Ship **code only** to staging via git. Do not migrate the local
   database. Staging is the content source of truth; GF tables stay
   per environment. After the code deploy, repeat the verification
   list on staging against staging’s own forms 32–36.

Rollback: restore the 2.10.3 plugin folder and the pre-upgrade **local**
DB snapshot. Form JSON is forwards-compatible enough that a code
rollback without DB rollback is usually enough if no one saved a
form in 3.0 admin. Staging rollback is a git revert of the plugin
folder only — never a local→staging DB push.

---

## Verification (must all pass on local before staging)

**Villa (34)**

- [x] Prefill from listing search (dates + pax) — covered by enquiry-panel Playwright
- [x] Custom date picker still writes hidden GF dates — Playwright + markup (no WhatSock on hidden inputs)
- [x] Available dates: price paints, contact fields show, submit works — Playwright
- [x] Unavailable dates: notice, form still open, submit works — Playwright
- [ ] Invalid phone blocked; valid E.164 stored — **manual submit**
- [ ] AJAX validation errors readable (no giant `gform_submission_error`) — **manual**
- [ ] Success → `/booking-request-received/?…&ref={id}&type=villa` — **manual submit**
- [ ] Guest notification still fires (Disable Emails off for this test) — **manual**

**Accommodation (35)** — picker/phone/submit widgets bind (smoke spec). Full submit still manual.

**Concierge (33)** — service dropdown has choices; intl-tel-input binds; WhatSock
toggle opens a visible calendar (smoke spec). Submit still manual.

**Contact (36)** — phone + 3.0 submit handler bind (smoke spec). Submit still manual.

**Newsletter (32)** — footer form + 3.0 submit handler (smoke spec). Submit still manual.

**Admin** — edit form 34 in GF; save; front-end still works — **manual**

---

## Out of scope

- Gravity Forms 2.10.x patch-only zip
- Yoast Free/Premium pair
- Rewriting the custom date picker to WhatSock
- Cleaning or migrating forms 1–31
- Enabling GF add-on feeds on 32–36 (separate product decision)

---

## Notes

- `ibv_core_gform_submit_button_with_arrow()` was already rewritten
  once for GF 2.6 `<button>` markup (`7d80374`). 3.0 did not need a
  second rewrite.
- Dual-bind comments in enquiry-panel / accommodation / contact JS
  now describe the 3.0 event model (jQuery event still fires).
- Theme `ibv` has no direct `gform_*` hooks; all risk is in
  `ibv-core`.
