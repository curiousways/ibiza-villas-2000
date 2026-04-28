<?php
/**
 * Villa Type taxonomy (slug: villa_type).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_register_villa_type_taxonomy' ) ) {

	function ibv_register_villa_type_taxonomy() {
		register_taxonomy(
			'villa_type',
			'villas',
			array(
				'hierarchical' => true,
				'labels'       => array(
					'name'              => _x( 'Villa Types', 'taxonomy general name' ),
					'singular_name'     => _x( 'Villa Type', 'taxonomy singular name' ),
					'search_items'      => __( 'Search Villa Types' ),
					'all_items'         => __( 'All Villa Types' ),
					'parent_item'       => __( 'Parent Villa Type' ),
					'parent_item_colon' => __( 'Parent Villa Type:' ),
					'edit_item'         => __( 'Edit Villa Type' ),
					'update_item'       => __( 'Update Villa Type' ),
					'add_new_item'      => __( 'Add New Villa Type' ),
					'new_item_name'     => __( 'New Villa Type Name' ),
					'menu_name'         => __( 'Villa Types' ),
				),
				'rewrite'      => array(
					'slug'         => 'villatype',
					'with_front'   => false,
					'hierarchical' => true,
				),
			)
		);
	}
}

add_action( 'init', 'ibv_register_villa_type_taxonomy', 0 );
