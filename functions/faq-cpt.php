<?php

// Register Custom Post Type
function faq() {

	$labels = array(
		'name'                => _x( 'FAQs', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'FAQ', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'FAQs', 'text_domain' ),
		'name_admin_bar'      => __( 'FAQ', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All FAQs', 'text_domain' ),
		'add_new_item'        => __( 'Add New FAQ', 'text_domain' ),
		'add_new'             => __( 'Add FAQ', 'text_domain' ),
		'new_item'            => __( 'New FAQ', 'text_domain' ),
		'edit_item'           => __( 'Edit FAQ', 'text_domain' ),
		'update_item'         => __( 'Update FAQ', 'text_domain' ),
		'view_item'           => __( 'View FAQ', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'FAQ', 'text_domain' ),
		'description'         => __( 'FAQs to go on the FAQ page', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor',),
		'taxonomies'          => array( 'faq-category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-clipboard',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'faq', $args );

}
add_action( 'init', 'faq', 0 );

// Register Custom Taxonomy
function FAQ_Category() {

	$labels = array(
		'name'                       => _x( 'FAQ Categories', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'FAQ Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'FAQ Category', 'text_domain' ),
		'all_items'                  => __( 'All FAQ Categories', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New FAQ Category Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New FAQ Category', 'text_domain' ),
		'edit_item'                  => __( 'Edit FAQ Category', 'text_domain' ),
		'update_item'                => __( 'Update FAQ Category', 'text_domain' ),
		'view_item'                  => __( 'View FAQ Category', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove FAQ Category', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'faq-category', array( 'faq' ), $args );

}
add_action( 'init', 'FAQ_Category', 0 );