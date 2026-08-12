# IBZ002 — Bob API work: end-to-end test suite

**Purpose:** exhaustive manual verification of every piece of Bob's API
integration before the staging link goes to the client. Basic testing is
done; this is the be-seriously-sure pass. Work through in order — the
suite follows the guest journey, and later sections assume earlier ones
pass.

**Built from:** Bob's merged work (PRs #3/#4/#5 + June commits) — villa
listing search mode, card price hydration, date-range picker, enquiry
panel live pricing + RTB gating, booking-confirmation flow, accommodation
enquiry forms, GF plumbing — and the current component code
(`enquiry-panel.js`, `date-range-picker.js`, `villa-listing-grid.php`,
`hero-search.php`).

---

## 0. Environment setup (do first, every run)

- [ ] Test on **staging** (the environment the client will click), with
  the remedial Maps key in place. A local pass first is fine but staging
  is the sign-off environment.
- [ ] Fresh private window per journey; DevTools open with **Console**
  and **Network** tabs visible throughout. **Zero console errors is a
  pass criterion on every single test below** — note any that appear.
- [ ] Clear WP Rocket page cache before starting, then run the full
  suite a second time **with cache warm** — API-driven content
  (prices, availability) must never be baked into cached pages.
- [ ] **Forms safety check before anything else:** confirm where GF
  submissions route on staging (notifications, Zoho, SendGrid,
  Campaign Monitor/Mailchimp feeds). Either point them at a test
  address or warn the recipients — the suite below submits real
  entries repeatedly. Record the arrangement here: ________
- [ ] Test data identified: one villa **with** current offers, one
  **without**; one villa with a known **unavailable** date range in
  Bob's system; one villa at the **cheapest** and one at the **most
  expensive** end (price formatting extremes); the villas from the
  content batch (copy just changed — good double-duty).
- [ ] Device matrix for the full journey (sections 1–5): desktop Chrome,
  desktop Safari, desktop Firefox, iPhone Safari, Android Chrome.
  Sections 6–8 can be desktop Chrome + iPhone Safari only.

---

## 1. Hero search (homepage)

**Date-range picker**
- [ ] Opens from the trigger; closes on outside click and on Esc;
  page **scroll is not blocked** while the popover is open (regression:
  `eb755a2`).
- [ ] Cannot select past dates; selecting a from-date then an earlier
  to-date behaves sanely (re-anchors or rejects — no broken state).
- [ ] Selected range shows correctly in the display element
  (`data-bob-date-range-display`); hidden `date-from`/`date-to` inputs
  carry the values (inspect DOM).
- [ ] **Clear** resets display, hidden inputs, and calendar state.
- [ ] Long ranges (3+ weeks), single-week, and Saturday-to-Saturday
  ranges all selectable; month navigation forward ~12 months works.
- [ ] Mobile: picker fits the viewport at 390px, is scrollable if tall,
  and doesn't trap focus.

**Guests + submit**
- [ ] Guests dropdown offers plain **1–12** (no "guests" suffix
  weirdness); default state sensible.
- [ ] Submit with dates + pax → lands on villa listing **with search
  context applied** (URL carries the search params — record the exact
  param names here for the rest of the suite: ________).
- [ ] Submit with **no dates** → acceptable behaviour (browse mode, no
  error, no broken request).
- [ ] On the villa listing page itself, hero search submit
  **anchor-scrolls to the results grid** (`a0b4990`) rather than
  reloading to the top.
- [ ] Keyboard-only: the whole search is operable by Tab/Enter/arrows.

## 2. Villa listing grid

**Browse mode (no search)**
- [ ] All published villas render; order matches the intended
  `menu_order`; grid columns stay at fixed size even when a filter
  leaves few cards (`9f2e411`).
- [ ] Card prices hydrate from the API (`data-bob-from-price`) shortly
  after load; before hydration there's no broken placeholder; a villa
  the API returns **no price** for shows the honest empty state — and
  **no** "Select dates for price" prompt anywhere on cards (`6bcbe72`).

**Search mode (arrive from hero search with dates + pax)**
- [ ] **Skeleton loading state** appears, then soft-reveals results
  (`f94aea5`); the head-start fetch means results appear without a
  perceptible double-load.
- [ ] Non-matching villas are **hidden** — cross-check one hidden villa
  by testing its dates directly on its detail page: the grid and the
  enquiry panel must agree about availability.
- [ ] **Selected-dates chip** shows the searched dates (`6897fe3`); the
  **active-search pill is server-rendered** — no toolbar reflow/flash on
  load (`2876cc6`).
- [ ] Dated **weekly rates** on cards match what the enquiry panel
  quotes for the same villa + dates (spot-check 3 villas).
- [ ] **Offers filter** (`fccea43`): toggling narrows to villas with
  offers; combined with an active date search it applies both; badges
  on cards match the villas that actually carry offers.
- [ ] Clearing the search returns to browse mode cleanly (no stuck chip,
  pill, or hidden villas).
- [ ] URL is shareable: paste the search URL into a fresh private window
  — same results, chip, and pill render.

## 3. Villa detail — enquiry panel (the money component)

**Arrival + prefill**
- [ ] Clicking a card from an active search **hands the context over**:
  panel arrives with dates + pax prefilled (`1a8fb7a`); arriving with
  no search → panel is empty-but-inviting, no errors.
- [ ] **Price block stays hidden until the API paints** (`ada756c`) —
  throttle to Slow 3G and confirm no flash of empty/zero pricing.

**Live pricing**
- [ ] Selecting available dates + pax fetches pricing: base rental,
  cleaning, and total (`data-bob-base-rental`, `data-bob-cleaning`,
  `data-bob-total-eur`) render and **sum correctly**; the dated weekly
  rate appears in the villa overview (`35e5ea5`).
- [ ] Change dates → prices update (no stale totals); change pax →
  re-fetches if pax affects price; rapid date-flipping doesn't produce
  a race where an older response overwrites a newer one (flip quickly
  between two ranges with different prices and check the final total
  matches the final range).
- [ ] Currency/format: EUR formatting consistent everywhere (cards,
  panel, overview); GBP indicator (Stripe-rates feature) sane against
  the EUR figure; cheapest + most expensive villas both format
  correctly (thousands separators).

**Availability gating (RTB)**
- [ ] Known-unavailable range → **unavailable notice** shows
  (`data-bob-msg-unavailable`), request-to-book is **gated off**
  (`eb271a6`), and contact-detail capture behaves per design
  (`0cdd0ff`).
- [ ] Switching from unavailable → available dates re-enables the flow
  cleanly (no lingering notice or disabled button).

**Contact details + phone field**
- [ ] Pax selector in panel is plain 1–12, matching hero format
  (`9ada205`); guests field placeholder correct (`ibv-pax` filter —
  attribute order and null-safety were hardened in `5de7943`, so check
  it on **every** form in the suite, not just this one).
- [ ] intl-tel-input: country selector works; placeholder matches the
  chosen country (`bca7aaf`); an invalid phone shows the invalid-phone
  message (`data-bob-msg-invalid-phone`); a valid non-UK number passes;
  the country selector is **skipped in tab order** (`39a0305`).
- [ ] Panel's embedded Gravity Form (`81535eb`): arrow submit renders
  and works (`7d80374`); validation errors display at sane size (the
  giant-submission-error fix, `71cde88`); no WP screen-reader-text
  artefacts visible anywhere (`c47c1ca`).

**Failure modes**
- [ ] DevTools → block the API endpoint (`data-bob-endpoint` URL) →
  panel degrades to the error/fallback state (`data-bob-error`,
  `data-bob-msg-price-error`) — **never** a spinner forever, never a
  bookable state with no price.
- [ ] Offline mid-flow (set Network to Offline after dates chosen) →
  no unhandled promise errors in console.

## 4. Booking confirmation flow

- [ ] Complete an RTB from the enquiry panel → **redirect** to the
  booking-confirmation page with villa identified and renamed params
  (`b76eefa`, `3922a85`) — record the exact URL shape here: ________
- [ ] Confirmation page renders the **URL-driven details list** —
  correct villa, dates, pax, price — centred and styled (`712dffa`).
- [ ] The concierge CTA and any page-picker-driven links on the page
  resolve on **staging** (July lesson: page-picker fields are
  per-environment — this is where a missed admin-checklist item shows
  up).
- [ ] **Direct access** to the confirmation URL with **no params** →
  page degrades gracefully (no PHP notices, no half-empty list).
- [ ] **Tampered params**: nonsense villa id/slug, garbage dates,
  `pax=999`, script tags in a param → output is escaped, nothing
  breaks, nothing echoes raw input (Bob fixed escaped output twice —
  `3ce1b00`, `5afbab5` — verify it held).
- [ ] Browser **back** from confirmation → enquiry panel state sane
  (no resubmission prompt loop, no double entry in GF).
- [ ] The one-shot deploy/seed notices are gone for ordinary users
  (`dd5ba89`, `f3a9d77`) — check as a logged-out visitor **and** as a
  non-deployer admin.

## 5. Accommodation enquiry pages

- [ ] Both accommodation pages (`81535eb`) render; form layout matches
  design (Figma clean-up pass `7f217a4`).
- [ ] Arrow submit full-width and functional (`71cde88`); phone field
  placeholder + validation as per section 3; guests placeholder filter
  correct.
- [ ] Successful submission → redirect/confirmation behaviour correct
  (`c96dbd9` code-seeded flow); the GF entry records every field.
- [ ] Submission-error state (submit empty) renders tame and readable.

## 6. Forms plumbing (backstage)

- [ ] Every test submission from sections 3–5 appears in **GF Entries**
  with complete data.
- [ ] Notifications arrive where expected (per the section-0
  arrangement); check spam.
- [ ] Zoho / SendGrid / Campaign Monitor / Mailchimp / Zapier /
  webhook feeds: confirm which are **active on staging** and that test
  entries either route to test targets or the feeds are paused —
  document the launch-day state to restore.
- [ ] Double-submit (double-click the arrow) creates **one** entry.

## 7. Cross-cutting sweeps

- [ ] **Cache interaction:** with WP Rocket warm, load a villa page,
  note the price, change dates — prices are always fetched live, never
  cached; the search-context handoff works from a cached listing page.
- [ ] **Second full pass on mobile** (iPhone Safari): the entire
  journey 1→4 end-to-end, one villa, one booking. Thumb-reach, tap
  targets, picker usability, no horizontal scroll anywhere.
- [ ] **Keyboard-only pass** (desktop): hero search → listing → panel
  → RTB → confirmation, without touching the mouse.
- [ ] **Console/network audit:** across all of the above, zero JS
  errors, no 4xx/5xx from the API in normal flows, no mixed-content
  warnings on staging HTTPS.
- [ ] **Maps smoke test** (post-remedial-key): map renders on villa
  detail, styled (grey/white, POI + transit hidden), no console key
  errors — full maps verification is its own task, this is just
  confirming the remedial swap didn't interact with Bob's panel JS on
  the same page.

## 8. Sign-off

- [ ] Both cache states passed; all five devices passed sections 1–5.
- [ ] Issue log below is empty, or every open item is triaged as
  fix-before-link / fix-during-testing-window / cosmetic.
- [ ] GF/CRM feeds restored to the agreed staging state.

| # | Where | What happened | Severity | Fix before link? |
|---|---|---|---|---|
| 1 | | | | |

---

*Companion task, not in this suite: the remedial Google Maps setup
(CW key, staging hostname restriction) and the later swap to the
client's console/Map ID once Steve responds.*
