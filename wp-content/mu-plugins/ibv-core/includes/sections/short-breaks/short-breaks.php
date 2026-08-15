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

	if ( ! $title && ! $text && ! $img ) {
		return;
	}

	// Deep-link the listing with a pre-filled 4-night search (~a month out,
	// mirroring the listing's own probe window) so the short-breaks promise
	// is kept by the search itself — the specced Short Breaks filter was
	// removed as a deliberate spec change (no villa carries a short-break
	// flag; see the listing's short-breaks statement). Four nights is the
	// safe default while the minimum-stay question is with Luke/Steve.
	// Computed per render: safe under page caching as long as cache
	// lifetime stays well inside the 30-day lead-in.
	$cta_url = add_query_arg(
		[
			'date_from' => gmdate( 'Y-m-d', time() + 30 * DAY_IN_SECONDS ),
			'date_to'   => gmdate( 'Y-m-d', time() + 34 * DAY_IN_SECONDS ),
			'pax'       => 2,
		],
		ibv_get_search_villas_url()
	);

	ibv_core_image_text_section(
		[
			'title'       => $title ? (string) $title : '',
			'description' => $text ? (string) $text : '',
			// CTA destination is fixed by design: always the villa listing page.
			'cta_url'     => $cta_url,
			'cta_label'   => __( 'Search short breaks', 'ibv' ),
			'image'       => $img,
			'image_side'  => 'right',
			'surface'     => $surface,
		]
	);
}
