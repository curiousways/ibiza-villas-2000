<?php
/**
 * Section: Concierge cross-sell (Site Options).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string|null $heading_override Optional heading; falls back to Site Option.
 */
function ibv_core_section_concierge_cross_sell( $heading_override = null ) {
	$image = get_field( 'concierge_image', 'option' );
	$h     = $heading_override ? (string) $heading_override : (string) get_field( 'concierge_heading', 'option' );
	$body  = get_field( 'concierge_body', 'option' );
	$cta   = get_field( 'concierge_cta_label', 'option' );
	$url   = get_field( 'concierge_cta_url', 'option' );

	if ( ! $h && ! $body && empty( $image['ID'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-concierge-cross-sell' );
	wp_enqueue_style( 'ibv-button' );

	if ( ! $cta ) {
		$cta = __( 'View concierge services', 'ibv' );
	}
	?>
	<section class="ibv-concierge-cross-sell">
		<div class="ibv-concierge-cross-sell__grid ibv-grid ibv-grid--2">
			<?php if ( ! empty( $image['ID'] ) ) : ?>
				<div class="ibv-concierge-cross-sell__media">
					<?php ibv_core_image( $image, 'ibv-card', [ 'class' => 'ibv-concierge-cross-sell__image' ] ); ?>
				</div>
			<?php endif; ?>
			<div class="ibv-concierge-cross-sell__content">
				<?php if ( $h ) : ?>
					<h2 class="ibv-concierge-cross-sell__title"><?php echo esc_html( $h ); ?></h2>
				<?php endif; ?>
				<?php if ( $body ) : ?>
					<div class="ibv-concierge-cross-sell__body ibv-prose">
						<?php echo wp_kses_post( wpautop( $body ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $url ) : ?>
					<?php
					ibv_core_button(
						[
							'url'     => esc_url( $url ),
							'label'   => $cta,
							'variant' => 'primary',
						]
					);
					?>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
