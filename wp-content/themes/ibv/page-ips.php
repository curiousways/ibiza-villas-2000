<?php
/**
 * Template Name: Ibiza Preservation Society
 *
 * Composition-only page: hero + "What we do" (image-entries-section) +
 * "Want to find out more?" (image-text-section). No bespoke sections.
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

	ibv_core_image_entries_section(
		[
			'title'      => get_field( 'ips_wwd_title' ),
			'entries'    => get_field( 'ips_wwd_entries' ),
			'image'      => get_field( 'ips_wwd_image' ),
			'image_side' => 'right',
			'surface'    => 'bg',
		]
	);

	ibv_core_image_text_section(
		[
			'title'       => get_field( 'ips_fom_title' ),
			'description' => get_field( 'ips_fom_description' ),
			'cta_label'   => get_field( 'ips_fom_cta_label' ),
			'cta_url'     => get_field( 'ips_fom_cta_url' ),
			'image'       => get_field( 'ips_fom_image' ),
			'image_side'  => 'left',
			'surface'     => 'white',
		]
	);

endwhile;

get_footer();
