# Brief 03 — Newsletter helper extraction

> Before implementing, scrutinise these instructions and raise any concerns
> or suggest alternatives — but only where there is a genuine technical
> reason to do so. Do not flag things for the sake of it.

## Standards

- ACF field groups registered in PHP under
  `mu-plugins/ibv-core/includes/acf/`
- Components live under `mu-plugins/ibv-core/includes/components/{name}/`
  with a `.php` and a `.css`
- Buttons via `ibv_core_button()` helper
- BEM naming, `ibv-` prefixes, no new dependencies, no build step

## Scope

The footer's newsletter form is currently rendered by the
`newsletter-cta` section, which inlines a Gravity Forms shortcode and
reads the form ID from `newsletter_form_id` on Site Options.

Two changes:

1. The Gravity Form ID is global config, not footer-specific. Promote
   it to the same globals file created in brief 01
   (`register-globals-content.php`), under a more accurate name
   (`newsletter_gravity_form_id`).

2. The form rendering needs to be reusable across contexts. The
   Special Offers empty-state in brief 05 will render the same
   newsletter form with different surrounding copy and styling.
   Extract the rendering into a `ibv_core_newsletter_form()` helper
   accepting context-specific args.

The footer must remain visually identical after this refactor — it's
still rendering the same form with the same intro/body/styling, just
through one extra function indirection.

This brief depends on brief 01 for the globals file, but doesn't
depend on briefs 02 or 04.

## Files

```
NEW   mu-plugins/ibv-core/includes/components/newsletter-form/newsletter-form.php
NEW   mu-plugins/ibv-core/includes/components/newsletter-form/newsletter-form.css
EDIT  mu-plugins/ibv-core/includes/acf/register-globals-content.php (from brief 01)
EDIT  mu-plugins/ibv-core/includes/acf/register-site-options-content.php
EDIT  mu-plugins/ibv-core/includes/sections/newsletter-cta/newsletter-cta.php
EDIT  mu-plugins/ibv-core/includes/sections/newsletter-cta/newsletter-cta.css
EDIT  wherever component CSS is enqueued/registered
```

## Change 1 — Add Gravity Form ID to globals

In `register-globals-content.php` (created by brief 01), append a
third field group OR add a single field to an existing group — agent's
call.

**Field: `newsletter_gravity_form_id`**

- Type: number
- Instructions: "Gravity Form ID used by all newsletter forms across
  the site (footer, empty states, etc.). Set once here."
- Field key: `field_ibv_global_newsletter_gravity_form_id`

## Change 2 — Drop the form ID from `register-site-options-content.php`

Locate the existing `newsletter_form_id` field. Remove it entirely
from whatever group it currently sits in. Keep `newsletter_intro` and
`newsletter_body` (the footer-specific copy fields) where they are —
those stay footer-scoped.

## Change 3 — Create `ibv_core_newsletter_form()` helper

New file at
`mu-plugins/ibv-core/includes/components/newsletter-form/newsletter-form.php`.

```php
<?php
/**
 * Component: Newsletter form.
 *
 * Renders a Gravity Form-backed newsletter signup. Reads the Gravity
 * Form ID from globals; each caller passes its own copy and style
 * variant.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @param array $args {
 *     @type string $title       Optional heading.
 *     @type string $description Optional body copy above the form.
 *     @type string $variant     'footer' | 'empty-state' | 'default'.
 * }
 */
function ibv_core_newsletter_form( $args = [] ) {
    wp_enqueue_style( 'ibv-newsletter-form' );

    $defaults = [
        'title'       => '',
        'description' => '',
        'variant'     => 'default',
    ];
    $args = wp_parse_args( $args, $defaults );

    $valid_variants = [ 'default', 'footer', 'empty-state' ];
    $variant        = in_array( $args['variant'], $valid_variants, true )
        ? $args['variant']
        : 'default';

    $form_id = (int) get_field( 'newsletter_gravity_form_id', 'option' );
    if ( ! $form_id ) {
        return;
    }
    ?>
    <div class="ibv-newsletter-form ibv-newsletter-form--<?php echo esc_attr( $variant ); ?>">
        <?php if ( $args['title'] ) : ?>
            <h3 class="ibv-newsletter-form__title ibv-font-display"><?php echo esc_html( $args['title'] ); ?></h3>
        <?php endif; ?>
        <?php if ( $args['description'] ) : ?>
            <p class="ibv-newsletter-form__body"><?php echo esc_html( $args['description'] ); ?></p>
        <?php endif; ?>
        <div class="ibv-newsletter-form__embed">
            <?php
            echo do_shortcode( '[gravityform id="' . absint( $form_id ) . '" title="false" description="false" ajax="true"]' );
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </div>
    </div>
    <?php
}
```

## Change 4 — Create `newsletter-form.css`

The base `.ibv-newsletter-form` plus three modifier classes
(`--footer`, `--empty-state`, `--default`).

For the **`--footer` modifier**: port the existing styling from
`newsletter-cta.css`. The footer is the visual reference; the refactor
must be a visual no-op there. Move the rules from
`.ibv-newsletter-cta` / `.ibv-newsletter-cta__title` /
`.ibv-newsletter-cta__body` / `.ibv-newsletter-cta__form` to the
equivalent `.ibv-newsletter-form--footer` / `__title` / `__body` /
`__embed` selectors.

For the **`--empty-state` modifier**: leave a stub block with
`/* Styled by special-offers-empty-state context — see brief 05. */`
or similar. Brief 05 fills it in.

For the **`--default` modifier**: empty selector or omit if not
needed.

## Change 5 — Update `newsletter-cta.php` to delegate

```php
function ibv_core_section_newsletter_cta() {
    $intro = (string) get_field( 'newsletter_intro', 'option' );
    $body  = (string) get_field( 'newsletter_body', 'option' );

    if ( ! $intro && ! $body ) {
        return;
    }

    if ( ! $intro ) {
        $intro = __( 'Newsletter set-up', 'ibv' );
    }
    if ( ! $body ) {
        $body = __( 'Sign up to receive marketing from Ibiza Villas 2000', 'ibv' );
    }

    ibv_core_newsletter_form(
        [
            'title'       => $intro,
            'description' => $body,
            'variant'     => 'footer',
        ]
    );
}
```

The function reads its copy fields, applies fallbacks, and delegates
to the helper. Footer.php (or wherever this section is called from)
continues to call `ibv_core_section_newsletter_cta()` — no changes
needed at the call site.

## Change 6 — Strip styling from `newsletter-cta.css`

After Change 4 has ported the styles to `newsletter-form.css` under
the `--footer` modifier, the section's own CSS is empty. Either:

- **Delete** `newsletter-cta.css` and remove its enqueue registration
  in the section's PHP (and wherever sections register their CSS).
- **Leave** the file in place with just a comment explaining styling
  has moved to the helper, and keep the (no-op) enqueue.

Deletion is cleaner. Pick that unless the registry pattern requires a
CSS file per section.

## Verification

1. **WP admin smoke test:**
   - Site Options → globals tab: `newsletter_gravity_form_id` field
     visible.
   - Site Options → footer/newsletter tab: `newsletter_form_id` is
     gone. Intro and body copy fields remain.

2. **Re-set the Gravity Form ID** under the new globals field.

3. **Footer frontend smoke test:**
   - Newsletter form renders identically to before — same heading,
     same body copy, same form fields, same submit button styling.
   - DOM markup wrapper now reads
     `<div class="ibv-newsletter-form ibv-newsletter-form--footer">`
     instead of `<div class="ibv-newsletter-cta">`.
   - Form submission still works (Gravity Forms ajax submit, success
     state).

4. **CSS check:**
   - Footer newsletter visual is pixel-identical (or as close as the
     refactor allows). Eye-test against a saved screenshot of the
     footer pre-brief.

## Notes

- **Why move the form ID to globals.** The footer is one consumer; the
  SO empty-state in brief 05 is a second. Any future newsletter
  consumer is a third. One field, set once.
- **Why keep `newsletter_intro` / `newsletter_body` on Site Options.**
  Those are footer-specific copy. The empty state has its own copy
  fields (declared in brief 05). They aren't the same content.
- **`newsletter-cta` section keeps its name.** The section is the
  footer block; the helper is the form. Keeping the section name
  unchanged preserves the footer.php call site.
- **Form ID rename: `newsletter_form_id` → `newsletter_gravity_form_id`.**
  More descriptive. Old field is dropped, new field is set fresh —
  no migration since staging is throwaway and this is one number to
  re-enter.
- **Visual no-op is the contract.** If footer rendering changes
  pixel-wise post-refactor, that's a regression. Catch it on smoke
  test, don't ship it.
