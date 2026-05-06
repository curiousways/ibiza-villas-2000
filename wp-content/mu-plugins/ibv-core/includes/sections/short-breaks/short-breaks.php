<?php
/**
 * Section: Short breaks (Site Options).
 *
 * Thin wrapper around `ibv_core_image_text_section()`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Short breaks teaser.
 *
 * @param array $args {
 *     @type string $surface Surface modifier for `ibv_core_image_text_section`: `bg`, `white`, etc. Default `bg`.
 * }
 */
function ibv_core_section_short_breaks( $args = [] ) {
	$defaults = [
		'surface' => 'bg',
	];
	$args = wp_parse_args( $args, $defaults );

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	$surface        = in_array( $args['surface'], $valid_surfaces, true ) ? $args['surface'] : 'bg';

	$img   = get_field( 'short_breaks_image', 'option' );
	$title = get_field( 'short_breaks_title', 'option' );
	$text  = get_field( 'short_breaks_text', 'option' );
	$cta   = get_field( 'short_breaks_cta_url', 'option' );

	if ( ! $title && ! $text && ! $img ) {
		return;
	}

	ibv_core_image_text_section(
		[
			'title'       => $title ? (string) $title : '',
			'description' => $text ? (string) $text : '',
			'cta_url'     => $cta ? esc_url( $cta ) : '',
			'cta_label'   => __( 'Search Short Breaks', 'ibv' ),
			'image'       => $img,
			'image_side'  => 'right',
			'surface'     => $surface,
		]
	);
}
