# About FAQ accordion section

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Greenfield. New ACF fields on the About page, new section
component, slot into the About page composition where the FAQ
TODO currently sits.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
frame `282-3846` ("05 | About"), the "Frequently asked questions"
block. Working PDF reference is provided alongside the brief as
the authoritative comp.

## Scope

Build the FAQ accordion on the About page:

- Extend `register-page-about.php` with a heading + repeater for
  question/answer pairs
- New section `sections/about-faq/` rendering each item as a
  native `<details>` element
- Slot into `themes/ibv/page-about.php` replacing the existing FAQ
  TODO comment

Out of scope:
- Site-wide / shared FAQ — this is About-page-scoped
- JavaScript for "single open at a time" behaviour — multi-open is
  the chosen interaction
- Schema.org FAQ structured data — defer to a separate SEO pass
  brief if/when wanted

## Standards

- ACF in PHP under `mu-plugins/ibv-core/includes/acf/`
- Sections under `mu-plugins/ibv-core/includes/sections/{name}/`
- Section root: `ibv-section-{name} ibv-section ibv-section--surface-{X}`,
  optional `--rhythm-sm`. No `padding-block`, `background`, or
  `color` declared on root.
- BEM naming, `ibv-` prefixes, no new dependencies
- Native `<details>` / `<summary>` — no JavaScript
- Editorial copy from explicit ACF fields (no `the_content()`)

## Files

```
EDIT  mu-plugins/ibv-core/includes/acf/register-page-about.php
NEW   mu-plugins/ibv-core/includes/sections/about-faq/about-faq.php
NEW   mu-plugins/ibv-core/includes/sections/about-faq/about-faq.css
EDIT  themes/ibv/page-about.php
EDIT  mu-plugins/ibv-core/bootstrap.php (require new section)
```

## Change 1 — ACF fields on About page

In `register-page-about.php`, add three new fields to the existing
About field group, after the Our Story block:

- `about_faq_eyebrow` — text, optional, default `Frequently asked questions`
- `about_faq_title` — text, optional. Use only if the design wants
  a separate large title in addition to the eyebrow; default blank.
- `about_faq_items` — repeater, no min/max:
  - `question` — text, required
  - `answer` — wysiwyg, required, basic toolbar (no media buttons)

Field key prefix: `field_ibv_page_about_faq_*`. Match the existing
About group's ACF style and instruction patterns.

The wysiwyg on `answer` is intentional — FAQ answers commonly
contain links (`Contact us`, page references, mailto: etc.).
Plain text would force editors to break up answers awkwardly. The
basic toolbar restricts to inline formatting only — no headings, no
images, no embeds.

## Change 2 — Section: `about-faq.php`

```php
<?php
/**
 * Section: About — FAQ accordion.
 *
 * Native <details> / <summary> per item. Multi-open by default;
 * no JavaScript.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ibv_core_section_about_faq() {
    $items = get_field( 'about_faq_items' );
    if ( ! is_array( $items ) || ! count( $items ) ) {
        return;
    }

    $eyebrow = (string) get_field( 'about_faq_eyebrow' );
    $title   = (string) get_field( 'about_faq_title' );

    wp_enqueue_style( 'ibv-section-about-faq' );
    ?>
    <section class="ibv-section-about-faq ibv-section ibv-section--surface-white">
        <div class="ibv-container ibv-section-about-faq__inner">
            <header class="ibv-section-about-faq__header">
                <?php if ( $eyebrow ) : ?>
                    <p class="ibv-section-about-faq__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                <?php endif; ?>
                <?php if ( $title ) : ?>
                    <h2 class="ibv-section-about-faq__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
            </header>

            <ol class="ibv-section-about-faq__list">
                <?php
                $i = 0;
                foreach ( $items as $item ) :
                    $question = (string) ( $item['question'] ?? '' );
                    $answer   = (string) ( $item['answer'] ?? '' );
                    if ( ! $question || ! $answer ) {
                        continue;
                    }
                    ++$i;
                    $num = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
                    ?>
                    <li class="ibv-section-about-faq__item">
                        <details class="ibv-section-about-faq__details">
                            <summary class="ibv-section-about-faq__summary">
                                <span class="ibv-section-about-faq__icon" aria-hidden="true"></span>
                                <span class="ibv-section-about-faq__question"><?php echo esc_html( $question ); ?></span>
                                <span class="ibv-section-about-faq__num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
                            </summary>
                            <div class="ibv-section-about-faq__answer">
                                <?php echo wp_kses_post( $answer ); ?>
                            </div>
                        </details>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>
    <?php
}
```

Notes on the markup:

- `<ol>` is correct — FAQ items are numbered ("01", "02", …) per
  the design. The numbering is part of the visual, so semantic
  ordering matters.
- `<details>` / `<summary>` provides keyboard navigation, expand /
  collapse, and an accessible disclosure widget for free.
- The `__icon` span is the +/− toggle, styled in CSS via the
  `[open]` selector. No JS required.
- `wp_kses_post()` on `$answer` allows safe inline formatting
  (links, em, strong, br) from the wysiwyg field.
- `__num` (the "01", "02") is `aria-hidden` because it's
  presentational ordering already conveyed by `<ol>`.

## Change 3 — `about-faq.css`

CSS structure (don't hardcode tokens — verify each against
`tokens.css`):

```css
.ibv-section-about-faq__inner {
    /* Header above, list below — single column, full container width. */
    display: flex;
    flex-direction: column;
    gap: var(--ibv-space-xl);
}

.ibv-section-about-faq__list {
    list-style: none;
    margin: 0;
    padding: 0;
    border-top: 1px solid var(--ibv-color-border);
}

.ibv-section-about-faq__item {
    border-bottom: 1px solid var(--ibv-color-border);
}

.ibv-section-about-faq__summary {
    list-style: none; /* hide native disclosure marker */
    cursor: pointer;
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: var(--ibv-space-md);
    padding-block: var(--ibv-space-md);
}

.ibv-section-about-faq__summary::-webkit-details-marker {
    display: none;
}

.ibv-section-about-faq__icon {
    /* +/− toggle in a tinted square panel on the left. Default = +, [open] = − */
    width: 2.5rem;
    height: 2.5rem;
    background: var(--ibv-color-bg);  /* tinted off-white square — verify token */
    position: relative;
    flex-shrink: 0;
}
.ibv-section-about-faq__icon::before,
.ibv-section-about-faq__icon::after {
    content: '';
    position: absolute;
    background: currentColor;
    inset-inline: 30%;
    inset-block: 50%;
    height: 1px;
    transform: translateY(-50%);
}
.ibv-section-about-faq__icon::after {
    /* vertical bar — hidden when open */
    inset-inline: 50%;
    inset-block: 30%;
    width: 1px;
    height: auto;
    transform: translateX(-50%);
    transition: transform 0.15s ease;
}
.ibv-section-about-faq__details[open] .ibv-section-about-faq__icon::after {
    transform: translateX(-50%) scaleY(0);
}

.ibv-section-about-faq__question {
    /* body copy size, weight per design — verify against tokens */
}

.ibv-section-about-faq__num {
    font-family: var(--ibv-font-display);
    color: var(--ibv-color-text-muted);
    /* small display-font number per design */
}

.ibv-section-about-faq__answer {
    /* Indent answer to align with the question (past the icon column). */
    padding-inline-start: calc(2.5rem + var(--ibv-space-md));
    padding-block-end: var(--ibv-space-md);
    max-width: 60ch;
    color: var(--ibv-color-text-muted);
}
```

Verify token names against `tokens.css` before writing CSS. The
icon's tinted-square background is the off-white panel visible in
the design — it's NOT the section's own surface (which is white).
If `--ibv-color-bg` resolves correctly to that off-white tint, use
it. If not, find the existing token used for similar tinted
elements (search bar, badge backgrounds) and match. Don't invent.

## Change 4 — Page composition

In `themes/ibv/page-about.php`, replace the FAQ TODO with the
section call:

```php
ibv_core_section_three_step();
ibv_core_section_testimonials();

ibv_core_section_about_faq();    // ← was: TODO: FAQ — brief 04.

// TODO: Team section — brief 05.

ibv_core_section_newsletter_cta();
```

Leave the Team TODO in place — that's brief 05.

## Change 5 — Bootstrap inclusion

In `mu-plugins/ibv-core/bootstrap.php`, add the new section file
to the sections require block (alphabetical or by feature, match
existing convention):

```php
require_once IBV_CORE_PATH . 'includes/sections/about-faq/about-faq.php';
```

## Verification

1. **Admin smoke test:**
   - About page edit screen now shows FAQ fields below the Our
     Story block: Eyebrow, Title (optional), Items repeater
   - Repeater rows accept question + answer (wysiwyg)
   - Other pages don't show these fields

2. **Populate sample content** from the PDF (3 items minimum to
   test ordering):
   - 01 — How do I book? — body copy from PDF
   - 02 — What is the deposit?
   - 03 — Do you offer short breaks?

3. **Frontend smoke test:**
   - Section renders below Testimonials on the About page
   - Numbering starts at 01 and increments
   - Click question → expands; click again → collapses
   - Multiple items can be open at once
   - Keyboard: Tab focuses each summary; Enter / Space toggles
   - Mobile: header stacks above list, list still works
   - +/− icon toggles correctly via CSS only (no JS errors in
     console)

4. **Section-system compliance:**
   - Section root carries surface modifier; CSS doesn't redeclare
     `padding-block`, `background`, `color`
   - All tokens used exist in `tokens.css`

## Notes

- **Why native `<details>`.** Keyboard-accessible, screen-reader
  friendly, no JS dependency, no flash of unstyled content. The
  one trade-off — no fancy expand animation — is fine for this
  context. If animation becomes a requirement later, JS can wrap
  the existing markup without breaking semantics.
- **Multi-open is intentional.** Single-open forces editors to
  re-click between answers; multi-open lets users compare answers
  side-by-side. Worth more than the visual tidiness of one-at-a-time.
- **Why the answer field is wysiwyg with basic toolbar.** Plain
  text would force unnatural copy ("Contact us" can't be a link
  inline). Full wysiwyg invites heading drift and image pasting.
  Basic toolbar threads the needle: links + emphasis only.
- **Why About-page-scoped, not global.** These FAQs are
  about-specific (booking, deposit, short breaks). A site-wide FAQ
  block at e.g. the bottom of every page would have different
  questions. If shared FAQs become a pattern (Concierge,
  Special Offers, Villa pages), promote to a global structure
  then. Premature globalisation is its own kind of debt.
- **Surface choice — `surface-white`.** Per design: pure white,
  not the off-white `surface-bg`. The toggle's tinted-square
  panel provides the only off-white touch in the section.
- **Layout is single-column.** Header sits above the list; list
  spans full container width. No two-column split. Per design.
- **Icon is on the LEFT** of the question, as a tinted square
  panel. The "01"/"02" number is on the RIGHT, muted grey.
