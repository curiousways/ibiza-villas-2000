<?php
/**
 * Ibiza Villas 2000 Core — editor preferences.
 *
 * The site uses the Classic editor everywhere. CPTs registered by this
 * mu-plugin should opt out via `show_in_rest => false`; this module
 * extends the same policy to the core `post` and `page` types and the
 * widgets screen so the experience is consistent across the admin.
 *
 * If a future brief needs the block editor for a specific post type,
 * narrow `ibv_disable_block_editor()` rather than dropping the filter
 * altogether.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'use_block_editor_for_post_type', 'ibv_disable_block_editor', 100 );
function ibv_disable_block_editor() {
	return false;
}

// Use the classic widgets screen so the admin chrome matches.
add_filter( 'use_widgets_block_editor', '__return_false' );

/**
 * The Classic editor never produces block markup, so the block-library
 * stylesheets are dead weight on every front-end request. Strip them.
 */
add_action( 'wp_enqueue_scripts', 'ibv_dequeue_block_assets', 100 );
function ibv_dequeue_block_assets() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
