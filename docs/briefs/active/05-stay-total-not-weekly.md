# The API price is the stay total — stop converting it to a weekly rate

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Brief type:** Bug fix. Two JS files plus two small PHP label changes.
Deliberately changes the number and the label a guest sees on a dated
search. Net effect is a deletion — we stop calculating something we were
never entitled to calculate.

**Unblocks the item `04-zero-rate-unavailable.md` put out of scope.** That
brief said the `( eur_base_rental * 7 ) / nights` conversion was *"blocked on
Steve confirming whether that field is a stay total or already weekly."*
Steve answered on 28 August. Move `04-zero-rate-unavailable.md` to
`docs/briefs/done/` — it shipped in `ef9d7eb` and has been smoke-tested.

---

## Background

### What Steve confirmed, 28 August 2026

> *"The price the booking system gives you in the API is just 'the price'
> … for the exact search you put in — there is no daily / weekly equivalent
> derivable from it. It is a complex calculation of daily prices,
> weight-adjusted for short stays (so maybe a 4 day stay in August is same
> cost as a week)."*

Two things follow, and both matter:

1. **`eur_base_rental` is the total for the searched stay.** Not a weekly
   rate, and not a nightly rate multiplied up.
2. **No weekly equivalent can be derived from it.** The client's short-break
   pricing is *already applied inside that number* — a 4-night August stay is
   deliberately priced close to a full week. Dividing by nights and
   multiplying by seven doesn't normalise that; it destroys it.

So the conversion is not mis-tuned. It is invalid at any coefficient, and it
must be removed rather than corrected.

### What it currently produces

The site takes the stay total, multiplies by seven, divides by the nights
searched, and labels the result `/ wk`. On any search that isn't seven
nights this inflates, and it inflates worst on exactly the short breaks the
client sells.

Verified example: a one-night Villa Nieves search in July returns
`eur_base_rental: 9534`. The page renders `9534 × 7 / 1` = **€66,738 / wk**.

This is the single cause behind all three of the client's pricing reports —
the €56,378 on the listing, the ~€12,000 Alexa figure on a three-night
search, and the one-night price. One bug, three screenshots.

---

## Standards

Per `.cursor/rules/ibv-conventions.mdc`. Plain JS in each component's own
file, no new dependencies, no build step, British English in comments.
User-facing strings stay translatable and live in PHP, passed to JS the way
the existing ones are (`wp_localize_script` for the listing,
`data-bob-*` attributes for the enquiry panel) — do not hardcode display
copy in JS.

---

## Scope

**In:** the weekly conversion on both surfaces, and the price label that
describes the resulting number.

**Out — do not touch, in any file you open:**

- **The static ACF `villa_indicative_from_price`.** It is genuinely a weekly
  "from" figure, entered by hand by the client. Its `From … / wk` treatment
  in the undated state is correct and stays exactly as it is.
- The zero-rate guards shipped in `ef9d7eb` in both files. They stay.
- Minimum-stay enforcement. There is no `min_stay` field in the payload;
  this is an open question with Steve, not a defect.
- Offers of any kind, the Special Offers page, iCal, date-picker
  closed-season blocking, `property_id` data fixes.
- The sort control, the filter checkboxes, and the probe-mode path — beyond
  the verification called for in §Changes 2.

---

## Files to edit

```
wp-content/mu-plugins/ibv-core/includes/components/enquiry-panel/enquiry-panel.js
wp-content/mu-plugins/ibv-core/includes/sections/villa-listing-grid/villa-listing-grid.js
wp-content/mu-plugins/ibv-core/includes/sections/villa-overview/villa-overview.php
wp-content/mu-plugins/ibv-core/includes/sections/villa-listing-grid/villa-listing-grid.php
```

No files created or deleted. No CSS, no ACF, no new markup beyond one
attribute and one span's copy.

---

## Changes

### 1. `enquiry-panel.js` — pass the stay total through unconverted

At line ~529 in `paint()`:

```js
var nights = data && data.query ? pickNumber( data.query, [ 'nights' ] ) : null;
if ( rent !== null && rent > 0 ) {
    showDatedOverviewPrice( nights > 0 ? ( rent * 7 ) / nights : rent );
} else {
    resetOverviewPrice();
}
```

`rent` is the total for the stay the guest searched. Pass it straight
through, with the nights count so the label can name the stay:

```js
var nights = data && data.query ? pickNumber( data.query, [ 'nights' ] ) : null;
// eur_base_rental is the rental total for the exact dates searched. Steve
// confirmed on 28 August that no weekly equivalent can be derived from it —
// short-break pricing is already weighted inside the figure, so dividing by
// nights and multiplying by seven inflates it. Show the stay total and say
// which stay it is for.
showDatedOverviewPrice( rent, nights );
```

The `rent > 0` branch is now redundant: the guard added in `ef9d7eb`
(`if ( total === null || rent === null || rent <= 0 ) return false;`) already
returned before this point. Drop the `else { resetOverviewPrice(); }` only
once you have confirmed that by reading the function top to bottom. If you
are not certain, leave the branch and say so in your report — a stale
`resetOverviewPrice()` here is what caused the bug in brief 04.

In `showDatedOverviewPrice()` (~line 360), rename the parameter off `weekly`,
and swap the unit label for a stay label:

```js
function showDatedOverviewPrice( stayTotal, nights ) {
    if ( ! ovAmount || ! ( stayTotal > 0 ) ) {
        return;
    }
    ovAmount.textContent = EUR.format( Math.round( stayTotal ) );
    ...
    if ( ovUnit ) {
        ovUnit.textContent = formatNightsLabel( nights );  // see below
        ovUnit.hidden = ! ( nights > 0 );
    }
```

`resetOverviewPrice()` must put the `/ wk` text back when dates are cleared,
not just un-hide the span. Capture the server-rendered unit text into the
existing `ovStatic` object at init (~line 353) alongside the other static
values, and restore it there. **This is the easiest thing in the brief to get
wrong** — verify it by entering dates and then clearing them.

### 2. `villa-listing-grid.js` — same, plus the misleading names

At lines ~106–108 the existing comment states the assumption Steve has now
contradicted. Replace it, don't leave it:

```js
// eur_base_rental covers the whole searched stay, not one week —
// normalise to an average per-week rate so non-7-night searches
// don't show stay totals labelled "/ wk".
```

The first clause is right, the conclusion is wrong. The fix is the opposite
of what it does: keep the stay total, change the label.

Delete the conversion at ~142–144:

```js
if ( nights > 0 ) {
    weekly = ( weekly * 7 ) / nights;
}
```

Keep the zero-rate drop immediately above it. Then **rename through**, because
a variable called `weekly` holding a stay total is exactly how this comes
back: `weekly` → `stayTotal`, the returned `weeklyRate` key → `stayTotal`,
and `rateByPropertyId` → `totalByPropertyId` in `applyResults()`. Update the
JSDoc at ~line 92. Leave the *input* key fallbacks (`row.weekly_rate`,
`row.weeklyRate`) alone — those read the API's own shapes, not ours.

Carry `nights` onto each returned row so `applyResults()` can label the card.

In `applyResults()` (~340–365), the card currently reveals its `From` prefix
and `/ wk` suffix on hydration. Both are now wrong: a dated price is exact,
not a "from", and it is a stay total, not a week.

- Leave `pricePrefix` **hidden** when hydrating a dated result.
- Set `priceSuffix.textContent` to the nights label and reveal it.

Keep the `rate > 0` belt-and-braces guard and its comment.

### 3. Sorting — verify, don't assume

`applyFilters()` sorts on `data-price`, which now holds stay totals for
searched cards and the static ACF weekly for everything else. Within one
dated search every visible card shares the same date range, so low-to-high is
still correct and is now more honest than it was.

**Confirm two things by reading `applyFilters()` and testing:** that cards not
in `availablePids` are hidden before the sort can mix the two units, and that
an undated browse still sorts on the untouched static values. Report what you
found. If the two can mix in any state, stop and raise it rather than
patching around it.

### 4. The label copy

**Listing** — add to the `i18n` array in `villa-listing-grid.php` (~line 38),
beside the existing `showing` string:

```php
'i18n' => [
    'showing' => __( 'Showing %d villas', 'ibv' ),
    /* translators: %d: number of nights in the searched stay. */
    'nights'  => _n( 'for %d night', 'for %d nights', 2, 'ibv' ),
],
```

`_n()` at a fixed 2 gives the plural form only, which is wrong for a
one-night search. Pass **both** forms across and pick in JS, following the
`config.i18n.showing` template pattern already there. Do it whichever way is
cleanest — just don't ship "for 1 nights".

**Villa overview** — the unit span in `villa-overview.php` (~line 94 and
~line 105) is server-rendered `/ wk`. Add a translatable dated label to the
price row as a `data-bob-*` attribute, following the
`data-bob-msg-unavailable` precedent already used in this component. Both
branches of the `if ( $indicative )` need it.

The existing `--dated` note ("Plus cleaning and damage waiver") is correct
and unchanged. The `--season` note ("Price varies by season") stays on the
undated state.

**Copy to use, unless you have a reason:** `for 3 nights`. Not "total", not
"per stay" — the guest picked those dates thirty seconds ago and the panel
below already itemises rental, waiver, cleaning and total.

---

## Verification

Capture the actual API response for each case and paste the figures. Do not
report a number you did not read off the payload.

1. **The reported bug, before the fix.** Villa Nieves, one night in July,
   pax 2. Confirm the payload's `eur_base_rental` and confirm the page renders
   that figure × 7. This is the evidence the bug existed.
2. **After**, same inputs: the overview shows the stay total from the payload,
   labelled for one night, with no `/ wk` and no `From`. The panel breakdown
   below is unchanged.
3. **Short break.** A three-night search on a villa with a real rate. The
   overview figure equals `eur_base_rental` exactly — byte-identical to the
   payload, no arithmetic.
4. **Seven-night regression.** A seven-night search. The figure still equals
   `eur_base_rental`. Note in your report whether it moved versus before the
   change: at exactly seven nights the old conversion was a no-op, so it
   should not have.
5. **Clearing dates.** The overview returns to `From €X / wk` with "Price
   varies by season" — the unit text restored, not just un-hidden.
6. **Listing, dated search.** Cards show stay totals labelled for the nights
   searched, no `From` prefix. Sort low-to-high still ascends.
7. **Listing, undated browse.** Every card unchanged: static price, `From`
   prefix, `/ wk` suffix, original order.
8. **Zero-rate behaviour from `ef9d7eb` still holds.** 13–20 November 2026,
   pax 2: the villa page shows the unavailable notice, and the listing shows
   only the villas with a real winter rate.
9. Zero console errors throughout.

---

## Notes

- **This is a deletion, and that is the point.** If you find yourself adding a
  coefficient, a rounding rule, or a nights-based adjustment anywhere, stop —
  the whole finding is that no such calculation is derivable.
- **One open question with Steve is still open** and is *not* this one:
  whether `available: 1` with `eur_base_rental: 0` should be `available: 0` at
  source. Unaffected either way.
- **A second, unresolved point.** Steve also suggested the €56,378 was the API
  returning the summed rental of all available villas on a search without a
  `villa` parameter. The captured payloads show per-villa rentals on each row,
  and 9534 × 7 ÷ 1 = 66,738 accounts for the family of figures arithmetically.
  Do not build anything against Steve's version. If you see evidence for it in
  a payload while working, report it — that would be a genuinely different
  bug and it changes the search-mode picture.
- **Staging is the content source of truth.** Pull only — never push the
  database. See `docs/content-push-runbook.md`.
- **Update `docs/testing/api-vs-site-triage.md`** when done: close A9 with
  Steve's answer quoted, and record that A2 (the €56k) resolves to this same
  cause.
- **Where this brief lives.** Commit to
  `docs/briefs/active/05-stay-total-not-weekly.md`, move
  `04-zero-rate-unavailable.md` to `docs/briefs/done/`, and reference the
  filename in the commit message per `docs/briefs/README.md`.
