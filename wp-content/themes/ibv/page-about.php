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
	ibv_core_section_about_story();
	ibv_core_section_three_step();
	ibv_core_section_testimonials();

	// TODO: FAQ section — brief 04.
	// TODO: Team section — brief 05.

	ibv_core_section_newsletter_cta();

endwhile;

get_footer();
