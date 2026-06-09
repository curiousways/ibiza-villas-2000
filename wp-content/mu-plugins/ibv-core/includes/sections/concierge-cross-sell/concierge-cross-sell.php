<?php
/**
 * Section: Concierge cross-sell (Site Options).
 *
 * Thin wrapper over `ibv_core_image_text_section()` — the bespoke markup
 * was replaced once the shared module covered the same shape (Short
 * Breaks, IPS, Meet the Team). On the villa detail page this is called
 * outside the `__main` two-column grid so it renders full-width.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string|null $heading_override Optional heading; falls back to Site Option.
 */
function ibv_core_section_concierge_cross_sell( $heading_override = null ) {
	$image   = get_field( 'concierge_image', 'option' );
	$heading = $heading_override ? (string) $heading_override : (string) get_field( 'concierge_heading', 'option' );
	$body    = (string) get_field( 'concierge_body', 'option' );
	$cta     = (string) get_field( 'concierge_cta_label', 'option' );
	$url     = (string) get_field( 'concierge_cta_url', 'option' );

	if ( ! $heading && ! $body && empty( $image['ID'] ) ) {
		return;
	}
	if ( ! $cta ) {
		$cta = __( 'View concierge services', 'ibv' );
	}

	ibv_core_image_text_section(
		[
			'title'       => $heading,
			'description' => $body,
			'cta_url'     => $url,
			'cta_label'   => $cta,
			'image'       => $image,
			'image_side'  => 'right',
			'surface'     => 'white',
		]
	);
}
