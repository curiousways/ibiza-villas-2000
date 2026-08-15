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
 * Listing-page block using the same Site Options as the homepage apartments
 * teaser. One property: Aparthotel Marian.
 */
function ibv_core_alternative_accommodation() {
	$img = get_field( 'fancy_apartments_image', 'option' );
	$txt = get_field( 'fancy_apartments_text', 'option' );

	if ( empty( $img['ID'] ) && ! $txt ) {
		return;
	}

	wp_enqueue_style( 'ibv-alternative-accommodation' );
	?>
	<section class="ibv-alternative-accommodation ibv-section">
		<div class="ibv-container">
			<h2 class="ibv-alternative-accommodation__title ibv-font-display">
				<?php esc_html_e( 'Travelling as a couple or small group?', 'ibv' ); ?>
			</h2>
			<p class="ibv-alternative-accommodation__subtitle">
				<?php esc_html_e( 'A whole villa isn’t always the answer. We also have self-catering apartments in San Antonio.', 'ibv' ); ?>
			</p>
			<?php ibv_core_accommodation_tile_from_options(); ?>
		</div>
	</section>
	<?php
}
