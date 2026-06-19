<?php
/**
 * Seeder — ensure the fixed scaffolding pages exist (no-SSH friendly).
 *
 * Creates the pages the enquiry flow depends on IF they are missing, then stops:
 *
 *   - Booking confirmation (template page-booking-confirmation.php) — the GF
 *     "Page" confirmation target. The form seeder resolves its id by template,
 *     so this page must exist BEFORE the forms are seeded; the orchestrator
 *     (ibv_seed_all()) runs pages first for exactly this reason.
 *   - Airstream + Hotel (template page-accommodation.php) — the embedded
 *     accommodation form wires itself via the global form-id option fallback
 *     (see ibv_core_section_accommodation_enquiry()), so no per-page field is set.
 *
 * Creates structure (title, slug, template, published) plus the page-IDENTITY
 * text fields (hero title/subtitle, overview + enquiry headings, price note) so a
 * fresh Hotel page doesn't inherit the Airstream field defaults. Heavy editorial
 * content — hero image, gallery, overview body, fact pills, "from" price — is NOT
 * seeded (images/attachment ids are environment-specific; that copy lives in
 * wp-admin). The identity text is written ON CREATE ONLY. Idempotent and
 * NON-DESTRUCTIVE: an existing page (matched per its rule) is left completely
 * untouched, never overwritten — so this never clobbers content you've added.
 *
 * This file only DEFINES ibv_seed_pages(); nothing runs on include. Apply it via
 * `wp ibv seed` (local WP-CLI) or the one-shot deploy trigger (seed-forms-once.php).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_seed_pages' ) ) {
	/**
	 * Ensure the scaffolding pages exist. Returns one status line per page.
	 *
	 * @return string[]
	 */
	function ibv_seed_pages() {
		$specs = array(
			array(
				'title'    => 'Booking Request Received',
				'slug'     => 'booking-request-received',
				'template' => 'page-booking-confirmation.php',
				'match'    => 'template', // unique template; the slug is editorial
			),
			array(
				'title'    => 'Airstream',
				'slug'     => 'airstream',
				'template' => 'page-accommodation.php',
				'match'    => 'slug', // template is shared with Hotel
				// Page-identity text only (set on CREATE, never on update). Images,
				// gallery, overview body, fact pills + price stay editorial (admin).
				'acf'      => array(
					'field_ibv_page_accom_hero_title'       => 'Airstreams',
					'field_ibv_page_accom_hero_subtitle'    => 'Iconic Trailers on the beach at Camping La Playa, Cala Martina',
					'field_ibv_page_accom_overview_heading' => 'About Airstream',
					'field_ibv_page_accom_enquiry_heading'  => 'Enquire about the airstreams',
					'field_ibv_page_accom_from_price_note'  => 'Price varies by season',
				),
			),
			array(
				'title'    => 'Hotel',
				'slug'     => 'hotel',
				'template' => 'page-accommodation.php',
				'match'    => 'slug',
				'acf'      => array(
					'field_ibv_page_accom_hero_title'       => 'Hotel',
					'field_ibv_page_accom_hero_subtitle'    => 'Our wonderful apartments at Aparthotel Marian Ibiza',
					'field_ibv_page_accom_overview_heading' => 'Ibiza begins in hotel Marian apartments',
					'field_ibv_page_accom_enquiry_heading'  => 'Enquire about the hotel',
					'field_ibv_page_accom_from_price_note'  => 'Price varies by season',
				),
			),
		);

		$messages = array();

		foreach ( $specs as $spec ) {
			$existing_id = ibv_find_seeded_page( $spec );

			if ( $existing_id ) {
				$messages[] = sprintf( 'Page exists: %s (id %d) — left untouched', $spec['slug'], $existing_id );
				continue;
			}

			$new_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $spec['title'],
					'post_name'    => $spec['slug'],
					'post_content' => '', // template composes via sections + ACF
				),
				true
			);

			if ( is_wp_error( $new_id ) ) {
				$messages[] = sprintf( 'Error creating page %s: %s', $spec['slug'], $new_id->get_error_message() );
				continue;
			}

			update_post_meta( $new_id, '_wp_page_template', $spec['template'] );

			// Seed page-identity text ON CREATE ONLY (this branch never runs for an
			// existing page), so editor changes are never overwritten. By field key
			// (from register-page-accommodation.php) for unambiguous ACF writes.
			if ( ! empty( $spec['acf'] ) && function_exists( 'update_field' ) ) {
				foreach ( $spec['acf'] as $field_key => $value ) {
					update_field( $field_key, $value, $new_id );
				}
			}

			$messages[] = sprintf( 'Created page: %s (id %d, %s)', $spec['slug'], $new_id, $spec['template'] );
		}

		return $messages;
	}
}

if ( ! function_exists( 'ibv_find_seeded_page' ) ) {
	/**
	 * Locate an existing scaffolding page per its match rule.
	 *
	 * @param array $spec Page spec (slug, template, match).
	 * @return int Page ID, or 0 if not found.
	 */
	function ibv_find_seeded_page( array $spec ) {
		if ( 'template' === $spec['match'] ) {
			$found = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'meta_key'       => '_wp_page_template',
					'meta_value'     => $spec['template'],
				)
			);
			return ! empty( $found ) ? (int) $found[0] : 0;
		}

		$page = get_page_by_path( $spec['slug'] );
		return $page ? (int) $page->ID : 0;
	}
}
