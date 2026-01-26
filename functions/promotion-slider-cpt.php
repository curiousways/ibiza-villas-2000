<?php


/* Promotion Slider custom post type */


function promotion_slider_post_types() {

	$labels = array(
		'name'                => _x( 'Promotion Slider', 'Promotion Slide General Name', 'text_domain' ),
		'singular_name'       => _x( 'Promotion Slide', 'Promotion Slide Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Promotion Slider', 'text_domain' ),
		'name_admin_bar'      => __( 'Promotion Slide', 'text_domain' ),
		// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Promotion Slider', 'text_domain' ),
		'add_new_item'        => __( 'Add New Promotion Slide', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Promotion Slider', 'text_domain' ),
		'not_found'           => __( 'Promotion Slide not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'No Promotion Slides found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'promotion_slide', 'text_domain' ),
		'description'         => __( 'Promotion Slider', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( ),
		// 'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 9,
		'menu_icon'           => 'dashicons-format-gallery',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'promotion_slider', $args );

}

add_action( 'init', 'promotion_slider_post_types' );



