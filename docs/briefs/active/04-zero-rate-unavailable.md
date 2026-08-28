# Zero-rate villas must read as unavailable, not fee-priced

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Bug fix, front-end JS only. Two files. Deliberately changes
what a guest sees when the API returns a villa with no rental rate.

**Supersedes `docs/briefs/active/03-zero-rate-price-display.md`** — that file
was never committed to this repo, so there is nothing to move to
`docs/briefs/superseded/`. It left the listing behaviour as an open
question; that question is now answered (drop the card), and the Savines key
issue it referenced is closed. Evidence below is from the 28 August pass,
which replays Steve's own November dates.

---

## Background (verified 28 August 2026)

Out of season the PMS returns villas as **available with a zero rental**:

```
?villa=nieves&date_from=2026-11-13&date_to=2026-11-20&pax=2

available: 1
eur_base_rental: 0
eur_adw_amount: 58
eur_extra_cleaning: 310
eur_total_price: 368
```

This is not an edge case. Search mode for that same week returns **15 villas,
all `available: 1`, with a real rental on only two** (`bellevista` 3852,
`mestre` 4701). Thirteen are fee-only.

**Villa page.** `paint()` (`enquiry-panel.js` ~496–530) treats any non-null
`eur_total_price` as a bookable price. It paints rental €0, ADW €58, cleaning
€310, total €368 — then, because the overview swap is gated separately on
`rent > 0`, calls `resetOverviewPrice()`, which restores the ACF
`villa_indicative_from_price` and its "Price varies by season" note. Result on
a villa that has an indicative price: a static "From €4,321 / wk" sitting
directly above a live "€368" for the dates the guest asked about.

**Listing.** `applyResults()` (`villa-listing-grid.js` ~319–369) has a
`rate > 0` guard so it doesn't *hydrate* a €0 price — but the row still
reaches `availablePids`, so the card stays visible in a dated search showing
its static price. A previously-painted inflated `/ wk` figure can also be left
sitting on the card from an earlier search.

The client has reported this three times in different words. It is ours.

---

## Standards

Per `.cursor/rules/ibv-conventions.mdc`. Relevant here: plain JS in the
component's own file, no new dependencies, no build step, British English in
comments. This brief adds no markup, no CSS and no ACF.

---

## Scope

**In:** the zero-rate branch on both surfaces.

**Out — do not touch, in any file you open:**

- **The `(eur_base_rental * 7) / nights` weekly conversion.** Blocked on
  Steve confirming whether that field is a stay total or already weekly. If
  it is already weekly, changing it breaks every correct 7-night price.
- Date-picker closed-season blocking, the Special Offers page, offers of any
  kind, iCal, and any client-side minimum-stay floor. All either out of
  scope, unsold, or waiting on the PMS.

---

## Files to edit

```
wp-content/mu-plugins/ibv-core/includes/components/enquiry-panel/enquiry-panel.js
wp-content/mu-plugins/ibv-core/includes/sections/villa-listing-grid/villa-listing-grid.js
```

No files created or deleted. No PHP, CSS or ACF changes.

---

## Changes

### 1. `enquiry-panel.js` — a zero rental is not a price

In `paint()`, extend the existing early-return guard, before any `setText()`:

```js
var total = pickNumber( node, [ 'eur_total_price' ] );
var rent  = pickNumber( node, [ 'eur_base_rental' ] );
var adw   = pickNumber( node, [ 'eur_adw_amount' ] );
var clean = pickNumber( node, [ 'eur_extra_cleaning' ] );

// Out of season the PMS returns available:1 with no rate card loaded —
// eur_base_rental 0 and a total that is only ADW + cleaning. That is not a
// price. Painting it also restores the static ACF "From" price above it via
// resetOverviewPrice(), so the guest sees a high-season figure over a
// fee-only total. Treat as unpriced and let the caller show the notice.
if ( total === null || rent === null || rent <= 0 ) {
    return false;
}
```

Returning `false` routes through the existing branch — `resetPrices()`,
`hidePriceBlock()`, `revealContactFields()`, `showNotice( msgUnavailable )` —
so the guest gets the enquiry route instead of a wrong number. No new copy.

Leave `data-bob-msg-unavailable` as it is. Note in your report whether a
distinct "no price for these dates" message would read better; don't add one.

### 2. `villa-listing-grid.js` — drop the row upstream

In `parseResults()` (~106–141), reject the row rather than filtering later, so
one guard governs both hydration and visibility:

```js
var weekly = ( rate !== undefined && rate !== null ) ? Number( rate ) : null;

// No rate card for these dates: available, but eur_base_rental 0. Not a
// sellable result — drop it, rather than leaving the card visible with its
// static price or a stale figure from an earlier search.
if ( weekly === null || isNaN( weekly ) || weekly <= 0 ) {
    return null;
}

if ( nights > 0 ) {
    weekly = ( weekly * 7 ) / nights;
}
```

**Confirm the `.filter( Boolean )` after the `.map()` exists** before relying
on it. Then confirm `availablePids` governs card visibility in
`applyFilters()` — the intended outcome is that a fee-only villa does not
appear in a dated search at all.

**Leave the `rate > 0` guard in `applyResults()` in place** and add a one-line
comment noting it is now belt-and-braces. Removing a guard because an upstream
change made it unreachable is how this comes back.

---

## Verification

**Use Pep Luis (WP 3155), not Nieves.** Nieves has an empty
`villa_indicative_from_price`, so the static-price half of the bug cannot
appear on it — that is exactly why the 21 August pass reached the wrong
conclusion. Pep Luis has an indicative price of **4321** and is fee-only for
the November week. Casa Peppe (16699, indicative 2000) is a second option.

1. **Before the fix**, villa page, 13–20 November 2026, pax 2: confirm you can
   see "From €4,321 / wk" and "Price varies by season" above a live total of
   €368. Paste the rendered values — that is the evidence the bug existed.
2. **After**, same inputs: no price block, unavailable notice shown, contact
   fields revealed, overview back to its undated state. No fee-only total.
3. **Listing**, dated search 13–20 November 2026, pax 2: only villas with a
   real winter rate appear — expect `bellevista` and `mestre`. The other
   thirteen are gone, not showing static prices.
4. **Regression — the normal path must be untouched.** A villa and window with
   a real rental (`savinas`, 20–22 September 2026, pax 2, rental 12137). Panel
   still paints rental, ADW, cleaning and total; overview still swaps to the
   dated weekly figure with the "Plus cleaning and damage waiver" note. **The
   number must be byte-identical before and after.** If it moved, you have
   touched the weekly formula, which is out of scope.
5. Clear the dates: overview restores the static "From" price and season note.
6. Undated listing browse: all cards visible with static prices, unchanged.
7. Zero console errors throughout.

---

## Notes

- **Do not revert Savines.** Its `property_id` is `savinas` on staging and
  that is correct — it matches the PMS `villa` value. `savines` returns an
  empty list.
- **Tegui's `property_id` is a separate manual CMS fix** (set to `tegui`, PMS
  `nice_name` "Can Tegui"), done by hand on staging. Not part of this brief;
  don't touch ACF.
- **Staging is the content source of truth.** Pull only — never push the
  database. See `docs/content-push-runbook.md`.
- **Steve has two open questions** that bear on adjacent behaviour: whether
  `available: 1` with `eur_base_rental: 0` should be `available: 0` at source,
  and the unit of `eur_base_rental` on non-7-night queries. This fix is
  correct either way — it stops us presenting fees as a price — but if he
  changes the payload, revisit rather than assuming.
- **Update `docs/testing/api-vs-site-triage.md`** when done: A5 fixed, and
  correct the A7 entry, which concluded "by design" from two villas that both
  had an empty indicative price.
- **Where this brief lives.** Commit to
  `docs/briefs/active/04-zero-rate-unavailable.md`, move
  `03-zero-rate-price-display.md` to `docs/briefs/superseded/` if it exists,
  and reference the filename in the commit message per `docs/briefs/README.md`.
