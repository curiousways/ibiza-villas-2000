<?php
/**
 * Villa Listing Main — page template ACF field groups.
 *
 * Image + description are shared with Villa Listing Filtered so a template
 * swap keeps the same meta. Notes stay on the main collection only.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Villa Listing Main page template.
 */
function ibv_register_page_villa_listing_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$both_listing_templates = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-villa-listing.php',
			),
		),
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-villa-listing-filtered.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_villa_listing_shared',
			'title'                 => __( 'Villa listing', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_listing_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'listing_hero_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'          => 'field_ibv_listing_description',
					'label'        => __( 'Description', 'ibv' ),
					'name'         => 'listing_description',
					'type'         => 'textarea',
					'rows'         => 3,
					'instructions' => __( 'Short intro paragraph shown below the page title.', 'ibv' ),
				),
			),
			'location'              => $both_listing_templates,
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => array(
				'the_content',
				'excerpt',
				'discussion',
				'comments',
				'send-trackbacks',
			),
			'active'                => true,
			'show_in_rest'          => false,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_villa_listing',
			'title'                 => __( 'Villa listing main', 'ibv' ),
			'fields'                => array(
				array(
					'key'          => 'field_ibv_listing_note_text',
					'label'        => __( 'Note text', 'ibv' ),
					'name'         => 'listing_note_text',
					'type'         => 'text',
					'instructions' => __( "Optional supporting note shown below the description (e.g. 'Looking for 12 or more guests?'). Leave blank to hide the note.", 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_listing_note_link_url',
					'label'        => __( 'Note link URL', 'ibv' ),
					'name'         => 'listing_note_link_url',
					'type'         => 'url',
					'instructions' => __( 'URL the note\'s link points to. Required if note text is set.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_listing_note_link_label',
					'label'         => __( 'Note link label', 'ibv' ),
					'name'          => 'listing_note_link_label',
					'type'          => 'text',
					'default_value' => __( 'Contact us', 'ibv' ),
					'instructions'  => __( 'The clickable text. Required if note text is set.', 'ibv' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-villa-listing.php',
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_page_villa_listing_fields', 15 );
