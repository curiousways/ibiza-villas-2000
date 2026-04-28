<?php




// Register Custom Post Type
function villas() {

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
        'label'                 => __( 'Villa', 'text_domain' ),
        'description'           => __( 'Villa Properties', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
        'taxonomies'            => array( 'property_location' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,        
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'villas', $args );

}
add_action( 'init', 'villas', 0 );





// Property Location Taxonomy
add_action( 'init', 'register_taxonomy_property_location' );

function register_taxonomy_property_location() {

    $labels = array( 
        'name' => _x( 'Property Location', 'property_location' ),
        'singular_name' => _x( 'Property Location', 'property_location' ),
        'search_items' => _x( 'Search Property Location', 'property_location' ),
        'popular_items' => _x( 'Popular Property Location', 'property_location' ),
        'all_items' => _x( 'All Property Location', 'property_location' ),
        'parent_item' => _x( 'Parent Property Location', 'property_location' ),
        'parent_item_colon' => _x( 'Parent Property Location:', 'property_location' ),
        'edit_item' => _x( 'Edit Property Location', 'property_location' ),
        'update_item' => _x( 'Update Property Location', 'property_location' ),
        'add_new_item' => _x( 'Add New Property Location', 'property_location' ),
        'new_item_name' => _x( 'New Property Location', 'property_location' ),
        'separate_items_with_commas' => _x( 'Separate property location with commas', 'property_location' ),
        'add_or_remove_items' => _x( 'Add or remove Property Location', 'property_location' ),
        'choose_from_most_used' => _x( 'Choose from most used Property Location', 'property_location' ),
        'menu_name' => _x( 'Property Location', 'property_location' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => false,
        'show_admin_column' => false,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'property_location', array('villas'), $args );
}

