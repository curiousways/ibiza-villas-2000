<?php
/**
 * Ibiza Villas 2000 Core — Site Options page (ACF).
 *
 * Adds a top-level "Site Options" admin page. The starter has no fields —
 * add them per project as global settings emerge (e.g. social links,
 * default fallback images, footer text, current-issue references).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'ibv_register_site_options_page' );
function ibv_register_site_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		[
			'page_title' => __( 'Site Options', 'ibv' ),
			'menu_title' => __( 'Site Options', 'ibv' ),
			'menu_slug'  => 'ibv-site-options',
			'capability' => 'manage_options',
			'redirect'   => false,
			'icon_url'   => 'dashicons-admin-settings',
			'position'   => 80,
		]
	);

	acf_add_local_field_group(
		[
			'key'                   => 'group_ibv_site_options',
			'title'                 => __( 'Site Options', 'ibv' ),
			'fields'                => [
				// Add site-wide fields here.
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ibv-site-options',
					],
				],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		]
	);
}
