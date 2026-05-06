<?php
/**
 * Template Name: Booking Confirmation
 *
 * The page Bob's enquiry form redirects to on a successful submission.
 * Composition:
 *   1. Confirmation panel (ACF heading + optional subheading)
 *   2. Booking details panel (URL-driven, optional — see helper below)
 *   3. Three-step (page-scoped content via the variant-prep args path)
 *   4. Concierge image-text section
 *
 * Bob's URL contract for step 2:
 *   ?villa={id}&arrival=YYYY-MM-DD&departure=YYYY-MM-DD&guests={n}&offer={string}
 * All params optional; the panel renders only the rows whose params
 * survive validation. See `ibv_render_booking_details_panel` below.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the URL-driven booking details panel.
 *
 * Whitelists each query param, validates strict shapes (Y-m-d dates,
 * integer guests, real villa post), and renders nothing if none of
 * them survive. The URL is user-mutable, so every input is treated
 * as untrusted.
 */
function ibv_render_booking_details_panel() {
	$villa_id  = isset( $_GET['villa'] ) ? absint( wp_unslash( $_GET['villa'] ) ) : 0;
	$arrival   = isset( $_GET['arrival'] ) ? sanitize_text_field( wp_unslash( $_GET['arrival'] ) ) : '';
	$departure = isset( $_GET['departure'] ) ? sanitize_text_field( wp_unslash( $_GET['departure'] ) ) : '';
	$guests    = isset( $_GET['guests'] ) ? absint( wp_unslash( $_GET['guests'] ) ) : 0;
	$offer     = isset( $_GET['offer'] ) ? sanitize_text_field( wp_unslash( $_GET['offer'] ) ) : '';

	$arrival   = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $arrival ) ? $arrival : '';
	$departure = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $departure ) ? $departure : '';

	$villa_name = '';
	$villa_url  = '';
	if ( $villa_id ) {
		$villa = get_post( $villa_id );
		if ( $villa && 'villas' === $villa->post_type && 'publish' === $villa->post_status ) {
			$villa_name = get_the_title( $villa );
			$villa_url  = get_permalink( $villa );
		}
	}

	if ( strlen( $offer ) > 80 ) {
		$offer = substr( $offer, 0, 80 );
	}

	$dates_label = '';
	if ( $arrival && $departure ) {
		// Reuse the villa-offers component's range formatter (Ymd input).
		$dates_label = ibv_core_villa_offers_format_range(
			str_replace( '-', '', $arrival ),
			str_replace( '-', '', $departure )
		);
	}

	if ( ! $villa_name && ! $dates_label && ! $guests && ! $offer ) {
		return;
	}
	?>
	<section class="ibv-booking-confirmation__details ibv-section ibv-section--surface-bg ibv-section--rhythm-sm">
		<div class="ibv-container">
			<ul class="ibv-booking-confirmation__details-list">
				<?php if ( $villa_name ) : ?>
					<li>
						<strong><?php esc_html_e( 'Villa', 'ibv' ); ?></strong>
						<?php if ( $villa_url ) : ?>
							<a href="<?php echo esc_url( $villa_url ); ?>"><?php echo esc_html( $villa_name ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $villa_name ); ?>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( $dates_label ) : ?>
					<li>
						<strong><?php esc_html_e( 'Dates', 'ibv' ); ?></strong>
						<?php echo esc_html( $dates_label ); ?>
					</li>
				<?php endif; ?>
				<?php if ( $guests ) : ?>
					<li>
						<strong><?php esc_html_e( 'Guests', 'ibv' ); ?></strong>
						<?php echo esc_html( (string) $guests ); ?>
					</li>
				<?php endif; ?>
				<?php if ( $offer ) : ?>
					<li>
						<strong><?php esc_html_e( 'Offer', 'ibv' ); ?></strong>
						<?php echo esc_html( $offer ); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</section>
	<?php
}

get_header();

while ( have_posts() ) :
	the_post();

	$confirmation_heading    = (string) get_field( 'confirmation_heading' );
	$confirmation_subheading = (string) get_field( 'confirmation_subheading' );

	if ( ! $confirmation_heading ) {
		$confirmation_heading = __( 'Booking request received', 'ibv' );
	}
	?>

	<section class="ibv-booking-confirmation__panel ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-booking-confirmation__panel-inner">
			<span class="ibv-booking-confirmation__icon">
				<?php echo ibv_core_icon( 'check', [ 'size' => 32 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored SVG. ?>
			</span>
			<h1 class="ibv-booking-confirmation__title ibv-font-display">
				<?php echo esc_html( $confirmation_heading ); ?>
			</h1>
			<?php if ( $confirmation_subheading ) : ?>
				<p class="ibv-booking-confirmation__subtitle">
					<?php echo esc_html( $confirmation_subheading ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<?php
	ibv_render_booking_details_panel();

	ibv_core_section_three_step(
		[
			// No eyebrow / title on this page — the confirmation H1 above
			// already establishes the section's purpose. Empty string opts
			// out of the global fallback that null would trigger.
			'eyebrow' => '',
			'title'   => '',
			'steps'   => get_field( 'three_step_steps' ),
			'surface' => 'bg',
		]
	);

	$concierge_image = get_field( 'concierge_image' );
	if ( $concierge_image ) {
		ibv_core_image_text_section(
			[
				'title'       => (string) get_field( 'concierge_title' ),
				'description' => (string) get_field( 'concierge_body' ),
				'cta_label'   => (string) get_field( 'concierge_cta_label' ),
				'cta_url'     => (string) get_field( 'concierge_cta_url' ),
				'image'       => $concierge_image,
				'image_side'  => 'right',
				'surface'     => 'white',
			]
		);
	}

endwhile;

get_footer();
