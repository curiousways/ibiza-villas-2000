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
 * URL of the villa listing / search page.
 *
 * Resolution order:
 * 1. Site Options → Search / villas listing page (ACF), when set — override.
 * 2. First published page using the Villa Listing template (`page-villa-listing.php`).
 * 3. Path fallback `/villas/`.
 *
 * All villa listing links should use this helper (forms, CTAs, buttons).
 *
 * @return string Escaped URL.
 */
function ibv_get_search_villas_url() {
	$page = get_field( 'search_villas_page', 'option' );

	if ( $page instanceof WP_Post ) {
		return esc_url( get_permalink( $page ) );
	}

	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-villa-listing.php',
			'orderby'        => [ 'menu_order' => 'ASC', 'post_title' => 'ASC' ],
		]
	);

	if ( ! empty( $pages ) ) {
		return esc_url( get_permalink( $pages[0] ) );
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
 * Booking confirmation page ID.
 *
 * Resolves the page using the page-booking-confirmation.php template. Returns 0
 * if no such published page exists on this environment yet. Resolving by
 * template (not a hardcoded slug) keeps the destination correct regardless of
 * the page's slug.
 *
 * @return int Page ID, or 0.
 */
function ibv_get_booking_confirmation_page_id() {
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
	return ! empty( $pages ) ? (int) $pages[0] : 0;
}

/**
 * Booking confirmation page URL.
 *
 * Permalink of the page resolved by ibv_get_booking_confirmation_page_id(),
 * falling back to /booking-confirmation/ only if that page does not exist yet.
 *
 * @return string Escaped URL.
 */
function ibv_get_booking_confirmation_url() {
	$page_id = ibv_get_booking_confirmation_page_id();
	$base    = $page_id ? get_permalink( $page_id ) : home_url( '/booking-confirmation/' );
	return esc_url( $base );
}

/**
 * Build the Gravity Forms confirmation that sends an enquiry to the booking
 * confirmation page. Shared by the Villa Enquiry and Accommodation Enquiry
 * seeders (the single source of truth for those forms) so both stay in step —
 * only the merge-tag query string differs.
 *
 * Produces a "Page" confirmation: GF redirects by page id, resolving the
 * permalink at submit time, so it survives slug + domain changes (no stored
 * URL). Falls back to a URL redirect only if the confirmation page has not been
 * created on this environment yet.
 *
 * @param string $id           Stable confirmation id/key for the form.
 * @param string $query_string GF merge-tag query string (no leading '?').
 * @return array GF confirmation definition.
 */
function ibv_build_gf_booking_confirmation( $id, $query_string = '' ) {
	$page_id = ibv_get_booking_confirmation_page_id();

	$confirmation = array(
		'id'          => $id,
		'name'        => 'Redirect to booking confirmation',
		'isDefault'   => true,
		'queryString' => $query_string,
	);

	if ( $page_id ) {
		$confirmation['type']   = 'page';
		$confirmation['page']   = $page_id;
		$confirmation['pageId'] = $page_id;
	} else {
		$confirmation['type'] = 'redirect';
		$confirmation['url']  = ibv_get_booking_confirmation_url();
	}

	return $confirmation;
}

/**
 * Absolute URL of Steve's PMS availability endpoint.
 *
 * Single source of truth for the Bob API base URL. Reused by JS hydration on
 * the villa listing, villa detail enquiry panel, and "from price" hooks.
 *
 * @return string
 */
function ibv_get_bob_endpoint_url() {
	return 'https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl';
}

/**
 * Format a single `villa_distances` repeater row for display.
 *
 * Joins the row's `distance_text` (editor-chosen qualifier, e.g. "5 mins
 * from") with the `poi` taxonomy term's name (the destination), trimming
 * empties.
 *
 * Known display-layer accommodation, not a permanent fix: two editorial
 * conventions live in the data today — newer rows split the phrase
 * (`distance_text` = qualifier, `poi` = destination), older rows put the
 * full sentence in `distance_text` with no `poi`. This helper does not
 * detect or dedupe; a villa with both a full-sentence `distance_text` and
 * a `poi` term will read with the destination repeated. The real fix is
 * content (qualifier-only convention in `distance_text`); flagged in
 * Tina's content guidelines.
 *
 * @param array $row One row of the `villa_distances` repeater.
 * @return string Joined label, trimmed; empty if both fields are blank.
 */
function ibv_villa_distance_label( array $row ) {
	$text = isset( $row['distance_text'] ) ? trim( (string) $row['distance_text'] ) : '';
	$poi  = ( isset( $row['poi'] ) && $row['poi'] instanceof WP_Term ) ? $row['poi']->name : '';
	return trim( $text . ' ' . $poi );
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
