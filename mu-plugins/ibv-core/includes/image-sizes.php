<?php
/**
 * Ibiza Villas 2000 Core — registered image sizes.
 *
 * Add new sizes here. Names are prefixed `ibv-` and are usable anywhere
 * a WP image size is expected (e.g. wp_get_attachment_image()).
 *
 * The starter sizes below are intentionally generic. Tune dimensions and
 * crop behaviour from the project design once the templates are clearer,
 * and add more sizes as templates need them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'ibv_register_image_sizes' );
function ibv_register_image_sizes() {
	// Wide hero / featured image — 16:9.
	add_image_size( 'ibv-hero', 1920, 1080, true );

	// Card thumbnail — used in archive grids. 3:2.
	add_image_size( 'ibv-card', 1200, 800, true );

	// Square portrait / headshot.
	add_image_size( 'ibv-square', 800, 800, true );
}
