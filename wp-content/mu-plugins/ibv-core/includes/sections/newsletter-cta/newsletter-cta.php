<?php
/**
 * Section: Newsletter CTA (footer; fields in Site Options).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gravity Forms newsletter embed. Reads ACF from Site Options (global footer).
 */
function ibv_core_section_newsletter_cta() {
	wp_enqueue_style( 'ibv-section-newsletter-cta' );

	$intro = (string) get_field( 'newsletter_intro', 'option' );
	$body  = (string) get_field( 'newsletter_body', 'option' );
	$fid   = (int) get_field( 'newsletter_form_id', 'option' );

	if ( ! $intro && ! $fid ) {
		return;
	}

	if ( ! $intro ) {
		$intro = __( 'Newsletter set-up', 'ibv' );
	}
	if ( ! $body ) {
		$body = __( 'Sign up to receive marketing from Ibiza Villas 2000', 'ibv' );
	}
	?>
	<div class="ibv-newsletter-cta">
		<h3 class="ibv-newsletter-cta__title ibv-font-display"><?php echo esc_html( $intro ); ?></h3>
		<?php if ( $body ) : ?>
			<p class="ibv-newsletter-cta__body"><?php echo esc_html( $body ); ?></p>
		<?php endif; ?>
		<?php if ( $fid ) : ?>
			<div class="ibv-newsletter-cta__form">
				<?php echo do_shortcode( '[gravityform id="' . absint( $fid ) . '" title="false" description="false" ajax="true"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
