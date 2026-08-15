<!--
STATUS — implemented 2026-08-15 (IBV_CORE_VERSION 0.1.67), with these
approved deviations from the text below:

- Listing empty-state copy kept as built ("Nothing matching your search?" /
  "Try adjusting your dates or group size.") — signed-off Figma copy
  (nodes 0:5783/0:5785); the copy in this brief was the older March
  wireframe. The short-breaks statement was added beneath it.
- Task 1 probes NOT run: they cannot distinguish a minimum-stay rule from
  ordinary calendar gaps. Minimum stay is a business-policy question with
  Luke and Steve. Task 2G is PARKED until Luke answers.
- Task 2B shipped without waiting on 2G: statement verbatim (no nights
  figure), homepage "Search short breaks" CTA pre-fills a 4-night search
  (~30 days out, pax 2).
- 2D/2F data changes (form 33 email-confirmation off; response-time note
  option value) applied to the LOCAL DB — they ride the next DB push to
  staging.
-->

# IBZ002 — Villa listing UX updates + behaviour verification

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

**Supersedes `feature-ibz002-api-probe-and-listing-ux.md`.** That version was written on a false premise — that questions on the API spec were still open. They are not; see "Corrected premise" below. Do not run the earlier brief. Delete it.

---

## Brief type

**Design-fix, with one short verification step.**

The build is complete. Bob's API work (PRs #3/#4/#5) was merged in May–June. Every component in this brief **already exists** — the work is correcting rendering, copy, states and semantics against the copy review, not building anything new.

**This framing matters.** Code written earlier in this project is not legacy code, and the Offers filter is not missing — it is built, unstyled, and lacking a count. Treating any of this as greenfield would produce the wrong instruction.

---

## Corrected premise — read this before anything else

An earlier version of this brief instructed a nine-request probe against the live availability API to resolve "five questions open since April". **That was wrong**, and the correction is instructive:

- The "open items" table on the Notion *API Integration Spec* page was last updated **5 May 2026**. It is a stale status field, not current status.
- Per the **1 August status review**: Bob's API work is *"all merged — nothing unmerged anywhere"*, `docs/briefs/active/` is empty, 45 briefs done, and the build is *"effectively complete"*.
- The merged work explicitly includes: villa listing grid wired to search mode, villa-card price hydration, **enquiry panel with live API pricing**, RTB gating, date-range picker, skeleton loading states, **the Offers filter**, and **group-size dropdowns**.

So the questions that brief proposed to probe were answered **in code, months ago**:

| Former "open" question | Actual status |
|---|---|
| Are ADW + cleaning in the response? Field names? | Answered — enquiry panel renders live total pricing |
| Is `pax` required or optional? | Answered — group-size dropdowns are built and wired |
| How is the 30-day cap enforced? | Answered — date-range picker is built |
| Empty-result and error response shapes | Answered — skeleton states built; spec'd fallback is "show all villas, never break the page" |

**Probing a client's production endpoint to rediscover facts already sitting in the repo would have been unnecessary risk and slower than reading the code.** Verify from the codebase. Do not call the live API as a research method.

### What is genuinely open

1. **Minimum stay.** Nothing in the spec, the build notes, or any call records whether a minimum stay exists, what it is, or whether it varies by villa. This only became material today, when the Short Breaks copy decision replaced a filter with "put your dates in". **This is Phase 1.**
2. **Steve's multi-week pricing doc** — the *documentation* was never confirmed received. The **logic is settled and built**. Out of scope here; noted so it isn't confused with a behavioural unknown.
3. **Luke's agent-link request (11 Aug email)** — a separate link for agents to check availability and prices. Unanswered, out of scope for this brief, flagged so it isn't lost.

---

## Inputs

| Input | Where | Status |
|---|---|---|
| Copy rewrite log — canonical source for every new string | Notion — *IBZ002 — Copy rewrite log (internal)* | Live, read it |
| Status review, 1 Aug 2026 — what's built | Claude project — `claude/status-review-2026-08-01.md` | Live |
| API Integration Spec — contract and decisions | Notion — *API Integration Spec* | Live. **Its "open items" table is stale — ignore that table** |
| Figma design file | `https://www.figma.com/design/PmQ7RMRODjEgf4bOe2NBXY/IBV2000` | Live |
| Bob's merged work | PRs #3, #4, #5 on `staging` | In repo |

Nothing in this brief requires a local artefact. If you cannot open the Notion pages or Figma, stop and say so.

---

## This brief was written without repo access

Authored from project documentation, the Figma file and a copy review — not from a read of the working tree. **Every file path below is a best guess marked for confirmation.** Find the real paths in Phase 0; where a guessed path doesn't exist, say so and use the real one rather than creating a file at the guess.

Where a **token, helper or class** is named, verify it against the canonical source in Standards. If you think a name is wrong, **grep the source and paste the output** — do not substitute silently.

---

## Phase 0 — Orientation (no edits)

Report on all of these before touching anything:

1. `tokens.css` — confirm tokens for surface, border, accent-gold, text, focus ring.
2. `sections.css` — confirm the surface modifier set and `--ibv-section-pad-y`.
3. Icon helper — expected `ibv_core_icon( $name, $args )`, icons vendored under `assets/icons/{lucide,brands}/`. Confirm the function name and that a `tag` icon exists.
4. **The existing Offers filter** — find its markup, CSS and JS. Report: how it's rendered, whether it re-queries on change or waits for a submit, and how it currently queries `villa_offers`.
5. **Whether a Short Breaks control exists** in the filter row. It's in the spec and wireframes; the operator believes it may never have been built.
6. Villa CPT + `villa_offers` repeater registration — confirm the valid-from/valid-to sub-field names **and their stored date format**.
7. **The date-range picker** — the Airbnb-style component from PR #3/#4. Report whether it enforces any minimum range, and whether that's client-side, server-side, or absent.
8. Form plugin in use (the sub-labelled Name/Email pattern suggests **Gravity Forms**) — this decides whether Task 2D is code or an admin setting.
9. Whether a Site Options field already exists for the response-time string.

---

## Standards

- Colours from `tokens.css` — no hardcoded values
- Spacing on the established scale; typography from the existing type system
- Section roots: `ibv-section` + an explicit `ibv-section--surface-{X}`; section CSS never sets `padding-block`, `background` or `color` on the root
- Icons via `ibv_core_icon()` — never inline `<svg>`, never a CDN
- Images via the project image helper
- All output escaped (`esc_html`, `esc_attr`, `esc_url`)
- No new dependencies; vanilla JS in an IIFE, matching the existing pattern
- **British English** in all user-facing strings

**Framework docs beat this brief.** Where the current docs for the installed version disagree with an instruction here, follow the docs and open your report with the deviation, citing the page.

- ACF — https://www.advancedcustomfields.com/resources/
- `WP_Query` / `meta_query` — https://developer.wordpress.org/reference/classes/wp_query/
- Gravity Forms email field — https://docs.gravityforms.com/email/

---

# PHASE 1 — Minimum-stay verification (code read, not API probe)

**Answer from the codebase.** Read the date-range picker and the search request builder from Bob's merged work.

Report:

1. Does the picker enforce a minimum range client-side? If so, what value, and where is it set?
2. Does the search request builder impose or validate a minimum?
3. Is there any per-villa minimum-stay field on the villa CPT?
4. What currently happens if a user selects a 2-night range — blocked in the UI, empty grid, or results returned?

**Only if the code read is genuinely inconclusive** and the answer matters for Task 2B, you may run **one** targeted read-only GET against the availability endpoint with a 2-night range, and one with 4 nights, to compare. Two requests, not nine. Tell the operator before you do it.

**Preferred route if inconclusive: ask Steve.** He is now on the bi-weekly calls and there is an open thread with him. A question to Steve is cheaper and more authoritative than inferring from responses.

**Then stop and report** before starting Task 2G.

---

# PHASE 2 — Implementation

## Files to create / edit

All paths **to be confirmed in Phase 0**.

```
EDIT   [villa listing filters markup]      — offers toggle restyle + count; remove short-breaks control if present
EDIT   [offers filter CSS]                 — existing file if one exists, else follow convention
EDIT   [offers filter JS]                  — only if behaviour changes
EDIT   [functions.php / mu-plugin]         — ibv_count_villas_with_active_offers()
EDIT   [villa listing template]            — no-results empty state + short-breaks statement
EDIT   [special offers page template]      — no-active-offers empty state
EDIT   [front-page.php]                    — featured offer absent/expired state
EDIT   [templates rendering ACF CTA URLs]  — empty-URL guard (Task 2E)
EDIT   [villa detail enquiry panel]        — response-time string from Site Options
EDIT   [Site Options registration]         — add response-time field
```

---

## Task 2A — Offers filter: restyle and add a count *(design-fix)*

**The filter is built and working.** It renders as an unstyled native checkbox labelled `Offers` — the only unstyled control on the page. Do not rebuild the query; correct the presentation and the label.

### Copy

Label becomes **"Special offers only"** — the site says "Special Offers" everywhere else (page title, nav, homepage block); this said "Offers". A filter label should also describe the *result* of ticking it, not name the attribute.

### The count — the change that actually moves the numbers

Append a live count: **"Special offers only (4)"**.

What stops people using filters is uncertainty about what happens next — ticking one might empty the page. A count removes that risk, and the number is itself a nudge that something is on. It is nearly free: the query already runs.

```php
/**
 * Count published villas with at least one currently-active offer.
 * MUST use the same active-offer test as the villa detail accordion
 * (today <= valid_to). If the two disagree, the page contradicts itself.
 */
function ibv_count_villas_with_active_offers() : int {
	// villa_offers is an ACF repeater. Repeater sub-fields don't reliably
	// support a date-comparison meta_query across storage formats — confirm
	// the stored format in Phase 0 before choosing an approach.
	//
	// Preferred: one WP_Query over published villas, loop have_rows( 'villa_offers' ),
	// count those with an active offer, cache in a transient keyed to today's date.
	// 15 villas — a single loop is cheap. Don't build a meta_query that only
	// works for one date format.
	//
	// If the existing filter already computes this set, reuse that logic
	// rather than writing a second implementation that can drift.
}
```

**Reuse before you write.** If the built filter already derives this set, extract and share it — do not create a parallel implementation.

### Zero state

**When the count is zero, do not render the toggle at all.** A filter that empties the grid looks broken and teaches users to distrust the other filters. Return early.

### Presentation

Restyle to a toggle with an icon, matching the design system:

- **Styled native `<input type="checkbox">`, not a `<div>`.** A div-based switch needs `role="switch"`, `aria-checked` and keyboard handling written by hand and routinely ships half-done. Styling the native input keeps keyboard operation, screen-reader semantics and form behaviour for free. **Do not substitute a div implementation.**
- Icon: `tag` via `ibv_core_icon()`. Confirm it exists in the vendored Lucide set; if the name differs, use the vendored equivalent and say so.
- Hit area **minimum 44 × 44px** including the label.
- Visible focus ring from the existing focus token — never remove the outline without replacing it.
- The count sits **inside the `<label>`** so it forms part of the accessible name.
- State must not be conveyed by colour alone; switch position carries it too.

**Switch or checkbox appearance depends on Phase 0 item 4.** If the filter re-queries on change, a switch is semantically correct. If it waits for a submit, a switch is misleading — keep a checkbox appearance. Report which you found and which you chose.

---

## Task 2B — Short breaks: statement, not control *(design-fix)*

**Background.** The spec and wireframes specify a Short Breaks checkbox. The operator has since confirmed **short breaks is not a flag on any villa** — every villa is available, with the team deciding per enquiry.

**The problem:** a filter that never narrows the result set is not a filter. Toggled, it returns the full grid. Beside a real filter it implies a symmetric choice that doesn't exist.

**Do:**

1. **If a Short Breaks control exists, remove it.** If it was never built, confirm that and skip — do not add one.
2. **Add the statement** near the filters. Verbatim from the copy log:

   > **Staying just a few nights?** Every villa is available for short breaks — put your dates in and we'll show you what's free.

3. Render as a plain statement, **not** an interactive element. It must not look clickable.
4. **Homepage deep link:** the Short Breaks CTA should land here with a short date range pre-filled. Confirm how the listing reads date params from the query string and wire it. If pre-fill isn't supported, report that rather than faking it.

**Spec change — record it.** The checkbox is specified in the API spec and the signed-off wireframes. Note the removal in the commit message and your report so nobody rebuilds it at UAT.

**Conditional on Phase 1:** if a minimum stay exists, the statement must not promise stays shorter than the system accepts. Bring the finding back before finalising the string.

---

## Task 2C — Empty states *(design-fix)*

Three places with undefined behaviour when content is absent. Same class of bug; do them together.

**1. Villa listing, no matches.** Spec'd copy exists:

> No villas available for these dates — try adjusting your search or contact us.

Add the short-breaks statement here too — someone who has just searched a short range is exactly who needs to know every villa qualifies. Include a contact link.

**Distinguish "no matches" from "API failure".** Per the spec, an API failure falls back to showing all villas and must never break the page. The two states need different messages; confirm how the built code currently distinguishes them.

**2. Special Offers page, nothing active.** Live aggregation with nothing to say when empty. Needs an honest empty state and a route to the full listing. Draft copy for operator approval — **do not invent a claim about offers returning on a schedule.** The "updated weekly" promise was removed deliberately; see the copy log.

**3. Homepage featured offer, absent or expired.** Currently renders a Villa Savines offer that expired 31 May. **Recommendation: hide the section.** A section advertising an absent offer is worse than no section. **An expired offer must never render**, whatever else is decided.

---

## Task 2D — Remove the duplicate email field *(design-fix)*

The shared enquiry form asks for the email address twice. Double-entry email fields measurably increase abandonment and fight browser autofill — how most of this audience completes forms.

Remove the "Confirm Email Address" sub-field; rely on inline validation of a single field.

**If Gravity Forms is in use, this is an admin setting, not code** — untick "Enable Email Confirmation" on the Email field. Do it in admin and **flag in your report that it's a config change**, so it gets replicated on production rather than assumed deployed.

Check **every** instance — the form is shared across villa enquiry, concierge, contact and hotel.

---

## Task 2E — Guard empty CTA URLs *(design-fix)*

Several ACF CTA URL fields are empty and templates render the button anyway, producing dead links. **Fix the pattern, not just the instances.**

```
ips_cta_label / ips_cta_url    — IbizaPreservation panel
short_breaks_cta_url           — Short Breaks section
guide_view_all_url             — Ibiza Guide preview
fancy_hotel_url                — Fancy something different
my_bookings_url                — currently points at the homepage
```

**Rule: empty URL → render no element.** Not a disabled button, not `href="#"`.

`my_bookings_url` is a **parked decision, not a bug** — leave the value, but don't let the guard mask it. Flag it.

`fancy_airstream_url` is being retired separately (the Airstreams have been sold). Don't build against it.

---

## Task 2F — Single-source the response-time claim *(refactor — visual no-op)*

The response-time reassurance exists in multiple places with inconsistent wording, including a duplicated clause in the design: *"We respond within 20 minutes within our business hours."*

Known locations: villa detail enquiry panel, homepage three-step panel, trust strip, About page FAQ answer.

**Do:** add one Site Options field (suggested key `global_response_time_note`); every location reads from it.

**Visual no-op** — rendered output must not change until the operator sets a new value. Populate with the current trust-strip wording and report that the value needs replacing.

**Do not invent the wording.** It's an open client decision. This is the highest-leverage item here: it turns a four-place edit into a one-place edit, and the wording is going to change.

---

## Task 2G — Minimum-stay handling *(CONDITIONAL on Phase 1)*

Do not start until Phase 1 is reported and the operator confirms.

If a minimum stay exists:

- The date picker should prevent or clearly message ranges below it, rather than returning an empty grid
- The message states the minimum, not just a failure
- If the minimum is **per-villa**, that is materially more work — report scope before building
- The 2B statement must be consistent with the actual minimum

If no minimum exists, the task is void — say so and close it.

---

## Smoke test

Per task:

- **2A** — compare against Figma. Tab to it, space to toggle, confirm visible focus. Confirm the accessible name includes the count. Expire all offers; confirm the toggle disappears. Confirm the count matches the number of cards shown when ticked.
- **2B** — no short-breaks control renders; statement renders and is not focusable; homepage CTA arrives with dates pre-filled.
- **2C** — force all three states. Confirm an expired pinned offer does not render.
- **2D** — submit every shared form instance; no confirmation field, validation still catches malformed addresses.
- **2E** — clear a CTA URL; confirm no button element renders.
- **2F** — **before/after comparison, not design comparison.** Output identical. Then change the Site Options value once and confirm all locations update.

---

## Notes

- **Evidence over substitution.** If you think a token, helper or field name here is wrong, grep the canonical source and paste the output. This brief's names are explicitly provisional — silent substitution compounds error rather than fixing it.
- **Small in-scope fixes: act on them.** Missing `esc_attr`, a missing `aria-label`, dead code in a file you're already editing — fix in the same commit, note it in the body. Larger or out-of-scope: surface in your report.
- **Do not call the live API as a research method.** See "Corrected premise". The endpoint belongs to the client's operating business and the answers are in the repo.
- **Out of scope, flagged so they aren't lost:** Steve's multi-week pricing doc (logic settled, documentation never confirmed received); Luke's 11 Aug request for a separate agent-facing availability link; the "large-groups filter" currently in progress on the Notion board; 301s for retired Airstream URLs.
- **British English.** The site contains existing US spellings (`specialize`). Don't add more; don't fix ones outside your edited files.
- **Do not touch copy strings not named here.** A full copy replacement is running separately and will conflict.
