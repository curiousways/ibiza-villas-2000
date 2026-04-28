<?php
/**
 * Villa custom post type (slug: villas — plural, must match DB / existing posts).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_register_villa_post_type' ) ) {
	/**
	 * Register the Villas CPT. Mirrors legacy `themes/ibiza-villas-2000/functions/properties-cpt.php`.
	 */
	function ibv_register_villa_post_type() {

		$labels = array(
			'name'                  => _x( 'Villas', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Villa', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Properties', 'text_domain' ),
			'name_admin_bar'        => __( 'Villas', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Villa:', 'text_domain' ),
			'all_items'             => __( 'Show All Villas', 'text_domain' ),
			'add_new_item'          => __( 'Add New Villa', 'text_domain' ),
			'add_new'               => __( 'Add New Villa', 'text_domain' ),
			'new_item'              => __( 'New Villa', 'text_domain' ),
			'edit_item'             => __( 'Edit Villa', 'text_domain' ),
			'update_item'           => __( 'Update Villa', 'text_domain' ),
			'view_item'             => __( 'View Villa', 'text_domain' ),
			'search_items'          => __( 'Search Villa', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'items_list'            => __( 'Villas list', 'text_domain' ),
			'items_list_navigation' => __( 'Villas list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter Villas list', 'text_domain' ),
		);

		$args = array(
			'label'               => __( 'Villa', 'text_domain' ),
			'description'         => __( 'Villa Properties', 'text_domain' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
			'taxonomies'          => array( 'property_location' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);

		register_post_type( 'villas', $args );
	}
}

add_action( 'init', 'ibv_register_villa_post_type', 0 );
