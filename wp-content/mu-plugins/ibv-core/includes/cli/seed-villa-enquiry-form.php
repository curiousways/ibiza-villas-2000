<?php
/**
 * Seeder — "Villa Enquiry" Gravity Form (#33), embedded on the villa detail page.
 *
 * The villa enquiry panel is now an EMBEDDED Gravity Form (same principle as the
 * accommodation enquiry form), with the live Steve pricing / availability gate
 * grafted on by enquiry-panel.js. GF owns submit / validation / notification /
 * confirmation; this seeder is the single source of truth for the form config.
 *
 * Graft contract (keep in sync with enquiry-panel.php + enquiry-panel.js):
 *   - HTML "When" field (#10) — the visible single-field range trigger.
 *   - Two date fields #5 / #6 (ymd_dash) carrying ibv-drp-from / ibv-drp-to —
 *     hidden via CSS; the picker writes the YYYY-MM-DD range into them. They are
 *     dynamically populated from the search params (date_from / date_to) via the
 *     ibv_arrival / ibv_departure input names.
 *   - Guests select #7 (ibv-pax, populated from `pax`) — JS reads it for pricing.
 *   - HTML price field #11 (ibv-enquiry-panel__price-field) + notice field #14 —
 *     the pricing JS paints into the data-bob-* targets inside them.
 *   - Contact fields #2 / #3 / #4 / #8 carry ibv-contact-field — hidden by CSS
 *     until availability resolves (the gate).
 *   - Hidden Villa ID #12 (ibv_villa_id) — the post id, dynamically populated; it
 *     keys the redirect + the server-side Property Name / Active Offers lookup.
 *   - Hidden Property Name #1 + Active Offers #9 — SERVER-SET in enquiry-panel.php
 *     via gform_pre_submission (not user input), so they're authoritative.
 *
 * "Page" confirmation → the booking-confirmation page, built by
 * ibv_build_gf_booking_confirmation() (resolves the page by the
 * page-booking-confirmation.php template — currently /booking-request-received/),
 * passing the villa / arrival / departure / guests / offer query string it reads.
 * GF redirects by page id at submit time, so it survives slug + domain changes.
 *
 * Idempotent UPSERT: updates the existing form (id in `ibv_villa_enquiry_form_id`,
 * else title match) in place so re-running syncs the structure; there are no real
 * entries pre-launch. Stores the id in `ibv_villa_enquiry_form_id`.
 *
 * This file only DEFINES ibv_seed_villa_enquiry_form(); nothing runs on include.
 * Apply it via `wp ibv seed` (local WP-CLI) or the one-shot deploy trigger
 * (seed-forms-once.php) on a no-SSH server.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_seed_villa_enquiry_form' ) ) {
	/**
	 * Create or update the "Villa Enquiry" form to match this definition.
	 *
	 * @return string Human-readable status line.
	 */
	function ibv_seed_villa_enquiry_form() {
		if ( ! class_exists( 'GFAPI' ) ) {
			return 'Villa Enquiry: GFAPI unavailable — is Gravity Forms active?';
		}

		$title = 'Villa Enquiry';

		// Visible "When" pill (single-field range trigger). The two hidden GF date
		// fields below hold the actual values; enquiry-panel.js wires them.
		$when_html = '<div class="ibv-enquiry-panel__field ibv-enquiry-panel__field--when" data-bob-date-range-anchor>'
			. '<button type="button" class="ibv-enquiry-panel__when-trigger" data-bob-date-range-trigger>'
			. '<span class="ibv-enquiry-panel__when-value is-empty" data-bob-date-range-display data-placeholder="When">When</span>'
			. '</button>'
			. '<button type="button" class="ibv-enquiry-panel__when-clear" data-bob-date-range-clear hidden aria-label="Clear dates"><span aria-hidden="true">&times;</span></button>'
			. '</div>';

		// Price breakdown — EUR only (per spec). enquiry-panel.js paints into the
		// data-bob-* targets; the whole field is hidden until pricing resolves.
		$price_html = '<div class="ibv-enquiry-panel__price-block">'
			. '<p class="ibv-enquiry-panel__price-label">Total price</p>'
			. '<ul class="ibv-enquiry-panel__price-list">'
			. '<li class="ibv-enquiry-panel__price-list-item ibv-enquiry-panel__price-eur" data-bob-total-eur></li>'
			. '</ul>'
			. '<ul class="ibv-enquiry-panel__breakdown">'
			. '<li class="ibv-enquiry-panel__breakdown-item"><span data-bob-base-rental></span> base rental</li>'
			. '<li class="ibv-enquiry-panel__breakdown-item"><span data-bob-adw></span> ADW (damage waiver)</li>'
			. '<li class="ibv-enquiry-panel__breakdown-item"><span data-bob-cleaning></span> cleaning fee</li>'
			. '</ul>'
			. '<p class="ibv-enquiry-panel__eco-note">Total does not include the government Eco Tax of &euro;2.20 per person, per night, payable in resort.</p>'
			. '</div>';

		// Availability / pricing notice (unavailable, fetch failed). Toggled by JS.
		$notice_html = '<div class="ibv-enquiry-panel__error" data-bob-error role="status" aria-live="polite" hidden></div>';

		// Guests choices 1–12.
		$guest_choices = array();
		for ( $i = 1; $i <= 12; $i++ ) {
			$guest_choices[] = array(
				'text'       => (string) $i,
				'value'      => (string) $i,
				'isSelected' => false,
			);
		}

		$form = array(
			'title'          => $title,
			'description'    => '',
			// Embedded form — MUST be active or GF renders nothing. GFAPI::update_form
			// rewrites is_active from this key on every run (omitting it deactivates).
			'is_active'      => 1,
			'labelPlacement' => 'hidden_label',
			'enableHoneypot' => true,
			'button'         => array(
				'type' => 'text',
				'text' => 'Request to Book',
			),
			'fields'         => array(

				// ── When picker graft ─────────────────────────────────────────
				array( 'id' => 10, 'type' => 'html', 'label' => 'Dates', 'content' => $when_html ),
				// `datepicker` (single text input) — NOT `datefield` (3 M/D/Y sub-inputs):
				// the grafted picker writes one YYYY-MM-DD value into the single input, so
				// GF stores it correctly and the redirect/notification merge tags resolve.
				array(
					'id'               => 5,
					'type'             => 'date',
					'label'            => 'Arrival',
					'dateType'         => 'datepicker',
					'dateFormat'       => 'ymd_dash',
					'isRequired'       => false,
					'cssClass'         => 'ibv-drp-from ibv-drp-hidden',
					'allowsPrepopulate' => true,
					'inputName'        => 'ibv_arrival',
				),
				array(
					'id'               => 6,
					'type'             => 'date',
					'label'            => 'Departure',
					'dateType'         => 'datepicker',
					'dateFormat'       => 'ymd_dash',
					'isRequired'       => false,
					'cssClass'         => 'ibv-drp-to ibv-drp-hidden',
					'allowsPrepopulate' => true,
					'inputName'        => 'ibv_departure',
				),

				// ── Guests (drives pricing; gate enforces it client-side) ──────
				// CONTRACT for ibv_gf_pax_placeholder_not_selectable() (helpers/
				// gravity-form.php): keep type=select + the `ibv-pax` class + a
				// non-empty placeholder, and never add an empty-value choice. That
				// filter makes the sole empty <option> (the placeholder) disabled/
				// hidden; break any of those and "Guests" becomes selectable again.
				array(
					'id'                => 7,
					'type'              => 'select',
					'label'             => 'Number of guests',
					'placeholder'       => 'Guests',
					'isRequired'        => true,
					'cssClass'          => 'ibv-pax',
					'allowsPrepopulate' => true,
					'inputName'         => 'ibv_pax',
					'choices'           => $guest_choices,
				),

				// ── Live price breakdown + notice (painted by JS) ──────────────
				array( 'id' => 11, 'type' => 'html', 'label' => 'Pricing', 'content' => $price_html, 'cssClass' => 'ibv-enquiry-panel__price-field' ),
				array( 'id' => 14, 'type' => 'html', 'label' => 'Notice', 'content' => $notice_html, 'cssClass' => 'ibv-enquiry-panel__notice-field' ),

				// ── Contact fields (revealed once availability resolves) ───────
				array( 'id' => 2, 'type' => 'text',     'label' => 'Name',  'placeholder' => 'Name',  'isRequired' => true, 'cssClass' => 'ibv-contact-field' ),
				array( 'id' => 3, 'type' => 'email',    'label' => 'Email', 'placeholder' => 'Email', 'isRequired' => true, 'cssClass' => 'ibv-contact-field' ),
				array( 'id' => 4, 'type' => 'phone',    'label' => 'Phone', 'placeholder' => 'Phone', 'phoneFormat' => 'international', 'isRequired' => true, 'cssClass' => 'ibv-contact-field' ),
				array( 'id' => 8, 'type' => 'textarea', 'label' => 'Message', 'placeholder' => 'Message (Optional)', 'cssClass' => 'ibv-contact-field' ),

				// ── Hidden plumbing ────────────────────────────────────────────
				array( 'id' => 12, 'type' => 'hidden', 'label' => 'Villa ID', 'allowsPrepopulate' => true, 'inputName' => 'ibv_villa_id' ),
				array( 'id' => 1,  'type' => 'hidden', 'label' => 'Property Name' ), // server-set (gform_pre_submission)
				array( 'id' => 9,  'type' => 'hidden', 'label' => 'Active Offers' ), // server-set (gform_pre_submission)
			),
			'confirmations'  => array(
				'ibv_villa_redirect' => ibv_build_gf_booking_confirmation(
					'ibv_villa_redirect',
					'villa={Villa ID:12}&arrival={Arrival:5}&departure={Departure:6}&guests={Guests:7}&offer={Active Offers:9}'
				),
			),
			'notifications'  => array(
				'ibv_villa_admin' => array(
					'id'       => 'ibv_villa_admin',
					'isActive' => true,
					'name'     => 'Admin Notification',
					'event'    => 'form_submission',
					'toType'   => 'email',
					'to'       => 'bookings@ibizavillas2000.com',
					'replyTo'  => '{Email:3}',
					'subject'  => 'Ibiza Villas 2000 Enquiry - {Name:2} - {Property Name:1}',
					'message'  => '{all_fields}',
				),
			),
		);

		// Resolve an existing form id: option first, then a title match (legacy create).
		$existing_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
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
				return 'Error updating Villa Enquiry form: ' . $result->get_error_message();
			}
			update_option( 'ibv_villa_enquiry_form_id', $existing_id );
			return 'Updated Villa Enquiry form id: ' . $existing_id;
		}

		$result = GFAPI::add_form( $form );
		if ( is_wp_error( $result ) ) {
			return 'Error creating Villa Enquiry form: ' . $result->get_error_message();
		}

		update_option( 'ibv_villa_enquiry_form_id', (int) $result );
		return 'Created Villa Enquiry form id: ' . (int) $result;
	}
}
