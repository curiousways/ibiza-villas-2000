<?php
/**
 * Template Name: Villa Listing
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_villa_listing_hero();
	ibv_core_section_villa_listing_grid();
	ibv_core_section_listing_empty_state();
	ibv_core_alternative_accommodation();

endwhile;

get_footer();
