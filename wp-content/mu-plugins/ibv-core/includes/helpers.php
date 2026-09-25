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
 * 2. Published page at `/all-villas/`.
 * 3. First published Villa Listing page with no Location set.
 * 4. Path fallback `/all-villas/`.
 *
 * All villa listing links should use this helper (forms, CTAs, buttons).
 *
 * @return string Escaped URL.
 */
function ibv_get_search_villas_url() {
	$page_id = ibv_get_search_villas_page_id();

	if ( $page_id ) {
		return esc_url( get_permalink( $page_id ) );
	}

	return esc_url( home_url( '/all-villas/' ) );
}

/**
 * ID of the villa listing / search page.
 *
 * Same resolution as ibv_get_search_villas_url() (ACF option override, then
 * /all-villas/, then a Location-empty Villa Listing page) but returns the
 * page ID — used where the page itself must be identified, e.g. matching
 * the menu item for nav on-state. Returns 0 when no page resolves.
 *
 * @return int Page ID, or 0.
 */
function ibv_get_search_villas_page_id() {
	$page = get_field( 'search_villas_page', 'option' );

	if ( $page instanceof WP_Post ) {
		return (int) $page->ID;
	}

	$canonical = get_page_by_path( 'all-villas' );
	if ( $canonical instanceof WP_Post && 'publish' === $canonical->post_status ) {
		return (int) $canonical->ID;
	}

	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-villa-listing.php',
			'orderby'        => [ 'menu_order' => 'ASC', 'post_title' => 'ASC' ],
		]
	);

	// Prefer a page with no Location or Minimum sleeps so a filtered
	// listing cannot become the site-wide search target when Site Options
	// is empty.
	foreach ( $pages as $page_id ) {
		if ( ! ibv_is_filtered_villa_listing( (int) $page_id ) ) {
			return (int) $page_id;
		}
	}

	return ! empty( $pages ) ? (int) $pages[0] : 0;
}

/**
 * Location term for a Villa Listing page, or null for the full collection.
 *
 * @param int $page_id Page ID. Current post if omitted.
 * @return WP_Term|null
 */
function ibv_get_villa_listing_location( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : (int) get_the_ID();
	if ( ! $page_id || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$term = get_field( 'listing_location', $page_id );

	return ( $term instanceof WP_Term ) ? $term : null;
}

/**
 * Minimum sleeps for a Villa Listing page, or 0 for no sleeps filter.
 *
 * @param int $page_id Page ID. Current post if omitted.
 * @return int
 */
function ibv_get_villa_listing_min_sleeps( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : (int) get_the_ID();
	if ( ! $page_id || ! function_exists( 'get_field' ) ) {
		return 0;
	}

	$raw = get_field( 'listing_min_sleeps', $page_id );
	if ( '' === $raw || null === $raw || false === $raw ) {
		return 0;
	}

	$n = (int) $raw;

	return $n >= 1 ? $n : 0;
}

/**
 * Whether a Villa Listing page is filtered (Location and/or Minimum sleeps).
 *
 * @param int $page_id Page ID. Current post if omitted.
 * @return bool
 */
function ibv_is_filtered_villa_listing( $page_id = 0 ) {
	return (bool) ibv_get_villa_listing_location( $page_id )
		|| ibv_get_villa_listing_min_sleeps( $page_id ) > 0;
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
 * Permalink of the page resolved by ibv_get_booking_confirmation_page_id().
 * Falls back to the canonical slug of the page-booking-confirmation.php template
 * page (/booking-request-received/) only if that page does not exist yet — using
 * the real slug, not a guess, so the fallback doesn't 404.
 *
 * @return string Escaped URL.
 */
function ibv_get_booking_confirmation_url() {
	$page_id = ibv_get_booking_confirmation_page_id();
	$base    = $page_id ? get_permalink( $page_id ) : home_url( '/booking-request-received/' );
	return esc_url( $base );
}

/**
 * Build the Gravity Forms confirmation that sends an enquiry to the booking
 * confirmation page. Villa and accommodation confirmations share this
 * helper so only the merge-tag query string differs.
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
 * Absolute URL of Bob's guest booking form.
 *
 * Expects a `bookingRef` GET param; without one it tells the guest to use
 * their confirmation-email link. The My Booking page collects the reference
 * and submits it here (the old theme did the same from a jQuery modal).
 *
 * @return string
 */
function ibv_get_bob_booking_form_url() {
	return 'https://ibizavillas2000.co.uk/cgi-bin/LIVE/bookingForm.pl';
}

/**
 * Format a single `villa_distances` ("Location tags") repeater row.
 *
 * The row is a single free-text tag ("3 mins from beach"). The old POI
 * taxonomy subfield is gone — it required terms nobody ever created, so
 * every label now lives whole in `distance_text`.
 *
 * @param array $row One row of the `villa_distances` repeater.
 * @return string Trimmed tag; empty if blank.
 */
function ibv_villa_distance_label( array $row ) {
	return isset( $row['distance_text'] ) ? trim( (string) $row['distance_text'] ) : '';
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

/**
 * Echo the short-breaks statement (IBZ002 copy log, Entry 13 — verbatim).
 *
 * Replaces the specced Short Breaks filter checkbox: short breaks is not a
 * flag on any villa (every villa qualifies; the team decides per enquiry),
 * so a control that never narrows the grid was removed as a deliberate
 * spec change. The fact survives as this plain statement — rendered on the
 * listing near the filters and again in the no-results empty state. Keep
 * the two in lockstep via this helper; it is a statement, never a control.
 *
 * @param string $class CSS class(es) for the wrapping <p>.
 */
/**
 * The response-time reassurance line (single source of truth).
 *
 * Site Options → Global → "Response-time note", falling back to the current
 * wording so the front end never goes blank while the exact promise is
 * still an open client decision. Returned without the "✓ " prefix — the
 * templates own that presentation.
 *
 * @return string
 */
function ibv_get_response_time_note() {
	$note = function_exists( 'get_field' ) ? trim( (string) get_field( 'global_response_time_note', 'option' ) ) : '';

	if ( '' !== $note ) {
		return $note;
	}

	return __( 'We respond within 20 minutes during our business hours', 'ibv' );
}

function ibv_the_short_breaks_statement( $class ) {
	printf(
		'<p class="%s"><strong>%s</strong> %s</p>',
		esc_attr( $class ),
		esc_html__( 'Staying just a few nights?', 'ibv' ),
		esc_html__( "Every villa is available for short breaks — put your dates in and we'll show you what's free.", 'ibv' )
	);
}
