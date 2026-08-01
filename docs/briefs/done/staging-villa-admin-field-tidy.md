# Staging villa-admin field tidy — retire the legacy DB field groups

> Record of work done 2026-08-01. Supersedes the server runbook in
> `admin-tidy-property-info-fields.md`, which was written against a stale
> local database and was wrong in its particulars.

**Brief type:** Admin/data tidy, executed. Batch 3 of the villa-admin series,
and the one that finishes it. Batches 1 and 2 removed duplicate DB fields;
this removed **every remaining legacy DB field group** that had no reason to
render on the villa edit screen.

**Outcome:** the villa edit screen went from **8 field groups to 4**.

---

## The finding that reframed everything

The runbook in `admin-tidy-property-info-fields.md` was written from the
**local** database and assumed staging matched it. It did not.

| | Local (before this session) | Staging |
|---|---|---|
| `acf-field` rows | 85, newest **2024-05-31** | 80, newest **2026-07-06 15:41** |
| `villas` newest edit | 2026-06-22 | **2026-07-06 14:54** |

The batch 1 session on 2026-07-06 was executed **on staging and never
propagated back to local**. So local was six weeks stale, and every number in
the old runbook — field IDs, the 9-villa/46-image portrait table — described a
database that no longer existed anywhere.

**Lesson: staging is the source of truth for this project, not local.** Audit
against a fresh dump before writing any runbook.

Consequences found on staging before any work started:

- Fields **403, 1128, 534, 404, 1106** were already gone — batches 1 and 2 had
  in fact been run there. Plus **1131** (`property_features`), which is in no
  brief at all.
- The portrait merge was **already 6/9 done** (18125, 16699, 10920, 4771,
  4067, 3155 — with 4067 curated: 2 of its 7 dropped, 2 others added).
- Key check across all 76 villas: **clean pass**, one canonical key each.

## Audit method (repeatable, zero-risk)

The server has **no WP-CLI and no PHP binary** — SSH is a chrooted Plesk jail
with `curl` and little else. So the audit ran locally instead:

1. Take a WP Migrate backup of staging, download it.
2. Import to a **scratch** database (`ibv_staging_audit`), never the working one.
3. Run all queries there.

This is the route to use for any future server-side field work.

## What was deleted

All 16 fields verified by key against the PHP registrations first — **no key
mismatches anywhere**, so no formatting could break.

**12 same-key duplicates** — PHP registers the identical key, so each rendered
twice:

| Group | Field IDs |
|---|---|
| 377 Property Required Fields | 380 `property_sleeps` · 6310 `property_bedrooms` · 6311 `property_bathrooms` · 525 `property_featured` · 11830 `notice_uk` |
| 1137 Property ID | 1127 `property_id` |
| 3982 Special Offers | 3983 `property_special_offers_text` · 3984 `property_special_offer_display` |
| 3990 Property Prices | 3991 `property_price_from_euros` · 3992 `property_price_to_euros` |
| 6917 Villa | 6918 `villa_pretty_name` · 7629 `additional_title_keyword` |

Group **6917 was also titled "Villa"**, so the edit screen showed two boxes of
that name.

**4 spent fields** — no PHP registration, no consumer:

- 3959 `property_sleeps_extra` (1/76 populated; its own label read "THIS SHOULD BE EMPTY")
- 1129 `property_more_info_title` (71/76, but 66 are the generic string "More Information")
- 1135 `property_more_info_intro` (8/76)
- 1130 `property_more_info_content` (10/76)

The `more_info_*` trio was retired from PHP by
`retire-property-more-info-fields.md`; the DB copies were never removed, so
they had been rendering as orphans ever since.

**4 field groups**, emptied by the above and still rendering as empty boxes:
**402**, **1137**, **3982**, **6917**.

**Postmeta untouched throughout.** Verified after: `property_sleeps` 75/76,
`villa_pretty_name` 74/76, `property_id` 36/76 — and `get_field()` still
returns formatted values via the PHP registration.

> **Correction to both earlier briefs.** They imply field *groups* can be
> trashed reversibly even though fields cannot. Neither can:
> `post_type_supports( 'acf-field-group', 'trash' )` is also `false`.
> Backups are the only rollback.

## Deliberately NOT deleted — content the rebuild dropped

Three groups remain, holding 8 fields. These are **not** legacy noise: the
active stack has zero references to any of them (grepped for `spanish`,
`pounds`, `GBP`, and every field name), yet they are heavily populated.

### Property Spanish Law (6306) — needs a compliance decision

The Spanish figures are **distinct data, and consistently lower**:

| | Both set | Identical | Differ |
|---|---|---|---|
| Sleeps | 74 | 58 | **16** |
| Bedrooms | 74 | 67 | **7** |
| Bathrooms | 48 | 45 | 3 |

e.g. villa 4327 sleeps 17 vs Spanish 12; villa 2966 14 vs 12; villa 3131 12 vs 10.

That pattern reads as **licensed occupancy** versus marketing occupancy — so
the site currently advertises higher headcounts than the registered figures on
16 villas. Also here: `property_dimension` (23/50) and
`property_description_spanish` (74/76 — distinct prose, matching `post_content`
on only 5 villas and `property_summary` on none).

**Open question for David:** is this a gap in the rebuild rather than dead
weight? Do not delete until answered.

### Property Prices (3990) — GBP, stale

`property_price_from_pounds` 73/76, `property_price_to_pounds` 72/76. Not a
fixed conversion: 73 EUR/GBP pairs give ratios from **0.692 to 0.950** across
20 distinct values — hand-entered over years of rate drift. If GBP pricing
returns it should be recalculated, not recovered.

### Property Required Fields (377) — `property_for_sale`

24/76 villas flagged. The new build has no for-sale concept at all.

## Still outstanding

**Portrait merge, 3 villas.** Field 1128 is gone, so there is no UI for these —
a merge now means writing `property_images` directly.

| Villa | | Attachment IDs |
|---|---|---|
| 12511 | Iconic Trailers, Cala Martina | 13171, 13145, 13137, 13128 |
| 4473 | Beautiful Ibiza villa with private security | 17387, 17357, 17356, 17352 |
| 11056 | *(draft)* large ibiza villa walking distance | 11090 |

Note **4473's four images are the same attachments already merged into 16699**,
whose title differs only by capitalisation. These look like duplicate listings —
a content question, not a field one.

**Stranded postmeta** (data with no field): `property_features` 65/76,
`property_images_portrait` 3/76, `property_video` 5/76.

## Migration notes — WP Migrate

The GUI push failed **twice** at 91% (218/241 MB), ~2:40 each, reporting only
"An unknown error occurred". The local log showed why:

```
PHP Warning: Trying to access array offset on null
  in wp-migrate-db-pro/class/Common/Error/HandleRemotePostError.php:29
```

That handler parses an error returned *by the remote* — a null offset means
staging returned something unparseable (HTML error page or truncated
response), so no message could be surfaced. The failure was server-side on
staging, and WP Migrate could not report it.

**Root cause: 236 MB was being pushed to move 20 deleted rows.** The bulk is
irrelevant — `wp_gf_entry_meta` 66 MB, `wp_wsal_metadata` 15 MB,
`wp_rg_lead_detail` 15 MB, `wp_check_email_log` 14 MB.

**What worked** — the CLI, restricted to the two tables that matter (~50 MB):

```bash
wp migratedb push "$URL" "$KEY" \
  --include-tables=wp_posts,wp_postmeta \
  --backup=selected
```

Both tables are needed: 8 of the 20 deleted posts carried postmeta (17 rows),
so `wp_posts` alone would strand orphans.

**Use this form for any future push on this project.** A full-database push is
the reason it kept failing.

Also of note: WP Migrate's find/replace does **not** see through URL-encoding.
286 rows (19 legacy WPBakery pages + revisions) hold
`url:https%3A%2F%2Fstaging.ibizavillas2000.com%2F...` which it silently skips.
Harmless in a round-trip, but it means a pull never fully de-stages content.

## Verify (all passed 2026-08-01)

1. Villa edit screen (18125): **4 groups** — Villa (31 fields), Property
   Spanish Law (5), Property Prices (2), Property Required Fields (1). No
   duplicates, no second "Villa", no empty boxes.
2. Villa front end unchanged.
3. Postmeta intact; `get_field()` returns formatted values.
4. Confirmed on staging after the CLI push, WP Rocket purged first.

## Backups taken

```
~/Downloads/ibizavillas2000-migrate-20260801093452-7aedk.sql.gz  staging, pre-work
~/db-backups/local-ibiza-villas-2000-20260801-111343.sql.gz      local, pre-pull
~/db-backups/local-predelete-20260801-112440.sql.gz              local, post-pull
~/db-backups/local-pregroupdelete-20260801-112802.sql.gz         local, pre-group-delete
```

Plus `wp_posts` / `wp_postmeta` backup tables created on staging by
`--backup=selected` — droppable once the change has bedded in.
