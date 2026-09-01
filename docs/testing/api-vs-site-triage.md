# Availability and pricing — our code vs Steve's API

**Date:** 21 August 2026, ~13:30–14:10 UK (before the 15:00 staging CMS session).  
**A1 re-run:** 21 August 2026, ~13:38 UK, after `property_id` was changed to `savinas`.  
**A5 / A7 update:** 28 August 2026. Steve's November dates replayed; zero-rate
gate shipped in `docs/briefs/done/04-zero-rate-unavailable.md` (`ef9d7eb`).
**A2 / A9 update:** 1 September 2026. Steve confirmed on 28 August that
`eur_base_rental` is the stay total for the exact search and that no weekly
equivalent can be derived from it. The `(rate * 7) / nights` conversion is
removed in `docs/briefs/active/05-stay-total-not-weekly.md`.
**Endpoint:** `https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl`  
**No Gravity Forms were submitted.**

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
| A1 | Savines shows unavailable when it is available | **DATA-ENTRY — fixed** | CMS `property_id` is now `savinas`. Enquiry panel and listing card emit that key. `?villa=savinas` 20–22 Sep returns the villa; the old `savines` key still returns `[]`. The 7-night probe week is still empty — that week is genuinely unavailable. |
| A2 | Nieves returns a £56k price | **OURS — same cause as A9, fixed** | 1-night Nieves 11–12 Jul 2026, pax 2: payload `eur_base_rental: 9534`. We painted `9534 × 7 / 1` = **€66,738 / wk**. The client's €56k-class screenshots are this conversion on short stays. |
| A3 | A price is returned for a 1-night stay against a 3-night minimum | **API-GAP** | No minimum-stay field exists in the payload. The API will return a priced, `available: 1` row for a 2-night search. |
| A4 | Short-breaks search returns Nieves on a 2-night search | **API-GAP** (Nieves itself not in this window) | Same missing min-stay field. This 2-night window returned 9 villas including Savines (`savinas`), not Nieves. |
| A5 | Out-of-season villa still priced as ADW + cleaning, no rental | **OURS — fixed** (`04-zero-rate-unavailable`) | Winter Nieves 13–20 Nov: `available: 1`, `eur_base_rental: 0`, fees + total €368. `paint()` now returns false when `rent <= 0`; listing `parseResults` drops the row so the card is not in `availablePids`. |
| A6 | Special Offers shows out-of-season villas; click-through price is wrong | **OURS** | That page never calls the API. Cards are ACF offers. Click-through is a bare permalink; A1's wrong key is no longer the reason the villa page disagrees. |
| A7 | Nieves shows two different prices | **PARTLY OURS** (see 1 Sep note) | After A9, overview and panel both use stay figures from the same row: overview shows `eur_base_rental` labelled for the nights searched; the panel still itemises rental / waiver / cleaning / `eur_total_price`. The remaining two-number view is fees, not a weekly conversion. The 21 Aug "by design" call also missed Pep Luis static From + fee-only winter paint — that path is A5 and is gated. |
| C6 | Enquiry panel traps scroll | **OURS** | Sticky rail + `overflow-y: auto; overscroll-behavior: contain` on the form body. No API. |
| A8 | Villa Tegui never hydrates / never matches a search | **DATA-ENTRY** | Listing card `data-bob-property-id=""`. Same family as A1. |
| A9 | Short-stay search paints enormous "/ wk" prices | **OURS — fixed** (`05-stay-total-not-weekly`) | Steve, 28 August: the API price is the stay total for the exact search; no daily/weekly equivalent is derivable. We were doing `(eur_base_rental * 7) / nights` and labelling `/ wk`. Removed. Dated surfaces now show the stay total labelled for the nights searched. |

---

## 2. Per-symptom detail

### Shared CMS pins

| Villa | WP ID (local) | `property_id` | Indicative from-price | Active ACF offers |
|---|---|---|---|---|
| Villa Savines | 6998 | `savinas` (was `savines`; updated 21 Aug) | empty | 0 (featured offer is Site Options, not a villa offer) |
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

**Verdict: DATA-ENTRY — fixed locally 21 Aug ~13:38.** The query we send is
well-formed. The key we sent was not the key Steve's system uses. The ACF
field is now `savinas`. There is no alias layer in our JS.

#### A1 re-run (after the field change)

Local CMS `get_field('property_id', 6998)` → `savinas`. Villa page
`data-bob-enquiry-panel` / `data-bob-property-id="savinas"`. Listing card
the same. The page will now send the URL that previously only worked when
we forged the spelling.

`?villa=savinas&date_from=2026-09-20&date_to=2026-09-22&pax=2` — replay
identical to the first pass:

```json
{"success":1,"count":1,"query":{"to":"2026-09-22","nights":2,"from":"2026-09-20","pax":"2"},"villas":[{"eur_extra_cleaning":310,"euro_rate":"1.17","gbp_adw_amount":50,"gbp_total_price":10689,"eur_total_price":12506,"gbp_base_rental":10374,"villa":"savinas","eur_adw_amount":58,"nice_name":"Villa Savines","eur_base_rental":12137,"gbp_extra_cleaning":265,"available":1}]}
```

`paint()` would now succeed (`available: 1`, `eur_total_price: 12506`) and
the listing card would match `row.villa`. Control: `?villa=savines` for
the same two nights is still `count: 0`.

`?villa=savinas&date_from=2026-09-20&date_to=2026-09-27&pax=2` is still
`villas: []`. That week is unavailable in Steve's system, not a key bug.
A dated search for 20–27 Sep will still hide / notice-unavailable Savines,
and that is now the correct answer.

Not re-checked: staging HTML (still password-gated). If staging does not
yet have `savinas`, the client environment will keep sending `savines`.

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

**1 September 2026 replay.** `?villa=nieves&date_from=2026-07-11&date_to=2026-07-12&pax=2`:

```json
{"success":1,"count":1,"query":{"to":"2026-07-12","nights":1,"from":"2026-07-11","pax":"2"},"villas":[{"eur_extra_cleaning":310,"euro_rate":"1.17","gbp_adw_amount":50,"gbp_total_price":8464,"eur_total_price":9902,"gbp_base_rental":8149,"villa":"nieves","eur_adw_amount":58,"nice_name":"Villa Nieves","eur_base_rental":9534,"gbp_extra_cleaning":265,"available":1}]}
```

`eur_base_rental: 9534`. The old conversion painted `9534 × 7 / 1` = **€66,738 / wk**. That is A9, and it is the family of figures in the client's screenshots (listing €56k-class, Alexa ~€12k on three nights, one-night price). Steve's alternate theory — that €56k was the API summing every available villa on a search without `villa=` — is not what the payloads show: each row carries its own rental.

**Verdict: OURS — same cause as A9, fixed** in `05-stay-total-not-weekly`. Dated surfaces now pass `9534` through unconverted.

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

**Does this trigger our unavailable path?** Yes, as of 28 August
(`04-zero-rate-unavailable`).

`paint()` now returns false when `total === null || rent === null || rent <= 0`,
before any `setText()`. The caller shows the existing unavailable notice,
hides the price block, and reveals contact fields. Listing `parseResults`
drops the row, so it never reaches `availablePids`.

The 21 August write-up below is the pre-fix behaviour, kept so the
recommendation is not lost.

`paint()` (enquiry-panel.js, before the gate):

1. `available === 1` → do not return false.
2. `eur_total_price === 368` → not null → paint.
3. Rental `0` is a number, so the breakdown shows **€0** rental, **€58**
   ADW, **€310** cleaning, **€368** total.
4. `rent > 0` fails, so the overview dated price is reset — but the panel
   still shows the fee-only total and treats the villa as priced.

Listing `parseResults` kept the row (`available: 1`). `applyResults`
skipped writing "From €0 / wk" (`rate > 0`) but **still put Nieves in
`availablePids`**, so a dated search showed the card with the empty/static
price.

There is no unavailable marker to ignore. The API says available, with a
total that is only fees.

**Verdict: OURS — fixed.** Ask Steve whether `available: 1` +
`eur_base_rental: 0` is an intentional "open but no rate card" or should
have been `available: 0`. The front-end gate is correct either way.

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
now sends `villa=savinas`. The 7-night probe week is still `villas: []`
(genuine unavailability). A 2-night pick would paint €12,506 (A1 re-run).

Side by side for the listing probe week:

| Surface | Request | Result |
|---|---|---|
| Special Offers | none | Editorial €1,111 |
| Villa page (after dates, first pass) | `?villa=savines&…` | empty → unavailable |
| Villa page (after dates, re-run) | `?villa=savinas&date_from=2026-09-20&date_to=2026-09-27&pax=2` | still empty — week unavailable |
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

Nieves has no static from-price, so a dated overwrite has nothing to sit
next to. That is why the 21 August pass called this by-design and missed
the real clash: **Pep Luis** (`villa_indicative_from_price` 4321) plus a
fee-only winter payload. `paint()` showed the static "From €4,321 / wk"
and "Price varies by season" above a live €368. That is A5, not two
presentations of one rate, and is gated as of 28 August.

A guest can still see two different numbers on one villa page if they pick
dates that return a *real* rental: overview `eur_base_rental` (labelled for
the nights searched, plus "Plus cleaning and damage waiver") vs panel
`eur_total_price` (rental + waiver + cleaning). That is two presentations
of one payload, not two API answers, and it is no longer a weekly-vs-stay
mismatch.

**Verdict: PARTLY OURS.** The fee-only + static-From clash is fixed with
A5. The weekly-vs-stay mismatch is fixed with A9. The remaining two
figures are the rental and the stay total with fees. The "keep the static
From after search" behaviour is **not implemented** — we overwrite. Not a
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

Steve answered on 28 August 2026:

> "The price the booking system gives you in the API is just 'the price'
> … for the exact search you put in — there is no daily / weekly equivalent
> derivable from it. It is a complex calculation of daily prices,
> weight-adjusted for short stays (so maybe a 4 day stay in August is same
> cost as a week)."

So 12137 is the 2-night stay total. The conversion was invalid at any
coefficient: short-break weighting is already inside the figure. We were
painting `(12137 * 7) / 2` = **€42,480 / wk**. Same formula on Nieves
1-night (`eur_base_rental: 9534`) produced **€66,738 / wk** (A2).

**Verdict: OURS — fixed** in `05-stay-total-not-weekly`. The conversion is
deleted. Dated overview and listing cards show the stay total and label
the nights searched. The static ACF `villa_indicative_from_price` "From …
/ wk" treatment on the undated state is unchanged.

---

## 3. Questions for Steve

Phrased so they can be answered without the rest of this doc.

1. **Villa Savines — what is the villa key in your system?** *(Answered
   by the payload; CMS now stores `savinas`.)* Your search payload returns
   `"villa":"savinas"` with `"nice_name":"Villa Savines"`. Confirm that
   spelling is stable so we do not flip it back.

2. **Is there a minimum-stay value we should be reading?** Every villa
   object we saw has only: `available`, `villa`, `nice_name`,
   `eur_base_rental`, `eur_adw_amount`, `eur_extra_cleaning`,
   `eur_total_price`, the `gbp_*` twins, and `euro_rate`. A 2-night
   search (20–22 Sep 2026, pax 2) returned 9 priced villas, including
   Savines. Options if there is no field: you expose one; we store one
   in WordPress; or we hide prices when stay length cannot be checked.

3. **On a 2-night search, is `eur_base_rental` the stay total or a weekly
   rate?** *(Answered 28 August 2026.)* Stay total for the exact search.
   No daily or weekly equivalent is derivable — short-break weighting is
   already inside the figure. Conversion removed in `05-stay-total-not-weekly`.

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
| A1 / A8 | Savines (6998) is now `savinas` locally — propagate to staging if not already. Tegui still empty. Optional later: an alias map in `enquiry-panel.js` / `parseResults` if keys will keep drifting. |
| A5 | **Fixed** in `04-zero-rate-unavailable`. `paint()` returns false when `rent <= 0`. Listing `parseResults` drops the row before `availablePids`. `applyResults` `rate > 0` guard left as belt-and-braces. |
| A6 | `page-special-offers.php` + `special-offers-grid.php` — no API by design. Click-through is a bare permalink (`villa-card.php:186`). Offer panel hook uses the WP post ID (`offer-panel.php:188`). |
| A7 | `showDatedOverviewPrice` in `enquiry-panel.js:360` (overwrites static From). `applyResults` in `villa-listing-grid.js:343` (same overwrite on cards). |
| A9 | **Fixed** in `05-stay-total-not-weekly`. Conversion deleted; dated surfaces show `eur_base_rental` labelled for the nights searched. A2 resolves to this same cause. |
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
