<?php
/**
 * Section: My Booking (reference lookup).
 *
 * Collects the guest's booking reference and GETs it to Bob's booking form
 * (see `ibv_get_bob_booking_form_url()`), which verifies the guest and shows
 * their booking. Replaces the old theme's jQuery modal — same one-field
 * flow, no JavaScript.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the My Booking lookup panel.
 *
 * @param array $args {
 *     @type string $title Heading. Defaults to the page title in the loop.
 * }
 */
function ibv_core_section_my_booking( $args = [] ) {
	$defaults = [
		'title' => get_the_title(),
	];
	$args = wp_parse_args( $args, $defaults );

	wp_enqueue_style( 'ibv-section-my-booking' );
	wp_enqueue_style( 'ibv-forms' );
	?>
	<section class="ibv-section-my-booking ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-section-my-booking__inner">
			<?php if ( $args['title'] ) : ?>
				<h1 class="ibv-section-my-booking__title ibv-font-display">
					<?php echo esc_html( $args['title'] ); ?>
				</h1>
				<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
			<?php endif; ?>

			<p class="ibv-section-my-booking__lead">
				<?php esc_html_e( 'Enter the booking reference from your confirmation email to view and manage your booking.', 'ibv' ); ?>
			</p>

			<?php // Bob's booking system is a separate site (old .co.uk domain) — open it in a new tab. ?>
			<form class="ibv-form ibv-section-my-booking__form" method="get" action="<?php echo esc_url( ibv_get_bob_booking_form_url() ); ?>" target="_blank" rel="noopener">
				<label class="ibv-u-visually-hidden" for="ibv-my-booking-ref">
					<?php esc_html_e( 'Booking reference', 'ibv' ); ?>
				</label>
				<input
					id="ibv-my-booking-ref"
					class="ibv-section-my-booking__input"
					type="text"
					name="bookingRef"
					required
					autocomplete="off"
					placeholder="<?php esc_attr_e( 'Your booking reference', 'ibv' ); ?>"
				>
				<?php
				ibv_core_button(
					[
						'tag'   => 'button',
						'type'  => 'submit',
						'label' => __( 'View my booking', 'ibv' ),
					]
				);
				?>
			</form>
		</div>
	</section>
	<?php
}
