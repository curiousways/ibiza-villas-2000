<?php
/**
 * Contact page — page template ACF field group.
 *
 * Contact-enquiry copy (page heading + subheading + banner image + form
 * card title) + the Gravity Forms form ID live on the page. Phone /
 * email / WhatsApp contact details and all the form behaviour stay in
 * Site Options / GF admin.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Contact page template.
 */
function ibv_register_page_contact_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_contact = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-contact.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_contact',
			'title'                 => __( 'Contact page', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_page_contact_tab_heading',
					'label'     => __( 'Heading', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_contact_page_heading',
					'label'         => __( 'Page heading', 'ibv' ),
					'name'          => 'page_heading',
					'type'          => 'text',
					'default_value' => __( 'Dive into Ibiza Villas 2000', 'ibv' ),
					'instructions'  => __( 'Page H1. Newsreader 64.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_contact_page_subheading',
					'label'         => __( 'Page subheading', 'ibv' ),
					'name'          => 'page_subheading',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => __( 'We respond within 20 minutes.', 'ibv' ),
				),
				array(
					'key'       => 'field_ibv_page_contact_tab_form',
					'label'     => __( 'Form', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_contact_form_banner_image',
					'label'         => __( 'Form banner image', 'ibv' ),
					'name'          => 'form_banner_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Wide image sitting above the form card (~248px tall, 12px radius). Decorative — no alt text needed.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_contact_form_title',
					'label'         => __( 'Form card title', 'ibv' ),
					'name'          => 'form_title',
					'type'          => 'text',
					'default_value' => __( 'Send an enquiry', 'ibv' ),
					'instructions'  => __( 'Newsreader 24 title above the form fields. Leave empty to render the form without card chrome.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_contact_form_id',
					'label'        => __( 'Contact enquiry Gravity Form ID', 'ibv' ),
					'name'         => 'contact_enquiry_gravity_form_id',
					'type'         => 'number',
					'min'          => 0,
					'instructions' => __(
						'Gravity Form ID embedded in the contact section. Leave empty to hide the form.<br><br>'
						. '<strong>Editorial setup in the GF admin (one-time):</strong><ol>'
						. '<li><strong>Honeypot</strong> — Form Settings → "Enable anti-spam honeypot" → on.</li>'
						. '<li><strong>Notifications</strong> — set the recipient to the Site Options "Contact email"; set <em>Reply-To</em> to the enquirer\'s email so the office can reply directly.</li>'
						. '<li><strong>Date fields</strong> — for "Dates of Stay" use Field Type <strong>Date Drop Down</strong> in the field\'s General settings. Renders as three native selects (day / month / year), styled by our base form rules — no jQuery UI Datepicker popover quirks.</li>'
						. '<li><strong>Field set:</strong> Name, Email, Phone (Phone type), Dates of Stay, Number of Guests, Villa preference (optional), Message. <em>No</em> Service dropdown on the contact form.</li>'
						. '</ol>',
						'ibv'
					),
				),
				array(
					'key'       => 'field_ibv_page_contact_tab_cta',
					'label'     => __( 'CTA', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_contact_image_text_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'image_text_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Right-hand image for the closing image-and-text band (Figma 1:6578).', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_contact_image_text_title',
					'label'         => __( 'Title', 'ibv' ),
					'name'          => 'image_text_title',
					'type'          => 'text',
					'default_value' => __( 'Based in Ibiza — local team', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_contact_image_text_body',
					'label'         => __( 'Body', 'ibv' ),
					'name'          => 'image_text_body',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => __( 'Permanent local team, not seasonal agency staff.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_contact_image_text_cta_label',
					'label'         => __( 'CTA label', 'ibv' ),
					'name'          => 'image_text_cta_label',
					'type'          => 'text',
					'default_value' => __( 'Search Short Breaks', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_contact_image_text_cta_url',
					'label'        => __( 'CTA page', 'ibv' ),
					'name'         => 'image_text_cta_url',
					'type'         => 'page_link',
					'instructions' => __( 'Pick the page the button should link to (e.g. short breaks / villa listing).', 'ibv' ),
				),
			),
			'location'              => $loc_contact,
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

add_action( 'acf/init', 'ibv_register_page_contact_fields', 15 );
