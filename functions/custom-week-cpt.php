<?php


/* custom_weeks custom post type */


function custom_weeks_post_types() {

	/* VILLAS POST TYPE */
	$labels = array(
		'name'                => _x( 'Custom Weeks', 'Custom Week General Name', 'text_domain' ),
		'singular_name'       => _x( 'Custom Week', 'Custom Week Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Custom Weeks', 'text_domain' ),
		'name_admin_bar'      => __( 'Custom Week', 'text_domain' ),
		// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Custom Weeks', 'text_domain' ),
		'add_new_item'        => __( 'Add New Custom Week', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Custom Weeks', 'text_domain' ),
		'not_found'           => __( 'Custom Week not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'No custom weeks found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'custom_week', 'text_domain' ),
		'description'         => __( 'Custom Weeks', 'text_domain' ),
		'labels'              => $labels,
		// 'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 9,
		'menu_icon'           => 'dashicons-megaphone',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'rewrite' => array( 'slug' => 'custom-weeks' ),
		'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
	);
	register_post_type( 'custom_weeks', $args );

}

add_action( 'init', 'custom_weeks_post_types' );



