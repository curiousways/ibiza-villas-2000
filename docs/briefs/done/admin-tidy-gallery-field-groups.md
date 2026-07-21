# Admin tidy — retire duplicate gallery field groups (villa edit screen)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Admin/data tidy — **no PHP changes expected.** The duplicates
are DB-registered ACF field groups (legacy, from the migrated database), not
code. This is the first of a series of villa-admin tidy-ups; scope here is
**the two gallery groups only**.

## Background (verified against the codebase)

The villa edit screen shows the gallery three times:

1. **"Property Image Gallery — Horizontal (Landscape) Images only"** — legacy
   metabox, no registration anywhere in PHP → a **DB field group**.
2. **"Property Image Gallery — Vertical (Portrait) Images only"** — same: DB
   field group, and its field has **no consumer anywhere** (no reads in the
   legacy theme or the new stack).
3. **Villa → Media tab → "Property Image Gallery"** — the canonical one:
   PHP-registered in `register-villa-fields.php`, name **`property_images`**,
   key **`field_55799b13e3584`** (deliberately adopted from the legacy field so
   it reads the same postmeta). Consumed by `ibv_core_gallery()` (villa detail)
   and referenced by the accommodation pages' page-level twin.

So box 1 is the same field rendered twice, and box 2 is dead. Both legacy
groups get retired **non-destructively** — trashing an ACF field group never
touches postmeta (same guarantee as the `property_more_info_*` retirement).

## Step 0 — Audit the DB groups before touching anything

Via WP-CLI:

1. List the DB-registered field groups:
   `wp post list --post_type=acf-field-group --fields=ID,post_title,post_status`
   Identify the two gallery groups by title. Report what else is in the list —
   **do not** touch any other legacy group (they're deliberate reference
   material for the old-field mix).
2. For each of the two, list their fields:
   `wp post list --post_type=acf-field --post_parent=<group_id> --fields=ID,post_title,post_excerpt,post_name`
   (`post_excerpt` = field name, `post_name` = field key). Confirm each group
   contains **only** its gallery field — if either group contains additional
   fields, stop and report before proceeding.
3. **The key check (the one real trap):** compare the Horizontal group's field
   key with the PHP registration's `field_55799b13e3584`.
   - Also sample the reference meta on a few villas:
     `wp db query "SELECT meta_value, COUNT(*) FROM wp_postmeta WHERE meta_key='_property_images' GROUP BY meta_value"`
   - **If all references = `field_55799b13e3584`** (expected): trashing the DB
     group is a non-event — the PHP registration keeps resolving it.
   - **If any villas reference a different key** (the DB group's own key):
     trashing the group would break gallery *formatting* on those villas
     (`get_field` would return unformatted values). Fix first: update those
     `_property_images` rows to `field_55799b13e3584`, then verify one such
     villa's gallery renders, then proceed.
4. Note the Vertical group's field **name** (for the record) and confirm zero
   code references to it (repo-wide grep) — expected: none.

## Step 0.5 — Portrait-content audit (report, then PAUSE for David)

Editors may want portrait images merged into the single canonical gallery, and
spot checks show some vertical galleries are blank — so audit before retiring.
Using the vertical field name from Step 0.2:

```bash
wp eval '
$vert = "VERTICAL_FIELD_NAME"; // from Step 0.2
$ids  = get_posts([ "post_type" => "villas", "post_status" => "any", "numberposts" => -1, "fields" => "ids" ]);
$tot  = 0;
foreach ( $ids as $id ) {
  $v = get_post_meta( $id, $vert, true );
  $v = array_filter( array_map( "absint", is_array( $v ) ? $v : (array) maybe_unserialize( $v ) ) );
  if ( ! $v ) continue;
  $h = get_post_meta( $id, "property_images", true );
  $h = array_filter( array_map( "absint", is_array( $h ) ? $h : (array) maybe_unserialize( $h ) ) );
  $missing = array_diff( $v, $h );
  $tot++;
  printf( "%d\t%s\tportrait: %d\tnot in main gallery: %d%s\n",
    $id, get_the_title( $id ), count( $v ), count( $missing ),
    $missing ? " (" . implode( ",", $missing ) . ")" : "" );
}
printf( "-- %d of %d villas have portrait images\n", $tot, count( $ids ) );
'
```

Report the full table. Interpretation: rows with **"not in main gallery: 0"**
need nothing (pure duplication); rows with a non-zero count are **merge
candidates** — their portrait attachment IDs are listed.

**PAUSE here.** David decides per the results:
- **Merge:** append the missing attachment IDs to each villa's
  `property_images` (CLI, preserving existing order, appending portraits at
  the end), re-run the audit to confirm zero missing, then proceed to trash.
- **Skip:** proceed straight to trashing. The portrait postmeta survives
  either way, so a later merge remains possible from preserved data — but the
  admin UI for eyeballing the portrait sets disappears with the group, which
  is why the call comes first.

## The change

1. Trash both legacy gallery field groups (`wp post delete <id>` — default is
   trash, keep it as trash rather than force-delete so it's reversible).
2. **No postmeta deletion.** The vertical gallery's postmeta stays in the DB,
   orphaned — consistent with the non-destructive retirement precedent. Note
   in your summary roughly how many villas carry it (count of its meta_key)
   purely for the record.
3. No PHP changes. If the audit reveals anything that *does* need a code
   change, stop and report rather than improvising.

## Verify

1. Villa edit screen (e.g. the "Stunning Villa in Playa d'en Bossa" post):
   exactly **one** gallery — Villa → Media tab. The two legacy boxes gone.
2. Front end: that villa's detail gallery renders identically (formatted
   images, popup viewer working) — proves the key resolution held.
3. Spot-check a second villa the same way.
4. Confirm nothing else vanished from the edit screen (the other legacy
   reference fields David is keeping must all still be present).

## Commit / record

- Nothing code-side to commit if the audit is clean (DB-only change). Record
  the action instead: add a line to the build feedback log or your summary —
  group IDs trashed, key-check result, vertical postmeta count.
- If the key-fix branch was needed, include the exact query run and row count.

## Notes

- **Scope discipline:** galleries only. The wider old/new field mix on the
  villa screen is being retired in stages — David will brief the next batch.
- The legacy groups are trashed, not force-deleted, so this is reversible from
  ACF → Field Groups → Trash if anything looks off.
- Staging/local DBs are throwaway, but this same trash action will need
  repeating on whichever DB goes live if it isn't this one — note it in the
  summary so it lands on the go-live checklist.

---

## Outcome / record (2026-07-06)

**Structural correction:** the two gallery "boxes" are not standalone field
groups — they are `acf-field` posts inside group **402 "Property Information"**,
which also holds 7 fields being kept. Correct action is trashing the two
*fields*, not the group:

- **403** — `property_images` / key `field_55799b13e3584` — "…Horizontal
  (Landscape)". Pure duplicate: the PHP Villa stack registers the same key, so
  it renders again in Villa → Media.
- **1128** — `property_images_portrait` / key `field_5582c23e7ad08` — "…Vertical
  (Portrait)". Dead: zero code references.

(Boat copies **15453/15454** under group **15452 "Boat Information"** are out of
scope — untouched.)

**Key check — PASS.** All 76 villas reference `field_55799b13e3584` for
`_property_images` (the canonical PHP key). Non-canonical `field_6282b8ca5c850`
= 97 boats + 1 revision, zero villas. No key-fix branch needed.

**Portrait audit.** 9 of 76 villas carry portrait images; all 9 have portraits
not in the main gallery (46 images total). `property_images_portrait` postmeta
present on 816 rows (mostly boats). Postmeta survives trashing.

**Execution.** Local is throwaway, so it was **not** modified here. David runs it
manually on the live server: eyeball each of the 9 villas' portrait sets, add
the keepers to the canonical Media-tab gallery, then remove the two fields via
the ACF UI (Field Groups → "Property Information"). Server runbook + per-villa
attachment-ID tables delivered in the conversation.

**Wider sweep (same session).** The same same-key-duplicate pattern was
confirmed for the other legacy "Property Information" fields — all safe to
remove the same way (all 76 villas resolve to the shared PHP keys; the PHP Villa
stack re-registers each key and keeps rendering):

- **534** — Property Map — `property_map` / `field_558065ef4f993` (canonical in
  Villa → Location). Consumed by `villa-location.php`.
- **404** — Property Location — `property_summary` / `field_5579a38eaa707`
  (canonical in Villa → Content). Consumed by `villa-location.php` +
  `villa-card.php`. Its freetext distances → structured `villa_distances`
  ("Distance ticks") is a **separate future content pass**.
- **1106** — Property Video — `property_video` / `field_5581b076c15be`. Safe to
  remove, but note the canonical field has **zero front-end consumers** — the
  video is stored but never displayed; wire-up-or-retire is a future call.

**Go-live:** these DB field removals must be repeated on whichever DB goes live.

---

## Correction (2026-07-21, from batch 2)

**The reversibility claim in this brief is wrong.** Two statements above —
*"keep it as trash rather than force-delete so it's reversible"* and *"The
legacy groups are trashed, not force-deleted, so this is reversible from
ACF → Field Groups → Trash"* — cannot be honoured for individual fields.

ACF registers the `acf-field` post type **without trash support**. Verified
while running batch 2 (`done/admin-tidy-property-info-fields.md`):

```
$ wp post delete 534 404 1106
Warning: Posts of type 'acf-field' do not support being sent to trash.
Please use the --force flag to skip trash and delete them permanently.

$ wp eval 'var_export( post_type_supports( "acf-field", "trash" ) );'
false
```

Removing a field is permanent whether done with `wp post delete --force` or by
removing it in the ACF UI and saving the group. There is no field-level Trash to
restore from.

**What this changes in practice:** the *safety* argument this brief makes still
holds — the postmeta is never touched and the canonical PHP registrations keep
rendering it, so no villa content is at risk. What's lost on deletion is only
the legacy *field definition*. But the recovery route is a **DB backup**, not
ACF's Trash, so back up before running this on any DB that matters.

**Still outstanding on the server** — this brief was audit-only; fields 403 and
1128 have never actually been removed anywhere. See the combined runbook at the
end of `done/admin-tidy-property-info-fields.md`, which sequences batch 1's
portrait-merge decision ahead of both batches' deletions.
