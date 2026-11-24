# Enquire label — report

Guest-facing villa form wording changes from “request to book” to “enquire”. Code fallback is in this PR. Live copy in Gravity Forms, Site Options, and Pages is still the old wording until David edits it by hand on live. The form button guests see today comes from Gravity Forms, not from the code fallback.

Pulled live → local on 24 Nov 2026 (content tables plus Gravity Forms form definitions, not entries). Villa Torres rendered after the pull.

---

## 1. Changed in code

| File | Line | Before | After |
|---|---|---|---|
| `wp-content/mu-plugins/ibv-core/includes/components/enquiry-panel/enquiry-panel.php` | 205 | `__( 'Request to Book', 'ibv' )` | `__( 'Enquire', 'ibv' )` |

The filter still prefers the Gravity Forms button text when that field is set. Guests will keep seeing **Request to Book** until the live form button is cleared or changed (item 1 in the checklist below). After that, an empty button field falls back to **Enquire**, with the arrow icon still on the button.

No other guest-facing code strings needed changing. The panel heading is already “Enquire about this villa”.

---

## 2. For David to change in wp-admin on live

Do these on **live** (`ibizavillas2000.com`). Do not push the local database.

- [ ] **Forms → Request to Book → Settings → Form Settings → Form Button text**
  - Current: `Request to Book`
  - New: `Enquire`
  - This is the label guests see on every villa page. Change this first.

- [ ] **Forms → Request to Book → Settings → Notifications → Guest confirmation → Subject**
  - Current: `We've got your request: {Villa}, {Arrival:format:j M Y} to {Departure:format:j M Y}`
  - New: `We've got your enquiry: {Villa}, {Arrival:format:j M Y} to {Departure:format:j M Y}`

- [ ] **Forms → Request to Book → Settings → Notifications → Guest confirmation → Message** (opening line)
  - Current: `Thanks, your request is with us.`
  - New: `Thanks, your enquiry is with us.`
  - Leave the rest of the email as it is.

- [ ] **Site Options → Shared content → 3-step process → 3-step cards → second card → Title**
  - Current: `Request to book`
  - New: `Enquire`
  - This is the “How booking works” panel on the homepage (and anywhere else that section is used). Live copy is sentence case; the October export had Title Case on leftover homepage fields that the front end does not read.

- [ ] **Pages → Cookies Policy** (the sentence about sending a form)
  - Current: `When you send an enquiry or request to book, the details you enter are sent to us by email and stored in the site's form records so we can reply.`
  - New: `When you send an enquiry, the details you enter are sent to us by email and stored in the site's form records so we can reply.`

After the Gravity Forms button change, purge WP Rocket (or equivalent) so the villa pages do not keep serving the old button HTML.

---

## 3. Not changed, and why

**Internal submission types** — left as `Request to Book`, `Enquiry`, and `Special offer`. They tag the team notification subject. Guests never see them.

- `ibv_gf_submission_type_values()` in `includes/helpers/gravity-notifications.php`
- `setSubmissionType( 'Request to Book' )` in `enquiry-panel.js`

**Concierge** — “We've got your request” stays. That flow is not the villa form.

- Form 33 (Concierge Services) guest email subject `We've got your request` and body `Thanks, your request is with us.`
- Booking Confirmation page, concierge variant heading (already `We've got your request` in both the template fallback and the saved ACF row)

**Confirmation page slug** `/booking-request-received/` — not changed. Forms 34 and 35 still redirect to page 18542. Changing the slug would need a redirect and form edits for no guest benefit.

**Page title** of 18542 is still **Request Received**. Suggested replacement if you want the admin list and browser tab to match the new wording: **Enquiry received**. The public URL would stay the same unless the slug is edited (do not edit the slug).

**Villa thank-you heading** on that page is already `Thanks, that's with us` (variant key `villa`). It does not say “your request”. No edit.

**Form 34 title** `Request to Book` is admin-only (the Forms list). Suggested replacement: **Villa enquiry**. Not required for guests.

**Form 34 admin notification subject** uses `{Submission type:15}` (the internal tag). Leave it.

**Accommodation and Contact forms** already say enquiry (`Send Enquiry` / `We've got your enquiry` / `Send enquiry` / `We've got your message`). No edit.

**Privacy Policy** (“responding to your requests if you contact us”, “email your requests to operations@…”) and **Concierge** page body (“an outline of your requests and potential dates”) use “request” in the ordinary sense, not the villa button label. Suggested: leave them.

**Orphaned fields (not in the editor, not on the front end)** — leftover homepage post meta still has `Request to Book` on Home page ID 2, and leftover `confirmation_heading` on page 18542 still says `We've got your request`. The live homepage reads Site Options; the thank-you page reads the variant repeater. No wp-admin control for these. Do not write a cleanup script.

**Local smoke test (24 Nov 2026)** — Villa Torres. No dates, available dates (`?date_from=2027-08-14&date_to=2027-08-21&pax=4`), and unavailable dates (`?date_from=2027-05-08&date_to=2027-05-15&pax=4`): button still read **Request to Book** with the arrow while form 34’s button text was set. Clearing that text locally (and flushing WP Rocket’s HTML cache) showed **Enquire** with the arrow still on the button. The live form was not touched; the local form button was restored afterwards. Live pricing did not paint on local (BOB from `.test` returns the “couldn’t fetch” notice) — that is existing local behaviour, not this copy change.
