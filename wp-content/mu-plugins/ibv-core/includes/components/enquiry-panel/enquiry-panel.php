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
	         - Fetch from villa_search endpoint:
	           ?villa={villa_id}&date_from=...&date_to=...&pax=...
	         - Update [data-bob-total-eur], [data-bob-total-gbp],
	           [data-bob-base-rental], [data-bob-adw], [data-bob-cleaning]
	         - Enable/disable .ibv-enquiry-panel__submit based on availability

	       On submit:
	         - POST to API enquiry endpoint (TBD with Steve)
	         - On success: redirect to /booking-confirmation/?villa={slug}
	         - On failure: show inline error in [data-bob-error]

	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>

	<div class="ibv-enquiry-panel" data-bob-enquiry-panel data-villa-id="<?php echo esc_attr( (string) $villa_id ); ?>">
		<h2 class="ibv-enquiry-panel__title"><?php esc_html_e( 'Enquire about this villa', 'ibv' ); ?></h2>

		<form class="ibv-enquiry-panel__form" method="post" action="#">
			<input type="hidden" name="villa_id" value="<?php echo esc_attr( (string) $villa_id ); ?>">

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-from"><?php esc_html_e( 'Arrive', 'ibv' ); ?></label>
				<input type="date" id="ibv-ep-from" name="date_from" required>
			</div>

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-to"><?php esc_html_e( 'Depart', 'ibv' ); ?></label>
				<input type="date" id="ibv-ep-to" name="date_to" required>
			</div>

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-pax"><?php esc_html_e( 'Guests', 'ibv' ); ?></label>
				<input type="number" id="ibv-ep-pax" name="pax" min="1" max="30" required>
			</div>

			<div class="ibv-enquiry-panel__field ibv-enquiry-panel__field--message">
				<label for="ibv-ep-message"><?php esc_html_e( 'Message (optional)', 'ibv' ); ?></label>
				<textarea id="ibv-ep-message" name="message" rows="3"></textarea>
			</div>

			<div class="ibv-enquiry-panel__price-block">
				<p class="ibv-enquiry-panel__price-label"><?php esc_html_e( 'Total price', 'ibv' ); ?></p>
				<p class="ibv-enquiry-panel__price-eur" data-bob-total-eur><?php echo esc_html( '—' ); ?></p>
				<p class="ibv-enquiry-panel__price-gbp" data-bob-total-gbp></p>
				<ul class="ibv-enquiry-panel__breakdown">
					<li><span data-bob-base-rental><?php echo esc_html( '—' ); ?></span> <?php esc_html_e( 'base rental', 'ibv' ); ?></li>
					<li><span data-bob-adw><?php echo esc_html( '—' ); ?></span> <?php esc_html_e( 'ADW (damage waiver)', 'ibv' ); ?></li>
					<li><span data-bob-cleaning><?php echo esc_html( '—' ); ?></span> <?php esc_html_e( 'cleaning fee', 'ibv' ); ?></li>
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
			<p class="ibv-enquiry-panel__chat-label"><?php esc_html_e( 'Prefer to chat?', 'ibv' ); ?></p>
			<?php
			$whatsapp = get_field( 'whatsapp_number', 'option' );
			if ( $whatsapp ) :
				$digits       = preg_replace( '/[^0-9]/', '', (string) $whatsapp );
				$whatsapp_url = $digits ? 'https://wa.me/' . $digits : '';
				if ( $whatsapp_url ) :
					?>
					<a class="ibv-enquiry-panel__chat-link" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'WhatsApp us', 'ibv' ); ?>
						<span class="ibv-enquiry-panel__chat-number"><?php echo esc_html( (string) $whatsapp ); ?></span>
					</a>
					<?php
				endif;
			endif;
			?>

			<?php
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
