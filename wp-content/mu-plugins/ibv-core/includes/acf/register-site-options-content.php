<?php
/**
 * Site Options content fields — global chrome + shared cross-page content.
 *
 * Homepage-only fields live on the Front Page (register-page-home.php).
 * Featured offer and short breaks live in register-globals-content.php.
 * Newsletter footer copy lives here; Gravity Form ID lives in register-globals-content.php.
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

	$loc_global = array(
		array(
			array(
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => 'ibv-options-global',
			),
		),
	);
	$loc_shared = array(
		array(
			array(
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => 'ibv-options-shared-content',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_global',
			'title'                 => __( 'Global (header / footer)', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_global_tab_contact',
					'label'     => __( 'Contact', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
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
					'key'          => 'field_ibv_global_response_time_note',
					'label'        => __( 'Response-time note', 'ibv' ),
					'name'         => 'global_response_time_note',
					'type'         => 'text',
					'instructions' => __( 'The reassurance line shown beside enquiry forms (villa enquiry panel, apartments enquiry). One sentence, no tick — the templates add it. The exact wording is still an open client decision.', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_global_contact_page',
					'label' => __( 'Contact page', 'ibv' ),
					'name'  => 'contact_page',
					'type'  => 'page_link',
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
					'key'       => 'field_ibv_global_tab_footer',
					'label'     => __( 'Footer', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
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
					'key'       => 'field_ibv_global_tab_integrations',
					'label'     => __( 'Integrations', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_global_google_maps_api_key',
					'label'         => __( 'Google Maps API key', 'ibv' ),
					'name'          => 'google_maps_api_key',
					'type'          => 'text',
					'instructions'  => __( 'Use HTTP referrer restrictions in Google Cloud Console.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_global_google_maps_map_id',
					'label'         => __( 'Google Maps Map ID', 'ibv' ),
					'name'          => 'google_maps_map_id',
					'type'          => 'text',
					'instructions'  => __( 'Required for the AdvancedMarkerElement marker. Create one in Google Cloud Console → Map Management. Configure map styles against the same Map ID via Cloud Map Styles.', 'ibv' ),
				),
			),
			'location'              => $loc_global,
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
					'key'       => 'field_ibv_home_tab_cross_sell',
					'label'     => __( 'Cross-sell', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				// fancy_airstream_* retired. fancy_apartments_* is the
				// homepage teaser; fancy_hotel_* is the listing cross-sell.
				// Both describe Aparthotel Marian — keys kept so stored
				// option values are not orphaned.
				array(
					'key'           => 'field_ibv_home_fancy_hotel_image',
					'label'         => __( 'Apartments image (listing cross-sell)', 'ibv' ),
					'name'          => 'fancy_hotel_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_fancy_hotel_text',
					'label' => __( 'Apartments text (listing cross-sell)', 'ibv' ),
					'name'  => 'fancy_hotel_text',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_ibv_home_fancy_hotel_url',
					'label' => __( 'Apartments page (listing cross-sell)', 'ibv' ),
					'name'  => 'fancy_hotel_url',
					'type'  => 'page_link',
				),
				array(
					'key'           => 'field_ibv_home_fancy_apartments_image',
					'label'         => __( 'Apartments image (homepage teaser)', 'ibv' ),
					'name'          => 'fancy_apartments_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Homepage / Page Builder apartments teaser ("Travelling as a couple…").', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_home_fancy_apartments_url',
					'label' => __( 'Apartments page (homepage teaser)', 'ibv' ),
					'name'  => 'fancy_apartments_url',
					'type'  => 'page_link',
				),
				array(
					'key'       => 'field_ibv_home_tab_three_step',
					'label'     => __( '3-step process', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_globals_three_step_intro_eyebrow',
					'label'         => __( '3-step eyebrow', 'ibv' ),
					'name'          => 'three_step_intro_eyebrow',
					'type'          => 'text',
					'default_value' => __( 'Simple & Swift', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_globals_three_step_intro_title',
					'label'         => __( '3-step title', 'ibv' ),
					'name'          => 'three_step_intro_title',
					'type'          => 'text',
					'default_value' => __( 'Our 3-Step Process', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_globals_three_step_steps',
					'label'        => __( '3-step cards', 'ibv' ),
					'name'         => 'three_step_steps',
					'type'         => 'repeater',
					'layout'       => 'block',
					'max'          => 3,
					'button_label' => __( 'Add step', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_ibv_globals_three_step_card_title',
							'label' => __( 'Title', 'ibv' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_globals_three_step_card_text',
							'label' => __( 'Text', 'ibv' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'key'       => 'field_ibv_home_tab_testimonials',
					'label'     => __( 'Testimonials', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
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
			'location'              => $loc_shared,
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
