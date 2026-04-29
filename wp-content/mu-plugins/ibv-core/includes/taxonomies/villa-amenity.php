<?php
/**
 * Villa amenity taxonomy — admin only (no front-end routes).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register villa_amenity taxonomy on villas CPT.
 */
function ibv_register_taxonomy_villa_amenity() {
	register_taxonomy(
		'villa_amenity',
		'villas',
		array(
			'labels'             => array(
				'name'          => __( 'Amenities', 'ibv' ),
				'singular_name' => __( 'Amenity', 'ibv' ),
				'menu_name'     => __( 'Amenities', 'ibv' ),
				'all_items'     => __( 'All amenities', 'ibv' ),
				'edit_item'     => __( 'Edit amenity', 'ibv' ),
				'view_item'     => __( 'View amenity', 'ibv' ),
				'update_item'   => __( 'Update amenity', 'ibv' ),
				'add_new_item'  => __( 'Add new amenity', 'ibv' ),
				'new_item_name' => __( 'New amenity name', 'ibv' ),
				'search_items'  => __( 'Search amenities', 'ibv' ),
				'not_found'     => __( 'No amenities found.', 'ibv' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'show_in_rest'       => false,
			'show_admin_column'  => true,
			'rewrite'            => false,
			'hierarchical'       => false,
		)
	);
}

add_action( 'init', 'ibv_register_taxonomy_villa_amenity' );
