# Legals — follow-up: tighten band/tabs/content spacing, then commit

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Follow-up / finish (small CSS spacing fix + housekeeping on
the already-implemented Legals work — nothing else changes).
**Context:** the Legals template (`title-band`, `legal-tabs`, `legal-content`,
`page-legal.php`, four Pages) is built and verified; it's uncommitted, held
for this fix. Design source as before: node `301:9442` in David's MCP copy
`3g57x18kqjjNtnJ9SVgTUI`.

---

## 1 — The spacing fix (the only functional change)

**Measured from the design (no MCP call needed):** the hero band frame ends at
y=261, tabs run 261–297, the content frame starts at 297 with its first text
~24px in. The visible whitespace is the band's own 48px internal padding plus
that ~24px content offset — there is **no rhythm gap** between the three
blocks. The current build has 72px standard rhythm at each junction, roughly
triple the design.

Treat **band → tabs → content as one visual unit**:

- Collapse the inter-block gaps at the two junctions (band/tabs, tabs/content).
  All three are `surface-bg`, so use the **same-surface adjacency collapse**
  where the system already provides it, and remove the remaining
  `padding-block-end` at these junctions in whatever way is cleanest **within
  the rhythm system** — e.g. the existing `--rhythm-sm` modifier if it gets
  close, or a scoped rule keyed to the legal-page composition. Do **not** hack
  `padding-block` overrides onto the section roots in their own component CSS
  (the system owns rhythm; a page/composition-scoped exception is acceptable,
  a per-section self-override is not).
- Preserve the band's internal 48px padding and give the content column its
  ~24px top offset per the design.
- Keep normal rhythm **below** the content column (before the footer) — the
  collapse applies only inside the band/tabs/content unit.

**Acceptance:** on all four pages, the tabs sit tight under the band and the
content column starts ~24px under the tabs, visually matching node `301:9442`.
Other pages' section rhythm is untouched.

## 2 — Housekeeping (already-agreed calls; no new decisions)

- **Adopted pages stay as-is:** Terms & Conditions and Privacy Policy remain on
  `page-legal.php` with their content untouched; **no rename** of "Terms &
  Conditions" (editorial, deferred to the content pass).
- **Footer legal links:** no code change to `footer_legal_html`. In your
  summary, restate the editorial rule for David's admin pass: links inside that
  WYSIWYG must use **relative paths** (`/privacy-policy/`,
  `/terms-conditions/`), never absolute `.test` URLs (same cross-environment
  footgun as the `page_link` fixes, buried in HTML).
- **`listing_note_link_url`:** still held for David — do not convert.

## 3 — Verify

1. Re-render all four legal pages (200, no fatals); band → tabs → content
   spacing matches the design; correct active tab per page with
   `aria-current="page"` (re-confirm on `/terms-conditions/`, the adopted one).
2. Quick regression glance at one non-legal page (e.g. About) to confirm no
   rhythm/system rule leaked beyond the legal composition.

## 4 — Commit

- Commit everything as **`feat(legal): add legals template — four pages, one
  template, tabbed nav`** (one commit; the spacing fix is part of the feature
  landing, not a separate fix).
- Reference the brief paths in the commit body; move **both** Legals briefs —
  the original and this follow-up — from `docs/briefs/active/` to
  `docs/briefs/done/` **within the same commit**.
- Working tree clean afterwards; nothing pushed (local commits only, as usual).

## Notes

- Scope discipline: this brief is the spacing fix + commit. No other visual
  tweaks, no content edits, no field changes.
- If the same-surface collapse can't produce the tight junctions without a
  genuinely ugly exception, stop and describe the options rather than forcing
  it — but a composition-scoped rule is expected to be enough.
