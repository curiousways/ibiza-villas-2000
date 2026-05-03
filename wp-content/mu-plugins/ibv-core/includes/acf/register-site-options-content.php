<?php
/**
 * Site Options content fields — global chrome + shared cross-page content.
 *
 * Homepage-only fields live on the Front Page (register-page-home.php).
 * Newsletter footer fields live here (global footer).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Global + shared content field groups on the Site Options page.
 */
function ibv_register_site_options_content_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_option = array(
		array(
			array(
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => 'ibv-site-options',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_global',
			'title'                 => __( 'Global (header / footer)', 'ibv' ),
			'fields'                => array(
				array(
					'key'   => 'field_ibv_global_phone_uk',
					'label' => __( 'Phone (UK)', 'ibv' ),
					'name'  => 'phone_uk',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_global_phone_ibiza',
					'label' => __( 'Phone (Ibiza)', 'ibv' ),
					'name'  => 'phone_ibiza',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_global_whatsapp_number',
					'label' => __( 'WhatsApp number', 'ibv' ),
					'name'  => 'whatsapp_number',
					'type'  => 'text',
					'instructions' => __( 'International format, no + prefix.', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_global_contact_email',
					'label' => __( 'Contact email', 'ibv' ),
					'name'  => 'contact_email',
					'type'  => 'email',
				),
				array(
					'key'   => 'field_ibv_global_my_bookings_url',
					'label' => __( 'My Bookings URL', 'ibv' ),
					'name'  => 'my_bookings_url',
					'type'  => 'url',
				),
				array(
					'key'           => 'field_ibv_global_search_villas_page',
					'label'         => __( 'Search / villas listing page', 'ibv' ),
					'name'          => 'search_villas_page',
					'type'          => 'post_object',
					'instructions'  => __( 'Optional override. Leave empty to use whichever published page has the Villa Listing template; URLs fall back to /villas/ if none is found.', 'ibv' ),
					'post_type'     => array( 'page' ),
					'taxonomy'      => array(),
					'allow_null'    => 1,
					'multiple'      => 0,
					'return_format' => 'object',
					'ui'            => 1,
					'required'      => 0,
				),
				array(
					'key'        => 'field_ibv_global_socials',
					'label'      => __( 'Social profiles', 'ibv' ),
					'name'       => 'socials',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'     => 'field_ibv_global_social_network',
							'label'   => __( 'Network', 'ibv' ),
							'name'    => 'network',
							'type'    => 'select',
							'choices' => array(
								'facebook'  => 'Facebook',
								'x'         => 'X',
								'instagram' => 'Instagram',
								'youtube'   => 'YouTube',
								'linkedin'  => 'LinkedIn',
							),
						),
						array(
							'key'   => 'field_ibv_global_social_url',
							'label' => __( 'URL', 'ibv' ),
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),
				array(
					'key'           => 'field_ibv_global_accreditations',
					'label'         => __( 'Accreditations', 'ibv' ),
					'name'          => 'accreditations',
					'type'          => 'repeater',
					'layout'        => 'block',
					'instructions'  => __( 'Logos appear in the footer below the newsletter form. Upload pre-prepared white versions of each logo (the footer background is dark). Most accreditation bodies provide brand-compliant white variations on request.', 'ibv' ),
					'sub_fields'    => array(
						array(
							'key'           => 'field_ibv_global_accreditation_image',
							'label'         => __( 'Logo', 'ibv' ),
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'instructions'  => __( 'White on transparent. SVG preferred for crisp scaling; PNG fine.', 'ibv' ),
						),
						array(
							'key'          => 'field_ibv_global_accreditation_url',
							'label'        => __( 'URL (optional)', 'ibv' ),
							'name'         => 'url',
							'type'         => 'url',
							'instructions' => __( 'Optional link to the accrediting body\'s website.', 'ibv' ),
						),
					),
				),
				array(
					'key'   => 'field_ibv_global_footer_tagline',
					'label' => __( 'Footer tagline', 'ibv' ),
					'name'  => 'footer_tagline',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_ibv_global_footer_legal_html',
					'label' => __( 'Footer legal (WYSIWYG)', 'ibv' ),
					'name'  => 'footer_legal_html',
					'type'  => 'wysiwyg',
					'tabs'  => 'all',
					'toolbar' => 'basic',
					'media_upload' => 0,
				),
				array(
					'key'           => 'field_ibv_global_google_maps_api_key',
					'label'         => __( 'Google Maps API key', 'ibv' ),
					'name'          => 'google_maps_api_key',
					'type'          => 'text',
					'instructions'  => __( 'Use HTTP referrer restrictions in Google Cloud Console.', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_global_contact_page',
					'label' => __( 'Contact page', 'ibv' ),
					'name'  => 'contact_page',
					'type'  => 'page_link',
				),
				array(
					'key'            => 'field_ibv_global_newsletter_intro',
					'label'          => __( 'Newsletter intro', 'ibv' ),
					'name'           => 'newsletter_intro',
					'type'           => 'text',
					'instructions'   => __( 'Title above the newsletter form. Renders in the global footer.', 'ibv' ),
				),
				array(
					'key'            => 'field_ibv_global_newsletter_body',
					'label'          => __( 'Newsletter body', 'ibv' ),
					'name'           => 'newsletter_body',
					'type'           => 'textarea',
					'rows'           => 2,
					'instructions'   => __( 'Subtitle line beneath the newsletter title. Defaults to "Sign up to receive marketing from Ibiza Villas 2000" if empty.', 'ibv' ),
				),
				array(
					'key'            => 'field_ibv_global_newsletter_form_id',
					'label'          => __( 'Newsletter Gravity Form ID', 'ibv' ),
					'name'           => 'newsletter_form_id',
					'type'           => 'number',
					'min'            => 0,
					'instructions'   => __( 'ID of the Gravity Form to embed. Find it under Forms in the WP admin.', 'ibv' ),
				),
			),
			'location'              => $loc_option,
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_home',
			'title'                 => __( 'Shared content (testimonials, cross-sell)', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_home_fancy_airstream_image',
					'label'         => __( 'Airstreams image', 'ibv' ),
					'name'          => 'fancy_airstream_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_fancy_airstream_text',
					'label' => __( 'Airstreams text', 'ibv' ),
					'name'  => 'fancy_airstream_text',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_ibv_home_fancy_airstream_url',
					'label' => __( 'Airstreams page', 'ibv' ),
					'name'  => 'fancy_airstream_url',
					'type'  => 'page_link',
				),
				array(
					'key'           => 'field_ibv_home_fancy_hotel_image',
					'label'         => __( 'Hotel image', 'ibv' ),
					'name'          => 'fancy_hotel_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_fancy_hotel_text',
					'label' => __( 'Hotel text', 'ibv' ),
					'name'  => 'fancy_hotel_text',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_ibv_home_fancy_hotel_url',
					'label' => __( 'Hotel page', 'ibv' ),
					'name'  => 'fancy_hotel_url',
					'type'  => 'page_link',
				),
				array(
					'key'        => 'field_ibv_home_testimonials',
					'label'      => __( 'Testimonials', 'ibv' ),
					'name'       => 'testimonials',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'   => 'field_ibv_home_testimonial_quote',
							'label' => __( 'Quote', 'ibv' ),
							'name'  => 'quote',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'field_ibv_home_testimonial_attribution',
							'label' => __( 'Attribution', 'ibv' ),
							'name'  => 'attribution',
							'type'  => 'text',
						),
					),
				),
			),
			'location'              => $loc_option,
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_site_options_content_fields', 15 );
