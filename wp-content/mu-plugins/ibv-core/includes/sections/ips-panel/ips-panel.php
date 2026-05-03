<?php
/**
 * Section: IPS responsible tourism panel.
 *
 * Thin wrapper around `ibv_core_image_text_section()`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IPS panel — image + copy.
 */
function ibv_core_section_ips_panel() {
	$img   = get_field( 'ips_image' );
	$title = get_field( 'ips_title' );
	$text  = get_field( 'ips_text' );
	$clab  = get_field( 'ips_cta_label' );
	$curl  = get_field( 'ips_cta_url' );

	$has_image = ( is_array( $img ) && ! empty( $img['ID'] ) ) || ( is_numeric( $img ) && (int) $img > 0 );

	if ( ! $title && ! $text && ! $has_image ) {
		return;
	}

	ibv_core_image_text_section(
		[
			'title'       => $title ? (string) $title : '',
			'description' => $text ? (string) $text : '',
			'cta_url'     => $curl ? esc_url( $curl ) : '',
			'cta_label'   => $clab ? (string) $clab : '',
			'image'       => $img,
			'image_side'  => 'left',
			'surface'     => 'white',
		]
	);
}
