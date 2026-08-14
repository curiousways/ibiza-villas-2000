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
	load_theme_textdomain( 'ibv', get_template_directory() . '/languages' );
}

/**
 * Output the bundled IV2000 logo (SVG). Not configurable in admin.
 *
 * @param array $args {
 *     Optional. Arguments.
 *
 *     @type string $link_class Classes for the home link (include layout classes).
 *     @type string $img_class  Classes for the img element.
 *     @type string $variant    'default' | 'on-dark'. Uses ibv-logo.svg vs ibv-logo-on-dark.svg.
 * }
 */
function ibv_the_theme_logo( array $args = [] ) {
	$args = wp_parse_args(
		$args,
		[
			'link_class' => 'custom-logo-link',
			'img_class'  => 'custom-logo',
			'variant'    => 'default',
		]
	);
	$home = home_url( '/' );
	$file = ( 'on-dark' === $args['variant'] ) ? 'ibv-logo-on-dark.svg' : 'ibv-logo.svg';
	$src  = get_template_directory_uri() . '/assets/brand/' . $file;
	$name = get_bloginfo( 'name', 'display' );
	printf(
		'<a href="%1$s" class="%2$s" rel="home"><img class="%3$s" src="%4$s" width="842" height="229" alt="%5$s" decoding="async" /></a>',
		esc_url( $home ),
		esc_attr( $args['link_class'] ),
		esc_attr( $args['img_class'] ),
		esc_url( $src ),
		esc_attr( $name )
	);
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
	// Header mobile-menu toggle (registered in ibv-core's shared-assets.php).
	if ( wp_script_is( 'ibv-site-nav', 'registered' ) ) {
		wp_enqueue_script( 'ibv-site-nav' );
	}
}

/**
 * Strip emoji bloat — not used on this site.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
