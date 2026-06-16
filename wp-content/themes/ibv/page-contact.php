<?php
/**
 * Template Name: Contact
 *
 * Matches Figma frame 07 | Contact (node 282:4057). The contact-enquiry
 * section is the same shared component the concierge page uses
 * (`ibv_core_section_contact_enquiry`) — per-page composition passed as
 * args. The image-and-text band is stubbed for the follow-up brief.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_contact_enquiry(
		[
			'heading'       => (string) get_field( 'page_heading' ),
			'subheading'    => (string) get_field( 'page_subheading' ),
			'heading_level' => 'h1',
			'heading_size'  => 'display',
			'banner_image'  => get_field( 'form_banner_image' ),
			'form_title'    => (string) get_field( 'form_title' ),
			'form_id'       => (int) get_field( 'contact_enquiry_gravity_form_id' ),
		]
	);
	?>

	<?php
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
