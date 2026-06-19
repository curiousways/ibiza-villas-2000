<?php
/**
 * TEMPORARY one-shot form sync for no-SSH environments. ⚠️ DELETE AFTER USE.
 *
 * Why this exists: staging / production have no WP-CLI, so `wp ibv seed` can't be
 * run there. When present, this file runs ibv_seed_all() ONCE on the next
 * wp-admin load — creating the scaffolding pages (Booking confirmation, Airstream,
 * Hotel) if missing, then syncing the forms (resolving THAT server's booking-
 * confirmation page id) — then shows the result as an admin notice so you can
 * confirm it worked.
 *
 * Deploy workflow:
 *   1. Bump IBV_SEED_ONCE_TOKEN below when a form's config has changed.
 *   2. Push this file with your form changes.
 *   3. Load wp-admin on the server (as an admin) → the sync runs once and an
 *      admin notice confirms it ("IBV forms synced …").
 *   4. Push again with this file DELETED. Nothing else needs editing — bootstrap
 *      requires it behind a file_exists() guard, so removal is clean.
 *
 * Safe to leave temporarily: it runs at most once per token (gated by the
 * `ibv_forms_seed_once_token` option) and never on the front end. The permanent
 * pieces (the ibv_seed_*() functions + `wp ibv seed`) live in ibv-core and
 * stay — this file is the ONLY throwaway part.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bump this string whenever you want the server to re-sync (e.g. after changing
// a form). A new value forces exactly one more run on each environment.
if ( ! defined( 'IBV_SEED_ONCE_TOKEN' ) ) {
	define( 'IBV_SEED_ONCE_TOKEN', '2026-06-19-page-confirmations' );
}

/**
 * Run the sync once per token, in the admin only, for capable users.
 */
add_action(
	'admin_init',
	static function () {
		if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! function_exists( 'ibv_seed_all' ) || ! class_exists( 'GFAPI' ) ) {
			return;
		}
		if ( get_option( 'ibv_forms_seed_once_token' ) === IBV_SEED_ONCE_TOKEN ) {
			return; // already run for this token on this environment
		}

		$messages = ibv_seed_all();

		update_option( 'ibv_forms_seed_once_token', IBV_SEED_ONCE_TOKEN );
		update_option( 'ibv_forms_seed_once_result', $messages );
	}
);

/**
 * Show what happened so a no-SSH admin can confirm before deleting this file.
 */
add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( get_option( 'ibv_forms_seed_once_token' ) !== IBV_SEED_ONCE_TOKEN ) {
			return;
		}

		$result = (array) get_option( 'ibv_forms_seed_once_result', array() );
		if ( ! $result ) {
			return;
		}

		echo '<div class="notice notice-success"><p><strong>IBV pages + forms synced</strong> (token '
			. esc_html( IBV_SEED_ONCE_TOKEN ) . '):<br>'
			. esc_html( implode( ' · ', $result ) )
			. '.<br>You can now delete <code>includes/cli/seed-forms-once.php</code> on the next deploy.</p></div>';
	}
);
