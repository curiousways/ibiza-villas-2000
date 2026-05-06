# About Team section

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
component, slot into the About page composition where the Team
TODO currently sits. Closes out the About page sequence.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
frame `282-3846` ("05 | About"), the "The Team" block. Working
PDF reference is provided alongside the brief; the design detail
in the screenshots shared with this brief is the authoritative
visual reference.

## Scope

Build the Team section on the About page:

- Extend `register-page-about.php` with eyebrow/title fields, a
  members repeater, and a closing copy field
- New section `sections/about-team/` with a 2-column grid layout
- Slot into `themes/ibv/page-about.php` replacing the existing
  Team TODO comment

## Standards

- ACF in PHP under `mu-plugins/ibv-core/includes/acf/`
- Sections under `mu-plugins/ibv-core/includes/sections/{name}/`
- Section root: `ibv-section-{name} ibv-section ibv-section--surface-{X}`,
  optional `--rhythm-sm`. No `padding-block`, `background`, or
  `color` declared on root.
- BEM naming, `ibv-` prefixes, no new dependencies, no JavaScript
- Editorial copy from explicit ACF fields (no `the_content()`)
- All typography and colour values use existing tokens. **No new
  tokens. No design-driven token additions in this brief.** If the
  design appears to require a colour or size that doesn't exist,
  pick the closest existing match and flag the gap rather than
  invent.

## Files

```
EDIT  mu-plugins/ibv-core/includes/acf/register-page-about.php
NEW   mu-plugins/ibv-core/includes/sections/about-team/about-team.php
NEW   mu-plugins/ibv-core/includes/sections/about-team/about-team.css
EDIT  themes/ibv/page-about.php
EDIT  mu-plugins/ibv-core/bootstrap.php (require new section)
EDIT  mu-plugins/ibv-core/includes/shared-assets.php (register CSS handle)
```

## Change 1 — ACF fields on About page

In `register-page-about.php`, add four new fields to the existing
About field group, after the FAQ block:

- `about_team_eyebrow` — text, optional, default `The Team`
- `about_team_title` — text, optional. The large display heading
  ("The people who chose to take on this problem — and stay with
  it.")
- `about_team_members` — repeater, no min/max, button label "Add
  member":
  - `image` — image, return array, required, instructions: "Portrait orientation."
  - `name` — text, required
  - `role` — text, optional, instructions: "Job title or role."
  - `bio` — textarea, required, 5 rows, plain text
- `about_team_closing` — textarea, optional, 4 rows, plain text.
  The paragraph at the bottom of the section ("We're a small,
  dedicated team who believe great service comes from real people,
  not systems...")

Field key prefix: `field_ibv_page_about_team_*`. Match the existing
About group's ACF style.

## Change 2 — Section: `about-team.php`

```php
<?php
/**
 * Section: About — The Team.
 *
 * Eyebrow + display title spanning two columns at the top, members
 * grid (2 columns at desktop, each member's image-name-role-bio
 * stacked), closing paragraph below spanning the right column.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ibv_core_section_about_team() {
    $members = get_field( 'about_team_members' );
    if ( ! is_array( $members ) || ! count( $members ) ) {
        return;
    }

    $eyebrow = (string) get_field( 'about_team_eyebrow' );
    $title   = (string) get_field( 'about_team_title' );
    $closing = (string) get_field( 'about_team_closing' );

    wp_enqueue_style( 'ibv-section-about-team' );
    ?>
    <section class="ibv-section-about-team ibv-section ibv-section--surface-bg">
        <div class="ibv-container ibv-section-about-team__inner">

            <header class="ibv-section-about-team__header">
                <?php if ( $eyebrow ) : ?>
                    <p class="ibv-section-about-team__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                <?php endif; ?>
                <?php if ( $title ) : ?>
                    <h2 class="ibv-section-about-team__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
            </header>

            <ul class="ibv-section-about-team__grid">
                <?php foreach ( $members as $member ) :
                    $image = $member['image'] ?? null;
                    $name  = (string) ( $member['name'] ?? '' );
                    $role  = (string) ( $member['role'] ?? '' );
                    $bio   = (string) ( $member['bio'] ?? '' );
                    if ( ! $name || ! $bio || empty( $image['ID'] ) ) {
                        continue; // Defensive: skip incomplete members.
                    }
                    ?>
                    <li class="ibv-section-about-team__member">
                        <div class="ibv-section-about-team__media">
                            <?php
                            ibv_core_image(
                                $image,
                                'ibv-card',
                                [ 'class' => 'ibv-section-about-team__image' ]
                            );
                            ?>
                        </div>
                        <p class="ibv-section-about-team__name-role">
                            <strong class="ibv-section-about-team__name"><?php echo esc_html( $name ); ?></strong>
                            <?php if ( $role ) : ?>
                                <span class="ibv-section-about-team__role"><?php echo esc_html( $role ); ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="ibv-section-about-team__bio"><?php echo esc_html( $bio ); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ( $closing ) : ?>
                <p class="ibv-section-about-team__closing"><?php echo esc_html( $closing ); ?></p>
            <?php endif; ?>

        </div>
    </section>
    <?php
}
```

Notes on markup:

- **Single repeater, no separate "featured" flag.** The first
  member is rendered in the same shape as the rest — visual
  prominence comes from the grid layout (member cards are large
  enough that the first feels featured by default).
- **Name + role on one line.** `<strong>` for the name (bold) plus
  a regular-weight span for the role, separated by a space. Per
  design.
- **Closing paragraph spans only the right column at desktop.**
  CSS controls the alignment, not markup. See screenshot detail
  showing the closing copy aligned with the right column of
  members.

## Change 3 — `about-team.css`

```css
.ibv-section-about-team__inner {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    column-gap: var(--ibv-space-xl);
    row-gap: var(--ibv-space-2xl);
}

.ibv-section-about-team__header {
    grid-column: 1 / -1; /* Header spans both columns. */
    display: flex;
    flex-direction: column;
    gap: var(--ibv-space-md);
}

.ibv-section-about-team__eyebrow {
    font-size: var(--ibv-fs-small);
    font-weight: var(--ibv-font-weight-regular);
    color: var(--ibv-color-text);
    margin: 0;
}

.ibv-section-about-team__title {
    font-family: var(--ibv-font-display);
    font-size: var(--ibv-fs-display);
    font-weight: var(--ibv-font-weight-regular);
    line-height: var(--ibv-lh-tight);
    color: var(--ibv-color-text);
    margin: 0;
    max-width: 22ch;
}

.ibv-section-about-team__grid {
    grid-column: 1 / -1; /* Members fill both columns. */
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    column-gap: var(--ibv-space-xl);
    row-gap: var(--ibv-space-2xl);
}

.ibv-section-about-team__member {
    display: flex;
    flex-direction: column;
    gap: var(--ibv-space-md);
}

.ibv-section-about-team__media {
    aspect-ratio: 4 / 5;
    overflow: hidden;
    border-radius: var(--ibv-radius-lg);
}

.ibv-section-about-team__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.ibv-section-about-team__name-role {
    margin: 0;
}

.ibv-section-about-team__name {
    font-weight: var(--ibv-font-weight-bold);
    color: var(--ibv-color-text);
}

.ibv-section-about-team__role {
    color: var(--ibv-color-text);
    font-weight: var(--ibv-font-weight-regular);
}

.ibv-section-about-team__bio {
    color: var(--ibv-color-text-muted);
    margin: 0;
}

.ibv-section-about-team__closing {
    grid-column: 2 / -1; /* Right column only at desktop. */
    color: var(--ibv-color-text-muted);
    margin: 0;
}

@media (max-width: 47.999rem) {
    .ibv-section-about-team__inner,
    .ibv-section-about-team__grid {
        grid-template-columns: minmax(0, 1fr);
    }
    .ibv-section-about-team__closing {
        grid-column: 1 / -1; /* Full width on mobile. */
    }
}
```

Verify each token against `tokens.css` before writing CSS:

- `--ibv-fs-small`, `--ibv-fs-display`
- `--ibv-color-text`, `--ibv-color-text-muted`
- `--ibv-font-display`, `--ibv-font-weight-regular`, `--ibv-font-weight-bold`
- `--ibv-lh-tight`
- `--ibv-space-md`, `--ibv-space-xl`, `--ibv-space-2xl`
- `--ibv-radius-lg`

If any of these don't exist, find the closest existing equivalent
and use that. **Do not add new tokens.** If a value the design
appears to want has no existing token, pick the closest match,
note the gap in the commit message — don't invent.

The portrait `aspect-ratio: 4 / 5` is not a token; it's a fixed
ratio on this section's image wrapper. Surface-level CSS-only
concern.

## Change 4 — Page composition

In `themes/ibv/page-about.php`, replace the Team TODO with the
section call:

```php
ibv_core_section_about_faq();

ibv_core_section_about_team();    // ← was: TODO: Team — brief 05.

ibv_core_section_newsletter_cta();
```

This closes out the About page composition. No more TODOs.

## Change 5 — Bootstrap inclusion

In `mu-plugins/ibv-core/bootstrap.php`, add the new section file
to the sections require block (alphabetical or by feature, match
existing convention):

```php
require_once IBV_CORE_PATH . 'includes/sections/about-team/about-team.php';
```

## Change 6 — Register CSS handle

In `mu-plugins/ibv-core/includes/shared-assets.php`, register the
new section's CSS handle (`ibv-section-about-team`) following the
pattern used by other sections.

## Verification

1. **Admin smoke test:**
   - About page edit screen shows Team fields below FAQ:
     Eyebrow, Title, Members repeater, Closing
   - Members repeater accepts image (required), name, role, bio
   - Other pages don't show these fields

2. **Populate sample content:**
   - Eyebrow: `The Team`
   - Title: `The people who chose to take on this problem — and stay with it.`
   - Members: 4 entries (any combination from the design or
     placeholder content). Each with portrait image, name, role,
     bio.
   - Closing: paragraph from PDF

3. **Frontend smoke test:**
   - Section renders below FAQ on About page
   - Header (eyebrow + display title) spans both columns at top
   - Members render in 2-column grid at desktop
   - Each member: portrait image (4:5 aspect), name in bold + role
     regular weight on same line, bio paragraph in muted body
     colour
   - Closing paragraph aligns with right column at desktop, full
     width at mobile
   - Mobile: members stack to single column
   - No console errors

4. **Token compliance:**
   - All values used in `about-team.css` exist in `tokens.css` —
     grep to confirm
   - No hardcoded colours, font sizes, or spacing values
   - No new tokens added by this brief

## Notes

- **No featured/non-featured distinction.** The first repeater
  entry is rendered in the same shape as the rest. Visual
  prominence comes from the grid layout (member cards are large
  enough on their own). Earlier consideration of an "is_featured"
  flag rejected — a flag would add complexity without changing
  what gets rendered.
- **Eyebrow stays regular weight.** Design shows the eyebrow as
  bold; the existing system convention (per `three-step.css`) is
  regular weight on small text. Sticking with the system. If a
  bold variant becomes a recurring need, that's a foundation
  decision, not a per-section override.
- **Image aspect 4:5 portrait.** The screenshots show portrait
  member photos at roughly 4:5 ratio. Using CSS `aspect-ratio` on
  the wrapper with `object-fit: cover` so any uploaded source
  crops to portrait. No new registered image size needed.
- **Closing paragraph alignment.** Per the design detail, the
  closing copy aligns with the right column. CSS `grid-column: 2 /
  -1` puts it there at desktop; full width at mobile.
- **No JavaScript.** Pure CSS layout. Static content.
- **Surface — `surface-bg`.** Matches the off-white background
  visible in the design detail screenshots. Not pure white. Verify
  during smoke test; if it reads wrong against the design,
  surface-white may be correct instead — but pick from existing
  surface modifiers, do not add new ones.
