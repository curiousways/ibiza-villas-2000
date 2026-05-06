# `docs/briefs/` — implementation briefs

This folder holds **implementation briefs** — structured specifications
written for an AI agent to execute. Each brief describes a discrete
piece of work: the scope, the files to touch, the exact changes to
make, and how to verify it landed correctly. Once a brief has been
implemented and merged, it stays here as a record of what was built
and why.

## Why briefs exist

Curious Ways uses agentic coding (currently Claude Code; Cursor in
agent mode for some past work) to do most of the implementation.
The briefs are how the work is specified — they exist between
"David has a clear idea of what needs to happen" and "the code
ships". Writing a brief forces edge cases to surface before code is
written, and gives the agent enough structure to make consistent
decisions without back-and-forth.

Briefs are a deliberate artefact of the workflow, not internal scratch
notes. Future collaborators reading the commit history can read the
brief that produced any given commit and understand the decision
context.

## Structure

```
docs/briefs/
├── README.md                  ← this file
├── active/                    ← briefs currently in flight or queued
├── completed/                 ← briefs that have shipped, kept for history
└── superseded/                ← briefs reverted or rewritten before shipping
```

A brief moves from `active/` to `completed/` once the corresponding
work is merged to the staging branch and verified.

## File naming

`NN-slug.md` where `NN` is a two-digit sequence number scoped to a
piece of work. Slugs are kebab-case and describe the scope.

Example sequence for the Special Offers page rebuild:

```
completed/
├── 01-acf-globals.md
├── 02-hero-extract-search.md
├── 03-newsletter-helper.md
├── 04-villa-card-extension.md
└── 05-special-offers-page.md
```

Numbers reset per piece of work — the next major workstream starts
again at `01`. The folder structure preserves chronology via git
log; the numbers are for human readability when reading several
related briefs in sequence.

## Anatomy of a brief

Every brief follows the same shape, enforced by the
`agent-brief` skill in David's Claude account:

1. **Scrutiny line** — the brief opens by telling the agent to
   push back on anything that doesn't make technical sense before
   implementing.
2. **Standards** — codebase conventions the brief assumes
   (BEM naming, file locations, helper functions, section rhythm
   system, etc.).
3. **Scope** — what's in, what's out, why.
4. **File list** — every file that will be created, edited, renamed,
   or deleted.
5. **Changes** — numbered, file-by-file, with code samples where
   helpful.
6. **Verification** — admin and frontend smoke-test steps.
7. **Notes** — design decisions, tradeoffs considered, things
   deliberately deferred.

## How a brief is implemented

1. Brief is written in a Claude conversation (Curious Ways' Claude
   project for this client).
2. Brief is committed to `docs/briefs/active/`.
3. Claude Code (or another agent) is pointed at the brief and
   executes it. The agent reads relevant files, makes the changes,
   and commits with a meaningful message.
4. David smoke-tests in admin + frontend.
5. Once verified, the brief is moved to `docs/briefs/completed/` and
   the commit message references the brief filename.

## Reading the codebase via the briefs

If you're new to the codebase and want to understand why something
is the way it is, the brief that introduced it is usually the
fastest read. `git log -- path/to/file.php` gives you commits
touching that file; the commit messages reference the brief that
produced them; the brief explains the design context that the code
itself doesn't.

This is especially useful for things like:

- Why `weekly-offer/` was renamed to `featured-offer/` (brief 01 of
  the SO sequence)
- Why the hero takes an `after_copy` callable instead of rendering
  search inline (brief 02)
- Why the villa card's `badge` arg was opened to all variants
  (brief 04)

## What briefs are NOT

- **Not client-facing.** Clients don't need to read these. Strategy,
  positioning, and deliverable communication live in Notion and
  Qwilr proposals.
- **Not a substitute for code review.** A brief specifies what
  should happen; David reviews what actually happened in the diff.
- **Not architectural documentation.** They describe specific
  pieces of work, not the system overall. For architecture and
  conventions, read the codebase's own README and the section/CSS
  organisation directly.
- **Not eternal.** A brief reflects the codebase as it was at
  implementation time. Reading an old brief 6 months later, expect
  some details to be out of date — the codebase has moved on.
  The brief is still useful for understanding the original
  decision, just not as a current spec.
