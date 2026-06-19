<?php
/**
 * Seeder — "Accommodation Enquiry" Gravity Form (Hotel + Airstream).
 *
 * This form IS rendered (embedded via `ibv_core_gravity_form()`), so GF owns
 * submit / validation / notification / confirmation redirect. The
 * accommodation-enquiry section grafts
 * our single-field "When" date-range picker onto it: the form carries an HTML
 * field (the visible "When" pill) plus two CSS-classed date fields
 * (`ibv-drp-from` / `ibv-drp-to`) which are visually hidden and written to by
 * `accommodation-enquiry.js`. No PHP gform_* filters needed — the JS keys off
 * those classes.
 *
 * Field ids are explicit + stable. Idempotent UPSERT: updates the existing form
 * (id in `ibv_accommodation_enquiry_form_id`, else title match) in place so
 * re-running syncs the structure — this seeder is the single source of truth for
 * the form config (don't hand-edit it in the GF admin; edit here and re-apply).
 * Stores the id in `ibv_accommodation_enquiry_form_id` (convenience default;
 * the page ACF `accommodation_enquiry_gravity_form_id` is the per-page source).
 *
 * This file only DEFINES ibv_seed_accommodation_form(); nothing runs on include.
 * Apply it via `wp ibv seed` (local WP-CLI) or the one-shot deploy trigger
 * (seed-forms-once.php) on a no-SSH server.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_seed_accommodation_form' ) ) {
	/**
	 * Create or update the "Accommodation Enquiry" form to match this definition.
	 *
	 * @return string Human-readable status line.
	 */
	function ibv_seed_accommodation_form() {
		if ( ! class_exists( 'GFAPI' ) ) {
			return 'Accommodation Enquiry: GFAPI unavailable — is Gravity Forms active?';
		}

		$title = 'Accommodation Enquiry';

		// Visible "When" pill markup (single-field range trigger). Hidden GF date
		// fields below hold the actual values; accommodation-enquiry.js wires them.
		$when_html = '<div class="ibv-accommodation-enquiry__when" data-bob-date-range-anchor>'
			. '<button type="button" class="ibv-accommodation-enquiry__when-trigger" data-bob-date-range-trigger>'
			. '<span class="ibv-accommodation-enquiry__when-value is-empty" data-bob-date-range-display data-placeholder="When (optional)">When (optional)</span>'
			. '</button>'
			. '<button type="button" class="ibv-accommodation-enquiry__when-clear" data-bob-date-range-clear hidden aria-label="Clear dates"><span aria-hidden="true">&times;</span></button>'
			. '</div>';

		$form = array(
			'title'          => $title,
			'description'    => '',
			'labelPlacement' => 'hidden_label',
			'enableHoneypot' => true,
			// GFAPI::update_form() rewrites is_active from this key on every run —
			// omitting it deactivates the form (and it stops rendering). Keep it set.
			'is_active'      => 1,
			'button'         => array(
				'type' => 'text',
				'text' => 'Send Enquiry',
			),
			'fields'         => array(
				array( 'id' => 1, 'type' => 'text',     'label' => 'Name',  'placeholder' => 'Name*',  'isRequired' => true ),
				array( 'id' => 2, 'type' => 'email',    'label' => 'Email', 'placeholder' => 'Email*', 'isRequired' => true ),
				array( 'id' => 3, 'type' => 'phone',    'label' => 'Phone', 'placeholder' => 'Phone*', 'phoneFormat' => 'international', 'isRequired' => true ),
				// Date-range picker graft — REQUIRED by accommodation-enquiry.js: the
				// HTML "When" field (#8) is the visible trigger; the two date fields
				// (#4/#5) MUST keep the ibv-drp-from / ibv-drp-to classes (the JS binds
				// to `.ibv-drp-from input` / `.ibv-drp-to input`). Removing/renaming
				// them silently disables the picker. They are intentionally optional.
				//
				// `datepicker` (single text input) — NOT `datefield` (3 M/D/Y sub-inputs):
				// the JS writes one YYYY-MM-DD value into the single input, so GF stores it
				// and the confirmation merge tags ({Arrival:4}/{Departure:5}) resolve. With
				// `datefield` the JS fills only the first sub-input and the dates submit
				// blank (the booking page then drops them). Mirrors the Villa Enquiry form.
				array( 'id' => 8, 'type' => 'html',      'label' => 'Dates', 'content' => $when_html ),
				array( 'id' => 4, 'type' => 'date',      'label' => 'Arrival',   'dateType' => 'datepicker', 'dateFormat' => 'ymd_dash', 'isRequired' => false, 'cssClass' => 'ibv-drp-from ibv-drp-hidden' ),
				array( 'id' => 5, 'type' => 'date',      'label' => 'Departure', 'dateType' => 'datepicker', 'dateFormat' => 'ymd_dash', 'isRequired' => false, 'cssClass' => 'ibv-drp-to ibv-drp-hidden' ),
				array( 'id' => 6, 'type' => 'number',    'label' => 'Number of guests', 'placeholder' => 'Number of guests (optional)' ),
				array( 'id' => 7, 'type' => 'textarea',  'label' => 'Message', 'placeholder' => 'Message' ),
				array( 'id' => 9, 'type' => 'hidden',    'label' => 'Accommodation', 'allowsPrepopulate' => true, 'inputName' => 'ibv_accommodation' ),
			),
			// "Page" confirmation → the booking-confirmation page, built by
			// ibv_build_gf_booking_confirmation() (resolves the page by template; see
			// helpers.php). Mirrors the Villa Enquiry flow. Carries arrival / departure /
			// guests (field ids 4 / 5 / 6) so the page's booking-details panel shows the
			// requested dates and party size. No `villa` param (an accommodation enquiry
			// has no villa post id) and no `offer`; the page renders only the rows whose
			// params validate, so empty/optional fields simply drop their row.
			'confirmations'  => array(
				'ibv_accommodation_redirect' => ibv_build_gf_booking_confirmation(
					'ibv_accommodation_redirect',
					'arrival={Arrival:4}&departure={Departure:5}&guests={Number of guests:6}'
				),
			),
			'notifications'  => array(
				'ibv_accommodation_admin' => array(
					'id'       => 'ibv_accommodation_admin',
					'isActive' => true,
					'name'     => 'Admin Notification',
					'event'    => 'form_submission',
					'toType'   => 'email',
					'to'       => 'bookings@ibizavillas2000.com',
					'replyTo'  => '{Email:2}',
					'subject'  => 'Accommodation Enquiry - {Name:1}',
					'message'  => '{all_fields}',
				),
			),
		);

		// Resolve an existing form id: option first, then a title match (legacy create).
		$existing_id = (int) get_option( 'ibv_accommodation_enquiry_form_id' );
		if ( ! $existing_id || ! GFAPI::get_form( $existing_id ) ) {
			$existing_id = 0;
			foreach ( GFAPI::get_forms() as $candidate ) {
				if ( isset( $candidate['title'] ) && $candidate['title'] === $title ) {
					$existing_id = (int) $candidate['id'];
					break;
				}
			}
		}

		if ( $existing_id ) {
			$form['id'] = $existing_id;
			$result     = GFAPI::update_form( $form, $existing_id );
			if ( is_wp_error( $result ) ) {
				return 'Error updating Accommodation Enquiry form: ' . $result->get_error_message();
			}
			update_option( 'ibv_accommodation_enquiry_form_id', $existing_id );
			return 'Updated Accommodation Enquiry form id: ' . $existing_id;
		}

		$result = GFAPI::add_form( $form );
		if ( is_wp_error( $result ) ) {
			return 'Error creating Accommodation Enquiry form: ' . $result->get_error_message();
		}

		update_option( 'ibv_accommodation_enquiry_form_id', (int) $result );
		return 'Created Accommodation Enquiry form id: ' . (int) $result;
	}
}
