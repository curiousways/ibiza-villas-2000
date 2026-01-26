<?php


/* Rude Brands custom post type */


function rude_brand_post_types() {

	/* VILLAS POST TYPE */
	$labels = array(
		'name'                => _x( 'Rude Brands', 'Rude Brand General Name', 'text_domain' ),
		'singular_name'       => _x( 'Rude Brand', 'Rude Brand Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Rude Brands', 'text_domain' ),
		'name_admin_bar'      => __( 'Rude Brand', 'text_domain' ),
		// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Rude Brands', 'text_domain' ),
		'add_new_item'        => __( 'Add New Rude Brand', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Rude Brands', 'text_domain' ),
		'not_found'           => __( 'Rude Brand not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'No Rude Brands found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'rude_brand', 'text_domain' ),
		'description'         => __( 'Rude Brand', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( ),
		// 'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 8,
		'menu_icon'           => 'dashicons-networking',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'rude_brand', $args );

}

add_action( 'init', 'rude_brand_post_types' );



