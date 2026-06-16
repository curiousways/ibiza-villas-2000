<?php
/**
 * Concierge page — page template ACF field group.
 *
 * All Concierge page content lives on the page itself: hero, the services
 * card grid (repeater), the contact band copy, and the enquiry Gravity Form
 * ID. The cross-sell image-text band reused on villa detail / booking
 * confirmation stays global in Site Options (register-concierge.php) — only
 * the Concierge-page-specific content lives here.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Concierge page template.
 */
function ibv_register_page_concierge_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_concierge = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-concierge.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_concierge',
			'title'                 => __( 'Concierge page', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_page_concierge_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_concierge_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Hero background image. Used at compact height.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_concierge_hero_title',
					'label'         => __( 'Hero title', 'ibv' ),
					'name'          => 'hero_title',
					'type'          => 'text',
					'default_value' => __( 'Concierge Services', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_concierge_hero_subtitle',
					'label'         => __( 'Hero subtitle', 'ibv' ),
					'name'          => 'hero_subtitle',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => __( "From airport transfers to pre-arrival shopping — we'll take care of it.", 'ibv' ),
				),
				array(
					'key'       => 'field_ibv_page_concierge_tab_services',
					'label'     => __( 'Services', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'          => 'field_ibv_concierge_services',
					'label'        => __( 'Services', 'ibv' ),
					'name'         => 'concierge_services',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add service', 'ibv' ),
					'instructions' => __( 'The list of concierge services rendered as cards on this page.', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'          => 'field_ibv_concierge_service_icon',
							'label'        => __( 'Icon', 'ibv' ),
							'name'         => 'icon',
							'type'         => 'text',
							'required'     => 1,
							'instructions' => __( 'Lucide glyph name (e.g. <code>shopping-bag</code>, <code>plane</code>, <code>wine</code>). Browse all icons at <a href="https://lucide.dev/icons/" target="_blank" rel="noopener noreferrer">lucide.dev/icons</a>. New picks may need to be vendored before they render.', 'ibv' ),
						),
						array(
							'key'      => 'field_ibv_concierge_service_title',
							'label'    => __( 'Title', 'ibv' ),
							'name'     => 'title',
							'type'     => 'text',
							'required' => 1,
						),
						array(
							'key'          => 'field_ibv_concierge_service_description',
							'label'        => __( 'Description', 'ibv' ),
							'name'         => 'description',
							'type'         => 'textarea',
							'rows'         => 3,
							'required'     => 1,
							'instructions' => __( 'Plain text. Short — ~2 sentences max.', 'ibv' ),
						),
					),
				),
				array(
					'key'       => 'field_ibv_page_concierge_tab_contact',
					'label'     => __( 'Contact', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_concierge_contact_title',
					'label'         => __( 'Contact heading', 'ibv' ),
					'name'          => 'contact_title',
					'type'          => 'text',
					'default_value' => __( 'Enquire Concierge Services', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_concierge_contact_subtitle',
					'label'         => __( 'Contact subtitle', 'ibv' ),
					'name'          => 'contact_subtitle',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => __( "From airport transfers to pre-arrival shopping, we'll take care of it.", 'ibv' ),
					'instructions'  => __( 'Phone, email, and WhatsApp shown beneath this come from Site Options.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_concierge_enquiry_gravity_form_id',
					'label'        => __( 'Concierge enquiry Gravity Form ID', 'ibv' ),
					'name'         => 'concierge_enquiry_gravity_form_id',
					'type'         => 'number',
					'min'          => 0,
					'instructions' => __(
						'Gravity Form ID embedded in the contact section. Leave empty to hide the form.<br><br>'
						. '<strong>Editorial setup in the GF admin (one-time):</strong><ol>'
						. '<li><strong>Honeypot</strong> — Form Settings → "Enable anti-spam honeypot" → on.</li>'
						. '<li><strong>Notifications</strong> — set the recipient to the Site Options "Contact email"; set <em>Reply-To</em> to the enquirer\'s email so the office can reply directly.</li>'
						. '<li><strong>Date fields</strong> — set both Arrival and Departure "Date Format" to <code>yyyy-mm-dd</code> so the HTML5 date upgrade picks up pre-filled values. (The first two date fields in form order are treated as Arrival / Departure.)</li>'
						. '<li><strong>Service dropdown</strong> — add the CSS class <code>ibv-gf-concierge-service</code> in the field\'s Appearance settings. Its options will be populated automatically from the Services tab on this page.</li>'
						. '</ol>',
						'ibv'
					),
				),
				array(
					'key'       => 'field_ibv_page_concierge_tab_image_text',
					'label'     => __( 'CTA', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_concierge_image_text_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'image_text_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Right-hand image for the closing image-and-text band (Figma 1:7042).', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_concierge_image_text_title',
					'label'         => __( 'Title', 'ibv' ),
					'name'          => 'image_text_title',
					'type'          => 'text',
					'default_value' => __( 'You can always add this later', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_concierge_image_text_body',
					'label'         => __( 'Body', 'ibv' ),
					'name'          => 'image_text_body',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => __( 'Add concierge services when you enquire, or contact us any time before your stay.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_concierge_image_text_cta_label',
					'label'         => __( 'CTA label', 'ibv' ),
					'name'          => 'image_text_cta_label',
					'type'          => 'text',
					'default_value' => __( 'Enquire about a villa', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_concierge_image_text_cta_url',
					'label'        => __( 'CTA page', 'ibv' ),
					'name'         => 'image_text_cta_url',
					'type'         => 'page_link',
					'instructions' => __( 'Pick the page the button should link to (e.g. villa listing).', 'ibv' ),
				),
			),
			'location'              => $loc_concierge,
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_page_concierge_fields', 15 );

/**
 * Resolve the Concierge page ID by its assigned page template.
 *
 * @return int Page ID, or 0 if no page uses the Concierge template yet.
 */
function ibv_get_concierge_page_id() {
	$pages = get_pages(
		array(
			'meta_key'    => '_wp_page_template',
			'meta_value'  => 'page-concierge.php',
			'number'      => 1,
			'post_status' => array( 'publish', 'private', 'draft', 'pending' ),
		)
	);

	return $pages ? (int) $pages[0]->ID : 0;
}

/**
 * One-time seed / migration of Concierge page content.
 *
 * The services repeater and enquiry form ID used to live on Site Options.
 * On first run this:
 *   - migrates any existing Site Options values onto the Concierge page, then
 *   - seeds the nine Figma-spec services if the page is still empty, and
 *   - clears the now-orphaned Site Options copies.
 *
 * Guarded by a one-time flag so the page lookup only runs until it succeeds.
 * Never overrides content already present on the page.
 */
function ibv_seed_concierge_page() {
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( get_option( 'ibv_concierge_page_seeded' ) ) {
		return;
	}

	$page_id = ibv_get_concierge_page_id();
	if ( ! $page_id ) {
		return; // Page not created yet — retry on a later load.
	}

	// Services: keep page content if present, else migrate from options, else seed defaults.
	$page_services = get_field( 'concierge_services', $page_id );
	if ( empty( $page_services ) ) {
		$option_services = get_field( 'concierge_services', 'option' );
		$services        = ! empty( $option_services ) ? $option_services : ibv_default_concierge_services();
		update_field( 'field_ibv_concierge_services', $services, $page_id );
	}

	// Enquiry form ID: migrate from options only if the page has none yet.
	$page_form_id = get_field( 'concierge_enquiry_gravity_form_id', $page_id );
	if ( empty( $page_form_id ) ) {
		$option_form_id = get_field( 'concierge_enquiry_gravity_form_id', 'option' );
		if ( ! empty( $option_form_id ) ) {
			update_field( 'field_ibv_concierge_enquiry_gravity_form_id', $option_form_id, $page_id );
		}
	}

	// Clear the orphaned Site Options copies — the page is now the source.
	if ( function_exists( 'delete_field' ) ) {
		delete_field( 'concierge_services', 'option' );
		delete_field( 'concierge_enquiry_gravity_form_id', 'option' );
	}

	update_option( 'ibv_concierge_page_seeded', 1 );
}

add_action( 'acf/init', 'ibv_seed_concierge_page', 30 );

/**
 * The nine Figma-spec concierge services used to seed a fresh page.
 *
 * @return array[] Rows keyed by sub-field name (icon, title, description).
 */
function ibv_default_concierge_services() {
	return array(
		array(
			'icon'        => 'shopping-bag',
			'title'       => __( 'Pre-Arrival Shopping', 'ibv' ),
			'description' => __( "Arrive to a stocked villa. We'll have everything ready before you land.", 'ibv' ),
		),
		array(
			'icon'        => 'plane',
			'title'       => __( 'Airport Transfers & Villa Taxis', 'ibv' ),
			'description' => __( 'Reliable transfers from Ibiza airport direct to your villa.', 'ibv' ),
		),
		array(
			'icon'        => 'wine',
			'title'       => __( 'Drinks Packs', 'ibv' ),
			'description' => __( 'Curated drinks packages for your first night or the full stay.', 'ibv' ),
		),
		array(
			'icon'        => 'clock',
			'title'       => __( 'Early Check-in / Late Checkout', 'ibv' ),
			'description' => __( 'Subject to availability. Ask when you enquire.', 'ibv' ),
		),
		array(
			'icon'        => 'utensils',
			'title'       => __( 'Private Catering', 'ibv' ),
			'description' => __( 'Private chefs for a night or the full week.', 'ibv' ),
		),
		array(
			'icon'        => 'sailboat',
			'title'       => __( 'Boat Charter', 'ibv' ),
			'description' => __( 'Day trips, sunset cruises, island-hopping.', 'ibv' ),
		),
		array(
			'icon'        => 'sparkles',
			'title'       => __( 'Massage & Beauty', 'ibv' ),
			'description' => __( 'In-villa treatments — book in advance.', 'ibv' ),
		),
		array(
			'icon'        => 'car',
			'title'       => __( 'Car / Moped / Quad Hire', 'ibv' ),
			'description' => __( 'Explore the island on your own schedule.', 'ibv' ),
		),
		array(
			'icon'        => 'trash-2',
			'title'       => __( 'Rubbish Collection', 'ibv' ),
			'description' => __( 'Arranged during longer stays on request.', 'ibv' ),
		),
	);
}
