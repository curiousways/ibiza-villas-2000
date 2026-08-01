<?php
/**
 * Cookie consent — orestbida vanilla-cookieconsent v3 (UMD, self-hosted).
 *
 * Site-wide: banner + preferences modal, and the consent-gated GA4 bootstrap.
 * Categories: necessary (read-only) + analytics. No marketing category — the
 * new stack ships no ad/remarketing pixels.
 *
 * Handles are registered in includes/shared-assets.php (`ibv-cookieconsent`,
 * `ibv-cookieconsent-init`) alongside every other shared asset; this file only
 * decides when they load and prints the gated GA4 pair.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'ibv_core_cookieconsent_enqueue' );

/**
 * Load the consent banner on every front-end view.
 */
function ibv_core_cookieconsent_enqueue() {
	wp_enqueue_style( 'ibv-cookieconsent' );
	wp_enqueue_script( 'ibv-cookieconsent-init' );
}

add_action( 'wp_head', 'ibv_core_ga4_gated', 20 );

/**
 * Print the GA4 tag pair, consent-gated.
 *
 * `type="text/plain"` + `data-category="analytics"` stops the browser
 * executing either tag; CookieConsent's script-tag manager flips the type and
 * re-inserts them once analytics consent is given (and strips them again on
 * withdrawal). Printed by hand because wp_enqueue_script() cannot emit a tag
 * with a non-executable type.
 *
 * ga4.js additionally refuses to run on local/staging hostnames, so consent on
 * a non-live domain still results in zero GA traffic.
 */
function ibv_core_ga4_gated() {
	// GA4 stream for the live site (ibizavillas2000.com). The same ID is
	// hard-coded in assets/js/ga4.js — keep the two in sync.
	$ga4_id = 'G-NW8WQP42F9';

	$loader = 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $ga4_id );
	$boot   = IBV_CORE_URL . 'assets/js/ga4.js?ver=' . IBV_CORE_VERSION;

	printf(
		'<script async src="%s" type="text/plain" data-category="analytics" data-service="Google Analytics"></script>' . "\n",
		esc_url( $loader )
	);
	printf(
		'<script src="%s" type="text/plain" data-category="analytics" data-service="Google Analytics"></script>' . "\n",
		esc_url( $boot )
	);
}
