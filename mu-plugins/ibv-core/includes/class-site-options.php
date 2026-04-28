<?php
/**
 * Admin: empty Site Options screen (slug ibv-site-options). Fields arrive in pass 3b.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ibv_Core_Site_Options {

	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'register_menu' ) );
	}

	public static function register_menu(): void {
		add_menu_page(
			__( 'Site Options', 'ibv-core' ),
			__( 'Site Options', 'ibv-core' ),
			'manage_options',
			'ibv-site-options',
			array( self::class, 'render_page' ),
			'dashicons-admin-settings',
			59
		);
	}

	public static function render_page(): void {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Site Options', 'ibv-core' ) . '</h1>';
		echo '</div>';
	}
}
