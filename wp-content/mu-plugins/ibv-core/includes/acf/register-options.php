<?php
/**
 * Ibiza Villas 2000 Core — Site Options pages (ACF).
 *
 * One top-level "Site Options" menu that redirects to the first sub-page,
 * with a sub-page per concern. Field groups target their sub-page slug in
 * register-site-options-content.php / register-globals-content.php.
 *
 * All sub-pages share the default 'options' post_id, so moving a field
 * group between pages never touches stored values.
 *
 * Capability is `edit_pages` so Editors can update featured offer and
 * other global copy. Do not use `manage_options` (Administrators only)
 * or `edit_theme_options` (that cap unlocks Appearance → Menus).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'ibv_register_site_options_page' );
function ibv_register_site_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	$capability = 'edit_pages';

	acf_add_options_page(
		[
			'page_title' => __( 'Site Options', 'ibv' ),
			'menu_title' => __( 'Site Options', 'ibv' ),
			'menu_slug'  => 'ibv-site-options',
			'capability' => $capability,
			'redirect'   => true,
			'icon_url'   => 'dashicons-admin-customizer',
			'position'   => 80,
		]
	);

	$subpages = [
		[ __( 'Global (header / footer)', 'ibv' ), __( 'Global', 'ibv' ), 'ibv-options-global' ],
		// Short breaks has no page of its own — its group renders on
		// Shared content (see register-globals-content.php).
		[ __( 'Shared content', 'ibv' ), __( 'Shared content', 'ibv' ), 'ibv-options-shared-content' ],
		[ __( 'Featured offer', 'ibv' ), __( 'Featured offer', 'ibv' ), 'ibv-options-featured-offer' ],
		[ __( 'Newsletter', 'ibv' ), __( 'Newsletter', 'ibv' ), 'ibv-options-newsletter' ],
		[ __( '404 page', 'ibv' ), __( '404 page', 'ibv' ), 'ibv-options-404' ],
	];

	foreach ( $subpages as $subpage ) {
		acf_add_options_sub_page(
			[
				'page_title'  => $subpage[0],
				'menu_title'  => $subpage[1],
				'menu_slug'   => $subpage[2],
				'parent_slug' => 'ibv-site-options',
				'capability'  => $capability,
			]
		);
	}
}
