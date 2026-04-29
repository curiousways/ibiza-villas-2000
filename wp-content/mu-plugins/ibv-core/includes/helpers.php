<?php
/**
 * Ibiza Villas 2000 Core — generic helpers.
 *
 * Project-agnostic helpers. Keep this file lean — anything that resolves
 * a project-specific concern (a feature flag, a custom field accessor,
 * a domain rule) belongs in its own module under `includes/`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convenience wrapper around `home_url()` that always returns an escaped URL.
 *
 * @param string $path Path relative to home.
 * @return string
 */
function ibv_url( $path = '' ) {
	return esc_url( home_url( $path ) );
}

/**
 * Villas listing URL (Site Options override).
 *
 * @return string Escaped URL.
 */
function ibv_get_search_villas_url() {
	$url = get_field( 'search_villas_url', 'option' );
	if ( $url ) {
		return esc_url( $url );
	}
	return esc_url( home_url( '/villas/' ) );
}

/**
 * Special Offers page URL (template lookup with path fallback).
 *
 * @return string Escaped URL.
 */
function ibv_get_special_offers_url() {
	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-special-offers.php',
		]
	);
	if ( ! empty( $pages ) ) {
		return esc_url( get_permalink( $pages[0] ) );
	}
	return esc_url( home_url( '/special-offers/' ) );
}

/**
 * Estimated reading time in minutes from post content.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function ibv_estimate_reading_minutes( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( (string) $content ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Contact page URL from Site Options (fallback).
 *
 * @return string Escaped URL.
 */
function ibv_get_contact_page_url() {
	$url = get_field( 'contact_page', 'option' );
	if ( $url ) {
		return esc_url( $url );
	}
	return esc_url( home_url( '/contact/' ) );
}

/**
 * Booking confirmation page URL; optional villa slug query arg.
 *
 * @param string $villa_slug Post slug for ?villa=.
 * @return string Escaped URL.
 */
function ibv_get_booking_confirmation_url( $villa_slug = '' ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-booking-confirmation.php',
		)
	);
	$base = ! empty( $pages ) ? get_permalink( $pages[0] ) : home_url( '/booking-confirmation/' );
	if ( $villa_slug ) {
		return esc_url( add_query_arg( 'villa', sanitize_title( $villa_slug ), $base ) );
	}
	return esc_url( $base );
}

/**
 * Current GET params when on the villa listing template (search form persistence).
 *
 * @return array{date_from: string, date_to: string, pax: string}
 */
function ibv_get_villa_listing_search_params() {
	if ( ! is_page_template( 'page-villa-listing.php' ) ) {
		return [
			'date_from' => '',
			'date_to'   => '',
			'pax'       => '',
		];
	}

	return [
		'date_from' => isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '',
		'date_to'   => isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '',
		'pax'       => isset( $_GET['pax'] ) ? sanitize_text_field( wp_unslash( $_GET['pax'] ) ) : '',
	];
}
