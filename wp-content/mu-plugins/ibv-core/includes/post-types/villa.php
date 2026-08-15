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
			'parent_item_colon'     => __( 'Parent Villa:', 'ibv' ),
			'all_items'             => __( 'All Villas', 'ibv' ),
			'add_new'               => __( 'Add New', 'ibv' ),
			'add_new_item'          => __( 'Add New Villa', 'ibv' ),
			'edit_item'             => __( 'Edit Villa', 'ibv' ),
			'update_item'           => __( 'Update Villa', 'ibv' ),
			'new_item'              => __( 'New Villa', 'ibv' ),
			'view_item'             => __( 'View Villa', 'ibv' ),
			'view_items'            => __( 'View Villas', 'ibv' ),
			'search_items'          => __( 'Search Villas', 'ibv' ),
			'not_found'             => __( 'No Villas found', 'ibv' ),
			'not_found_in_trash'    => __( 'No Villas found in Trash', 'ibv' ),
			'archives'              => __( 'Villa Archives', 'ibv' ),
			// Names the Order metabox: menu_order drives the villa listing sequence.
			'attributes'            => __( 'Villa display order', 'ibv' ),
			'insert_into_item'      => __( 'Insert into Villa', 'ibv' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Villa', 'ibv' ),
			'filter_items_list'     => __( 'Filter Villas list', 'ibv' ),
			'items_list_navigation' => __( 'Villas list navigation', 'ibv' ),
			'items_list'            => __( 'Villas list', 'ibv' ),
		);

		$args = array(
			'label'               => __( 'Villa', 'ibv' ),
			'description'         => __( 'Villa properties', 'ibv' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes' ),
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
