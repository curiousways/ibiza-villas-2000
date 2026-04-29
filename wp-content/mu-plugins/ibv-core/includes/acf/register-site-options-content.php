<?php
/**
 * Site Options content fields — global chrome + homepage (Pass 3c-foundation).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Global + Homepage field groups on the Site Options page.
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
					'instructions'  => __( 'Pick the page that uses the Villa Listing template. Header and hero search forms will submit here.', 'ibv' ),
					'post_type'     => array( 'page' ),
					'taxonomy'      => array(),
					'allow_null'    => 0,
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
					'key'   => 'field_ibv_global_newsletter_intro',
					'label' => __( 'Newsletter intro', 'ibv' ),
					'name'  => 'newsletter_intro',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_global_newsletter_form_id',
					'label' => __( 'Newsletter Gravity Form ID', 'ibv' ),
					'name'  => 'newsletter_form_id',
					'type'  => 'number',
					'min'   => 0,
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
			'title'                 => __( 'Homepage', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_home_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_hero_title',
					'label' => __( 'Hero title', 'ibv' ),
					'name'  => 'hero_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_hero_subtitle',
					'label' => __( 'Hero subtitle', 'ibv' ),
					'name'  => 'hero_subtitle',
					'type'  => 'text',
				),
				array(
					'key'        => 'field_ibv_home_trust_strip',
					'label'      => __( 'Trust strip', 'ibv' ),
					'name'       => 'trust_strip',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'           => 'field_ibv_home_trust_logo',
							'label'         => __( 'Logo', 'ibv' ),
							'name'          => 'logo',
							'type'          => 'image',
							'return_format' => 'array',
						),
						array(
							'key'   => 'field_ibv_home_trust_label',
							'label' => __( 'Label', 'ibv' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_home_trust_subtext',
							'label' => __( 'Subtext', 'ibv' ),
							'name'  => 'subtext',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_home_trust_url',
							'label' => __( 'URL (optional)', 'ibv' ),
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),
				array(
					'key'           => 'field_ibv_home_featured_villas',
					'label'         => __( 'Featured villas', 'ibv' ),
					'name'          => 'featured_villas',
					'type'          => 'post_object',
					'post_type'     => array( 'villas' ),
					'multiple'      => true,
					'min'           => 0,
					'max'           => 8,
					'return_format' => 'id',
				),
				array(
					'key'           => 'field_ibv_home_weekly_offer_villa',
					'label'         => __( 'Weekly offer villa', 'ibv' ),
					'name'          => 'weekly_offer_villa',
					'type'          => 'post_object',
					'post_type'     => array( 'villas' ),
					'return_format' => 'id',
					'multiple'      => 0,
				),
				array(
					'key'   => 'field_ibv_home_weekly_offer_was_price',
					'label' => __( 'Weekly offer “was” price (€)', 'ibv' ),
					'name'  => 'weekly_offer_was_price',
					'type'  => 'number',
				),
				array(
					'key'   => 'field_ibv_home_weekly_offer_now_price',
					'label' => __( 'Weekly offer “now” price (€)', 'ibv' ),
					'name'  => 'weekly_offer_now_price',
					'type'  => 'number',
				),
				array(
					'key'   => 'field_ibv_home_weekly_offer_valid_from',
					'label' => __( 'Weekly offer valid from', 'ibv' ),
					'name'  => 'weekly_offer_valid_from',
					'type'  => 'date_picker',
					'display_format' => 'd/m/Y',
					'return_format'  => 'Ymd',
				),
				array(
					'key'   => 'field_ibv_home_weekly_offer_valid_to',
					'label' => __( 'Weekly offer valid to', 'ibv' ),
					'name'  => 'weekly_offer_valid_to',
					'type'  => 'date_picker',
					'display_format' => 'd/m/Y',
					'return_format'  => 'Ymd',
				),
				array(
					'key'           => 'field_ibv_home_short_breaks_image',
					'label'         => __( 'Short breaks image', 'ibv' ),
					'name'          => 'short_breaks_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_short_breaks_title',
					'label' => __( 'Short breaks title', 'ibv' ),
					'name'  => 'short_breaks_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_short_breaks_text',
					'label' => __( 'Short breaks text', 'ibv' ),
					'name'  => 'short_breaks_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_home_short_breaks_cta_url',
					'label' => __( 'Short breaks CTA URL', 'ibv' ),
					'name'  => 'short_breaks_cta_url',
					'type'  => 'url',
				),
				array(
					'key'        => 'field_ibv_home_why_pillars',
					'label'      => __( 'Why IV2000 pillars', 'ibv' ),
					'name'       => 'why_pillars',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'           => 'field_ibv_home_why_icon',
							'label'         => __( 'Icon', 'ibv' ),
							'name'          => 'icon',
							'type'          => 'image',
							'return_format' => 'array',
						),
						array(
							'key'   => 'field_ibv_home_why_title',
							'label' => __( 'Title', 'ibv' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_home_why_text',
							'label' => __( 'Text', 'ibv' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'key'   => 'field_ibv_home_fancy_different_intro',
					'label' => __( 'Fancy different intro', 'ibv' ),
					'name'  => 'fancy_different_intro',
					'type'  => 'text',
				),
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
					'key'           => 'field_ibv_home_ips_image',
					'label'         => __( 'IPS panel image', 'ibv' ),
					'name'          => 'ips_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_ips_title',
					'label' => __( 'IPS title', 'ibv' ),
					'name'  => 'ips_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_ips_text',
					'label' => __( 'IPS text', 'ibv' ),
					'name'  => 'ips_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_home_ips_cta_label',
					'label' => __( 'IPS CTA label', 'ibv' ),
					'name'  => 'ips_cta_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_ips_cta_url',
					'label' => __( 'IPS CTA URL', 'ibv' ),
					'name'  => 'ips_cta_url',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_ibv_home_three_step_intro_eyebrow',
					'label' => __( '3-step eyebrow', 'ibv' ),
					'name'  => 'three_step_intro_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_three_step_intro_title',
					'label' => __( '3-step title', 'ibv' ),
					'name'  => 'three_step_intro_title',
					'type'  => 'text',
				),
				array(
					'key'        => 'field_ibv_home_three_step_steps',
					'label'      => __( '3-step cards', 'ibv' ),
					'name'       => 'three_step_steps',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'   => 'field_ibv_home_three_step_title',
							'label' => __( 'Title', 'ibv' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_home_three_step_text',
							'label' => __( 'Text', 'ibv' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'key'   => 'field_ibv_home_guide_intro',
					'label' => __( 'Ibiza guide intro', 'ibv' ),
					'name'  => 'guide_intro',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_ibv_home_guide_articles',
					'label'         => __( 'Guide articles', 'ibv' ),
					'name'          => 'guide_articles',
					'type'          => 'post_object',
					'post_type'     => array( 'post' ),
					'multiple'      => true,
					'min'           => 0,
					'max'           => 6,
					'return_format' => 'id',
				),
				array(
					'key'   => 'field_ibv_home_guide_view_all_url',
					'label' => __( 'Guide view-all link', 'ibv' ),
					'name'  => 'guide_view_all_url',
					'type'  => 'page_link',
				),
				array(
					'key'           => 'field_ibv_home_meet_team_image',
					'label'         => __( 'Meet the team image', 'ibv' ),
					'name'          => 'meet_team_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_home_meet_team_title',
					'label' => __( 'Meet the team title', 'ibv' ),
					'name'  => 'meet_team_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_meet_team_text',
					'label' => __( 'Meet the team text', 'ibv' ),
					'name'  => 'meet_team_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_home_meet_team_cta_label',
					'label' => __( 'Meet the team CTA label', 'ibv' ),
					'name'  => 'meet_team_cta_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_home_meet_team_cta_url',
					'label' => __( 'Meet the team CTA page', 'ibv' ),
					'name'  => 'meet_team_cta_url',
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
