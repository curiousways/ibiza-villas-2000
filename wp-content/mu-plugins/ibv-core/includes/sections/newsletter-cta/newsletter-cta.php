<?php
/**
 * Section: Newsletter CTA (footer + reuse).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gravity Forms newsletter embed.
 */
function ibv_core_section_newsletter_cta() {
	wp_enqueue_style( 'ibv-section-newsletter-cta' );

	$intro = get_field( 'newsletter_intro', 'option' );
	$fid   = (int) get_field( 'newsletter_form_id', 'option' );

	if ( ! $intro && ! $fid ) {
		return;
	}
	?>
	<div class="ibv-newsletter-cta">
		<?php if ( $intro ) : ?>
			<h4 class="ibv-newsletter-cta__title"><?php echo esc_html( $intro ); ?></h4>
		<?php endif; ?>
		<?php if ( $fid ) : ?>
			<div class="ibv-newsletter-cta__form">
				<?php echo do_shortcode( '[gravityform id="' . absint( $fid ) . '" title="false" description="false" ajax="true"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
