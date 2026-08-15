<?php
/**
 * Section: Fancy something different (apartments teaser).
 *
 * Thin wrapper around `ibv_core_image_text_section()` — 50/50 split, image
 * right, standard bg surface. Image and destination page come from dedicated
 * Site Options fields (fancy_apartments_*); the description is fixed copy.
 * The same fields feed the listing cross-sell. Airstreams are retired.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Apartments teaser.
 */
function ibv_core_section_fancy_different() {
	$intro = get_field( 'fancy_different_intro' );
	if ( ! $intro ) {
		$intro = __( 'Travelling as a couple or a small group?', 'ibv' );
	}

	$img = get_field( 'fancy_apartments_image', 'option' );
	$url = get_field( 'fancy_apartments_url', 'option' );

	ibv_core_image_text_section(
		[
			'title'       => (string) $intro,
			'description' => __( 'A whole villa isn\'t always the answer. Our apartments in San Antonio put you a short walk from the beach and the bars, at a fraction of the price.', 'ibv' ),
			'cta_url'     => $url ? esc_url( $url ) : '',
			'cta_label'   => __( 'View the apartments', 'ibv' ),
			'image'       => $img,
			'image_side'  => 'right',
			'surface'     => 'bg',
		]
	);
}
