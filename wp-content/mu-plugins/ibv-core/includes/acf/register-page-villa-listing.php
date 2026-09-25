<?php
/**
 * Villa Listing page — page template ACF field groups.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Villa Listing page template.
 */
function ibv_register_page_villa_listing_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_villa_listing',
			'title'                 => __( 'Villa listing page', 'ibv' ),
			'fields'                => array(
				array(
					'key'          => 'field_ibv_listing_description',
					'label'        => __( 'Description', 'ibv' ),
					'name'         => 'listing_description',
					'type'         => 'textarea',
					'rows'         => 3,
					'instructions' => __( 'Short intro paragraph shown below the page title.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_listing_location',
					'label'         => __( 'Location', 'ibv' ),
					'name'          => 'listing_location',
					'type'          => 'taxonomy',
					'taxonomy'      => 'property_location',
					'field_type'    => 'select',
					'allow_null'    => 1,
					'add_term'      => 0,
					'save_terms'    => 0,
					'load_terms'    => 0,
					'multiple'      => 0,
					'return_format' => 'object',
					'instructions'  => __( 'Optional. Show only villas in this location. Leave empty for the full collection.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_listing_min_sleeps',
					'label'        => __( 'Minimum sleeps', 'ibv' ),
					'name'         => 'listing_min_sleeps',
					'type'         => 'number',
					'min'          => 1,
					'step'         => 1,
					'instructions' => __( 'Optional. Show only villas that sleep this many or more. Leave empty for all.', 'ibv' ),
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
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			// page-villa-listing.php never calls the_content(); the editor
			// body is leftover and must not look live in admin.
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
}

add_action( 'acf/init', 'ibv_register_page_villa_listing_fields', 15 );
