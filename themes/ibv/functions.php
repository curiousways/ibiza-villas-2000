<?php
/**
 * Ibiza Villas 2000 thin theme bootstrap (inactive scaffold).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IBV_THEME_VERSION', '0.1.0' );

add_theme_support( 'title-tag' );

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style(
			'ibv-style',
			get_stylesheet_uri(),
			array(),
			defined( 'IBV_THEME_VERSION' ) ? IBV_THEME_VERSION : '0.1.0'
		);
	}
);
