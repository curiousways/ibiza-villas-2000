<?php
/**
 * Template Name: About
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_hero( [ 'compact' => true ] );
	ibv_core_section_about_stats();
	ibv_core_image_entries_section(
		[
			'title'   => get_field( 'about_story_title' ),
			'entries' => get_field( 'about_story_entries' ),
			'image'   => get_field( 'about_story_image' ),
		]
	);
	ibv_core_section_three_step();
	ibv_core_section_testimonials();

	ibv_core_section_about_faq();
	ibv_core_section_about_team();

endwhile;

get_footer();
