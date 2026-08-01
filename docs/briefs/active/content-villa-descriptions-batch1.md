# Content migration — villa descriptions batch 1 into local DB (Tina-corrected)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit any repo-side artefacts with a meaningful
> message and report what you did.

**Brief type:** Content/data migration — **local environment only**
(`ibiza-villas-2000.test`, Herd). Take the merged villa description
drafts, apply Tina's 27 July corrections (enumerated in full below),
and write the finished HTML into each villa's `post_content` in the
local WordPress database. No PHP changes. No staging/production writes.
Goal: local site content-correct before the client relaunch call.

## Inputs (all in-repo — no external files needed)

1. **The final content files** — `docs/briefs/active/villa-content/
   villa-{name}-description.md`, one per in-scope villa. Each contains
   the finished HTML block (**Tina's corrections already applied**,
   1 Aug 2026) plus review notes. These are the source of truth for
   this run — extract the HTML from the fenced block in each file and
   write it verbatim. Do not re-edit the copy; the per-villa
   corrections listed below are the audit trail of what was already
   changed, not instructions to apply again.
2. **WP-CLI** against the local Herd site (run `wp post list
   --post_type=villas --fields=ID,post_title,post_name,post_status`
   first to build the ID map). If wp-cli isn't wired to this install,
   stop and ask.

Note: each content file carries "Facts to verify" review notes (e.g.
Tegui's 4-bedrooms-vs-5-listed-rooms, Torres' floor-split count).
These are **call agenda items, not blockers** — the copy as written
follows Tina's explicit instructions; write it as-is and leave the
flags for David.

## Background

The 15 merged descriptions were drafted and logged in Notion for
Tina's review; she reviewed on 27 July via inline comments. Her
corrections are transcribed below **per villa, in full** — the agent
does not need Notion access. Seven villas are confirmed and go in now
(batch 1). The rest are held (see Scope) pending the client call.

Tina's direct Notion edits ("I have changed in the copy", 27–28 Jul)
are already folded in — her Villa Torres content expansion was pulled
from the edited Notion draft and cleaned up into the Torres file.

## Standards (from the villa-descriptions house rules)

- **House skeleton, exactly:** opening (no heading, 1–2 paras,
  80–150 words, headline facts stated plainly, sleeps/bedrooms fact
  bolded once at first mention) → `<h3>Inside the villa</h3>` →
  `<h3>The pool and outside space</h3>` → optional
  `<h3>Good to know</h3>`.
- **Markup:** `<p>`, `<h3>`, `<strong>`, `<ul>`/`<li>`, `<a>` only.
  In-copy headings are h3 only. No inline styles, no shortcodes, no
  `&nbsp;` padding.
- **Links:** production-relative paths only (`/ibiza-town/`) — never
  `.test`, never legacy `/wp-ibiza/…`. If a target permalink is
  uncertain, flag it, don't guess.
- **No invented facts.** Where a Tina correction removes detail (e.g.
  Nieves' room-by-room), the rewrite states the confirmed counts
  plainly and stays vague on unconfirmed layout — it does not invent
  replacement detail.
- **No prices, offers, or availability** in description copy — ACF/API
  territory.
- British English; specific not superlative.

## Scope

**In — batch 1 (write to local DB):**
Savines, Tom (Can Petit), Nieves, Tegui (Can Teni), Torres,
Pep Luis (Can Pep Mortera), Can Vincente.

**Out — held, do not touch their posts at all:**
- **Alexa (Can Vincent)** — "4 bedrooms sleeps 8" comment attribution
  unconfirmed.
- **KM2 (Villa Maria)** — sixth-bedroom question unresolved.
- **Casa Maymo, Villa Omni, Villa Patxi** — Tina has flagged these for
  *removal from the site*; that's a call-level decision (redirects,
  inventory count). **Do not update, unpublish, or delete them** — they
  stay exactly as they are until Luke confirms.
- **Daniel, Bella Vista, Can Tunicu** — drafts fine but sleeps figures
  unconfirmed; they go in a later batch.
- The six legacy keyword-titled listings — untouched, pending Luke.

## Tina's corrections — already applied in the content files (audit reference)

### Villa Savines
- State **sleeps 8** (confirmed) in the opening, bolded with the
  bedroom count (e.g. "**sleeps eight across four en-suite bedrooms**").
- Bathrooms: say simply that **each bedroom has an en-suite** — remove
  the "every bathroom has both a bath and a shower, with mosaic
  detailing" sentence entirely.
- Remove the sentence "There's a second kitchenette with its own dining
  area and a small reception room on the lower floor, plus a utility
  room…" — **there is no second kitchenette**. (If the utility room
  is asserted elsewhere in a source, it may stay in Good to know;
  otherwise it goes with the sentence.)
- Drop the word "main" before "kitchen-diner" (there's only one).
- Adjust the floor breakdown `<ul>` so bathroom counts are consistent
  with all-en-suite (no separate "three bathrooms" line).

### Villa Tom (Can Petit)
- Facts: **4 bedrooms, 4 bathrooms** — state in the opening (no sleeps
  figure; none confirmed, don't assert one).
- Remove the first-floor line "a twin with en-suite shower room and
  terrace access" from the breakdown.
- Remove the sentence "…just 75 metres from Villa Patxi, so larger
  groups can take two neighbouring villas together — ask us about
  combined bookings." (Patxi is likely leaving the site.) The
  next-door-to-Villa-Daniel fact may stay only if it stands without
  the Patxi/combined-bookings framing — if in doubt, cut the whole
  Good-to-know entry.

### Villa Nieves — needs a redraft of the layout section
- Facts: **6 bedrooms, 4 bathrooms, sleeps 11** — opening states this
  bolded (the draft said nine bedrooms; that's wrong).
- Ground floor: **one double with en-suite** (replaces the draft's
  "a twin at one end, two doubles linked by an en-suite…" line).
- **Remove** the first-floor room-by-room line ("large en-suite double
  with French doors… small double with beamed ceiling…") — Tina struck
  it and supplied no replacement. Rewrite "Inside the villa" to state
  the confirmed totals and the main-house/annex split only as far as
  sources still support it — **do not invent** a new room-by-room.
- Everything else in the draft (grounds, pool, voice) stands.

### Villa Tegui (Can Teni)
- Opening **must state "sleeps 8 guests with 4 bedrooms"** (bolded,
  natural phrasing).
- **Remove** the "extra beds available on request" line — legal
  capacity is 8; no extra-beds wording anywhere.

### Villa Torres
- Bedrooms: phrase as **bedrooms with en-suite**; note that **all the
  bathrooms are showers** (no baths). This resolves the missing
  bathroom info — the draft can now state it plainly instead of
  staying silent.

### Villa Pep Luis (Can Pep Mortera)
- Facts: **3 bedrooms, 3 bathrooms** — the draft's "six bedrooms" is
  wrong; correct the opening and anywhere else a count appears.
- "Nothing has changed since the renovation in 2016" — keep the
  evergreen "renovated" phrasing as drafted; no copy change needed
  beyond the counts.
- The "every bedroom en-suite" positioning: Tina corrected only the
  number, not the en-suite claim. With 3/3 it remains plausible —
  keep it, but list it in the run report as a fact to confirm on the
  call before staging/production.

### Villa Can Vincente
- Replace the on-foot San Antonio route sentence with wording along
  the lines of: "a few minutes' taxi ride from the nightlife of San
  Antonio" — the walk is through woods and not ideal at night, so no
  walking framing at all.
- No sleeps figure is confirmed (draft noted bed mix implies fifteen;
  Tina's master-list comments are ambiguous) — **do not assert sleeps**;
  keep capacity out of the copy for now.

## Process

1. Build the villa ID map (`wp post list --post_type=villas …`) and
   match the seven in-scope villas by slug/title. Any villa that can't
   be matched unambiguously: stop and ask.
2. For each villa: extract the HTML from the fenced block in its
   `villa-content/` file into `docs/briefs/active/villa-content/final/
   {slug}.html` → validate against Standards (skeleton order, markup
   whitelist, no `.test`/legacy links, bolded facts present).
3. **Back up before writing:** `wp post get <ID> --field=post_content >
   docs/briefs/active/villa-content/backups/{slug}-pre-tina.html`.
   Revisions are on for villas, but keep the file backup anyway.
   (`final/` and `backups/` are gitignored-or-committed at David's
   preference — default: commit them; they're the replay + rollback
   record.)
4. Write: `wp post update <ID> final/{slug}.html` (content from file,
   never inline shell strings — quoting will mangle the HTML).
5. The `final/` files are the exact payload to replay onto staging/
   production after the call sign-off — don't regenerate them then,
   reuse them.

## Verification

- [ ] Seven villas updated; `wp post list` shows unchanged statuses and
  the held villas' `post_modified` untouched.
- [ ] Each updated villa's detail page on `.test`: opening renders
  before the read-more clamp with the bolded fact line; h3 sections in
  house order; no stray markup, no `.test`/legacy links, no shortcode
  debris.
- [ ] Bedroom/bathroom/sleeps figures on each page match this brief's
  table exactly — cross-check all seven.
- [ ] No villa asserts a sleeps figure that isn't in this brief
  (Savines 8, Tegui 8, Nieves 11 — and no others).
- [ ] Villa cards (listing page): excerpt fallback still renders
  sensibly for updated villas.
- [ ] Backups exist for all seven.

## Run report (required output)

A short markdown report listing per villa: draft file used, corrections
applied, word count, backup path — plus the carry-forward list for the
call: Pep Luis en-suite claim to confirm, Alexa/KM2 held, the three
removal-flagged villas untouched, Can Vincente sleeps outstanding.
David uses this to update the Notion migration log statuses (Notion is
out of scope for this run) and to walk Tina through what changed.

## Notes

- Local only by design: staging and production get the same `final/`
  files **after** the call confirms the villa list and remaining facts
  — content doesn't migrate with code, so this replay step is already
  part of the go-live checklist.
- The removal-flagged villas are deliberately untouched even though
  Tina's intent seems clear — unpublishing before the redirect map
  exists would leak 404s on live-adjacent URLs and pre-empt Luke.
- Deliberately deferred: Notion log updates (David/Claude session),
  `property_summary` location-field pass, Ibiza Guide article
  candidates noted in the drafts, and any SEO meta work.
