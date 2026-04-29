<?php
/**
 * Ibiza Villas 2000 theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'ibv_theme_setup' );
function ibv_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		]
	);
	add_theme_support(
		'custom-logo',
		[
			'flex-height' => true,
			'flex-width'  => true,
		]
	);

	load_theme_textdomain( 'ibv', get_template_directory() . '/languages' );
}

add_action( 'wp_enqueue_scripts', 'ibv_theme_enqueue' );
function ibv_theme_enqueue() {
	// The aggregate `ibv-base` handle is registered in ibv-core's shared-assets.php.
	if ( wp_style_is( 'ibv-base', 'registered' ) ) {
		wp_enqueue_style( 'ibv-base' );
	}
	if ( wp_style_is( 'ibv-site-chrome', 'registered' ) ) {
		wp_enqueue_style( 'ibv-site-chrome' );
	}
}

/**
 * Strip emoji bloat — not used on this site.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
