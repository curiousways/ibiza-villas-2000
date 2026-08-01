<?php
/**
 * Template Name: Page Builder
 *
 * Editor-composed page: fixed compact hero, then sections assembled from the
 * `builder_sections` flexible content field. Composition only — every layout
 * dispatches to an existing ibv-core section with no changes to its signature.
 *
 * Adding a layout: register it in
 * `mu-plugins/ibv-core/includes/acf/register-page-builder.php`, then add the
 * matching `case` below. The layout `name` there is the string returned by
 * `get_row_layout()` here.
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

	if ( have_rows( 'builder_sections' ) ) :
		while ( have_rows( 'builder_sections' ) ) :
			the_row();

			switch ( get_row_layout() ) {

				case 'image_entries':
					ibv_core_image_entries_section(
						[
							'title'      => get_sub_field( 'title' ),
							'entries'    => get_sub_field( 'entries' ),
							'image'      => get_sub_field( 'image' ),
							'image_side' => get_sub_field( 'image_side' ),
							'surface'    => get_sub_field( 'surface' ),
						]
					);
					break;

				case 'image_text':
					$ibv_builder_surface = get_sub_field( 'surface' );
					ibv_core_image_text_section(
						[
							'title'       => get_sub_field( 'title' ),
							'description' => get_sub_field( 'description' ),
							'cta_label'   => get_sub_field( 'cta_label' ),
							'cta_url'     => get_sub_field( 'cta_page' ),
							'image'       => get_sub_field( 'image' ),
							'image_side'  => get_sub_field( 'image_side' ),
							// `none` is the editor-facing spelling of this
							// section's own default (no surface at all).
							'surface'     => 'none' === $ibv_builder_surface ? '' : $ibv_builder_surface,
						]
					);
					break;

				case 'title_band':
					ibv_core_title_band(
						[
							'title'   => get_sub_field( 'title' ),
							'meta'    => get_sub_field( 'meta' ),
							'surface' => get_sub_field( 'surface' ),
						]
					);
					break;

				case 'prose':
					ibv_core_prose_section(
						[
							'content' => get_sub_field( 'content' ),
							'surface' => get_sub_field( 'surface' ),
						]
					);
					break;

				case 'three_step':
					ibv_core_section_three_step();
					break;

				case 'trust_strip':
					ibv_core_section_trust_strip();
					break;

				case 'testimonials':
					ibv_core_section_testimonials();
					break;

				case 'newsletter_cta':
					ibv_core_section_newsletter_cta();
					break;

				case 'short_breaks':
					ibv_core_section_short_breaks( [ 'surface' => get_sub_field( 'surface' ) ] );
					break;

				case 'meet_team_teaser':
					ibv_core_section_meet_team_teaser();
					break;

				case 'concierge_cross_sell':
					ibv_core_section_concierge_cross_sell( get_sub_field( 'heading_override' ) );
					break;

				case 'ibiza_guide_preview':
					ibv_core_section_ibiza_guide_preview();
					break;

				case 'why_iv2000':
					ibv_core_section_why_iv2000();
					break;

				case 'fancy_different':
					ibv_core_section_fancy_different();
					break;

				case 'featured_villas':
					ibv_core_section_featured_villas();
					break;
			}

		endwhile;
	endif;

endwhile;

get_footer();
