<?php


/* Testimonials custom post type */


function testimonials_post_types() {

	/* VILLAS POST TYPE */
	$labels = array(
		'name'                => _x( 'Testimonials', 'Testimonial General Name', 'text_domain' ),
		'singular_name'       => _x( 'Testimonial', 'Testimonial Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Testimonials', 'text_domain' ),
		'name_admin_bar'      => __( 'Testimonial', 'text_domain' ),
		// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Testimonials', 'text_domain' ),
		'add_new_item'        => __( 'Add New Testimonial', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Testimonials', 'text_domain' ),
		'not_found'           => __( 'Testimonial not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'No testimonials found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'testimonial', 'text_domain' ),
		'description'         => __( 'TripAdvisor Testismonials', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( ),
		// 'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 8,
		'menu_icon'           => 'dashicons-format-quote',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'testimonials', $args );

}

add_action( 'init', 'testimonials_post_types' );



