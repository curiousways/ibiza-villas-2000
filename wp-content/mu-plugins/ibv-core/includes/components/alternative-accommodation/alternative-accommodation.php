<?php
/**
 * Component: Alternative accommodation (listing page cross-sell).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listing-page block using same Site Options as fancy-different.
 */
function ibv_core_alternative_accommodation() {
	$ai_img = get_field( 'fancy_airstream_image', 'option' );
	$ai_txt = get_field( 'fancy_airstream_text', 'option' );
	$ho_img = get_field( 'fancy_hotel_image', 'option' );
	$ho_txt = get_field( 'fancy_hotel_text', 'option' );

	if ( ! $ai_txt && ! $ho_txt && empty( $ai_img['ID'] ) && empty( $ho_img['ID'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-alternative-accommodation' );
	?>
	<section class="ibv-alternative-accommodation ibv-section">
		<div class="ibv-container">
			<h2 class="ibv-alternative-accommodation__title ibv-font-display">
				<?php esc_html_e( 'Traveling as couple or small group?', 'ibv' ); ?>
			</h2>
			<p class="ibv-alternative-accommodation__subtitle">
				<?php esc_html_e( 'We also have licensed apartments and beachside Airstreams.', 'ibv' ); ?>
			</p>
			<?php ibv_core_accommodation_tile_pair(); ?>
		</div>
	</section>
	<?php
}
