# Villa descriptions batch 1 — run report

**Run:** 2026-08-01, local only (`ibiza-villas-2000.test`). Seven villas
updated. No staging or production writes. No PHP changes.

## What was written

| Villa | ID | Source file | Words | Backup |
|---|---|---|---|---|
| Villa Savines | 6998 | `villa-savines-description.md` | 275 | `backups/villa-savines-pre-tina.html` |
| Villa Tom (Can Petit) | 2894 | `villa-tom-can-petit-description.md` | 208 | `backups/villa-tom-pre-tina.html` |
| Villa Nieves | 2818 | `villa-nieves-description.md` | 227 | `backups/villa-nieves-pre-tina.html` |
| Villa Tegui (Can Teni) | 9082 | `villa-tegui-can-teni-description.md` | 255 | `backups/villa-tegui-pre-tina.html` |
| Villa Torres | 4435 | `villa-torres-description.md` | 208 | `backups/villa-torres-pre-tina.html` |
| Villa Pep Luis (Can Pep Mortera) | 3155 | `villa-pep-luis-description.md` | 181 | `backups/villa-pep-luis-can-pep-mortera-pre-tina.html` |
| Villa Can Vincente | 3524 | `villa-can-vincente-description.md` | 194 | `backups/villa-can-vicente-pre-tina.html` |

Payload files are in `final/{slug}.html` — **these are the exact bytes to
replay onto staging and production after sign-off.** Do not regenerate them.

## Corrections carried in (per the brief's audit trail)

- **Savines** — sleeps 8 bolded; en-suite phrasing simplified; second-kitchenette sentence removed; "main" dropped before kitchen-diner; floor breakdown made consistent with all-en-suite.
- **Tom** — 4 bed / 4 bath bolded, no sleeps asserted; first-floor twin's en-suite/terrace claim removed; the whole Patxi / combined-bookings Good-to-know entry removed.
- **Nieves** — corrected nine bedrooms → **6 bed / 4 bath / sleeps 11**; ground floor rewritten to one double with en-suite; first-floor room-by-room removed and *not* replaced with invented detail.
- **Tegui** — "sleeps eight guests across four air-conditioned bedrooms" bolded; all extra-beds wording removed.
- **Torres** — bedrooms stated with en-suite; all bathrooms stated as showers.
- **Pep Luis** — corrected six bedrooms → **3 bed / 3 bath**; six-room breakdown removed rather than reconciled by guesswork.
- **Can Vincente** — the twenty-minute woodland walk to San Antonio replaced with "a few minutes' taxi ride"; no sleeps figure asserted.

## Verification

| Check | Result |
|---|---|
| Seven villas updated | ✅ exactly 7 rows changed |
| Statuses unchanged | ✅ all still `publish` |
| Held villas untouched | ✅ Alexa 3174, KM2 4067, Maymo 4732, Omni 9129, Patxi 4001, Daniel 2782, Bella Vista 3186, CanTunicu 3322 — all retain their prior `post_modified` |
| Content stored verbatim | ✅ SHA match, file vs DB, all seven — no `wpautop` mangling |
| Markup whitelist | ✅ only `p`, `h3`, `strong`, `ul`, `li` used |
| No `.test` / `/wp-ibiza/` / `&nbsp;` / shortcodes / inline styles | ✅ |
| House skeleton order | ✅ opening `<p>` → Inside the villa → The pool and outside space → (Savines) Good to know |
| Bolded fact in opening | ✅ all seven |
| Sleeps asserted only where permitted | ✅ Savines 8, Tegui 8, Nieves 11 — **and nowhere else** |
| Card excerpt fallback | ✅ all seven still resolve from `property_summary` |

**Note:** the replaced content carried `style="text-align: justify;"` inline
styles throughout. Those are now gone — a side benefit, but it means these
seven villas now render differently from the untouched ones.

## Carry-forward for the call

**Found during this run — not in the brief:**

> **Can Vincente's `property_summary` still says "20 min walk to San Antonio
> Bay".** That is precisely the claim Tina had struck from the description
> ("the walk is through woods and not ideal at night"). The correction landed
> in `post_content`, but `property_summary` is what feeds the **villa cards and
> listing pages** — so the walking claim is still on the site, just somewhere
> else. This makes the deferred `property_summary` location-field pass more
> urgent than a tidy-up: it currently contradicts approved copy.

**From the brief, unchanged:**

- **Pep Luis** — the "every bedroom en-suite" claim. Tina corrected only the number; 3 bed / 3 bath is consistent with it, but confirm before staging or production.
- **Tegui** — the breakdown lists five rooms (master + two twins + annex double + annex twin) against Tina's stated four bedrooms. Is the annex twin not counted?
- **Torres** — floor split lists 3 + 4 = seven rooms against "eight bedrooms" in the opening; one unaccounted. Also: bathroom arrangement for the three ground-floor rooms is unconfirmed.
- **Tom** — what, if anything, replaced the struck first-floor twin en-suite/terrace claim; and which four rooms account for the four bathrooms.
- **Nieves** — first-floor composition (two rooms, types unknown) and where the remaining two bathrooms sit. Room count does reconcile: annex 3 + ground 1 + first floor 2 = 6.
- **Can Vincente** — legal sleeps still unresolved, and whether Tina's ambiguous master-list comment ("3 bedrooms but can sleep 6… can actually sleep 14") belongs to this villa at all.
- **Alexa (Can Vincent) 3174 and KM2 (Villa Maria) 4067** — held, untouched.
- **Casa Maymo 4732, Villa Omni 9129, Villa Patxi 4001** — flagged by Tina for removal from the site; deliberately untouched pending Luke and a redirect map.
- **Daniel 2782, Bella Vista 3186, CanTunicu 3322** — drafts fine, sleeps unconfirmed, later batch.
- **Internal area links** omitted from all seven pending settled area-page permalinks. Each file's review notes name the natural link to add.

## Replay to staging / production

After sign-off, the same payload — do not redraft:

```bash
wp post update 6998 docs/briefs/done/villa-content/final/villa-savines.html
wp post update 2894 docs/briefs/done/villa-content/final/villa-tom.html
wp post update 2818 docs/briefs/done/villa-content/final/villa-nieves.html
wp post update 9082 docs/briefs/done/villa-content/final/villa-tegui.html
wp post update 4435 docs/briefs/done/villa-content/final/villa-torres.html
wp post update 3155 docs/briefs/done/villa-content/final/villa-pep-luis-can-pep-mortera.html
wp post update 3524 docs/briefs/done/villa-content/final/villa-can-vicente.html
```

**The server has no WP-CLI** (chrooted Plesk jail, no PHP binary), so this
cannot be run there. Replay locally and push `wp_posts` with
`wp migratedb push --include-tables=wp_posts` — verify the villa IDs match on
the target first.
