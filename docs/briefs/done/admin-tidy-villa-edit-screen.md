# Admin tidy — villa edit screen, batch 3 (legacy metaboxes + CPT supports)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Admin/data tidy — batch 3 of the villa-admin tidy-up
series. Batch 1 (`done/admin-tidy-gallery-field-groups.md`) retired the
duplicate gallery DB groups; batch 2 (`done/admin-tidy-property-info-fields.md`)
retired the map/location/video duplicates from DB group 402 and
established the audit-then-trash pattern this brief repeats. One small
PHP change this time (CPT `supports` trim); everything else is DB-side
group retirement plus one taxonomy decision.

## Background (verified against the codebase, 2026-08-01)

The villa edit screen still carries legacy furniture below and beside
the new tabbed **Villa** group (PHP-registered,
`includes/acf/register-villa-fields.php`, title "Villa"):

1. **Four identical sidebar checkboxes** — "Check this box if this
   property is for sale". This is `property_for_sale`, marked **REMOVE**
   in `docs/cleanup-audit.md` (line ~226, "Sale logic"). It is not
   registered anywhere in the PHP stack and **no new-stack code reads
   it** (repo-wide grep: zero hits outside the legacy theme, which does
   not ship). Four boxes almost certainly means four separate legacy
   DB-registered field groups each carrying the same checkbox — the
   same duplicate-group disease batches 1–2 treated.
2. **A collapsed "Property Special Offers" metabox** — a legacy
   DB-registered group. The *real* offers UI is the `villa_offers`
   repeater (keys `field_ibv_villa_offers*`) inside the PHP Villa
   group — visible at the top of the screen (Valid from / Valid to /
   Headline). The legacy group's fields use different keys, so this is
   **not** the key-collision case from batches 1–2 — it's an entire
   orphaned group.
3. **CPT `supports` bloat** — `includes/post-types/` registers `villas`
   with `supports` including `'custom-fields'` (exposes the raw Custom
   Fields box) and `'post-formats'` (meaningless for villas). Neither
   is used by the new stack.
4. **`villa_type` taxonomy** — registered in
   `includes/taxonomies/villa-type.php` and referenced **nowhere else
   in the entire mu-plugin, theme, or admin columns**. Same situation
   as the retired `villa_amenity`
   (`done/refactor-retire-villa-amenity-taxonomy.md`).

### What on that screen is LIVE — do not touch

| Screen element | Why it stays |
|---|---|
| Classic editor (`the_content`) | **Canonical villa overview source** since `00e57f7` (`fix(villa-detail): source overview from post content`); also final fallback in `ibv_villa_excerpt_plain()`. Not "reference" — live. |
| Excerpt box | Second fallback in `ibv_villa_excerpt_plain()` (villa-card.php) — feeds villa cards + offer panels when `property_summary` is empty. |
| Villa attributes → Order | `menu_order` drives villa ordering in `villa-listing-grid.php` (default listing `orderby => menu_order`). |
| Villa group (tabbed) incl. offers repeater | The new stack. |
| `property_location`, `villa_poi` taxonomies | Registered and consumed (location pills, ACF fields, admin columns). |
| Revisions / Yoast boxes | Standard editorial kit. |

## Standards

- Follow the batch 1–2 procedure exactly: identify DB group IDs on the
  edit screen (group ID shows in each metabox's `id` attribute /
  ACF → Field Groups), verify key ownership and consumers, **trash**
  (not delete) DB groups so the move is reversible, record findings in
  this brief before moving it to `done/`.
- Postmeta is never deleted — retiring a group hides the UI; data stays.
- PHP changes in the mu-plugin follow existing file conventions; bump
  `IBV_CORE_VERSION`.
- All DB work is per-environment: local first, then replicate on
  staging and production (same lesson as the July checklist — DB
  changes don't migrate with code).

## Scope

**In:**
1. Audit + trash the legacy `property_for_sale` DB group(s) — expected
   four.
2. Audit + trash the legacy "Property Special Offers" DB group.
3. Trim `villas` CPT `supports`: remove `'custom-fields'` and
   `'post-formats'`.
4. Retire the orphaned `villa_type` taxonomy (Change 4 — has a
   stop-and-confirm gate).
5. Relabel the "Villa attributes" box's Order field context via the CPT
   `attributes` label so editors know what it does.

**Out:**
- The editor, Excerpt, Order, Villa group, location/POI taxonomies —
  live (table above).
- The `post-types-order` plugin — if it turns out to be active and
  managing villa ordering UI, note it and leave it; plugin rationalisation
  is Notion task 1.3's territory.
- Legacy theme files — never ship, never touched.
- Any new fields or UI.

## File list

| File | Action |
|---|---|
| `mu-plugins/ibv-core/includes/post-types/` (villas registration file) | **Edit** (supports trim + `attributes` label) |
| `mu-plugins/ibv-core/includes/taxonomies/villa-type.php` | **Delete** (Change 4, gated) |
| `mu-plugins/ibv-core/ibv-core.php` | **Edit** (drop require for villa-type.php; bump `IBV_CORE_VERSION`) |
| DB (per environment) | Trash legacy field groups (Changes 1–2) |

## Changes

### 1. `property_for_sale` groups — audit, then trash

On the villa edit screen (or ACF → Field Groups), identify every DB
group rendering a "for sale" checkbox — expected four. For each:

1. Record group ID, title, and the field name/key it contains.
2. Confirm the field name is `property_for_sale` (or a variant) and the
   key does **not** match any key in `register-villa-fields.php` — if a
   key *does* match a PHP-registered field, stop: that's the batch-1/2
   key-collision case and the group trash is a non-event, but it must
   be recorded as such.
3. Trash the group. Verify the checkbox count on the edit screen drops
   accordingly and the Villa group is unaffected.

If the count isn't four, or a group contains fields beyond the
checkbox, record what was actually found and handle each extra field on
its own merits (consumer grep first) — don't force the expected shape.

### 2. Legacy "Property Special Offers" group — audit, then trash

1. Record its group ID and full field list (likely the legacy
   offers/sale-price fields).
2. Grep the new stack for each field name found — expected zero
   consumers (the new stack reads only `villa_offers` /
   `field_ibv_villa_offers*`). Any unexpected consumer: stop and report.
3. Trash the group. Confirm the new offers repeater in the Villa group
   still renders and saves.

### 3. CPT supports trim + attributes label

In the villas registration:

- Remove `'custom-fields'` and `'post-formats'` from `supports`.
  Keep: `'title', 'editor', 'excerpt', 'thumbnail', 'revisions',
  'page-attributes'`.
- Add/adjust the CPT `labels['attributes']` to something editorial,
  e.g. `Villa display order`, so the metabox stops reading as generic
  "Villa attributes" mystery-meat. (The Order field inside it drives
  the villa listing sequence — that's now discoverable from the label.)

### 4. Retire `villa_type` — gated

`villa-type.php` registers the taxonomy; nothing else references it.
Before deleting, check the DB on local/staging: does the taxonomy have
terms, and do any villas have terms assigned?

- **No terms / no assignments:** delete the file, remove its require,
  done — mirror the `villa_amenity` retirement.
- **Terms exist with assignments:** stop and surface the term list to
  David before proceeding — data suggests someone used it for
  something, and the call (retire vs wire up) is his.

## Verification

Admin (per environment — local, then staging, then production):
- [ ] Villa edit screen: zero "for sale" checkboxes; no legacy
  "Property Special Offers" box; no Custom Fields box; no post-format
  UI; Order box present under its new label.
- [ ] Villa group (all tabs, incl. offers repeater) renders and saves
  exactly as before.
- [ ] Excerpt box present; Classic editor present; Revisions present.
- [ ] ACF → Field Groups: trashed groups in Trash (recoverable), not
  deleted.

Frontend:
- [ ] Villa detail page unchanged (overview still sourced from post
  content; offers accordion intact).
- [ ] Villa listing order unchanged (menu_order still honoured).
- [ ] Villa cards: excerpt fallback chain still produces text.
- [ ] No PHP notices with `WP_DEBUG` on.

Record actual group IDs/field lists found (Changes 1–2) and the
`villa_type` term-check result in this brief before moving it to
`done/`.

## Notes

- **Why trash, not delete:** identical reasoning to batches 1–2 —
  reversible, postmeta untouched, zero front-end effect because the
  new stack never read these fields.
- David's framing "legacy the_content() needs to remain for editor
  reference" is understated — it's the live overview source. The brief
  records this so nobody future-tidies it away.
- `'custom-fields'` support removal also removes the meta-box query on
  every villa edit-screen load (it's the classic slow-edit-screen
  culprit on sites with heavy postmeta — this site has 76 villas'
  worth of legacy meta).
- Deliberately deferred: any migration of legacy `property_for_sale` /
  legacy offers postmeta (worthless once the site relaunches), the
  `post-types-order` plugin question (task 1.3), and any villa_poi
  admin UX polish.

---

## Outcome / record (2026-08-01)

Executed on **local, then pushed to staging**. Ran alongside a wider tidy
recorded in `staging-villa-admin-field-tidy.md` — read that first; it
explains why the earlier runbooks were wrong (staging had diverged from
local since 2026-07-06, so local was six weeks stale).

**Villa edit screen: 8 field groups → 3.**

| Group | Fields | State |
|---|---|---|
| **Villa** `group_ibv_villa` | 31 | The real one, PHP-registered |
| Property Spanish Law (6306) | 5 | Kept — see below |
| Property Prices (3990) | 2 | Kept — stale GBP |

### Change 1 — `property_for_sale`: one group, not four

The brief expected four. A search across every `acf-field` row in the
database returned **exactly one**: field **10329** `property_for_sale` /
`field_5b2d11afef7f4`, in group **377 Property Required Fields**. The four
sidebar checkboxes observed were four *different* legacy groups rendering
in the sidebar, most of which had already been deleted earlier the same
session.

Deleted field 10329, then group 377 which it emptied. Postmeta survives on
**24** villas.

**The decision inverted the brief.** It treats the checkbox as live and
`villa_type` as the orphan; the data says the reverse (Change 4).

**Forward note:** `property_for_sale` *is* consumed by the legacy theme —
`page-all-villas.php`, `front-page.php`, `similar-properties.php`,
`related-properties-widget.php`, `templates/single-property-price.php`. That
theme is dormant on staging but **active on live**. If live moves to the new
stack, for-sale filtering must be rebuilt from `villa_type`.

### Change 2 — legacy Special Offers group: done

Group **3982** with fields 3983 `property_special_offers_text` and 3984
`property_special_offer_display`. Both keys **did** match PHP registrations,
so this was the batch-1/2 key-collision case, not an orphan as the brief
predicted — the deletion was a non-event and the `villa_offers` repeater was
never involved.

### Change 3 + 5 — CPT supports and label: done

`includes/post-types/villa.php`:

```diff
- 'supports' => array( 'title','editor','excerpt','thumbnail','revisions','custom-fields','page-attributes','post-formats' ),
+ 'supports' => array( 'title','editor','excerpt','thumbnail','revisions','page-attributes' ),
- 'attributes' => __( 'Villa attributes', 'ibv' ),
+ 'attributes' => __( 'Villa display order', 'ibv' ),
```

Verified safe: zero references to `post-formats`, `post_format` or
`custom-fields` anywhere in `ibv-core` or the `ibv` theme, and **zero**
villas carry a `post_format` term. `IBV_CORE_VERSION` → `0.1.47`.

### Change 4 — `villa_type`: GATE FIRED, taxonomy KEPT

Not an empty orphan. It classifies **every** villa:

| Term | Slug | Villas |
|---|---|---|
| For Sale | `sale` | 23 |
| For Rent | `rental` | 53 |

And it duplicates `property_for_sale` almost exactly:

| | Villas |
|---|---|
| Term **and** checkbox | 23 |
| Term only | 0 |
| **Checkbox only** | **1** (villa 16699) |
| Neither | 52 |

The taxonomy is the better record — it classifies all 76 into sale-or-rent,
where the checkbox is a one-way flag on 24. **David's call: keep the
taxonomy, retire the checkbox.** `villa-type.php` untouched; no file deleted,
no require removed.

Villa **16699** carries the checkbox without the term. Postmeta survives, so
nothing is lost, but someone should confirm whether it really is for sale.
(It is also the near-duplicate of villa 4473 — near-identical titles, shared
attachments.)

### Kept, with reasons

**Property Spanish Law (6306)** — investigated as a possible compliance gap;
it is not one. `grep -rn "spanish"` over the **legacy** theme (what live
actually runs) returns **zero results**, as does `property_dimension`. These
fields have never been rendered by any front end, old or new. Confirmed
against the live site: villa 12511 displays sleeps **5**, the UK figure, not
the Spanish **4**. Live shows only an agency-level registration in the footer
(`RGE CR-0120-E`) and a generic "maximum occupancy … is 12 people" note.

The data is still real and distinct — 17 villas differ from the UK figures,
Spanish consistently lower — but **only one of those is published** (12511,
5 vs 4); the rest are draft or private. So this is internal record-keeping,
not a website regression. Retiring the fields would be safe; where that data
should live is a business question.

**Property Prices (3990)** — `property_price_from_pounds` 73/76,
`property_price_to_pounds` 72/76, but not a fixed conversion: 73 EUR/GBP
pairs give ratios spanning **0.692–0.950** across 20 distinct values.
Hand-entered over years of rate drift. If GBP returns it should be
recalculated, not recovered.

> **Correction.** This brief's "trash (not delete) DB groups so the move is
> reversible" cannot be honoured. ACF registers **both** `acf-field` and
> `acf-field-group` without trash support — `post_type_supports()` is `false`
> for each. Everything here was `--force` deleted. Backups are the only
> rollback; four were taken (listed in the companion brief).

### Verification — passed

Villa edit screen: 3 groups, no for-sale checkbox, no Custom Fields box,
"Villa display order" replacing "Villa attributes", Order still saving. Villa
group and offers repeater intact. Front end unchanged. Confirmed on local and
on staging after the push.
