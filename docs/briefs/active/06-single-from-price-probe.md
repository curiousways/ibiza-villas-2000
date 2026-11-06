# Villa single “From” price — same undated probe as listing cards

> **For the implementing agent:** Implement the brief below. Read the
> relevant codebase files before starting. Push back on anything that
> doesn't make technical sense — ask rather than guess. When done,
> commit only if asked; report what you changed and how to verify.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** UX consistency fix. One JS file, small PHP + CSS on the
villa single surface. Reuses the listing’s existing probe window — does
not invent a second date formula.

**Follows** `docs/briefs/done/04-zero-rate-unavailable.md` and
`docs/briefs/active/05-stay-total-not-weekly.md` (or `done/` if that has
moved by the time you run). Those briefs left the undated single overview
on ACF by design; this brief deliberately changes that undated path only.

---

## Background

Smoke testing on 6 November 2026 showed listing cards and villa singles
disagreeing on “From €X / wk” for the same villa with no dates selected:

| Villa | Card (listing) | Single overview |
|---|---|---|
| Villa Nieves | €5,575 | €5,575 (matched) |
| Villa Bella Vista | €3,935 | €5,236 |
| Villa KM2 | €2,971 | €5,491 |

**Why.** Both surfaces server-render ACF `villa_indicative_from_price` into
`[data-bob-from-price]`. On the listing, when there is no dated search,
`villa-listing-grid.js` runs **probe mode**: it fetches Steve’s API for a
default future week (`ibv_villa_listing_probe_params()` — UTC +30 / +37
days, pax 2) and overwrites card amounts with `eur_base_rental`, still
labelled “From … / wk”. On the villa single, `enquiry-panel.js` only
touches the overview price after the guest picks dates
(`showDatedOverviewPrice` / `resetOverviewPrice`). Undated singles keep
the ACF figure forever.

Editors are **not** overriding the API in ACF. The field label already
says “Static fallback before live API prices.” Cards replace that
fallback; the single does not.

**Product decision (confirmed):** make them consistent by running the
**same undated probe** on the villa single overview. Do not chase parity
by editing ACF indicative prices — the probe window moves every day.

---

## Standards

Per `.cursor/rules/ibv-conventions.mdc`:

- Plain JS in the component’s own file; no new dependencies; no build step.
- British English in comments.
- User-facing strings stay in PHP (existing overview copy is fine — “From”,
  “/ wk”, “Price varies by season”). Do not hardcode new display copy in JS.
- Escape every PHP output. Reuse `ibv_villa_listing_probe_params()` —
  do not duplicate the +30/+37 UTC formula in a second PHP function or in
  JS as the primary source of truth.
- No new ACF fields. No changes to Special Offers, offers accordion, or
  the listing grid’s probe behaviour beyond what this brief explicitly
  allows (shared helper use only).

---

## Scope

**In:**

- Undated villa single overview: hydrate `[data-bob-from-price]` from the
  same probe window the listing uses, via the enquiry panel’s existing
  detail-mode endpoint (`?villa={property_id}&date_from=&date_to=&pax=`).
- Keep ACF as fallback when the probe fails, times out, returns
  unavailable, or returns `eur_base_rental <= 0`.
- After a successful probe, treating that figure as the undated “static”
  restore target when the guest later clears dates (so clear → probe
  “From”, not a flash back to a different ACF number).
- Optional but recommended: avoid ACF→API flash on first paint (mask
  overview amount while the undated probe is in flight), mirroring listing
  `--price-pending`.
- Optional but recommended: `<link rel="preload">` on villa singles for
  the undated probe URL (villa mode), byte-identical to the JS fetch.

**Out — do not touch:**

- Dated search / enquiry behaviour (`showDatedOverviewPrice`, panel
  breakdown, zero-rate guard, stay-total labelling from brief 05).
- Listing probe logic, sort, filters, empty state.
- ACF field registration or CMS values for `villa_indicative_from_price`.
- Offer mode (when offer dates are applied, the existing dated fetch
  owns the overview — do not race a probe over an active offer stay).
- Minimum-stay enforcement, iCal, Special Offers page.
- Renaming `ibv_villa_listing_probe_params()` (name is listing-centric;
  call it from the single path anyway — do not rename in this brief).

---

## Files to edit

```
wp-content/mu-plugins/ibv-core/includes/components/enquiry-panel/enquiry-panel.js
wp-content/mu-plugins/ibv-core/includes/components/enquiry-panel/enquiry-panel.php
wp-content/mu-plugins/ibv-core/includes/sections/villa-overview/villa-overview.php
wp-content/mu-plugins/ibv-core/includes/sections/villa-overview/villa-overview.css
```

Likely small addition (preload), same pattern as listing:

```
wp-content/mu-plugins/ibv-core/includes/sections/villa-listing-grid/villa-listing-grid.php
```

(or a villa-single preload helper next to the existing
`ibv_villa_listing_preload_availability()` — prefer extending that file
with a second `wp_head` callback gated on `is_singular( 'villas' )` rather
than inventing a new includes folder).

No files created unless you extract a one-line shared preload URL builder;
prefer not to. No CSS/JS registration changes unless a new handle is
unavoidable (it should not be).

---

## Changes

### 1. Pass the shared probe window into the enquiry panel

In `enquiry-panel.php`, when rendering the panel, call
`ibv_villa_listing_probe_params()` (already defined in
`villa-listing-grid.php`; confirm that file is loaded before the panel on
singles — it is required from bootstrap for the listing section and is
available site-wide).

Emit data attributes on `[data-bob-enquiry-panel]`:

```php
$probe = function_exists( 'ibv_villa_listing_probe_params' )
	? ibv_villa_listing_probe_params()
	: array();
```

```html
data-bob-probe-date-from="…"
data-bob-probe-date-to="…"
data-bob-probe-pax="…"
```

Only when those three values are non-empty. Escape with `esc_attr()`.

Do **not** invent a parallel JS `defaultProbeRange()` as the source of
truth on singles. The listing JS still has a client-side fallback for when
`ibvListingSearch.probe` is missing; the single should trust the PHP
attributes. If PHP cannot supply them, skip the undated probe and leave
ACF.

### 2. `enquiry-panel.js` — undated probe for the overview only

After panel boot (same place that wires `fetchPricing` / date listeners),
add an undated probe path:

**When to run**

- `endpoint` and `propertyId` are present.
- Panel is **not** in offer mode with applied offer dates.
- `gateReady( readState() )` is **false** (no complete guest date+pax
  selection yet).

**Request**

```
{endpoint}?villa={propertyId}&date_from={probe.from}&date_to={probe.to}&pax={probe.pax}
```

Same detail mode the dated path already uses — not search mode. Timeout
and abort behaviour should match the existing pricing fetch (reuse the
inflight abort pattern or a dedicated probe controller; do not leave two
overlapping villa fetches racing when the guest starts typing dates —
abort the probe if a dated `fetchPricing` starts).

**On success** (`paint`-compatible row: `available === 1`,
`eur_base_rental > 0`):

- Write the overview amount with the same whole-euro formatter the dated
  path uses (`EUR_WHOLE`).
- Keep **undated** chrome: “From” visible, unit text “/ wk” (restore from
  the PHP-rendered unit, not a nights label), season note visible, dated
  note hidden.
- Update `ovStatic.text` (and related flags if needed) to this probed
  “From” state so `resetOverviewPrice()` after a later dated search
  returns to the probe figure, not the original ACF number.

**On failure / unavailable / zero rental / abort:**

- Leave the server-rendered ACF (or empty) overview alone.
- Clear any pending mask (below).

**Do not**

- Reveal or populate the enquiry panel’s price breakdown for the probe.
- Enable submit / show contact fields based on the probe.
- Call `showDatedOverviewPrice()` for the probe (that hides “From” and
  swaps the unit to “for N nights”).

Suggested helper name: `showProbeOverviewPrice( stayTotal )` next to
`showDatedOverviewPrice` / `resetOverviewPrice`.

Wire the probe once on init when undated. If the guest later clears dates
after a dated stay, `resetOverviewPrice()` already restores `ovStatic` —
no second probe required unless `ovStatic` was never updated (probe never
succeeded); that is fine.

### 3. First-paint flash — mask overview while probing

Listing already masks card amounts with
`.ibv-listing-grid--price-pending`. Mirror that for the single:

**PHP (`villa-overview.php`):** when the villa has an indicative price
(or always when the amount node exists and the page will run the probe —
prefer always adding a modifier when JS is expected), add a class such as
`ibv-villa-overview__price--pending` on `.ibv-villa-overview__price`.

**CSS (`villa-overview.css`):** under `@media (scripting: enabled)`, mask
`.ibv-villa-overview__price--pending .ibv-villa-overview__price-amount`
the same way as the listing skeleton (tokens only:
`--ibv-color-web-grey-100`, `--ibv-radius-md`). With scripting none /
unsupported, do not mask.

**JS:** remove `--pending` after probe success, probe failure, or when
skipping probe (no property id / no probe attrs). Also remove it as soon
as a dated `fetchPricing` starts so a guest who picks dates quickly never
stays masked.

If the villa has **no** ACF indicative and no amount text, the empty
amount node already collapses via existing `:empty` CSS — still clear
pending the same way so a successful probe can reveal “From … / wk”
(you may need to un-hide the “From” prefix and “/ wk” unit and show the
season note when painting a probe onto a previously empty overview;
match listing card behaviour for empty ACF + live rate).

### 4. Head preload on villa singles (recommended)

Alongside `ibv_villa_listing_preload_availability()`, add a callback that
runs only on `is_singular( 'villas' )` when the enquiry panel would probe
(undated — singles do not put dates in the URL today; if that changes
later, gate accordingly).

Build:

```
{endpoint}?villa={property_id}&date_from=…&date_to=…&pax=…
```

using `ibv_get_bob_endpoint_url()` if available, otherwise the same
hardcoded CGI URL the panel already uses — prefer one shared getter so
preload and fetch stay byte-identical. Use `rawurlencode` and the same
key order as `enquiry-panel.js` will use.

Skip preload when `property_id` is empty.

### 5. Comments

Update the Bob shell comment in `villa-overview.php` so it no longer says
the enquiry panel only swaps in a stay total while a priced search is
active. State clearly:

- Undated: probe may replace ACF with a live “From … / wk”.
- Dated: stay total labelled for nights searched.
- Fallback: ACF (or empty) if probe/dated fetch cannot price.

---

## Integration contract (do not invent a second meaning)

| State | Overview UI | Source |
|---|---|---|
| First HTML (no JS) | From €ACF / wk + season note | ACF |
| Undated + probe OK | From €API / wk + season note | `eur_base_rental` for probe week |
| Undated + probe fail / €0 | Unchanged ACF (or empty) | ACF |
| Dates selected + priced | €stay · for N nights + fees note | dated detail fetch |
| Dates cleared | Restore last undated “From” (`ovStatic`) | probe if it ran, else ACF |

Labelling a 7-night probe total as “/ wk” is the same compromise the
listing already ships. Do not reintroduce `(rate * 7) / nights`.

---

## Verification

Local or staging, JS on, hard-refresh:

1. Open `/our-villas/` with **no** date params. Note three card “From”
   figures (e.g. Bella Vista, KM2, Nieves).
2. Open each villa single with **no** dates in the enquiry panel. After
   the probe settles, overview “From” must match that villa’s card
   (allow €1 rounding only if formatters differ — they should not; both
   use whole euros).
3. Kill or block the API: single must keep ACF indicative; listing keeps
   ACF too. No blank overview if ACF was set.
4. Pick dates on a single that returns a real rental: overview switches
   to stay-total + nights label; panel breakdown still works; zero-rate
   still refuses to paint (brief 04).
5. Clear dates: overview returns to the probed “From … / wk”, not a
   different ACF number (when the probe had succeeded).
6. Villa with empty `property_id`: no probe, no console errors, ACF
   unchanged.
7. Offer deep-link / offer mode: dated offer path still owns pricing;
   undated probe must not overwrite an active offer stay price.

Network tab: undated single should show one villa-mode GET for the probe
window; with preload, the document request should reuse it (no duplicate
full download if the browser matches URL/mode/credentials).

---

## Notes

- Prefer extending `enquiry-panel.js` over a new script — the overview
  nodes and `ovStatic` already live there.
- `ibv_villa_listing_probe_params()` name is awkward on singles; leave it.
  A rename/move to something like `ibv_bob_probe_params()` is a follow-up,
  not this brief.
- WP Rocket is unrelated: cards hydrate client-side after cache; this fix
  is the missing client hydrate on singles.
- Do not ask the client to re-enter indicative prices as part of this
  work. ACF remains a fallback and CMS-editable for no-JS / API-down.
