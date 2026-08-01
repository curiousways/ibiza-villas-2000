# Retire `property_video` — remove the dark field from the Villa stack

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Retirement — one PHP field registration removed, no front-end
change (the field has no front-end). Follows the batch 1 / batch 2 villa-admin
tidy series, but differs from both: those removed *duplicate DB* fields whose
canonical PHP twin kept rendering. This removes the **canonical registration
itself**, because nothing renders it at all.

**Decision (David, 2026-07-21):** retire rather than wire up. Rationale below.

## Background (verified 2026-07-21)

`property_video` / `field_5581b076c15be` is registered in
`register-villa-fields.php:488-511`, on the **Media** tab, directly after the
gallery. It has **zero consumers in the active `ibv` stack** — the only template
that ever rendered it is the dormant legacy theme
(`themes/ibiza-villas-2000/templates/single-property-video.php`, included twice
from `single-villas.php:62` and `:148`).

Stored values — 5 published villas out of 76:

| Villa | Value |
|---|---|
| 3186 Villa Bella Vista (Ses Rotes) | self-hosted `.mp4` in `/uploads/2021/05/` |
| 3322 Villa CanTunicu | `https://youtu.be/WJOv6YPz4g4` |
| 4732 Casa Maymo | `https://youtu.be/BGanSY2y37k?autoplay=1` |
| 9082 Villa Tegui (Can Teni) | `https://youtu.be/GCtC-HrfDLk` |
| 13105 Ibiza villa with beautiful sea views | `https://youtu.be/QAeoFln9N8M` |

Plus 72 revision rows carrying the same meta key.

**Why retire, on the evidence:**

- 5/76 villas (6.6%) hold a value; effectively **4 usable videos**, because the
  legacy renderer was `wp_oembed_get()`, which returns `false` for a raw `.mp4`
  URL — villa 3186 rendered an empty `.videoWrapper` even on the old site. That
  entry has never worked.
- One URL carries `?autoplay=1`, which oEmbed ignores — evidence the stored
  values were never a reliable interface even when something consumed them.
- No design exists for a villa video treatment, so wiring up is a design task
  first, not an implementation task.
- The field has been dark since the rebuild and nobody has asked for it.

**Reversible in the way that matters:** removing the registration does **not**
delete postmeta. All 5 values (and the 72 revision rows) stay in the DB. If
video comes back with a design, the content is still there and the field can be
re-registered from this brief's record.

## Scope

Remove the PHP registration. Leave all postmeta. No front-end work, no CSS, no
new sections, no changes to the legacy theme (dormant, out of scope for this
series as established in batch 2).

**Out of scope:** deleting `property_video` postmeta; touching the legacy
theme's video template; any video feature. If video is later wanted, it starts
from a Figma node and a new brief, not from this one.

## Files to edit

```
EDIT  wp-content/mu-plugins/ibv-core/includes/acf/register-villa-fields.php   (remove the field array)
```

Nothing else. No asset wiring, no bootstrap change — the field has no
component, CSS, or helper of its own.

## The change

1. Delete the field array at `register-villa-fields.php:488-511` — the block
   opening `array(` on 488 and closing `),` on 511, identified by
   `'key' => 'field_5581b076c15be'`. Verify by key, not by line number; the file
   may have shifted.
2. Leave the surrounding structure intact: the gallery field above (ending 487)
   and the `// ─── Tab: Location ───` comment and tab below (513+) are
   untouched. The **Media** tab keeps the gallery, so no tab is left empty —
   check this holds rather than assuming it.
3. **No postmeta deletion**, and no `delete_post_meta` sweep. Consistent with
   the non-destructive precedent across this series.

## The DB side

DB field **1106** (the legacy duplicate of this same key, in group 402) is a
**batch 2** item, not this brief's. Its status:

- **Local:** already force-deleted during the batch 2 run.
- **Live server:** still present, and still on the combined runbook in
  `done/admin-tidy-property-info-fields.md`.

After this brief, deleting 1106 on the server remains correct — it just stops
being a "duplicate removal" and becomes part of the same retirement. Note in
your summary that once both this brief and the runbook have landed, the villa
edit screen will have **no** video field at all, which is the intended end state.

## Verify

1. Villa edit screen (e.g. villa 18125): **no** "Property Video" field under
   Villa → Media. The gallery is still there and the Media tab still renders.
2. No PHP notices/warnings on the villa edit screen or a villa front end.
3. Front end of a villa that *has* a video (e.g. **3322** Villa CanTunicu):
   renders exactly as before — it never displayed the video, so nothing should
   change. Diff the page before/after if practical.
4. **Postmeta survives** — confirm the values are still queryable after the
   registration is gone:
   ```bash
   wp eval 'global $wpdb; echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID=pm.post_id AND p.post_type=\"villas\" WHERE pm.meta_key=\"property_video\" AND pm.meta_value<>\"\"");'
   ```
   Expect **5**.

   > **Corrected during implementation.** This brief originally predicted that
   > `get_field( 'property_video', $id )` would return `null` once the
   > registration was gone. It does not — ACF falls back to a pseudo-field
   > derived from the meta key, so `get_field( 'property_video', 3322 )` still
   > returns `'https://youtu.be/WJOv6YPz4g4'`, identical to `get_post_meta()`.
   > The practical difference is **formatting**, not availability: an
   > unregistered field returns the raw stored value with no `return_format`
   > applied. Harmless for a URL string; it would matter for an image or
   > google_map field. Either way the data is plainly recoverable.

## Commit / record

- One commit, PHP change only.
- Reference this brief in the commit body and move it from
  `docs/briefs/active/` to `docs/briefs/done/` in the same commit.
- Record in the summary: the 5 villa IDs and their URLs (table above is the
  durable copy), so a future wire-up brief can find the content without
  re-deriving it.

## Notes

- **Correction to the batch 2 brief.** `done/admin-tidy-property-info-fields.md`
  states that `property_video`'s canonical field lives on the Villa → **Content**
  tab. It does not — it is on **Media**, at line 497, after the gallery. The
  claim for `property_summary` (Content tab) in that same table is correct. The
  error was cosmetic and did not affect the batch 2 outcome, which was verified
  by field key rather than by tab. Fix the table in that brief as part of this
  commit.
- **Boats.** `property_video` postmeta exists only on `villas` (5) and revisions
  (72) — the boat CPT does not carry this key, unlike the gallery fields in
  batch 1. No cross-CPT consideration here.
- **If a video feature is later commissioned**, the 5 stored URLs are the
  starting content, but treat them as unvalidated: one is a raw `.mp4` needing a
  native `<video>` path rather than oEmbed, and the YouTube URLs are `youtu.be`
  short links carrying stray query params.
