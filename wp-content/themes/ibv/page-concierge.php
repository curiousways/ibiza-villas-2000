<?php
/**
 * Template Name: Concierge
 *
 * Matches Figma frame 06 | Concierge (node 1:6981). Hero + services grid
 * are content sections; the contact and image-text bands are placeholder
 * stubs filled in by later briefs.
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
	ibv_core_section_concierge_services();
	?>

	<?php
	ibv_core_section_concierge_contact();

	ibv_core_image_text_section(
		[
			'title'       => (string) get_field( 'image_text_title' ),
			'description' => (string) get_field( 'image_text_body' ),
			'cta_label'   => (string) get_field( 'image_text_cta_label' ),
			'cta_url'     => (string) get_field( 'image_text_cta_url' ),
			'image'       => get_field( 'image_text_image' ),
			'image_side'  => 'right',
			'surface'     => 'white',
		]
	);
	?>

	<?php
endwhile;

get_footer();
