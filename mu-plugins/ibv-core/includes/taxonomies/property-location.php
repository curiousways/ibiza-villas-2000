<?php
/**
 * Property Location taxonomy (slug: property_location).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_register_property_location_taxonomy' ) ) {

	function ibv_register_property_location_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Property Location', 'property_location' ),
			'singular_name'              => _x( 'Property Location', 'property_location' ),
			'search_items'               => _x( 'Search Property Location', 'property_location' ),
			'popular_items'              => _x( 'Popular Property Location', 'property_location' ),
			'all_items'                  => _x( 'All Property Location', 'property_location' ),
			'parent_item'                => _x( 'Parent Property Location', 'property_location' ),
			'parent_item_colon'          => _x( 'Parent Property Location:', 'property_location' ),
			'edit_item'                  => _x( 'Edit Property Location', 'property_location' ),
			'update_item'                => _x( 'Update Property Location', 'property_location' ),
			'add_new_item'               => _x( 'Add New Property Location', 'property_location' ),
			'new_item_name'              => _x( 'New Property Location', 'property_location' ),
			'separate_items_with_commas' => _x( 'Separate property location with commas', 'property_location' ),
			'add_or_remove_items'        => _x( 'Add or remove Property Location', 'property_location' ),
			'choose_from_most_used'      => _x( 'Choose from most used Property Location', 'property_location' ),
			'menu_name'                  => _x( 'Property Location', 'property_location' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'rewrite'           => true,
			'query_var'         => true,
		);

		register_taxonomy( 'property_location', array( 'villas' ), $args );
	}
}

add_action( 'init', 'ibv_register_property_location_taxonomy' );
