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

1. Trash DB fields **534**, **404**, **1106** — `wp post delete <id>` (default
   trash, **not** `--force`, so it's reversible from ACF → Field Groups → Trash).
   Alternatively remove them via the ACF UI on group "Property Information" —
   same outcome, and the route David uses on the server.
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
- Trashed, not force-deleted — reversible from ACF → Field Groups → Trash.
- **Go-live:** as with batch 1, these DB field removals must be repeated on
  whichever DB goes live. Local is throwaway; if the work is done here it does
  **not** propagate. Add to the go-live checklist alongside the batch-1 items
  (fields 403/1128 + the 9 villas' portrait merge).
