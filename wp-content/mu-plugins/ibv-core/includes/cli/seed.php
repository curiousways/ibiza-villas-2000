<?php
/**
 * Seeding orchestrator (permanent).
 *
 * The site scaffolding that must exist for the enquiry flow — the fixed pages
 * (Booking confirmation, Airstream, Hotel) and the Gravity Forms (Villa Enquiry,
 * Accommodation Enquiry) — is defined in code. This file exposes the run surface:
 *
 *   - Locally (WP-CLI):   wp ibv seed
 *   - No-SSH server:      the one-shot deploy trigger (seed-forms-once.php), which
 *                         calls ibv_seed_all() once on admin_init.
 *
 * Order matters: PAGES FIRST, then forms. The forms' "Page" confirmation resolves
 * the booking-confirmation page id by template (ibv_build_gf_booking_confirmation()),
 * so that page must already exist when the forms are seeded.
 *
 * All steps are idempotent: pages are create-if-missing + non-destructive; forms
 * are an UPSERT keyed by the stored option / title.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_seed_all_forms' ) ) {
	/**
	 * Run every form seeder and collect their status lines.
	 *
	 * @return string[] One status message per form.
	 */
	function ibv_seed_all_forms() {
		$messages = array();

		if ( function_exists( 'ibv_seed_villa_enquiry_form' ) ) {
			$messages[] = ibv_seed_villa_enquiry_form();
		}
		if ( function_exists( 'ibv_seed_accommodation_form' ) ) {
			$messages[] = ibv_seed_accommodation_form();
		}

		return $messages;
	}
}

if ( ! function_exists( 'ibv_seed_all' ) ) {
	/**
	 * Seed everything: scaffolding pages first, then the enquiry forms.
	 *
	 * @return string[] Combined status lines.
	 */
	function ibv_seed_all() {
		$messages = array();

		if ( function_exists( 'ibv_seed_pages' ) ) {
			$messages = array_merge( $messages, ibv_seed_pages() );
		}

		return array_merge( $messages, ibv_seed_all_forms() );
	}
}

// Local convenience command (no-op when WP-CLI is not running).
if ( class_exists( 'WP_CLI', false ) ) {
	WP_CLI::add_command(
		'ibv seed',
		static function () {
			if ( ! class_exists( 'GFAPI' ) ) {
				WP_CLI::error( 'GFAPI unavailable — is Gravity Forms active?' );
			}
			foreach ( ibv_seed_all() as $line ) {
				WP_CLI::log( $line );
			}
			WP_CLI::success( 'Scaffolding pages + enquiry forms synced from code.' );
		}
	);
}
