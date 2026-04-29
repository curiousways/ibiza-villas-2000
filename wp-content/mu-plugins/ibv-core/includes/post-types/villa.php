<?php
/**
 * Villa custom post type (slug: villas — plural, must match DB / existing posts).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_register_villa_post_type' ) ) {
	/**
	 * Register the Villas CPT. Mirrors legacy theme registration before Pass 3b.
	 */
	function ibv_register_villa_post_type() {

		$labels = array(
			'name'                  => __( 'Villas', 'ibv' ),
			'singular_name'         => __( 'Villa', 'ibv' ),
			'menu_name'             => __( 'Villas', 'ibv' ),
			'name_admin_bar'        => __( 'Villa', 'ibv' ),
			'parent_item_colon'     => __( 'Parent villa:', 'ibv' ),
			'all_items'             => __( 'All villas', 'ibv' ),
			'add_new'               => __( 'Add new', 'ibv' ),
			'add_new_item'          => __( 'Add new villa', 'ibv' ),
			'edit_item'             => __( 'Edit villa', 'ibv' ),
			'update_item'           => __( 'Update villa', 'ibv' ),
			'new_item'              => __( 'New villa', 'ibv' ),
			'view_item'             => __( 'View villa', 'ibv' ),
			'view_items'            => __( 'View villas', 'ibv' ),
			'search_items'          => __( 'Search villas', 'ibv' ),
			'not_found'             => __( 'No villas found', 'ibv' ),
			'not_found_in_trash'    => __( 'No villas found in trash', 'ibv' ),
			'archives'              => __( 'Villa archives', 'ibv' ),
			'attributes'            => __( 'Villa attributes', 'ibv' ),
			'insert_into_item'      => __( 'Insert into villa', 'ibv' ),
			'uploaded_to_this_item' => __( 'Uploaded to this villa', 'ibv' ),
			'filter_items_list'     => __( 'Filter villas list', 'ibv' ),
			'items_list_navigation' => __( 'Villas list navigation', 'ibv' ),
			'items_list'            => __( 'Villas list', 'ibv' ),
		);

		$args = array(
			'label'               => __( 'Villa', 'ibv' ),
			'description'         => __( 'Villa properties', 'ibv' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
			'taxonomies'          => array( 'property_location' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-admin-home',
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			// No CPT archive — listing is a Page (template: Villa Listing), e.g. /villas/.
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);

		register_post_type( 'villas', $args );
	}
}

add_action( 'init', 'ibv_register_villa_post_type', 0 );
