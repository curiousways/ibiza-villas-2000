<?php
/**
 * Villa Listing Filtered — page template ACF field group.
 *
 * Hero image and description live in group_ibv_page_villa_listing_shared
 * so they keep the same keys after a template swap.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for area and large-group listing pages.
 */
function ibv_register_page_villa_listing_filtered_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_villa_listing_filtered',
			'title'                 => __( 'Villa listing filtered', 'ibv' ),
			'fields'                => array(
				array(
					'key'          => 'field_ibv_listing_intro',
					'label'        => __( 'Intro', 'ibv' ),
					'name'         => 'listing_intro',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'instructions' => __( 'Optional. Longer intro with links, shown below the description. Use the Text tab to paste HTML.', 'ibv' ),
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
					'instructions'  => __( 'Optional. Show only villas in this location. Leave empty for no location filter.', 'ibv' ),
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
				array(
					'key'          => 'field_ibv_listing_max_sleeps',
					'label'        => __( 'Maximum sleeps', 'ibv' ),
					'name'         => 'listing_max_sleeps',
					'type'         => 'number',
					'min'          => 1,
					'step'         => 1,
					'instructions' => __( 'Optional. Show only villas that sleep this many or fewer. Leave empty for all.', 'ibv' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-villa-listing-filtered.php',
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

add_action( 'acf/init', 'ibv_register_page_villa_listing_filtered_fields', 15 );
