# Villa descriptions batch 2 + status sweep — run report

**Run:** 2026-08-14, local only (`ibiza-villas-2000.test`). Five villas updated
with Tina-approved copy, twenty-nine posts set to draft status. No staging or
production writes. Sources: the Notion migration log
(`Villa descriptions — migration log`) and Tina's inline comments there
(27 Jul master-list comments + 11 Aug draft-page comments).

## What was written

| Villa | ID | Source file | Backup |
|---|---|---|---|
| Villa Daniel | 2782 | `villa-daniel-description.md` | `backups/villa-daniel-pre-tina.html` |
| Villa Alexa (Can Vincent) | 3174 | `villa-alexa-description.md` | `backups/villa-alexa-pre-tina.html` |
| Villa Bella Vista (Ses Rotes) | 3186 | `villa-bella-vista-description.md` | `backups/villa-bella-vista-pre-tina.html` |
| Villa Can Tunicu | 3322 | `villa-can-tunicu-description.md` | `backups/villa-can-tunicu-pre-tina.html` |
| Villa KM2 (Villa Maria) | 4067 | `villa-km2-description.md` | `backups/villa-km2-pre-tina.html` |

Payload files are in `final/{slug}.html` (`villa-daniel`, `villa-alexa`,
`villa-bella-vista`, `villa-tunicu`, `villa-km2`) — **these are the exact
bytes to replay onto staging and production after sign-off.** Do not
regenerate them.

## Corrections carried in (Tina's comments, audit trail)

- **Daniel** — **seven bedrooms** bolded, no sleeps. Her 11 Aug comment made
  the count conditional: "If this was in the original text then Luke is happy
  to keep it." The original never states a number but enumerates exactly seven
  sleeping rooms (2 ground twins + 3 first-floor + 2 annexes), which the draft
  mirrors one-for-one — judged "in the original text", kept.
- **Alexa** — was the "blocked" villa; her master-list comment supplies the
  facts: **3 bedrooms / sleeps 6** bolded. Room-by-room rewritten to vague
  prose per her instruction ("keep the actual layout vague") — the draft's
  bullet list implied more rooms than the official three. The physical
  14-sleeper capacity is nowhere in the copy, as she asked.
- **Bella Vista** — no comments; in as reviewed. **Five air-conditioned
  bedrooms** bolded, no sleeps.
- **Can Tunicu** — 11 Aug comment: "Take off the 5 beds and say each of its
  air conditioned bedrooms can be made into a twin or double." Applied — no
  count asserted anywhere in the opening. Post title also standardised
  **"Villa CanTunicu" → "Villa Can Tunicu"** per the log (slug untouched).
- **KM2** — sixth-bedroom claim confirmed dead (27 Jul "keep it at 5 bedrooms
  and sleeps 10" + 11 Aug "REMOVE THIS BIT PLEASE" on the blocker note).
  Opening asserts **5 bedrooms / sleeps 10** bolded.

## Status sweep — 29 posts set to draft

Per David's instruction: nothing with open questions or a removal flag stays
published amongst the polished villas. All content preserved; draft status
only, fully reversible per-post with `wp post update <ID> --post_status=publish`.

**Tina-flagged removals (3):** Villa Patxi 4001, Casa Maymo 4732,
Villa Omni 9129.

**Parked legacy listings (7 posts / 6 listings), pending David + Luke:**
Beautiful Ibiza villa with private security 4473 **and** its duplicate 16699
(both carry Bob id `peppe`), Can Reiet 4771, Can Mestre 9998, Villa near Blue
Marlin 10920, Ibiza villa with beautiful sea views 13105, Stunning Villa in
Playa d'en Bossa 18125 (`martha`).

**Unmatched 2021-import listings (17):** 13346, 13351, 13436, 13494, 13497,
13500, 13523, 13549, 13578, 13598, 13624, 13645, 13659, 13682, 13704, 13726,
13738. None are in Tina's migration log, none have a Bob `property_id`
(unbookable), all carry legacy copy; several are for-sale pages
(e.g. Villa Moda's slug). Flag for the call: retire or re-list.

**Accommodation posts living in the villas CPT (2):** Airstream trailers
12511, Jade Apartments 13349. The site now has dedicated Hotel / Airstream /
Apartments pages (`page-accommodation.php` + Site Options); nothing references
these villa-CPT copies, and they rendered as unbookable cards in the grid.

## Result

**Exactly 12 villas published**, all with polished Tina-approved copy:
batch 1 (Savines 6998, Tom 2894, Nieves 2818, Tegui 9082, Torres 4435,
Pep Luis 3155, Can Vincente 3524) + batch 2 (Daniel 2782, Alexa 3174,
Bella Vista 3186, Can Tunicu 3322, KM2 4067).

## Verification

| Check | Result |
|---|---|
| Five villas updated, content stored verbatim | ✅ byte-identical, file vs DB (diff; the earlier SHA "mismatch" was WP-CLI's trailing newline on output) |
| Markup whitelist (`p`, `h3`, `strong`, `ul`, `li`) | ✅ |
| No `.test` / `/wp-ibiza/` / `&nbsp;` / shortcodes / inline styles | ✅ |
| House skeleton order | ✅ opening → Inside the villa → The pool and outside space → (Alexa) Good to know |
| Sleeps asserted only where permitted | ✅ Alexa 6, KM2 10 — batch 1's Savines 8, Tegui 8, Nieves 11 unchanged — and nowhere else |
| Draft sweep | ✅ 29 drafted; publish list is exactly the 12 above |
| Backups | ✅ all five in `backups/` |

## Found during the run: legacy Redirection rules hijacked live permalinks

**Eight enabled rules in the Redirection plugin used a current canonical
permalink as their *source***, 301ing it to the old theme's
`/villas/rental/{location}/{slug}/` shape — which no longer resolves and falls
through to the homepage. Affected: Daniel, Alexa, Bella Vista, Tunicu, KM2
(rules 259/264/266/280/283) **and batch-1's Tom, Torres, Nieves (257/281/284)**
— meaning three polished batch-1 villas had been bouncing visitors to the
homepage since batch 1 went in. This is the same trap that forced the e2e
suite off Villa Daniel as its fixture.

Fixes applied (all in `wp_redirection_items`, reversible):

- The 8 hijack rules set to `disabled` (not deleted).
- **64 genuinely-legacy rules** (ancient `/ibiza/villas/details/…` etc. URLs)
  that pointed at the dead `/villas/rental/…` shape were retargeted to the
  live `/villas/{slug}/` permalinks for the 12 published villas — old inbound
  links and search results now land on the right pages instead of the
  homepage.
- All 12 published permalinks verified 200 with no redirect; legacy
  `/villas/daniel/` verified to chain to `/villas/villa-daniel/`.

**Carry-forward:** the Redirection table has 437 further enabled rules
(other legacy URLs, drafted villas, `/all-villas/` fallbacks). These belong to
the redirect-map exercise pending Luke's verdict on the parked listings.
Note the table is **not** covered by the content-push runbook's table list —
staging/production have their own copies, so these fixes must be repeated (or
the runbook extended) at go-live.

## Knock-on: e2e suite

- The suite's default villa was **18125 — now drafted** (permalink 404s).
  `tests/e2e/helpers/test-data.ts` re-pointed to **Villa Savines 6998**
  (`villa-savines`, Bob id `savines`; permalink verified 200, no redirect).
  README updated.
- First full-matrix run since the Google Maps villa-map landed: two new
  deliberate console-guard exclusions in `helpers/fixtures.ts` — the
  vector-map → raster fallback (headless browsers have no WebGL) and WebKit's
  `WebKitBlobResource error 1` (headless WebKit refuses Maps' blob: workers).
- Full matrix green after the fixes: 143 passed / 13 intentionally skipped
  (Chromium + Firefox), then WebKit + mobile-safari 63 passed / 5 skipped.

## Carry-forward for the call

- **Sleeps still unconfirmed** (not asserted in copy): Daniel, Bella Vista,
  Can Tunicu, Tom, Torres, Pep Luis, Can Vincente.
- **Batch-1 carry-forwards stand** (see `RUN-REPORT.md`): Pep Luis en-suite
  claim; Tegui room-count reconciliation; Torres floor split; Can Vincente's
  `property_summary` still claiming "20 min walk to San Antonio Bay".
- **The 17 unmatched 2021-import listings** need a keep/retire decision —
  they're now hidden but undecided.
- **Redirect map** still needed before any of the drafted posts are deleted
  or before this state is pushed to staging/production (drafting = 404s on
  previously-live URLs).
- **Parked six** await Luke's verdict (David, Thursday).

## Replay to staging / production

After sign-off, replay the same payloads (verify IDs on the target first):

```bash
wp post update 2782 docs/briefs/done/villa-content/final/villa-daniel.html
wp post update 3174 docs/briefs/done/villa-content/final/villa-alexa.html
wp post update 3186 docs/briefs/done/villa-content/final/villa-bella-vista.html
wp post update 3322 docs/briefs/done/villa-content/final/villa-tunicu.html
wp post update 4067 docs/briefs/done/villa-content/final/villa-km2.html
wp post update 3322 --post_title='Villa Can Tunicu'
```

Status changes travel with the normal `wp_posts` content push
(`docs/content-push-runbook.md`) — no separate replay needed if pushing the
whole table from local.
