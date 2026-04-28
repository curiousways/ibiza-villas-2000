<?php
/**
 * Ibiza Villas 2000 Core — shared asset registration.
 *
 * Registers (does not enqueue) the base CSS bundle and every component's CSS.
 * The theme enqueues `ibv-base` globally; components enqueue themselves
 * conditionally via their helper functions.
 *
 * Pattern for adding a new component:
 *   1. Co-locate the .css next to the component .php.
 *   2. Register the handle here, with `ibv-base` as a dependency.
 *   3. Call `wp_enqueue_style( 'ibv-{name}' )` from the component helper.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'ibv_register_styles', 5 );
function ibv_register_styles() {
	// Inter — neutral default. Loaded from Google Fonts; no build step.
	// Replace with the project typeface(s) by editing this URL and the
	// `--ibv-font-*` tokens in tokens.css. Version arg is null so the
	// request URL stays clean and Google's caching applies.
	wp_register_style(
		'ibv-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		[],
		null
	);

	// File-bundled handles. `base.css` registers as `ibv-elements` so that
	// `ibv-base` is free to act as the aggregate handle below.
	$foundation_files = [
		'ibv-tokens'     => 'assets/css/tokens.css',
		'ibv-reset'      => 'assets/css/reset.css',
		'ibv-elements'   => 'assets/css/base.css',
		'ibv-typography' => 'assets/css/typography.css',
		'ibv-layout'     => 'assets/css/layout.css',
	];

	foreach ( $foundation_files as $handle => $rel ) {
		wp_register_style(
			$handle,
			IBV_CORE_URL . $rel,
			[],
			IBV_CORE_VERSION
		);
	}

	// Aggregate handle — has no file of its own, just pulls in all the
	// foundation handles (and the font handle) via dependencies. The theme
	// enqueues this once globally; components depend on it.
	wp_register_style(
		'ibv-base',
		false,
		[ 'ibv-fonts', 'ibv-tokens', 'ibv-reset', 'ibv-elements', 'ibv-typography', 'ibv-layout' ],
		IBV_CORE_VERSION
	);

	// Components — each registered with `ibv-base` as a dependency.
	wp_register_style(
		'ibv-button',
		IBV_CORE_URL . 'includes/components/button/button.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-section-heading',
		IBV_CORE_URL . 'includes/components/section-heading/section-heading.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);
}

/**
 * Add preconnect hints for Google Fonts so the typeface loads as quickly
 * as possible once `ibv-fonts` is in the queue. Hints are only added on
 * pages that actually enqueue the font handle.
 */
add_filter( 'wp_resource_hints', 'ibv_resource_hints', 10, 2 );
function ibv_resource_hints( $hints, $relation ) {
	if ( 'preconnect' !== $relation ) {
		return $hints;
	}
	if ( ! wp_style_is( 'ibv-fonts', 'enqueued' ) ) {
		return $hints;
	}

	$hints[] = [ 'href' => 'https://fonts.googleapis.com' ];
	$hints[] = [
		'href'        => 'https://fonts.gstatic.com',
		'crossorigin' => 'anonymous',
	];

	return $hints;
}
