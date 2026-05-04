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
			echo do_shortcode( sprintf( '[gravityform id="%d" title="false" description="false" ajax="true"]', absint( $form_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
	<?php
}
