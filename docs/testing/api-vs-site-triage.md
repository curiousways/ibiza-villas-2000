# Availability and pricing — our code vs Steve's API

**Date:** 21 August 2026, ~13:30–14:10 UK (before the 15:00 staging CMS session).  
**Endpoint:** `https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl`  
**No Gravity Forms were submitted. No code was changed.**

## How this was tested

Staging HTML is behind the password-protect login. This environment could not open
`https://staging.ibizavillas2000.com/…` as a guest. The live API is the same
production CGI both staging and local call from the browser, so every request
below is the payload the client site actually consumes.

CMS values (`property_id`, indicative from-prices, offers) were read from
**local** WordPress. Staging is the content source of truth; if Tina edited
those fields this week, re-check them on staging before the Steve meeting.
Media on local already points at staging (`BE Media from Production`), and the
featured-offer figures look like current staging test data (€1,222 / €1,111).

Local pages used for render comparison: `/villas/villa-savines/`,
`/villas/villa-nieves/`, `/our-villas/`, `/special-offers/`.

Twelve deliberate GETs, spaced ~3s. No sweeps.

---

## 1. Summary

| ID | Symptom | Verdict | One-line reason |
|---|---|---|---|
| A1 | Savines shows unavailable when it is available | **DATA-ENTRY** | CMS `property_id` is `savines`. The API's villa key is `savinas`. `?villa=savines` returns `villas: []`; `?villa=savinas` returns the villa as available. |
| A2 | Nieves returns a £56k price | **UNREPRODUCIBLE** (see A9) | No captured Nieves row is in that range. The 56k-class figure is what our weekly renormalisation produces on a 1-night search if `eur_base_rental` is already a weekly rate. |
| A3 | A price is returned for a 1-night stay against a 3-night minimum | **API-GAP** | No minimum-stay field exists in the payload. The API will return a priced, `available: 1` row for a 2-night search. |
| A4 | Short-breaks search returns Nieves on a 2-night search | **API-GAP** (Nieves itself not in this window) | Same missing min-stay field. This 2-night window returned 9 villas including Savines (`savinas`), not Nieves. |
| A5 | Out-of-season villa still priced as ADW + cleaning, no rental | **OURS** (API sends `available: 1`) | Winter Nieves: `available: 1`, `eur_base_rental: 0`, fees + total €368. `paint()` treats a non-null total as a real price. |
| A6 | Special Offers shows out-of-season villas; click-through price is wrong | **OURS** | That page never calls the API. Cards are ACF offers. Click-through uses `villa=savines` (A1) and an empty indicative from-price. |
| A7 | Nieves shows two different prices | **BY-DESIGN** / not reproduced as a dated clash | Static "From" and live stay total are different numbers on purpose. We do **not** keep both dated prices — we overwrite. Nieves has no static from-price locally. |
| C6 | Enquiry panel traps scroll | **OURS** | Sticky rail + `overflow-y: auto; overscroll-behavior: contain` on the form body. No API. |
| A8 | Villa Tegui never hydrates / never matches a search | **DATA-ENTRY** | Listing card `data-bob-property-id=""`. Same family as A1. |
| A9 | Short-stay search paints enormous "/ wk" prices | **OURS** (needs Steve to confirm the unit) | For a 2-night Savines hit, `eur_base_rental` is €12,137. We do `(rate * 7) / nights` → **€42,480 / wk**. A 1-night search of an ~€8k weekly rate would display **€56,000 / wk**. |

---

## 2. Per-symptom detail

### Shared CMS pins

| Villa | WP ID (local) | `property_id` | Indicative from-price | Active ACF offers |
|---|---|---|---|---|
| Villa Savines | 6998 | `savines` | empty | 0 (featured offer is Site Options, not a villa offer) |
| Villa Nieves | 2818 | `nieves` | empty | 0 |
| Villa Tegui | (listing card) | **empty string** | — | — |

Enquiry panel attribute on both villa pages: `data-bob-property-id` matches the
table above. Endpoint attribute is the production CGI.

Listing browse/probe window on 21 Aug 2026 (server UTC +30 / +37 days):

```
date_from=2026-09-20&date_to=2026-09-27&pax=2
```

That URL is also the `<link rel="preload">` on `/our-villas/`.

---

### A1 — Savines unavailable

**Inputs.** Villa Savines, CMS `property_id=savines`. Dates used (listing probe
week and a 2-night slice of it), pax 2. Client did not supply the exact dates
they clicked; these are the dates the site itself probes.

**Request the page would send** (enquiry-panel.js:549):

```
https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl?villa=savines&date_from=2026-09-20&date_to=2026-09-27&pax=2
```

**Raw response** (replay identical):

```json
{"success":1,"count":0,"query":{"to":"2026-09-27","nights":7,"from":"2026-09-20","pax":"2"},"villas":[]}
```

Same empty body for `villa=savines` on 2026-09-20→21 (1 night) and
2026-07-04→11 (7 nights).

**Same 2-night window, API's own spelling**
`?villa=savinas&date_from=2026-09-20&date_to=2026-09-22&pax=2`:

```json
{"success":1,"count":1,"query":{"to":"2026-09-22","nights":2,"from":"2026-09-20","pax":"2"},"villas":[{"eur_extra_cleaning":310,"euro_rate":"1.17","gbp_adw_amount":50,"gbp_total_price":10689,"eur_total_price":12506,"gbp_base_rental":10374,"villa":"savinas","eur_adw_amount":58,"nice_name":"Villa Savines","eur_base_rental":12137,"gbp_extra_cleaning":265,"available":1}]}
```

Search mode (no `villa=`) for those two nights includes the same row:
`"villa":"savinas","nice_name":"Villa Savines"`.

`?villa=savinas` for the **7-night** probe week is empty — so Savines really
is not available 20–27 Sep. The 2-night slice is.

**What the page does.** `paint()` (enquiry-panel.js:498–507) takes
`villas[0]`; `[]` → `node === null` → `false` → unavailable notice
(*"This villa isn't available for your selected dates…"*). Listing search
matches `data-bob-property-id="savines"` to `row.villa` (`savinas`) and
**hides the card** even when the API named Villa Savines as available.

**Verdict: DATA-ENTRY.** The query we send is well-formed. The key we send
is not the key Steve's system uses. Changing the ACF field to `savinas`
(after Steve confirms) fixes enquiry and listing together. There is no
alias layer in our JS.

---

### A2 — Nieves £56k

**Inputs.** Villa Nieves, CMS `property_id=nieves`, pax 2.

**Requests / responses.**

`?villa=nieves&date_from=2026-09-20&date_to=2026-09-27&pax=2`:

```json
{"success":1,"count":0,"query":{"to":"2026-09-27","nights":7,"from":"2026-09-20","pax":"2"},"villas":[]}
```

`?villa=nieves&date_from=2026-07-04&date_to=2026-07-11&pax=2` — same empty
`count: 0` body.

`?villa=nieves&date_from=2027-01-16&date_to=2027-01-23&pax=2` — see A5
(`eur_total_price: 368`, rental 0). Not £56k.

2-night search 20–22 Sep: Nieves is **absent** from the 9 returned villas.

**What the page would render.** Empty `villas` → unavailable notice, no
price. Winter row → €368 total (A5), not €56k. We never paint GBP; the
`gbp_*` fields are ignored.

**Verdict: UNREPRODUCIBLE** for a Nieves-specific £56k. Closest mechanism
is A9: if the API returns a weekly-looking `eur_base_rental` of ~€8,000 on
a 1-night search, `parseResults` / `showDatedOverviewPrice` do
`(rate * 7) / nights` → **€56,000 / wk**. I did not fire a 1-night Nieves
query (no priced Nieves row in the windows already used).

---

### A3 / A4 — minimum stay

**Full field list** seen on every non-empty villa object (search-2n,
savinas-2n, nieves-winter). Nothing else is present:

```
available
eur_adw_amount
eur_base_rental
eur_extra_cleaning
eur_total_price
euro_rate
gbp_adw_amount
gbp_base_rental
gbp_extra_cleaning
gbp_total_price
nice_name
villa
```

Plus top-level `success`, `count`, `query.{from,to,nights,pax}`.

There is **no** `min_stay`, `minimum_nights`, `min_nights`, or equivalent.
We do not ignore a field — it is not there.

**A3 request** (1-night, CMS spelling — empty because of A1):

```json
{"success":1,"count":0,"query":{"to":"2026-09-21","nights":1,"from":"2026-09-20","pax":"2"},"villas":[]}
```

**A4 request** (2-night search, the shape listing-grid.js:382–386 sends):

```
https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl?date_from=2026-09-20&date_to=2026-09-22&pax=2
```

Response: `success: 1`, `count: 9`, `nights: 2`. Villas (ids only):
`bellevista`, `km2`, `tunicu`, `vincent`, `luis`, `alexa`, `torres`,
`savinas`, `martha`. All `available: 1` with full fee + rental totals.
**Nieves is not in this list.**

Our short-breaks CTA is **not** a 2-night search. It is +30 / +34 days
(4 nights, pax 2) — `short-breaks.php:46–52`. I did not replay that 4-night
URL.

**Verdict: API-GAP** for minimum stay. Options for the meeting:

1. Steve exposes a min-stay (and/or rejects short stays with `available: 0`).
2. We hold min-stay as a WordPress field and suppress the price ourselves.
3. We suppress the card/panel price whenever stay length cannot be verified.

A4 as "Nieves appears on a 2-night search" is **not** true for 20–22 Sep.
The underlying gap (API will happily price a 2-night stay) still stands —
Savines/`savinas` is the example.

---

### A5 — out-of-season price is ADW + cleaning

**Inputs.** Nieves, 16–23 Jan 2027, pax 2.

**Request:**

```
https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl?villa=nieves&date_from=2027-01-16&date_to=2027-01-23&pax=2
```

**Raw response:**

```json
{"success":1,"count":1,"query":{"to":"2027-01-23","nights":7,"from":"2027-01-16","pax":"2"},"villas":[{"eur_extra_cleaning":310,"euro_rate":"1.17","gbp_adw_amount":50,"gbp_total_price":315,"eur_total_price":368,"gbp_base_rental":0,"villa":"nieves","eur_adw_amount":58,"nice_name":"Villa Nieves","eur_base_rental":0,"gbp_extra_cleaning":265,"available":1}]}
```

**Does this trigger our unavailable path?** No.

`paint()` (enquiry-panel.js:506–530):

1. `available === 1` → do not return false.
2. `eur_total_price === 368` → not null → paint.
3. Rental `0` is a number, so the breakdown shows **€0** rental, **€58**
   ADW, **€310** cleaning, **€368** total.
4. `rent > 0` fails, so the overview dated price is reset — but the panel
   still shows the fee-only total and treats the villa as priced.

Listing `parseResults` keeps the row (`available: 1`). `applyResults`
skips writing "From €0 / wk" (`rate > 0` at villa-listing-grid.js:341)
but **still puts Nieves in `availablePids`**, so a dated search would
*show* the card with the empty/static price.

There is no unavailable marker to ignore. The API says available, with a
total that is only fees.

The 9 June "No availability" empty state is still unbuilt. Even if it
existed, this payload would not feed it.

**Verdict: OURS** for painting a fee-only total as a bookable price.
Ask Steve whether `available: 1` + `eur_base_rental: 0` is an intentional
"open but no rate card" or should have been `available: 0`.

Recommended gate (do not implement here):

```js
if ( total === null || rent === null || rent <= 0 ) {
    return false;
}
```

---

### A6 — Special Offers query and click-through

**What that page actually runs.** Nothing. `page-special-offers.php` renders
the featured-offer section, then `ibv_core_section_special_offers_grid()`,
then short-breaks. It does not enqueue `villa-listing-grid.js` and does not
localise `ibvListingSearch`. Confirmed on the local HTML: no preload of
`web_availability.pl`, no listing-grid script.

The grid is every published villa with an active `villa_offers` row. No
date filter against the API. Out-of-season villas stay on the page for as
long as the ACF offer dates say they should.

**Featured offer (local HTML, likely staging values).** Villa Savines.
Was €1,222 / wk. Now €1,111 / wk (`data-bob-from-price="6998"` — the
WordPress post ID, not `savines` / `savinas`). Valid 30 May 2026 – 31 Dec
2026. Those hooks are inert because listing JS is not on the page.

**Click-through.** `View Villa` → `/villas/villa-savines/` with **no**
`date_from` / `date_to` / `pax`. Overview indicative from-price is empty.
Enquiry panel will not fetch until the guest picks dates; when they do, it
sends `villa=savines` (A1) and gets `villas: []` for any window I tried
except the API spelling.

Side by side for the listing probe week:

| Surface | Request | Result |
|---|---|---|
| Special Offers | none | Editorial €1,111 |
| Villa page (after dates) | `?villa=savines&date_from=2026-09-20&date_to=2026-09-27&pax=2` | empty → unavailable |
| Listing search | `?date_from=2026-09-20&date_to=2026-09-27&pax=2` | only `martha` |

**Verdict: OURS.** Offers are editorial; the page is not an availability
surface. The click-through price cannot match a live rate because (a) no
dates are passed, (b) the villa page has no static from-price, (c) A1.

---

### A7 — two prices on Nieves

Agreed design (Airbnb precedent): after a dated search the static "From"
price stays visible, plus a dated figure.

**What is built.**

- Listing: `applyResults` **replaces** the `data-bob-from-price` text with
  the dated weekly rate and keeps the "From" / "/ wk" labels. One number.
- Villa overview: `showDatedOverviewPrice` (enquiry-panel.js:360) **hides**
  "From" and overwrites the amount with the dated weekly rate. One number.
- Enquiry panel: stay **total** (`eur_total_price`), not a weekly rate.

Nieves has no static from-price locally, so a dated overwrite has nothing
to sit next to.

I did not see two *dated* prices disagreeing. A guest can still see two
different numbers on one villa page if they pick dates that return a
price: overview weekly vs panel stay total. That is two presentations of
one payload, not two API answers.

**Verdict: BY-DESIGN** for "From" vs stay total. The "keep the static From
after search" behaviour is **not implemented** — we overwrite. Not a
Steve item.

---

### C6 — scroll trapped on the enquiry rail

No API. Desktop (`min-width: 64rem`) the panel is `position: sticky` with
`max-height: calc(100dvh - …)` and the form body is:

```css
/* enquiry-panel.css:50-54 */
.ibv-enquiry-panel__form-embed .gform_body {
    overflow-y: auto;
    overscroll-behavior: contain;
}
```

Wheel / trackpad over the right-hand rail scrolls the form, not the page.
Over the left column, the page scrolls. That matches the report.

**Verdict: OURS.** Start at `enquiry-panel.css:14-55`.

---

### A8 — Tegui empty `property_id`

On `/our-villas/` the Tegui card is `data-bob-property-id=""`. Search
matching and probe hydration both key off that attribute. An empty id
never matches an API `villa` value.

**Verdict: DATA-ENTRY.** Same family as A1. Confirm the Steve key (likely
`tegui` / `can-teni` or similar) before writing it.

---

### A9 — short-stay "/ wk" inflation

From the 2-night search row for Savines (`savinas`):

- `query.nights`: 2
- `eur_base_rental`: 12137
- `eur_total_price`: 12506 (rental + 58 + 310)

If 12137 is already a **weekly** rate, the API is not pro-rating short
stays (A3). Our listing then does `(12137 * 7) / 2` = **€42,480 / wk**.
A 1-night search of an ~€8,000 weekly rate would display **€56,000 / wk**
(A2's shape).

If 12137 is a **2-night stay total**, €6k/night is itself implausible for
this villa, and the weekly conversion is still the number we would paint
on the card.

**Verdict: OURS** for the display math (`villa-listing-grid.js:106-136`,
same formula at `enquiry-panel.js:525-526`). Steve needs to confirm the
unit of `eur_base_rental` on non-7-night queries before we change it.

---

## 3. Questions for Steve

Phrased so they can be answered without the rest of this doc.

1. **Villa Savines — what is the villa key in your system?** Our CMS
   sends `savines`. Your search payload returns `"villa":"savinas"` with
   `"nice_name":"Villa Savines"`. `?villa=savines` is always an empty
   list. `?villa=savinas` returns the villa. Which spelling should we
   store?

2. **Is there a minimum-stay value we should be reading?** Every villa
   object we saw has only: `available`, `villa`, `nice_name`,
   `eur_base_rental`, `eur_adw_amount`, `eur_extra_cleaning`,
   `eur_total_price`, the `gbp_*` twins, and `euro_rate`. A 2-night
   search (20–22 Sep 2026, pax 2) returned 9 priced villas, including
   Savines. Options if there is no field: you expose one; we store one
   in WordPress; or we hide prices when stay length cannot be checked.

3. **On a 2-night search, is `eur_base_rental` the stay total or a weekly
   rate?** Savines 20–22 Sep: `eur_base_rental` 12137, `eur_total_price`
   12506. We currently treat it as a stay total and convert to a weekly
   average. If it is already weekly, our cards will show ~€42k / wk for
   that search (and ~€56k / wk on a 1-night search of an €8k villa).

4. **Out of season: Nieves 16–23 Jan 2027 came back `available: 1`,
   `eur_base_rental: 0`, ADW 58, cleaning 310, total 368.** Is that
   "open, no rate card loaded" or should it have been `available: 0`?
   We currently paint €368 as a real price.

5. **Nieves — have you seen a ~£56k / €56k figure for a normal week?**
   We could not reproduce it with `villa=nieves` on 4–11 Jul 2026,
   20–27 Sep 2026, or 16–23 Jan 2027. If you have the query they used,
   we can replay it.

---

## 4. Our list

Not fixes — where a later brief would start.

| ID | Start here |
|---|---|
| A1 / A8 | CMS Identity tab `property_id` on Savines (6998) and Tegui. No code required if Steve confirms the keys. Optional later: an alias map in `enquiry-panel.js` / `parseResults` if keys will keep drifting. |
| A5 | `enquiry-panel.js` `paint()` lines 510–530 — `rent === 0` still paints. Listing `applyResults` ~325–369 still includes a 0-rate villa in `availablePids`. |
| A6 | `page-special-offers.php` + `special-offers-grid.php` — no API by design. Click-through is a bare permalink (`villa-card.php:186`). Offer panel hook uses the WP post ID (`offer-panel.php:188`). |
| A7 | `showDatedOverviewPrice` in `enquiry-panel.js:360` (overwrites static From). `applyResults` in `villa-listing-grid.js:343` (same overwrite on cards). |
| A9 | `villa-listing-grid.js:106-136` and `enquiry-panel.js:525-526`. Do not change the formula until question 3 is answered. |
| C6 | `enquiry-panel.css:14-55` (`overflow-y` / `overscroll-behavior` on `.gform_body`). |
| Unbuilt | 9 June "No availability" listing empty-state — still not built; would not have fired on the A5 payload anyway. |

---

## 5. Could not test

- **Staging HTML / Console.** Password gate. Re-check Savines and Tegui
  `property_id` on staging before the meeting; if they already say
  `savinas`, A1 is fixed there and this report's CMS pin is stale.
- **The client's exact A1/A2 dates and pax.** Not in the tracker text.
  Used the site's own probe week and a 2-night slice. A different week
  could still be a genuine booking conflict (`villas: []` is also what
  a booked week looks like).
- **Short-breaks CTA URL** (4 nights, +30/+34 days). A4 was tested as
  the reported 2-night search, not that CTA.
- **1-night `?villa=savinas` or `?villa=nieves`.** Stopped rather than
  add more combinations. Field list is already complete from other rows.
- **C6 in a real browser on staging.** Classification is from the CSS
  that ships on that page. Confirm visually after the password session
  if needed.
- **Whether Steve's back-office spelling is `savinas`.** Inferred only
  from the API's own `villa` field.

---

## Environment / console

- API HTTP 200 on every successful call. No retries on failure.
- Local villa and listing pages returned 200 with no PHP fatals.
- Staging document URL redirected to
  `?password-protected=login&redirect_to=…`.
- No form submissions.
- Enquiry-panel `console.warn` for a failed fetch was not triggered
  (requests were made with curl, not the page).
