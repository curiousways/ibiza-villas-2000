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

	// The grid section owns its own data — aggregates active offers
	// across all villas, falls back to the empty state internally.
	ibv_core_section_special_offers_grid();

	ibv_core_section_short_breaks(
		[
			'surface' => 'white',
		]
	);

endwhile;

get_footer();
