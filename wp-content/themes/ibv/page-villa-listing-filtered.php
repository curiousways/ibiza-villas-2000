<?php
/**
 * Template Name: Villa Listing Filtered
 *
 * Area and large-group pages: title, description, optional intro and
 * image, then a grid filtered by Location and/or Minimum sleeps.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_villa_listing_filtered_hero();
	ibv_core_section_villa_listing_grid();
	ibv_core_section_listing_empty_state();

endwhile;

get_footer();
