<?php
/**
 * Component: Enquiry / RTB panel (Bob shell).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_enquiry_panel( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id || 'villas' !== get_post_type( $villa_id ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-enquiry-panel' );
	wp_enqueue_style( 'ibv-button' );

	$property_id  = (string) get_field( 'property_id', $villa_id );
	$confirm_url  = ibv_get_booking_confirmation_url();
	$endpoint_url = 'https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl';

	$prefill_from = '';
	$prefill_to   = '';
	$prefill_pax  = '';
	if ( isset( $_GET['date_from'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['date_from'] ) );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
			$prefill_from = $raw;
		}
	}
	if ( isset( $_GET['date_to'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['date_to'] ) );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
			$prefill_to = $raw;
		}
	}
	if ( isset( $_GET['pax'] ) ) {
		$pax = absint( wp_unslash( $_GET['pax'] ) );
		if ( $pax > 0 ) {
			$prefill_pax = (string) $pax;
		}
	}

	?>
	<?php /* ─────────────────────────────────────────────────────────────
	       BOB API INTEGRATION SHELL — enquiry panel
	       ─────────────────────────────────────────────────────────────
	       Form fields:
	         - villa_id     (hidden, from current post)
	         - date_from    (required, YYYY-MM-DD)
	         - date_to      (required, YYYY-MM-DD)
	         - pax          (required, integer)
	         - message      (optional, text)

	       On date/pax change:
	         - Fetch in detail mode:
	           {endpoint}?villa={property_id}&date_from=...&date_to=...&pax=...
	         - Update [data-bob-total-eur], [data-bob-base-rental],
	           [data-bob-adw], [data-bob-cleaning]
	         - Enable/disable .ibv-enquiry-panel__submit based on dates+pax filled

	       On submit:
	         - TODO: POST to API enquiry endpoint when Steve confirms URL
	         - Currently: redirect to /booking-confirmation/?villa={post_id}&arrival=...&departure=...&guests=...

	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>

	<?php
	// Hide the price block until JS calls revealPriceBlock() after paint().
	// Both arrival flows (direct + search) start hidden — search arrival
	// fires the fetch on init and reveals once the response lands; direct
	// arrival fires the fetch when the visitor finishes the form.
	?>
	<div
		class="ibv-enquiry-panel is-pricing-pending"
		data-bob-enquiry-panel
		data-villa-id="<?php echo esc_attr( (string) $villa_id ); ?>"
		data-bob-property-id="<?php echo esc_attr( $property_id ); ?>"
		data-bob-endpoint="<?php echo esc_url( $endpoint_url ); ?>"
		data-bob-confirm-url="<?php echo esc_url( $confirm_url ); ?>"
	>
		<h2 class="ibv-enquiry-panel__title"><?php esc_html_e( 'Enquire about this villa', 'ibv' ); ?></h2>

		<form class="ibv-enquiry-panel__form" method="get" action="<?php echo esc_url( $confirm_url ); ?>">
			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-from" class="ibv-u-visually-hidden"><?php esc_html_e( 'Arrive', 'ibv' ); ?></label>
				<input type="date" id="ibv-ep-from" name="date_from" value="<?php echo esc_attr( $prefill_from ); ?>" placeholder="<?php esc_attr_e( 'Arrive', 'ibv' ); ?>" required>
			</div>

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-to" class="ibv-u-visually-hidden"><?php esc_html_e( 'Depart', 'ibv' ); ?></label>
				<input type="date" id="ibv-ep-to" name="date_to" value="<?php echo esc_attr( $prefill_to ); ?>" placeholder="<?php esc_attr_e( 'Depart', 'ibv' ); ?>" required>
			</div>

			<div class="ibv-enquiry-panel__field ibv-enquiry-panel__field--pax">
				<label for="ibv-ep-pax">
					<input type="number" id="ibv-ep-pax" name="pax" min="1" max="30" placeholder="1" value="<?php echo esc_attr( $prefill_pax ); ?>" placeholder="" required>
					<span class="ibv-enquiry-panel__field-suffix" aria-hidden="true"><?php esc_html_e( 'Guests', 'ibv' ); ?></span>
				</label>
			</div>

			<div class="ibv-enquiry-panel__field ibv-enquiry-panel__field--message">
				<label for="ibv-ep-message" class="ibv-u-visually-hidden"><?php esc_html_e( 'Message', 'ibv' ); ?></label>
				<textarea id="ibv-ep-message" name="message" rows="6" placeholder="<?php esc_attr_e( 'Message (Optional)', 'ibv' ); ?>"></textarea>
			</div>

			<div class="ibv-enquiry-panel__price-block">
				<p class="ibv-enquiry-panel__price-label"><?php esc_html_e( 'Total price', 'ibv' ); ?></p>
				<ul class="ibv-enquiry-panel__price-list">
					<li class="ibv-enquiry-panel__price-list-item ibv-enquiry-panel__price-eur" data-bob-total-eur></li>
					<li class="ibv-enquiry-panel__price-list-item ibv-enquiry-panel__price-gbp" data-bob-total-gbp></li>
				</ul>
				<ul class="ibv-enquiry-panel__breakdown">
					<li class="ibv-enquiry-panel__breakdown-item"><span data-bob-base-rental></span> <?php esc_html_e( 'base rental', 'ibv' ); ?></li>
					<li class="ibv-enquiry-panel__breakdown-item"><span data-bob-adw></span> <?php esc_html_e( 'ADW (damage waiver)', 'ibv' ); ?></li>
					<li class="ibv-enquiry-panel__breakdown-item"><span data-bob-cleaning></span> <?php esc_html_e( 'cleaning fee', 'ibv' ); ?></li>
				</ul>
				<p class="ibv-enquiry-panel__eco-note"><?php esc_html_e( 'Total does not include the government Eco Tax of €2.20 per person, per night, payable in resort.', 'ibv' ); ?></p>
			</div>

			<div class="ibv-enquiry-panel__error" data-bob-error hidden></div>

			<?php
			ibv_core_button(
				[
					'tag'         => 'button',
					'type'        => 'submit',
					'label'       => __( 'Request to book', 'ibv' ),
					'variant'     => 'primary',
					'size'        => 'large',
					'class'       => 'ibv-enquiry-panel__submit',
					'attributes'  => [
						'data-bob-submit' => '1',
						'disabled'        => 'disabled',
					],
				]
			);
			?>

			<p class="ibv-enquiry-panel__response-note"><?php esc_html_e( '✓ We respond within 20 minutes during our business hours', 'ibv' ); ?></p>
		</form>

		<hr class="ibv-enquiry-panel__divider">

		<div class="ibv-enquiry-panel__chat">
			<?php
			$whatsapp = get_field( 'whatsapp_number', 'option' );
			$digits   = $whatsapp ? preg_replace( '/[^0-9]/', '', (string) $whatsapp ) : '';
			$wa_url   = $digits ? 'https://wa.me/' . $digits : '';
			?>
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

	wp_enqueue_script( 'ibv-enquiry-panel' );
}
