# Ibiza Guide: in-page newsletter band

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Mixed:

- **Greenfield:** style the existing-but-unstyled `default` variant
  of `newsletter-form`, build a small section wrapper for in-page
  use, and add page-level ACF for the Ibiza Guide newsletter copy.
- **Bug fix:** brief 02 called `ibv_core_section_newsletter_cta()`
  from `page-ibiza-guide.php`. That section is a footer-context
  signup — it's also called from `footer.php`. Result: two stacked
  small newsletter signups on the Ibiza Guide page (one in body,
  one in actual footer). This brief replaces the page-body call
  with the new banded section so the page has the prominent
  banded treatment per design, and the footer keeps its small
  signup.

This is the final brief in the Ibiza Guide page sequence. After it
lands, the page is feature-complete; only content population and
launch QA remain.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
"Ibiza Guide" page (frame `04a`). Newsletter section sits between
the article grid pager and the footer. Forest-green-deep band,
centered content, italic display title with a small horizontal
rule beneath, body copy, inline email form with arrow submit.

## Scope

**In:**

1. Style the `default` variant of `newsletter-form` to match the
   banded design.
2. Add page-level ACF on the Ibiza Guide page for `newsletter_title`
   and `newsletter_body`.
3. Build a small section helper or inline section markup in the
   page template that wraps `newsletter-form (variant: 'default')`
   in a forest-green-deep banded section.
4. Replace the `ibv_core_section_newsletter_cta()` call in
   `page-ibiza-guide.php` with the new banded treatment.

**Out:**

- Any change to `newsletter-cta` section (footer signup) — leave
  unchanged.
- Any change to `footer.php` — leave unchanged.
- Any change to the `empty-state` variant of newsletter-form.
- Any change to the form mechanics (still uses Gravity Form via
  the existing component).
- Site Options newsletter copy fields — the page uses its own
  page-scoped copy, not the global Site Options copy.

## Standards

- BEM naming, `ibv-` prefixes
- Surface system: section root carries
  `ibv-section ibv-section--surface-forest-green-deep` (verify the
  exact surface modifier name in `tokens.css` — there's a forest
  green deep surface in the system per memory; if the name differs,
  use the actual one)
- Section CSS does NOT redeclare `padding-block`, `background`,
  or `color` on the root — that's the surface system's job
- Existing tokens only — no new tokens
- Italic display title via the existing display font + italic
  modifier or a token if one exists
- Gold rule under the title — same pattern as the three-step
  section's title rule (the 100px gold rule, see About-story or
  three-step for the existing pattern)

## Files

```
EDIT   mu-plugins/ibv-core/includes/components/newsletter-form/newsletter-form.css
                                  (style the .ibv-newsletter-form--default variant)
EDIT   mu-plugins/ibv-core/includes/components/newsletter-form/newsletter-form.php
                                  (only if markup adjustments needed for default variant
                                   — likely no PHP changes required)

EDIT   mu-plugins/ibv-core/includes/acf/register-page-ibiza-guide.php
                                  (add newsletter_title + newsletter_body fields)

EDIT   themes/ibv/page-ibiza-guide.php
                                  (replace newsletter-cta call with banded section + form)
```

The agent decides whether to:

- **Inline the banded section markup** in `page-ibiza-guide.php`
  (simplest, single-page use), or
- **Build a small section helper** at
  `mu-plugins/ibv-core/includes/sections/newsletter-band/` that
  takes title/body args and is reusable

Bias toward the inline approach if this is the only page that
will use it. Bias toward the section helper if there's any
indication another page will need the same treatment. The Concierge
and Contact pages haven't been built yet — if their designs imply
the same banded newsletter, the helper is correct. If unclear,
inline is fine; the lift to extract later is small.

## Change 1 — Style `default` variant of newsletter-form

Read `newsletter-form.php` first to understand the existing
markup and the class hooks the variant produces. The CSS file
already styles `--footer` and `--empty-state` variants — the
`--default` variant is unstyled.

Style the default variant per design:

- Centered content within a constrained max-width (verify against
  design — looks like ~600px for the form/text column)
- Title: large display font, italic. Verify the existing display
  font supports italic; if not, fall back to a regular weight.
- Title rule: short horizontal gold rule beneath the title,
  matching the pattern used on three-step section's heading and
  About story's title rule. Use the same token-driven approach.
- Body copy: standard body font, lighter weight, centered
- Form: inline email input + small arrow-submit button to the
  right (or stacked on mobile)
- Form input: rounded, subtle border, sized for prominent display
- Submit: small icon button (right arrow) — the design shows it
  as a square button with an arrow icon; check if there's an
  existing arrow icon vendored from Lucide (the brief 01
  precedent — `assets/icons/lucide/clock.svg` was added then;
  brief 03 may have added more). Use what exists; if none,
  vendor the appropriate Lucide icon.
- All colours via existing tokens: title in light/cream, body in
  light/cream, button in gold or sage per the project's accent
  conventions

Mobile: stack vertically with sensible spacing.

## Change 2 — Page-level ACF for newsletter copy

In `register-page-ibiza-guide.php`, add two fields to the
existing Ibiza Guide field group:

- **Newsletter Title** — text field, key
  `field_ibiza_guide_newsletter_title` (or matching the existing
  field-key convention in this group)
- **Newsletter Body** — textarea, key
  `field_ibiza_guide_newsletter_body`

Both optional. If both are empty at render time, the section
renders nothing (graceful fallback). This is the same pattern
newsletter-cta uses for the Site Options footer copy.

Do NOT add these fields to Site Options. The Ibiza Guide
newsletter copy is page-specific; the Site Options copy belongs
to the footer signup (current behaviour).

## Change 3 — Page template

Replace this block in `themes/ibv/page-ibiza-guide.php`:

```php
ibv_core_section_newsletter_cta();
```

With:

```php
$nl_title = (string) get_field( 'newsletter_title' );
$nl_body  = (string) get_field( 'newsletter_body' );

if ( $nl_title || $nl_body ) {
    ?>
    <section class="ibv-section ibv-section--surface-forest-green-deep">
        <div class="ibv-container">
            <?php
            ibv_core_newsletter_form( [
                'title'       => $nl_title,
                'description' => $nl_body,
                'variant'     => 'default',
            ] );
            ?>
        </div>
    </section>
    <?php
}
```

If the agent chose the section-helper approach instead (per the
decision in the Files section), the page-template change is a
single helper call and the section-helper file owns the wrapping
markup.

The exact `--surface-forest-green-deep` modifier name must be
verified against `tokens.css`. If the project's name for this
surface differs (e.g. it might be `--surface-forest-deep` or
similar), use the correct name. The brief shouldn't lock a token
name without an evidence-based read.

## Change 4 — Cleanup

- Remove the now-unused `ibv_core_section_newsletter_cta()` call
  from `page-ibiza-guide.php`
- The `newsletter-cta` section file itself stays as-is —
  `footer.php` still uses it for the footer signup

## Verification

1. **Frontend smoke test:**
   - Navigate to the Ibiza Guide page on staging
   - The banded newsletter section renders between the article
     grid pager and the footer — full-width forest-green-deep
     band, centered content
   - Title in italic display font with gold rule beneath
   - Body copy below, lighter weight, centered
   - Inline email form with arrow submit, mobile-stacked
   - Below the band, the standard footer renders with its own
     small newsletter signup ("Newsletter set-up") — unchanged
   - **Critically:** there is NOT a duplicate small signup in the
     page body above the band. The brief 02 bug is fixed.

2. **Admin setup:**
   - Page ACF for newsletter title and body work as expected
   - With both empty: section doesn't render (graceful fallback)
   - With copy: section renders the entered copy

3. **Other pages regression:**
   - Homepage: footer's newsletter signup still renders correctly
     (same as before)
   - Special Offers page: footer's newsletter signup still
     renders correctly
   - Special Offers empty-state: empty-state newsletter still
     renders correctly (its variant is unchanged)

4. **Token + system compliance:**
   - All colours, spacing, typography from existing tokens
   - Section uses surface system correctly; no padding-block,
     background, or color declarations on the root
   - Form input border-radius and padding from existing tokens

5. **Mobile:**
   - Form stacks vertically below ~36rem breakpoint
   - Text remains centered and readable

## Notes

- **Why page-scoped copy, not Site Options.** The Ibiza Guide
  newsletter copy is editorially specific to the page — it pitches
  Ibiza tips and local content. The footer's signup is generic
  marketing opt-in. Different intent, different audience moment.
  Site Options would force the same copy everywhere; page-level
  ACF lets each page (Concierge, Contact, etc.) have its own
  pitch when they're built.
- **Why not extend `newsletter-cta` to take a variant arg.**
  `newsletter-cta` is named and structured around the footer
  use case — it reads its copy from Site Options because that's
  the footer pattern. Pushing variant logic into it would muddy
  its single-purpose design. The page-template-level wrapping is
  cleaner.
- **The Step 12 rule applies.** If small in-scope improvements
  surface during the work — a token name that's slightly off, a
  missing escape, a stray hardcoded value — fix them in the same
  commit and note. Larger or out-of-scope: surface, don't act.
- **Why this is the final brief.** After this brief, the Ibiza
  Guide page renders end-to-end per design: hero (compact) →
  featured article → category filter → article grid → pager →
  banded newsletter → footer. Content population and launch QA
  are the remaining gates, not more code.
