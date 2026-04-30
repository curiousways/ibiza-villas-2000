<?php
/**
 * Section: Newsletter CTA (footer; fields stored on Front Page).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gravity Forms newsletter embed. Reads ACF from the site Front Page so the
 * block can render in the global footer while content is edited on Home.
 */
function ibv_core_section_newsletter_cta() {
	wp_enqueue_style( 'ibv-section-newsletter-cta' );

	$front_id = (int) get_option( 'page_on_front' );
	$intro    = $front_id ? (string) get_field( 'newsletter_intro', $front_id ) : '';
	$fid      = $front_id ? (int) get_field( 'newsletter_form_id', $front_id ) : 0;

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
