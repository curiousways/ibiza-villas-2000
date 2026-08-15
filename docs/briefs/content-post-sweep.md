# Content sweep — strip legacy junk from blog post content (plan)

> **For the implementing agent:** Scrutinise this brief and push back on
> anything that doesn't make technical sense before executing. This is a
> **content/data pass on the local DB only** (`ibiza-villas-2000.test`,
> Herd) — no PHP changes, no staging/production writes. Cleaned content
> reaches staging afterwards via the existing push runbook
> (`docs/content-push-runbook.md`).

**Brief type:** Content cleanup — remove hard-coded junk from legacy
blog posts (`post_type=post`, the Ibiza guide articles): embedded
Gravity Forms newsletter shortcodes and outdated trailing promo
boilerplate. Plan written 15 Aug 2026 from a read-only audit of the
local DB (144 posts + 77 pages scanned, all non-trash statuses).

## Background

The guide articles were migrated with their old-theme furniture baked
into `post_content`. Two kinds of junk render on the front end today:

1. **In-article newsletter form** — `[gravityform id="13" …]` (the old
   "Newsletter Signup" form) renders as a broken unstyled form inside
   the prose: visible "X/Twitter" honeypot field, "This field is for
   validation purposes…" text, bare inputs, ugly submit. Usually
   preceded by an invite line ("Want special villa rental offers and
   discount deals? Sign up to our newsletter!") and sometimes a
   `--------` divider.
2. **Trailing promo boilerplate** — a closing paragraph pushing villa
   pages ("Interested in great value Ibiza villa rental in and around
   San Antonio / Playa D'en Bossa / Ibiza Town… lush Ibiza villa
   collection…"), Facebook/Twitter/Instagram follow links, and in the
   newest posts a "Start planning now—booking early for your Ibiza 2025
   holiday ensures the trip of a lifetime" opener plus a "2025 insiders
   guide to Ibiza villa rentals" link. Many variants link legacy
   `/wp-ibiza/…` paths that no longer resolve.

The new site already has a styled site-wide newsletter band (Gravity
Form **32**, rendered by `ibv_core_newsletter_form()` /
the newsletter band section). In-article forms are therefore **removed,
not replaced**.

## Audit results (local DB, 15 Aug 2026)

Scanned 221 items (144 posts, 77 pages, every status except
trash/auto-draft/revision). 139 items matched at least one pattern
(116 posts, 23 pages). Raw pattern counts:

| Pattern | Posts | Pages |
| --- | --- | --- |
| `[gravityform` shortcode | 47 | 11 |
| "Sign up to our newsletter" invite line | 21 | 0 |
| `facebook.com/ibizavillas2000` link | 112 | 3 |
| Twitter / X link | 111 | 2 |
| `instagram.com/…` link | 111 | 3 |
| "insiders guide" reference | 36 | 8 |
| "Start planning now / booking early / trip of a lifetime" | 9 | 0 |
| Literal "2025" | 12 | 7 |
| Literal "2024" | 9 | 0 |
| Old domain `ibizavillas2000.co.uk` | 0 | 0 |

Notes from digging into contexts:

- **Every post-side GF embed is form 13** — 47 posts, near-identical
  shortcode (`[gravityform id="13" title="false" description="false"
  ajax="true" tabindex="32"]`). Full ID list:
  7958, 8028, 9465, 9500, 9855, 9922, 9964, 10122, 10154, 10209, 10222,
  10308, 10345, 10404, 10453, 10514, 10547, 10669, 10696, 10779, 10796,
  11029, 11118, 11537, 11799, 12577, 16559, 16607, 16624, 16680, 17080,
  17211, 17225, 17298, 17508, 17565, 17634, 17850, 17929, 18022, 18057,
  18264, 18309, 18373, 18486, 18500, 18512.
- Of those 47: **26** have a "Sign up…" invite line directly above the
  shortcode (three phrasings: "…Sign up to our newsletter!",
  "…Sign up to/for our monthly newsletter!", "Sign up below to get our
  quick Ibiza guides…"); **12** have only blank lines/`--------`
  dividers above; **9** have unrelated prose above (e.g. "Don't forget
  to tune in for the next exciting installment…") — for these only the
  shortcode + divider goes, the prose line stays.
- **The trailing promo block matched 75 posts** with a prototype
  pattern (`<p class="p1">…facebook.com/ibizavillas2000 or
  all-villas…</p>`, plus an older plain-text `<hr />` variant). It
  clusters into ~9 lead-in families — the biggest: "Planning a trip to
  Ibiza? Interested in great value…" (23 posts), "Start planning
  now—booking early for your Ibiza 2025 holiday…" (9 posts, the ones
  with the "2025 insiders guide" link), "Coming to Ibiza this spring or
  summer?…" (7 posts), "Ibiza Villas 2000 are the pioneers of weekend
  breaks…" (4), "Don't want to come to Ibiza for the whole week?…" (4).
- **42 posts have the social follow links *outside* that matched
  block** — earlier-era (2017) variants with different markup. The
  pattern needs tuning per family; whatever still doesn't match gets
  eyeballed, not force-deleted.
- **104 posts contain legacy `/wp-ibiza/…` links** (dead paths). The
  promo strip removes most; in-prose ones remain and are reported, not
  auto-edited (see class c).
- **16 posts still reference 2024/2025 in prose** after simulating the
  strip — human-review list below.
- **`[UNVRS]` is not a shortcode** — it's the club's actual name,
  appearing 15 times in prose (and in the title of post 18512). Any
  shortcode-stripping regex must not touch it. Other shortcodes found
  in posts: `[caption]` ×17 (legit core shortcode — keep), `[embed]`,
  `[video]` (core — keep), `[whatsapp_button]` ×2,
  `[ultimate_google_map]` ×1, and WPBakery residue (`[vc_row`,
  `[vc_column_text` etc.) in ~7 posts — report these, don't auto-strip
  in this pass.
- **Pages are a different problem, out of scope here.** The 11
  page-side GF embeds are *intentional enquiry forms* (form 16 on ten
  concierge-service pages, form 9 on "Lease your villa to us") — not
  newsletter junk. Pages also carry heavy WPBakery debris (162
  `[vc_row`, 202 `[vc_column`, …) and the seven "2025 insiders guide"
  chapter pages (11912, 11981, 12116–12122) — all of that belongs to
  the page-rebuild workstream, not this sweep.

### Sample inventory (representative; full lists above/below)

| ID | Title | Junk present |
| --- | --- | --- |
| 9964 | Ibiza in May \| weather, what's on… | invite line + GF13 + promo block (`/wp-ibiza/` links) |
| 17225 | Discover San Antonio, Ibiza | `--------` divider + invite + GF13 + "Start planning now… 2025" promo |
| 18512 | Ibiza's Game-Changing New Club [UNVRS]… | divider + invite + GF13 + 2025 promo; `[UNVRS]` in prose must survive |
| 18022 | Why Booking Early for Your 2025 Ibiza Holiday… | GF13 + 2025 promo + "2024 insiders guide" link + 2025-themed prose (title too — review) |
| 8711 | Things to do in Ibiza \| your top 10 summer 2017 | promo block only (2017-era variant) |
| 7207 | …fab May discounts! | `<hr />` plain-text promo variant |
| 10222 | Ibiza in June \| weather, what's on… | "Sign up below…" invite variant + GF13 + promo |
| 16680 | What to do for the New Year Event in Ibiza 2022/2023 | GF13 with in-prose lead-in (keep prose, strip form) |
| 3622 (page) | Ibiza Boat Hire | GF16 — intentional enquiry form, do NOT touch |

### Pattern classes

- **(a) Mechanical, safe to auto-strip** — the form-13 shortcode, its
  invite line (the three known phrasings), and the `--------` /
  `&nbsp;` divider furniture around it. 47 posts.
- **(b) Boilerplate trailing promo, safe with a tuned pattern** — the
  closing promo paragraph(s) in their ~9 variants (75 posts matched by
  the prototype; expect the tuned patterns to reach most of the 42
  remaining social-link posts; anything unmatched is reviewed by hand).
- **(c) In-prose outdated references, human review only** — 16 posts
  keep 2024/2025 references in genuine copy after the strip: 4823,
  8503, 9855, 10278, 10796, 17508, 17565, 17850, 17929, 18022, 18232,
  18264, 18486, 18500, 18512, 18057. Some are dated-by-design (event
  posts from that year); some need copy updates (e.g. 18022 is an
  entire "book early for 2025" article — likely unpublish/rewrite
  candidate). Also in this class: in-prose `/wp-ibiza/` links surviving
  the strip. The sweep **reports** these; it does not edit them.

## Process

1. **Working folder** — `docs/briefs/active/post-sweep/` with
   `backups/` and `report/` subfolders, mirroring the villa-content
   pattern (`docs/briefs/done/villa-content/backups/` is the precedent).
2. **Back up before any write** — for every post the sweep will touch:
   `wp post get <ID> --field=post_content >
   backups/{ID}-{slug}.html`. Backups are committed with the run —
   they're the rollback and replay record.
3. **Sweep script** — a PHP file run via `wp eval-file` (NB:
   `wp db query` doesn't work locally — no mysql binary; everything
   goes through `$wpdb`/WP-CLI). The script:
   - targets `post_type=post` only, all non-trash statuses;
   - applies class (a) removal: the GF13 shortcode line, plus the
     immediately-preceding invite line when it matches one of the three
     known phrasings, plus adjacent `--------`/`&nbsp;` divider lines;
   - applies class (b) removal: one tuned regex per promo-variant
     family (anchored to the *end* of `post_content`, requiring the
     distinctive link cluster — `villas-in-*`/`all-villas` +
     `facebook.com/ibizavillas2000` — so ordinary prose paragraphs
     can't match);
   - never touches `[caption]`, `[embed]`, `[video]`, or the literal
     text `[UNVRS]`;
   - trims trailing whitespace/`&nbsp;`/empty-paragraph leftovers at
     the content tail.
4. **Dry run first (mandatory)** — mode flag on the script. Dry run
   writes, per post, the proposed cleaned HTML to
   `report/{ID}-{slug}.html` and a summary table (ID, title, bytes
   removed, which patterns fired, residual flags: social links / years
   / `/wp-ibiza/` / unknown shortcodes still present). David reviews
   the summary — especially posts where >20% of content would be
   removed or where a promo pattern fired mid-document rather than at
   the tail — before any write.
5. **Write run** — same script, write mode: back up (step 2), then
   update via `wp_update_post()` per post. Expected touched-post count
   is fixed by the dry run; the script asserts it matches before
   writing and reports rows actually changed.
6. **Class (c) report** — the run emits the human-review list (the 16
   year-reference posts + any post with residual social links,
   `/wp-ibiza/` links, or unknown shortcodes). That list becomes a
   follow-up content session with David/Tina — not part of this run.

## Newsletter handling going forward

No replacement embed. The site-wide newsletter band (Gravity Form 32,
already styled and rendered on every page via the shared section)
covers signup. Form 13 stays untouched on staging (GF tables don't
exist locally and are never pushed — see runbook); retiring the form
itself in the staging GF admin is optional follow-up hygiene.

## Verification

- [ ] Re-run the audit patterns (same script, audit mode): zero posts
  matching `[gravityform`, zero matching the invite phrasings, zero
  matching the promo-block patterns.
- [ ] Social-link count in posts drops from 112 to ≈ the class (c)
  residual list only; each residual is on the review list, none in a
  trailing promo block.
- [ ] Spot-check rendered posts on `.test`, minimum: 9964 (Ibiza in
  May), 17225 (Discover San Antonio), 18512 (must still show "[UNVRS]"
  in prose and title), 7207 (hr-variant), 8711 (promo-only post),
  16680 (prose above stripped form retained). No broken form, no
  orphan invite line, no dangling divider, article ends on real copy.
- [ ] `[caption]` image shortcodes still render (e.g. any of the 17
  caption posts).
- [ ] `post_modified` changed only on touched posts; untouched posts
  (and all pages, all villas) retain prior `post_modified`.
- [ ] Backups exist for every touched post; SHA of backup ≠ SHA of new
  content; count matches the dry-run list.

## Follow-up: staging

Cleaned content reaches staging with the next content push per
`docs/content-push-runbook.md` (`wp migratedb push … --include-tables=
wp_posts,…`). Nothing extra to do — posts ride along with `wp_posts`.
After the push, spot-check the same sample posts on staging.

## Notes

- **Why posts only.** The page-side junk (WPBakery debris, the 2025
  insiders-guide chapter set, intentional service-enquiry forms) is
  structurally different and owned by the page-rebuild workstream.
  Mixing it in would turn a mechanical sweep into a judgement pass.
- **Why per-variant patterns instead of one clever regex.** The promo
  block has ~9 shapes across 8 years of posting styles. Small anchored
  patterns that each match one family are auditable in the dry-run
  report; one mega-regex is not.
- **Why report-don't-edit for class (c).** "2025" inside otherwise-good
  copy, dead `/wp-ibiza/` links mid-prose, and dated-by-design posts
  (2017 discount weeks, event announcements) need editorial decisions
  — update, keep as archive, or unpublish. That's a content call, not
  a sweep.
- **Old-domain links:** zero hits for `ibizavillas2000.co.uk` — the
  import's find-replace already rewrote them to `.test`; the remaining
  legacy problem is the `/wp-ibiza/` path prefix, not the domain.
- Deliberately deferred: retiring GF form 13 on staging, the class (c)
  editorial pass, page-side WPBakery cleanup, `[whatsapp_button]` /
  `[ultimate_google_map]` / `[mp-timetable]` orphan shortcodes, and any
  redirect work for the insiders-guide chapter pages.

---

## Outcome / record (2026-08-15)

**Executed on local only, as briefed.** Dry run reviewed, then write run:
**111 posts updated**, every write verified by content-hash comparison
against the dry-run manifest before and after writing. Pages, villas and
all other post types untouched (0 modified). No commits made.

| | |
|---|---|
| Posts modified | 111 (47 had the GF13 embed, 110 had a promo block, 46 both) |
| Backups | `docs/briefs/active/post-sweep/backups/{ID}-{slug}.html` — one per touched post |
| Replay payloads | `docs/briefs/active/post-sweep/report/proposed/{ID}-{slug}.html` + `report/manifest.tsv` (ID, before/after md5) |
| Audit re-run | class (a) `[gravityform` matches: **0 posts**; class (b) promo-line matches: **0 posts** |
| Integrity | pre-flight asserted DB == dry-run originals for all 111; post-write asserted stored == proposed for all 111 |

**Front-end spot-checks (all passed):** `/ibiza-in-may/`,
`/discover-san-antonio/`, `/ibiza-villa-discounts/` (wrapper-fragment
post), `/great-value-villas-in-ibiza-2017-fab-may-discounts/`
(hr-variant), `/new-year-event-in-ibiza-2022-2023/` (prose-above-form),
`/renting-a-villa-in-ibiza/` (invite+form only). All HTTP 200; zero
form-13 markup (only the site-wide form-32 newsletter band renders);
zero "Sign up to our newsletter" lines, promo copy, `--------` dividers
or empty `<p></p>` runs; `[caption]` images still render. Post 18512
still contains **[UNVRS]** in title and prose (12 occurrences in
content, untouched).

**Found during the run, beyond the plan's audit:** eight posts (10453,
10696, 10779, 11029, 11118, 11537, 11799, 12577) carried a copy-pasted
*theme fragment* around the form — invite paragraph, then literal
`<div class="site-content"><div class="row">…<article id="post-10404">
<div class="entry-content">` wrapper markup, the shortcode, the promo,
and orphaned closing tags. The sweep removed the whole fragment. Post
7958 used an `<h3>Subscribe to our newsletter!</h3>` heading instead of
the invite line — added to the invite predicate.

**Deviations from the plan:** none material. Two refinements: (1) posts
where no junk pattern fired are left byte-identical — 15 posts with
only trailing-whitespace tidy-ups were deliberately not touched;
(2) the wrapper-fragment and Subscribe-heading variants above were
added to class (a) handling after the first dry run.

### Class (c) — human review list (NOT edited by the sweep)

**In-prose 2024/2025 references (16 posts)** — need editorial calls
(update copy, keep as dated archive, or unpublish):

| ID | Title |
|---|---|
| 4823 | Christmas in Ibiza… (Updated 2024) |
| 8503 | Ibiza villa rental discount week 15 |
| 9855 | Ibiza in April \| Weather, what's on… |
| 10278 | Win a hand painted Ben Eine Ibiza Rocks plectrum… |
| 10796 | Ibiza October Guide |
| 17508 | Unforgettable Romance Awaits |
| 17565 | Ibiza in April : Island's Premier Events |
| 17850 | 5 Awesome Villa Rental Offers for July 2024 |
| 17929 | Ibiza Weather : Current Conditions & More |
| 18022 | Why Booking Early for Your 2025 Ibiza Holiday… (whole post is 2025-themed — unpublish/rewrite candidate) |
| 18057 | Ibiza Spain Villa Rentals: The Ultimate Holiday Guide |
| 18232 | Ibiza Taxi Travel Tips |
| 18264 | A Villa with Private Pool in Ibiza |
| 18486 | Ibiza Summer Guide 2025: Insider Secrets (title too) |
| 18500 | Discover Ibiza's Secret Treasures |
| 18512 | Ibiza's Game-Changing New Club [UNVRS] |

**In-prose social links kept (4 posts)** — links are part of genuine
copy (competition mechanics, corona update): 6893, 7490, 7958, 12700.

**Prose referencing the removed form (3 posts)** — kept per the
prose-survives rule, but now dangling: 16559 ("NEVER MISS OUR OFFERS.
Sign Up for Special Offers" heading), 16607 and 16624 ("…use the form
below to find out more").

**Dead `/wp-ibiza/…` links in prose (101 posts)** — mostly old
discount/offer posts; needs a link-fix or redirect decision, not a
content sweep. Full ID list in `report/` and reproducible with the
audit pattern.

**Next step:** cleaned content rides to staging with the next
`wp migratedb push` per `docs/content-push-runbook.md`. Re-run the
spot-checks on staging after the push.

---

## Pass 2 — presentational-markup sweep (2026-08-15)

Second sweep, same scope and discipline: strip hard-coded styling from
`post_content` so posts inherit all presentation from the theme's
`.ibv-prose` typography. Posts only; markup changes, **never words** —
every touched post passed a visible-text integrity assertion
(tags stripped, whitespace/`&nbsp;` normalised, before == after).

**121 posts updated** (of 144), every write hash-verified. Audit
re-run after the write: **0** `<style>` blocks, **0** `style=`
attributes (outside iframes), **0** junk classes, **0** `<div>`s in any
post. Pages and villas untouched.

### What was found and removed

| Pattern | Removed | Posts |
| --- | --- | --- |
| `style="…"` attributes (752× text-align, 122× color, 114× font-weight, spacing…) | 1,167 | 78 |
| Junk classes (Apple-paste `p1–p4`/`s1–s4`, Gmail-paste monsters, FB `_2pi9`-style, old-theme `col2`/`width-90`/`post-credits`…) | 1,684 | 61 |
| `<span>` wrappers unwrapped (mostly GDocs `font-weight: 400` soup) | 462 | 34 |
| `&nbsp;` runs, `&nbsp;`-only lines, empty `<p>`/`<h*>` | 151 | 45 |
| `<style>` blocks (page-wide h2/h3/li/img overrides, hand-built card CSS) | 12 | 12 |
| `<div>` wrappers unwrapped (FB-paste, old-theme `col2`, styled offer boxes) | 25 | 6 |
| Empty inline tags (`<b></b>`, `<strong> </strong>` spacing) | 7 | 4 |

Classes kept (WP-meaningful whitelist): `wp-image-*`, `size-*`,
`alignleft/right/center/none`, `wp-caption*`, `wp-block-*`.

### Judgment calls

- **Iframes untouched** — the six `style="border:none; overflow:hidden"`
  iframes are embed widgets (FB video), functional not typographic.
- **`<img>` styles removed** — all were spacing overrides
  (`margin-top: 40px` etc.) or nonsense (`font-size` on an image);
  theme rhythm takes over. `width`/`height` HTML attributes kept.
- **`<u>` kept in 12700** (corona update) — used as
  `<strong><u>…</u></strong>` pseudo-headings; meaningful emphasis,
  left for a human to decide if they should become real `<h3>`s.
- **Hand-built offer boxes collapsed to prose** — 18057/18264 had
  inline-styled price cards and CTA "buttons"; now plain paragraphs
  and links. Both posts are already on the class (c) year-review list
  (2024/2025 offers), so the copy is due an editorial pass anyway.
- **No cosmetic-only writes** — posts where no junk pattern fired were
  left byte-identical (23 posts untouched by this pass).
- `<font>`, `<center>`, `<big>`, `<small>`, `<strike>`, table
  presentational attributes, `width/height` on non-media tags,
  `<br>` runs, `align=` attributes: audited, **none found**.

### Backups (naming rule)

Same folder, `docs/briefs/active/post-sweep/backups/`:

- Posts already backed up by pass 1 keep that file as the **true
  original**; their pre-pass-2 state is `{ID}-{slug}-pre-styles.html`
  (102 files).
- Posts first touched in pass 2 get the plain `{ID}-{slug}.html` name
  (19 files) — that IS their true original.
- Proposed payloads: `report/proposed-styles/`, manifest with
  before/after hashes: `report/manifest-styles.tsv`. All 232 backup
  files hash-verified against the manifests after the run.

### Verification

- Audit re-run: zero across all four pattern classes (above).
- Front-end curls (`work-from-ibiza`, `february-in-ibiza-2018`,
  `a-villa-with-private-pool…`, the [UNVRS] post): HTTP 200, zero junk
  style/class signatures, article renders inside `.ibv-prose`, spot
  phrases from cleaned paragraphs render intact. Post 18512 still has
  all 12 "[UNVRS]" occurrences.
- Text integrity asserted per post at dry run AND re-asserted at write
  pre-flight; zero diffs.

**Observation (pre-existing, not caused by the sweep):** post 8050's
permalink (`/playa-den-bossa-ibiza-rude-cafe-luggage-storage/`) is
hijacked by a Redirection rule 301-ing to `/useful-info` — same
pattern as the villa permalink hijacks fixed in the batch-2 content
pass. Add to the redirect-review pile.
