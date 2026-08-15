<?php
/**
 * Component: Villa enquiry / RTB panel — embedded Gravity Form.
 *
 * The villa enquiry form is an EMBEDDED Gravity Form (#33, seeded by
 * seed-villa-enquiry-form.php), exactly like the accommodation enquiry form — GF
 * is the single source of truth (fields, validation, notification, entry, and the
 * booking-confirmation redirect). The villa-specific live pricing / availability
 * gate is grafted on top by enquiry-panel.js, the same way the date-range picker
 * and phone widget are grafted onto the accommodation form.
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
					$names = array();
					foreach ( ibv_villa_get_active_offers( $villa_id ) as $offer ) {
						$name = sanitize_text_field( (string) ( $offer['offer_name'] ?? '' ) );
						if ( '' !== $name ) {
							$names[] = $name;
						}
					}
					$offer_names = implode( ', ', $names );
				}
			}
		}

		$_POST['input_1'] = $villa_name;  // Property Name.
		$_POST['input_9'] = $offer_names; // Active Offers.
	}
);

// ── Submit button: render as <button> with the design's arrow icon ──────────

/**
 * Rewrite the villa enquiry form's GF submit from <input> to <button> so it can
 * carry an inline arrow icon beside the label (an <input type="submit"> can't
 * hold inline SVG). We rewrite the tag rather than rebuild it, preserving GF's
 * id / class / onclick attributes so AJAX submission and the JS gate keep
 * working. Registered on the generic `gform_submit_button` at file load (not
 * inside the render fn) and scoped to the villa form id, so the arrow survives
 * GF's AJAX re-render the same way the prepopulation/pre-submission filters do.
 * Full-width teal styling lives in enquiry-panel.css. Mirrors accommodation-enquiry.
 */
add_filter(
	'gform_submit_button',
	static function ( $button, $form ) {
		$villa_form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
		if ( ! $villa_form_id || (int) $form['id'] !== $villa_form_id ) {
			return $button;
		}
		if ( ! is_string( $button ) || '' === $button ) {
			return $button;
		}

		// Label: GF's button text (the field's `value`), with a fallback.
		$label = isset( $form['button']['text'] ) && '' !== $form['button']['text']
			? (string) $form['button']['text']
			: __( 'Request to Book', 'ibv' );

		$arrow = ibv_core_icon(
			'arrow-right',
			[
				'class' => 'ibv-button__arrow',
				'size'  => 18,
			]
		);

		$inner = '<span class="ibv-enquiry-panel__submit-label">' . esc_html( $label ) . '</span>' . $arrow;

		// input → button, drop the now-redundant `value`, then close the
		// self-closing tag around the inner content. Limited to 1 match so a
		// stray `value=` inside an onclick handler can't be clobbered.
		$out = preg_replace( '/^<input\b/', '<button', $button, 1 );
		$out = preg_replace( '/\svalue=("|\').*?\1/', '', $out, 1 );
		// Add the design-system button classes alongside GF's own.
		$out = preg_replace(
			'/\sclass=("|\')/',
			' class=$1ibv-button ibv-button--primary ibv-button--medium ',
			$out,
			1
		);
		$out = preg_replace( '#\s*/?>\s*$#', '>' . $inner . '</button>', $out, 1 );

		return $out ?? $button;
	},
	10,
	2
);

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
	         - the submit gate (GF submit disabled until dates + pax filled
	           and the villa is available) + the contact-field reveal
	         - phone E.164 normalisation (intl-tel-input)
	       Redirect to the booking-confirmation page (resolved by template; see
	       ibv_build_gf_booking_confirmation() / ibv_get_booking_confirmation_page_id())
	       is a GF "Page" confirmation
	       (villa / arrival / departure / guests / offer merge tags).
	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>
	<div
		class="ibv-enquiry-panel is-pricing-pending is-contact-pending"
		data-bob-enquiry-panel
		data-bob-property-id="<?php echo esc_attr( $property_id ); ?>"
		data-bob-endpoint="<?php echo esc_url( $endpoint_url ); ?>"
		data-iti-utils-url="<?php echo esc_url( $iti_utils_url ); ?>"
		data-bob-msg-unavailable="<?php echo esc_attr__( 'This villa isn’t available for your selected dates. Try different dates, or send us your enquiry and we’ll suggest great alternatives.', 'ibv' ); ?>"
		data-bob-msg-price-error="<?php echo esc_attr__( 'We couldn’t fetch live pricing just now. You can still send your enquiry and we’ll confirm the price by email.', 'ibv' ); ?>"
		data-bob-msg-invalid-phone="<?php echo esc_attr__( 'Please enter a valid phone number.', 'ibv' ); ?>"
	>
		<h2 class="ibv-enquiry-panel__title"><?php esc_html_e( 'Enquire about this villa', 'ibv' ); ?></h2>

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
