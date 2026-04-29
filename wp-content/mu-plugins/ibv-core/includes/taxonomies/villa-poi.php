<?php
/**
 * Villa POI taxonomy — admin only (no front-end routes).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register villa_poi taxonomy on villas CPT.
 */
function ibv_register_taxonomy_villa_poi() {
	register_taxonomy(
		'villa_poi',
		'villas',
		array(
			'labels'             => array(
				'name'          => __( 'Places of interest', 'ibv' ),
				'singular_name' => __( 'Place of interest', 'ibv' ),
				'menu_name'     => __( 'Places of interest', 'ibv' ),
				'all_items'     => __( 'All places of interest', 'ibv' ),
				'edit_item'     => __( 'Edit place', 'ibv' ),
				'view_item'     => __( 'View place', 'ibv' ),
				'update_item'   => __( 'Update place', 'ibv' ),
				'add_new_item'  => __( 'Add new place', 'ibv' ),
				'new_item_name' => __( 'New place name', 'ibv' ),
				'search_items'  => __( 'Search places', 'ibv' ),
				'not_found'     => __( 'No places found.', 'ibv' ),
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

add_action( 'init', 'ibv_register_taxonomy_villa_poi' );
