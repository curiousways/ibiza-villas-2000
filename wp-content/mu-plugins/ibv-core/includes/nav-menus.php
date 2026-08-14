<?php
/**
 * Ibiza Villas 2000 Core — register nav menu locations.
 *
 * The actual menu items are configured in WP Admin → Appearance → Menus.
 * Add or remove locations here as the project's chrome demands.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'ibv_register_nav_menus' );
function ibv_register_nav_menus() {
	register_nav_menus(
		[
			'primary'            => __( 'Primary navigation', 'ibv' ),
			'footer_quick_links' => __( 'Footer — quick links', 'ibv' ),
			'footer_support'     => __( 'Footer — support', 'ibv' ),
		]
	);
}

/**
 * Mark the logical parent item in the primary menu on singles WordPress
 * cannot map itself: single villas light up the villa-listing page's item,
 * single posts light up the Ibiza Guide page's item (the posts page isn't in
 * the menu). Pages are resolved by ACF option / template, never hardcoded
 * IDs, so this survives environment differences. The added class is the same
 * `current-menu-ancestor` WordPress uses natively, which the header CSS
 * already styles.
 */
add_filter( 'wp_nav_menu_objects', 'ibv_mark_nav_parent_on_singles', 10, 2 );
function ibv_mark_nav_parent_on_singles( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$parent_id = 0;

	if ( is_singular( 'villas' ) ) {
		$parent_id = ibv_get_search_villas_page_id();
	} elseif ( is_singular( 'post' ) ) {
		$pages     = get_posts(
			[
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'page-ibiza-guide.php',
			]
		);
		$parent_id = ! empty( $pages ) ? (int) $pages[0] : 0;
	}

	if ( ! $parent_id ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === $parent_id ) {
			$item->classes[] = 'current-menu-ancestor';
		}
	}

	return $items;
}
