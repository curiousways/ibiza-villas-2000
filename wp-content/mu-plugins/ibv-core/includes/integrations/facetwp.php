<?php
/**
 * FacetWP integration.
 *
 * Registers facets programmatically (PHP-first, version-controlled config).
 * Currently in use on the Ibiza Guide page — `category` filter and `pager`
 * pagination, paired with the `article-grid` section when called with
 * `facetwp => true`. To add a new facet: append to the array in
 * ibv_register_facetwp_facets() and reference its name from a template
 * via facetwp_display( 'facet', '{name}' ).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'facetwp_facets', 'ibv_register_facetwp_facets' );

/**
 * Add code-based facets to the FacetWP facet list.
 *
 * @param array $facets Facets currently registered (UI + previous code).
 * @return array Augmented list.
 */
function ibv_register_facetwp_facets( $facets ) {
	$facets[] = [
		'name'        => 'category',
		'label'       => 'Category',
		'type'        => 'radio',
		'source'      => 'tax/category',
		'label_any'   => 'All',
		'parent_term' => '',
		'modifiers'   => '',
		'ghosts'      => 'no',
		'orderby'     => 'display_value',
		'count'       => '-1',
	];

	$facets[] = [
		'name'                => 'pager',
		'label'               => 'Pager',
		'type'                => 'pager',
		'pager_type'          => 'numbers',
		'inner_size'          => 2,
		'dots_label'          => '…',
		'prev_label'          => '« Prev',
		'next_label'          => 'Next »',
		'count_text_plural'   => '[lower] – [upper] of [total] results',
		'count_text_singular' => '1 result',
		'count_text_none'     => 'No results',
		'load_more_text'      => 'Load more',
		'loading_text'        => 'Loading...',
		'per_page_options'    => '10,25,50,100',
		'default_label'       => '10',
		// Scroll back to the grid on page change. -40px offset gives the
		// first card a little breathing room from the viewport top.
		'scroll_target'       => '.facetwp-template',
		'scroll_offset'       => -40,
	];

	return $facets;
}
