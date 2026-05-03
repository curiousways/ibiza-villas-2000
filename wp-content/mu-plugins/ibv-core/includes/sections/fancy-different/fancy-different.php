<?php
/**
 * Section: Fancy something different (Airstream + Hotel).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Two-column teaser via shared accommodation tiles.
 */
function ibv_core_section_fancy_different() {
	$intro = get_field( 'fancy_different_intro' );
	if ( ! $intro ) {
		$intro = __( 'Fancy something a bit different?', 'ibv' );
	}

	$ai_img = get_field( 'fancy_airstream_image', 'option' );
	$ai_txt = get_field( 'fancy_airstream_text', 'option' );
	$ho_img = get_field( 'fancy_hotel_image', 'option' );
	$ho_txt = get_field( 'fancy_hotel_text', 'option' );

	if ( ! $ai_txt && ! $ho_txt && empty( $ai_img['ID'] ) && empty( $ho_img['ID'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-fancy-different' );
	?>
	<section class="ibv-section-fancy-different ibv-section ibv-section--alt">
		<div class="ibv-container">
			<header class="ibv-section-fancy-different__header">
				<h2 class="ibv-section-fancy-different__title ibv-font-display">
					<?php echo esc_html( $intro ); ?>
				</h2>
				<hr class="ibv-section-fancy-different__divider" aria-hidden="true">
			</header>
			<?php ibv_core_accommodation_tile_pair(); ?>
		</div>
	</section>
	<?php
}
