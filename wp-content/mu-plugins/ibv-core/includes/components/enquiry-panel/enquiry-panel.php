<?php
/**
 * Component: Villa enquiry / RTB panel — embedded Gravity Form.
 *
 * The villa enquiry form is an EMBEDDED Gravity Form (id in
 * `ibv_villa_enquiry_form_id`). GF is the single source of truth (fields,
 * validation, notification, entry, and the booking-confirmation redirect).
 * The villa-specific live pricing / availability gate is grafted on top by
 * enquiry-panel.js, the same way the date-range picker and phone widget are
 * grafted onto the accommodation form.
 *
 * This file wires the server side of that graft:
 *   - gform_field_value_* prepopulation (render time): the villa post id, plus the
 *     search params (date_from / date_to / pax) so an arrival-via-search pre-fills
 *     the form and auto-fetches pricing.
 *   - gform_pre_submission (submit time): Property Name + Active Offers are set
 *     server-side from the validated villa id, so attribution is authoritative
 *     (not user-editable) — the same trust model the old REST handler had.
 *
 * Filters are registered at file load (not inside the render fn) so they are
 * present when GF processes the AJAX submission early on the `wp` hook.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a strict YYYY-MM-DD search param from $_GET, or '' if absent/invalid.
 *
 * @param string $key Query var name.
 * @return string
 */
function ibv_enquiry_panel_get_date_param( $key ) {
	if ( ! isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only prefill.
		return '';
	}
	$raw = sanitize_text_field( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
}

/**
 * Resolve `?offer=` against the queried villa's active offers.
 *
 * Exact `offer_name` match only. Cached per request so the date
 * prefills and the panel data attributes stay in lockstep.
 *
 * @return array|null Active repeater row or null.
 */
function ibv_enquiry_panel_get_resolved_offer() {
	static $done  = false;
	static $offer = null;
	if ( $done ) {
		return $offer;
	}
	$done = true;
	if ( ! is_singular( 'villas' ) || ! isset( $_GET['offer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only prefill.
		return null;
	}
	$key = sanitize_text_field( wp_unslash( $_GET['offer'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' === $key || ! function_exists( 'ibv_villa_find_active_offer' ) ) {
		return null;
	}
	$offer = ibv_villa_find_active_offer( get_queried_object_id(), $key );
	return $offer;
}

// ── Prepopulation (render time, single villa pages only) ────────────────────

add_filter(
	'gform_field_value_ibv_villa_id',
	static function ( $value ) {
		if ( ! is_singular( 'villas' ) ) {
			return $value;
		}
		$id = get_queried_object_id();
		return $id ? (string) $id : $value;
	}
);

add_filter(
	'gform_field_value_ibv_arrival',
	static function ( $value ) {
		if ( ! is_singular( 'villas' ) ) {
			return $value;
		}
		$offer = ibv_enquiry_panel_get_resolved_offer();
		if ( $offer && function_exists( 'ibv_villa_offer_iso_date' ) ) {
			$d = ibv_villa_offer_iso_date( (string) ( $offer['offer_date_from'] ?? '' ) );
			if ( '' !== $d ) {
				return $d;
			}
		}
		$d = ibv_enquiry_panel_get_date_param( 'date_from' );
		return '' !== $d ? $d : $value;
	}
);

add_filter(
	'gform_field_value_ibv_departure',
	static function ( $value ) {
		if ( ! is_singular( 'villas' ) ) {
			return $value;
		}
		$offer = ibv_enquiry_panel_get_resolved_offer();
		if ( $offer && function_exists( 'ibv_villa_offer_iso_date' ) ) {
			$d = ibv_villa_offer_iso_date( (string) ( $offer['offer_date_to'] ?? '' ) );
			if ( '' !== $d ) {
				return $d;
			}
		}
		$d = ibv_enquiry_panel_get_date_param( 'date_to' );
		return '' !== $d ? $d : $value;
	}
);

add_filter(
	'gform_field_value_ibv_pax',
	static function ( $value ) {
		if ( ! is_singular( 'villas' ) ) {
			return $value;
		}
		if ( ! isset( $_GET['pax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only prefill.
			return $value;
		}
		$pax = absint( wp_unslash( $_GET['pax'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( $pax >= 1 && $pax <= 12 ) ? (string) $pax : $value;
	}
);

// ── Server-authoritative attribution (submit time) ──────────────────────────

/**
 * Set Property Name (field 1) + Active Offers (field 9) from the validated villa
 * id (hidden field 12) before the entry is saved / notified / redirected. This is
 * the authoritative source — the prepopulated hidden id is client-supplied but
 * re-validated here, exactly as the old REST handler did.
 */
add_action(
	'gform_pre_submission',
	static function ( $form ) {
		$villa_form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
		if ( ! $villa_form_id || (int) $form['id'] !== $villa_form_id ) {
			return;
		}

		$villa_id    = isset( $_POST['input_12'] ) ? absint( wp_unslash( $_POST['input_12'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- GF owns nonce/honeypot for this form.
		$villa_name  = '';
		$offer_names = '';

		if ( $villa_id ) {
			$villa = get_post( $villa_id );
			if ( $villa && 'villas' === $villa->post_type && 'publish' === $villa->post_status ) {
				$pretty     = (string) get_field( 'villa_pretty_name', $villa_id );
				$villa_name = sanitize_text_field( $pretty ? $pretty : get_the_title( $villa_id ) );

				if ( function_exists( 'ibv_villa_get_active_offers' ) ) {
					$posted = isset( $_POST['input_9'] ) ? sanitize_text_field( wp_unslash( $_POST['input_9'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- GF owns nonce/honeypot for this form.
					$names  = array();
					$match  = '';
					foreach ( ibv_villa_get_active_offers( $villa_id ) as $offer ) {
						$name = sanitize_text_field( (string) ( $offer['offer_name'] ?? '' ) );
						if ( '' === $name ) {
							continue;
						}
						$names[] = $name;
						if ( '' !== $posted && $posted === $name ) {
							$match = $name;
						}
					}
					$offer_names = '' !== $match ? $match : implode( ', ', $names );
				}
			}
		}

		$_POST['input_1'] = $villa_name;  // Property Name.
		$_POST['input_9'] = $offer_names; // Active Offers: one real offer, or all.
	}
);

// ── Submit button: render as <button> with the design's arrow icon ──────────

/**
 * Rewrite the villa enquiry submit into a design-system button with an
 * inline arrow. Handles both GF's legacy <input> and modern <button>
 * markup (see ibv_core_gform_submit_button_with_arrow()). Registered on
 * the generic `gform_submit_button` at file load and scoped to the villa
 * form id so the arrow survives GF's AJAX re-render. Full-width teal
 * styling lives in enquiry-panel.css.
 */
add_filter(
	'gform_submit_button',
	static function ( $button, $form ) {
		$villa_form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
		if ( ! $villa_form_id || (int) $form['id'] !== $villa_form_id ) {
			return $button;
		}
		$label = isset( $form['button']['text'] ) && '' !== $form['button']['text']
			? (string) $form['button']['text']
			: __( 'Request to Book', 'ibv' );

		return ibv_core_gform_submit_button_with_arrow(
			$button,
			$label,
			'ibv-enquiry-panel__submit-label'
		);
	},
	10,
	2
);

/**
 * Note under the Guests select: groups of 12+ go to the Contact page.
 *
 * Same destination as the listing-hero note (`ibv_get_contact_page_url()`).
 * Shown on every villa, regardless of sleeps capacity.
 *
 * @param string   $content Field HTML.
 * @param GF_Field $field   Field object.
 * @param mixed    $value   Posted / default value.
 * @param int      $lead_id Entry id (0 on render).
 * @param int      $form_id Form id.
 * @return string
 */
function ibv_enquiry_panel_pax_group_note( $content, $field, $value, $lead_id, $form_id ) {
	$villa_form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
	if ( ! $villa_form_id || (int) $form_id !== $villa_form_id ) {
		return $content;
	}
	if ( ! is_object( $field ) || 'select' !== (string) $field->type ) {
		return $content;
	}
	$classes = preg_split( '/\s+/', (string) $field->cssClass, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! in_array( 'ibv-pax', (array) $classes, true ) ) {
		return $content;
	}

	$note = sprintf(
		'<p class="ibv-enquiry-panel__group-note"><strong>%s</strong> <a href="%s">%s</a></p>',
		esc_html__( 'Looking for 12 or more guests?', 'ibv' ),
		ibv_get_contact_page_url(),
		esc_html__( 'Contact us', 'ibv' )
	);

	return $content . $note;
}
add_filter( 'gform_field_content', 'ibv_enquiry_panel_pax_group_note', 20, 5 );

/**
 * Render the villa enquiry panel (embedded GF + grafted pricing/gate + chrome).
 *
 * @param int $villa_id Villa post ID.
 */
function ibv_core_enquiry_panel( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id || 'villas' !== get_post_type( $villa_id ) ) {
		return;
	}

	$form_id      = (int) get_option( 'ibv_villa_enquiry_form_id' );
	$property_id  = (string) get_field( 'property_id', $villa_id );
	$endpoint_url = 'https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl';

	wp_enqueue_style( 'ibv-enquiry-panel' );

	$iti_utils_url = '';
	if ( $form_id ) {
		// Picker popover CSS + phone widget; the JS handle pulls in the calendar
		// lib + intl-tel-input via its registered deps.
		wp_enqueue_style( 'ibv-date-range-picker' );
		wp_enqueue_style( 'ibv-intl-tel-input' );
		wp_enqueue_script( 'ibv-enquiry-panel' );
		$iti_utils_url = add_query_arg(
			'ver',
			rawurlencode( IBV_CORE_VERSION ),
			IBV_CORE_URL . 'includes/components/enquiry-panel/vendor/intl-tel-input/js/utils.js'
		);
	}

	$whatsapp = get_field( 'whatsapp_number', 'option' );
	$digits   = $whatsapp ? preg_replace( '/[^0-9]/', '', (string) $whatsapp ) : '';
	$wa_url   = $digits ? 'https://wa.me/' . $digits : '';

	$resolved_offer = ( $villa_id === get_queried_object_id() )
		? ibv_enquiry_panel_get_resolved_offer()
		: null;
	$resolved_name  = $resolved_offer ? trim( (string) ( $resolved_offer['offer_name'] ?? '' ) ) : '';
	$resolved_from  = ( $resolved_offer && function_exists( 'ibv_villa_offer_iso_date' ) )
		? ibv_villa_offer_iso_date( (string) ( $resolved_offer['offer_date_from'] ?? '' ) )
		: '';
	$resolved_to    = ( $resolved_offer && function_exists( 'ibv_villa_offer_iso_date' ) )
		? ibv_villa_offer_iso_date( (string) ( $resolved_offer['offer_date_to'] ?? '' ) )
		: '';
	?>
	<?php /* ─────────────────────────────────────────────────────────────
	       BOB API INTEGRATION SHELL — villa enquiry panel
	       ─────────────────────────────────────────────────────────────
	       Embedded Gravity Form (#33) = single source of truth (submit /
	       validate / notify / redirect). enquiry-panel.js grafts on:
	         - the single-field "When" date-range picker (writes the
	           YYYY-MM-DD range into the .ibv-drp-from / .ibv-drp-to inputs)
	         - live pricing from Steve's PMS (detail mode):
	           {endpoint}?villa={property_id}&date_from=&date_to=&pax=
	           painted into [data-bob-total-eur] / [data-bob-base-rental] /
	           [data-bob-adw] / [data-bob-cleaning]
	         - the submit gate (GF submit disabled until dates + pax filled)
	           + the contact-field reveal (unavailable dates still show the form)
	         - phone E.164 normalisation (intl-tel-input)
	       Redirect to the booking-confirmation page (resolved by template; see
	       ibv_build_gf_booking_confirmation() / ibv_get_booking_confirmation_page_id())
	       is a GF "Page" confirmation
	       (villa / arrival / departure / guests / offer merge tags).
	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>
	<div
		id="ibv-enquiry"
		class="ibv-enquiry-panel is-pricing-pending is-contact-pending<?php echo $resolved_name ? ' is-offer-mode' : ''; ?>"
		data-bob-enquiry-panel
		data-bob-property-id="<?php echo esc_attr( $property_id ); ?>"
		data-bob-endpoint="<?php echo esc_url( $endpoint_url ); ?>"
		data-iti-utils-url="<?php echo esc_url( $iti_utils_url ); ?>"
		data-bob-msg-unavailable="<?php echo esc_attr__( 'This villa isn’t available for your selected dates. Try different dates, or send us your enquiry and we’ll suggest great alternatives.', 'ibv' ); ?>"
		data-bob-msg-price-error="<?php echo esc_attr__( 'We couldn’t fetch live pricing just now. You can still send your enquiry and we’ll confirm the price by email.', 'ibv' ); ?>"
		data-bob-msg-invalid-phone="<?php echo esc_attr__( 'Please enter a valid phone number.', 'ibv' ); ?>"
		data-bob-msg-offer-price="<?php echo esc_attr__( "We'll confirm the offer price by email.", 'ibv' ); ?>"
		data-bob-offer-line-prefix="<?php echo esc_attr__( "You're asking about:", 'ibv' ); ?>"
		data-bob-offer-clear-label="<?php echo esc_attr__( 'Clear', 'ibv' ); ?>"
		<?php if ( $resolved_name ) : ?>
			data-bob-offer-name="<?php echo esc_attr( $resolved_name ); ?>"
			data-bob-offer-from="<?php echo esc_attr( $resolved_from ); ?>"
			data-bob-offer-to="<?php echo esc_attr( $resolved_to ); ?>"
		<?php endif; ?>
	>
		<template data-ibv-offer-clear-icon-tpl>
			<?php
			echo ibv_core_icon( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored Lucide SVG.
				'x',
				[
					'size'  => 16,
					'class' => 'ibv-enquiry-panel__offer-clear-icon',
				]
			);
			?>
		</template>
		<h2 class="ibv-enquiry-panel__title"><?php esc_html_e( 'Enquire about this villa', 'ibv' ); ?></h2>

		<?php if ( $resolved_name ) : ?>
			<div class="ibv-enquiry-panel__offer-line" data-ibv-offer-line>
				<p class="ibv-enquiry-panel__offer-about">
					<span class="ibv-enquiry-panel__offer-kicker"><?php esc_html_e( "You're asking about:", 'ibv' ); ?></span>
					<span class="ibv-enquiry-panel__offer-name"><?php echo esc_html( $resolved_name ); ?></span>
				</p>
				<a class="ibv-enquiry-panel__offer-clear" href="#ibv-enquiry" data-ibv-offer-clear>
					<?php
					echo ibv_core_icon( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored Lucide SVG.
						'x',
						[
							'size'  => 16,
							'class' => 'ibv-enquiry-panel__offer-clear-icon',
						]
					);
					?>
					<?php esc_html_e( 'Clear', 'ibv' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( $form_id ) : ?>
			<div class="ibv-enquiry-panel__form-embed">
				<?php ibv_core_gravity_form( $form_id, [ 'ajax' => true ] ); ?>
			</div>

			<p class="ibv-enquiry-panel__response-note">✓ <?php echo esc_html( ibv_get_response_time_note() ); ?></p>
		<?php endif; ?>

		<hr class="ibv-enquiry-panel__divider">

		<div class="ibv-enquiry-panel__chat">
			<p class="ibv-enquiry-panel__chat-label">
				<?php esc_html_e( 'Prefer to chat?', 'ibv' ); ?>
				<?php if ( $wa_url ) : ?>
					<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp us', 'ibv' ); ?></a>
				<?php endif; ?>
			</p>
			<?php
			$phones = [
				get_field( 'phone_uk', 'option' ),
				get_field( 'phone_ibiza', 'option' ),
			];
			foreach ( $phones as $phone_val ) :
				if ( empty( $phone_val ) ) {
					continue;
				}
				$tel_digits = preg_replace( '/[^0-9+]/', '', (string) $phone_val );
				if ( ! $tel_digits ) {
					continue;
				}
				?>
				<span class="ibv-enquiry-panel__chat-number"><?php echo esc_html( (string) $phone_val ); ?></span>
				<?php
			endforeach;

			$email = get_field( 'contact_email', 'option' );
			if ( $email ) :
				$email_safe = sanitize_email( (string) $email );
				if ( $email_safe ) :
					?>
					<a class="ibv-enquiry-panel__chat-link" href="<?php echo esc_url( 'mailto:' . $email_safe ); ?>">
						<?php esc_html_e( 'Or send us an email', 'ibv' ); ?>
					</a>
					<?php
				endif;
			endif;
			?>
		</div>
	</div>
	<?php /* ─────────── END BOB SHELL ─────────── */ ?>
	<?php
}
