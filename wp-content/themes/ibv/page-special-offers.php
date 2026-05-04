<?php
/**
 * Template Name: Special Offers
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	// Hero — reads page-level hero_* ACF; no after_copy → no search.
	ibv_core_section_hero();

	// Featured offer — globals; suppress header CTA on this page.
	ibv_core_section_featured_offer(
		[
			'section_title'    => __( "This Week's Special Offer", 'ibv' ),
			'show_section_cta' => false,
		]
	);

	$offers = get_field( 'offer_table' );
	if ( ! empty( $offers ) && is_array( $offers ) ) {
		ibv_core_section_special_offers_grid( $offers );
	} else {
		ibv_core_section_special_offers_empty_state();
	}

	ibv_core_section_short_breaks(
		[
			'surface' => 'white',
		]
	);

endwhile;

get_footer();
