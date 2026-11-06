<?php
/**
 * WP Rocket — IBV-safe defaults and exclusions.
 *
 * Rocket options live in the DB per environment (not git). This file keeps
 * the dangerous optimisations off and excludes IBV/GF/FacetWP assets so a
 * settings click cannot silently break forms, calendars, or heroes.
 *
 * Enable page cache (and cautious image lazyload) in WP Admin after licence
 * activation. See ~/.cursor/skills/ibv-wordpress-updates/wp-rocket.md.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether WP Rocket is loaded.
 */
function ibv_core_wp_rocket_active() {
	return defined( 'WP_ROCKET_VERSION' ) || function_exists( 'get_rocket_option' );
}

/**
 * Force-off features that break this front end until deliberately proven.
 *
 * Page cache / minify CSS / image lazyload stay under admin control.
 *
 * @param mixed $value Option override (null = use stored).
 * @return int|mixed
 */
function ibv_core_wp_rocket_force_off( $value ) {
	return 0;
}

add_filter( 'pre_get_rocket_option_delay_js', 'ibv_core_wp_rocket_force_off' );
add_filter( 'pre_get_rocket_option_remove_unused_css', 'ibv_core_wp_rocket_force_off' );
add_filter( 'pre_get_rocket_option_minify_concatenate_js', 'ibv_core_wp_rocket_force_off' );
add_filter( 'pre_get_rocket_option_minify_js', 'ibv_core_wp_rocket_force_off' );
add_filter( 'pre_get_rocket_option_defer_all_js', 'ibv_core_wp_rocket_force_off' );
add_filter( 'pre_get_rocket_option_async_css', 'ibv_core_wp_rocket_force_off' );
add_filter( 'pre_get_rocket_option_lazyload_css_bg_img', 'ibv_core_wp_rocket_force_off' );

/**
 * Path substrings excluded from Delay JS / Defer / minify JS lists.
 *
 * @return string[]
 */
function ibv_core_wp_rocket_js_exclusions() {
	return [
		'/wp-content/plugins/gravityforms/',
		'/wp-content/plugins/facetwp/',
		'/wp-content/mu-plugins/ibv-core/',
		'gravityforms',
		'gform',
		'facetwp',
		'vanilla-calendar',
		'intlTelInput',
		'intl-tel-input',
		'cookieconsent',
		'enquiry-panel',
		'date-range-picker',
		'accommodation-enquiry',
		'contact-enquiry',
		'villa-listing',
		'google',
		'maps.googleapis',
	];
}

/**
 * @param string[] $excluded Exclusions.
 * @return string[]
 */
function ibv_core_wp_rocket_merge_js_exclusions( $excluded ) {
	$excluded = is_array( $excluded ) ? $excluded : [];
	return array_values( array_unique( array_merge( $excluded, ibv_core_wp_rocket_js_exclusions() ) ) );
}

add_filter( 'rocket_delay_js_exclusions', 'ibv_core_wp_rocket_merge_js_exclusions' );
add_filter( 'rocket_exclude_defer_js', 'ibv_core_wp_rocket_merge_js_exclusions' );
add_filter( 'rocket_exclude_js', 'ibv_core_wp_rocket_merge_js_exclusions' );

/**
 * Never page-cache dynamic booking surfaces.
 *
 * @param string[] $uris              Reject URI patterns.
 * @param bool     $show_safe_content Unused; Rocket passes it through.
 * @return string[]
 */
function ibv_core_wp_rocket_reject_uris( $uris, $show_safe_content = true ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	$uris   = is_array( $uris ) ? $uris : [];
	$uris[] = '/my-booking';
	$uris[] = '/booking-confirmation';
	return array_values( array_unique( $uris ) );
}

add_filter( 'rocket_cache_reject_uri', 'ibv_core_wp_rocket_reject_uris', 20, 2 );

/**
 * Do not lazyload logo / hero-ish images identified by attribute fragments.
 *
 * @param string[] $attributes Excluded attribute needles.
 * @return string[]
 */
function ibv_core_wp_rocket_lazyload_excluded_attributes( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : [];
	return array_values(
		array_unique(
			array_merge(
				$attributes,
				[
					'custom-logo',
					'ibv-section-villa-listing-hero__image',
					'ibv-hero',
					'data-no-lazy',
					'skip-lazy',
				]
			)
		)
	);
}

add_filter( 'rocket_lazyload_excluded_attributes', 'ibv_core_wp_rocket_lazyload_excluded_attributes' );

/**
 * @param string[] $src Excluded src needles.
 * @return string[]
 */
function ibv_core_wp_rocket_lazyload_excluded_src( $src ) {
	$src = is_array( $src ) ? $src : [];
	return array_values(
		array_unique(
			array_merge(
				$src,
				[
					'ibv-logo',
					'/wp-content/themes/ibv/assets/brand/',
				]
			)
		)
	);
}

add_filter( 'rocket_lazyload_excluded_src', 'ibv_core_wp_rocket_lazyload_excluded_src' );
