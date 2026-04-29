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
			'footer'             => __( 'Footer navigation (legacy)', 'ibv' ),
			'footer_quick_links' => __( 'Footer — quick links', 'ibv' ),
			'footer_support'     => __( 'Footer — support', 'ibv' ),
		]
	);
}
