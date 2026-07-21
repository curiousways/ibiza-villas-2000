# Admin tidy — retire duplicate Property Information fields (map, location, video)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Admin/data tidy — **no PHP changes expected.** Batch 2 of the
villa-admin tidy-up series; batch 1 (`done/admin-tidy-gallery-field-groups.md`)
retired the two duplicate gallery fields and established the pattern this brief
repeats.

## Background (verified against the codebase, 2026-07-21)

Legacy DB-registered ACF group **402 "Property Information"** still renders
fields on the villa edit screen that the PHP Villa stack *also* registers under
the same keys. Same key registered twice = the same postmeta rendered in two
boxes. Batch 1 removed the two gallery ones (403, 1128); these three remain:

| DB field | Name / key | Canonical home (PHP) | Front-end consumers |
|---|---|---|---|
| **534** Property Map | `property_map` / `field_558065ef4f993` | Villa → **Location** tab, `register-villa-fields.php:528` (`google_map`) | `villa-location.php:26` (presence check), `villa-map.php:23` (renders it) |
| **404** Property Location | `property_summary` / `field_5579a38eaa707` | Villa → **Content** tab, `register-villa-fields.php:317` (`wysiwyg`, label "Property Location") | `villa-location.php:38` (detail prose), `villa-card.php:32` (`ibv_villa_excerpt_plain`, first fallback) |
| **1106** Property Video | `property_video` / `field_5581b076c15be` | Villa → **Content** tab, `register-villa-fields.php:496` (`url`) | **none in the new stack** — see "Video" below |

Because the PHP registration owns each key, trashing the DB field is a non-event:
the field keeps rendering (once, in its canonical tab) and the postmeta is
untouched. Batch 1 proved the key resolution held on all 76 villas.

### Video — the one field needing a decision, not just a trash

`property_video` is **stored but never displayed**. The only consumer anywhere
is the **legacy theme**, `themes/ibiza-villas-2000/templates/single-property-video.php`
— not the active `ibv` theme. Removing DB field 1106 changes nothing on the
front end either way, so the duplicate-box removal is safe **now**; the
wire-up-or-retire call on the *canonical* field is a separate decision and is
**explicitly out of scope here** (see Step 3).

### Legacy-theme references are not consumers

The old theme also references `property_map` (`page-all-villas.php:21`,
`templates/single-property-map.php:6`) and `property_summary`
(`templates/single-property-summary.php`). It is not the active theme — confirm
this in Step 0 rather than assuming it, then disregard those hits.

## Step 0 — Audit before touching anything

Via WP-CLI, on the target DB:

1. Confirm the active theme is `ibv`: `wp theme list --status=active`. If it is
   **not**, stop and report — the legacy-theme consumers above become live and
   this brief's risk assessment no longer holds.
2. Confirm the three fields are children of group 402 and match the names/keys
   in the table:
   `wp post list --post_type=acf-field --post_parent=402 --fields=ID,post_title,post_excerpt,post_name --post_status=any`
   (`post_excerpt` = field name, `post_name` = field key.) Report the **full**
   list — the remaining fields in 402 are deliberate reference material for the
   old-field mix and **must not** be touched.
3. **Key check** (the trap batch 1 was watching for). For each of the three
   names, confirm every villa's ACF key reference points at the canonical PHP
   key:
   ```bash
   wp db query "SELECT pm.meta_key, pm.meta_value, COUNT(*) AS n
     FROM wp_postmeta pm
     JOIN wp_posts p ON p.ID = pm.post_id AND p.post_type = 'villas'
     WHERE pm.meta_key IN ('_property_map','_property_summary','_property_video')
     GROUP BY pm.meta_key, pm.meta_value"
   ```
   - **All rows = the canonical key per the table** (expected, matching batch 1):
     trashing is a non-event, proceed.
   - **Any villa referencing a different key:** trashing that field would break
     ACF *formatting* for those villas (`get_field` returns raw values — for
     `property_map` that means a serialized string instead of the lat/lng array,
     i.e. a dead map). Fix first: update those `_<name>` rows to the canonical
     key, verify one affected villa renders, then proceed.
   - Non-villa post types (boats carry their own copies) are **out of scope** —
     the query above is villa-scoped deliberately; don't widen it.
4. Record how many villas carry a non-empty value for each of the three, purely
   for the record.

## The change

1. Delete DB fields **534**, **404**, **1106** — either `wp post delete <id> --force`
   or by removing them in the ACF UI on group "Property Information" and saving
   (the route David uses on the server). **Both are permanent.**

   > **Corrected 2026-07-21.** An earlier draft of this brief (and batch 1's
   > outcome note) claimed the fields could be trashed reversibly. They cannot:
   > ACF registers the `acf-field` post type without trash support, so
   > `wp post delete` without `--force` refuses outright and
   > `post_type_supports( 'acf-field', 'trash' )` is `false`. There is no
   > field-level Trash to restore from — **take a DB backup (or export group 402
   > first) before running this on any DB you care about.**
   >
   > What is actually at risk is only the three legacy *field definitions*
   > (duplicate admin boxes). The postmeta is never touched, and the canonical
   > PHP registrations keep rendering it — so the realistic worst case is
   > recreating a small PHP field array, not recovering data.
2. **No postmeta deletion.** All three values stay in the DB and keep rendering
   via the PHP-registered fields. Non-destructive, consistent with batch 1.
3. **No PHP changes.** If the audit reveals anything that *does* need a code
   change, stop and report rather than improvising.

## Step 3 — Deferred calls, explicitly out of scope

Restate these in your summary; do **not** action them:

- **`property_video` wire-up-or-retire.** The canonical field has zero
  front-end consumers. Either it gets a section (design/Figma node needed) or
  the canonical field is retired from `register-villa-fields.php` too. David's
  call, separate brief.
- **Freetext distances → structured `villa_distances`.** `property_summary`
  prose carries distance detail that partly duplicates the "Distance ticks"
  repeater. Content pass, not a field tidy.
- **Remaining group 402 fields.** Whatever Step 0.2 lists beyond these three
  stays put until briefed.

## Verify

1. Villa edit screen (e.g. "Stunning Villa in Playa d'en Bossa"): the map,
   location WYSIWYG and video URL each appear **exactly once** — map under
   Villa → Location, the other two under Villa → Content. The legacy
   "Property Information" boxes for them are gone.
2. Front end, same villa: Location section renders the map **and** the prose
   below the distance ticks, unchanged from before. Proves key resolution held.
3. A villa listing/card view: the ~30-word excerpt still comes from
   `property_summary` (not the post content fallback) — a silent drop to the
   fallback is the tell-tale of a broken key.
4. Spot-check a second villa the same way, ideally one with a map but no
   location prose (or vice versa) to confirm the presence checks still behave.
5. Confirm nothing else vanished from the edit screen — the other legacy
   reference fields David is keeping must all still be present.

## Commit / record

- Nothing code-side to commit if the audit is clean (DB-only change). Record the
  action instead: group/field IDs trashed, key-check result per field, non-empty
  value counts, and the active-theme confirmation.
- If a key-fix branch was needed, include the exact query run and the row count.
- Move this brief to `docs/briefs/done/` with the outcome appended, matching the
  batch-1 format.

## Notes

- **Scope discipline:** these three fields only.
- **Deletion is permanent** — see the corrected note under "The change".
  Reversibility comes from a DB backup, not from ACF's Trash.
- **Go-live:** as with batch 1, these DB field removals must be repeated on
  whichever DB goes live. Local is throwaway; if the work is done here it does
  **not** propagate. Add to the go-live checklist alongside the batch-1 items
  (fields 403/1128 + the 9 villas' portrait merge).

---

## Outcome / record (2026-07-21)

**Executed on local** (unlike batch 1, which was audit-only) so the Verify
section could actually be run against a changed DB. Local is throwaway — this
does **not** propagate; the server runbook below is the real deliverable.

**Step 0 audit — all clean.**

| Check | Result |
|---|---|
| Active theme | `ibv` — legacy-theme consumers dormant, risk assessment holds |
| Group 402 contents | 9 fields; 534/404/1106 names + keys matched the brief exactly |
| Key check (villa-scoped) | **PASS** — all 76 villas on the canonical key for all three, one key each. No fix branch needed |
| Non-empty values | `property_map` 74/76 · `property_summary` 55/76 · `property_video` 5/76 |

**Premise correction — no field-level trash.** `wp post delete 534 404 1106`
refused all three: *"Posts of type 'acf-field' do not support being sent to
trash."* Confirmed via `post_type_supports( 'acf-field', 'trash' ) === false`.
Both this brief and batch 1's outcome note have been corrected. Proceeded with
`--force` on David's go-ahead.

**Verification — passed.**

1. **Front end byte-identical.** The `.ibv-villa-location` section for villas
   **18125** and **16699** diffed clean before vs after (`diff` → IDENTICAL).
2. **Formatting held.** `get_field( 'property_map', 18125 )` still returns a
   formatted array (`lat 38.9035162 / lng 1.4067963`) — the failure mode the key
   check guards against would have returned a raw serialized string.
3. **Card excerpt still sourced correctly.** `ibv_villa_excerpt_plain( 18125 )`
   returns the `property_summary` prose ("3 min drive to Playa Den Bossa…"), not
   the `post_content` fallback ("Villa Marta is a stunning villa…") — the silent
   regression this brief was watching for did not occur.
4. **Admin occurrences now exactly one each**, all resolved from the PHP-registered
   "Villa" group:

   ```
   property_summary   x1  Villa [field_5579a38eaa707]
   property_video     x1  Villa [field_5581b076c15be]
   property_map       x1  Villa [field_558065ef4f993]
   property_images    x2  Property Information [field_55799b13e3584] | Villa [...]
   ```

   `property_images` still showing **x2** is the control: batch 1 has not been
   run on this DB, so the duplicate it targets is still present — which is what
   makes the x1 results above meaningful rather than a measurement artefact.

**Deferred, unchanged.** `property_video` wire-up-or-retire (5 villas carry a
value — e.g. villa 3186 → a self-hosted `.mp4` in uploads — and nothing renders
it); freetext distances → `villa_distances`; the remaining group 402 fields
(1135, 1131, 1129, 1130 + batch 1's 403, 1128).

## Server runbook (batch 1 + batch 2 combined)

Run on whichever DB goes live. Batch 1 was never executed anywhere, so both
batches are outstanding on the server.

1. **Back up the DB.** Non-negotiable — field deletion is permanent (above).
   Optionally also export group 402 first as a safety net.
2. **Re-run the Step 0 audit on that DB before deleting anything.** The counts
   and key check above describe the local copy; the live DB may have moved.
   Abort on any non-canonical key reference and fix it first.
3. **Batch 1 content decision:** eyeball the 9 villas carrying portrait images
   (46 attachments not in the main gallery) and add the keepers to the canonical
   Villa → Media gallery. This must happen **before** deleting field 1128, since
   removing it takes away the only UI for reviewing those sets. The postmeta
   survives regardless, but blind is worse than sighted.
4. **Delete the fields** — ACF → Field Groups → "Property Information", remove
   and save: **403**, **1128** (batch 1), **534**, **404**, **1106** (batch 2).
5. **Verify** using the four checks above: a villa's Location section unchanged,
   the map still rendering, a listing card's excerpt still from `property_summary`,
   and each field appearing once on the edit screen.
