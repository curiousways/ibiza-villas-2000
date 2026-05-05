<?php
/**
 * Template Name: Ibiza Guide
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$featured_id = (int) get_field( 'ig_featured_article' );

	ibv_core_section_hero( [ 'compact' => true ] );

	if ( $featured_id ) {
		ibv_core_section_featured_article( [ 'post_id' => $featured_id ] );
	}

	/*
	 * TODO brief 03 (FacetWP): replace direct article-grid call with
	 * a FacetWP-wrapped listing for filter + pagination.
	 */
	ibv_core_section_article_grid(
		[
			'exclude' => $featured_id ? [ $featured_id ] : [],
		]
	);

	ibv_core_section_newsletter_cta();

endwhile;

get_footer();
