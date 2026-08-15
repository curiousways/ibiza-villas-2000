<?php
/**
 * Template Name: Booking Confirmation
 *
 * Shared thank-you page for enquiry forms. Copy is selected by ?type=
 * (villa | accommodation | concierge | general). Unknown or missing
 * values fall back to general — never to villa.
 *
 * Composition:
 *   1. Confirmation panel (variant heading + composed subheading)
 *   2. Booking details panel (URL-driven, optional)
 *   3. Steps (variant-scoped, 0–3; omitted when empty)
 *   4. Contact strip (variant intro + Site Options phone / WhatsApp)
 *   5. Concierge image-text section (held; suppressed on type=concierge)
 *
 * URL contract for the details panel:
 *   ?villa={id}&arrival=YYYY-MM-DD&departure=YYYY-MM-DD&guests={n}&offer={string}&ref={entry_id}&type={key}
 * All params optional and untrusted. `ref` is the Gravity Forms entry ID
 * ({entry_id} merge tag), displayed as IV-{n}.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed confirmation variants. general is the safe fallback.
 *
 * @return string[]
 */
function ibv_booking_confirmation_allowed_types() {
	return [ 'villa', 'accommodation', 'concierge', 'general' ];
}

/**
 * Resolve ?type= from the query string. Never echoes the raw value.
 *
 * @return string One of ibv_booking_confirmation_allowed_types().
 */
function ibv_booking_confirmation_type() {
	$allowed = ibv_booking_confirmation_allowed_types();
	$type    = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
	return in_array( $type, $allowed, true ) ? $type : 'general';
}

/**
 * Hardcoded fallbacks matching the copy log, used only when a variant
 * row has not been saved yet. Timing is not included — the template
 * composes that from Site Options.
 *
 * @return array<string, array<string, mixed>>
 */
function ibv_booking_confirmation_variant_defaults() {
	return [
		'villa'         => [
			'heading'      => __( "We've got your request", 'ibv' ),
			'subheading'   => __( 'A copy is on its way to your inbox. Someone from the team in Ibiza will come back to you personally', 'ibv' ),
			'contact'      => __( 'Need to change something, or add a night?', 'ibv' ),
			'append_note'  => true,
			'steps'        => [
				[
					'title' => __( 'We check the villa', 'ibv' ),
					'text'  => __( 'Someone in the Ibiza office confirms the dates are free and the price is right for your party.', 'ibv' ),
				],
				[
					'title' => __( 'You get it in writing', 'ibv' ),
					'text'  => __( 'Full price, and exactly what it covers — cleaning and damage waiver included. Nothing is committed until you say yes.', 'ibv' ),
				],
				[
					'title' => __( 'The deposit secures it', 'ibv' ),
					'text'  => __( "We don't hold dates until the deposit is paid, which is why we come back quickly. The balance is due before you travel.", 'ibv' ),
				],
			],
		],
		'accommodation' => [
			'heading'      => __( "We've got your enquiry", 'ibv' ),
			'subheading'   => __( 'A copy is on its way to your inbox. Someone from the team in Ibiza will come back to you personally with availability and a price', 'ibv' ),
			'contact'      => __( 'Need to change something, or add a night?', 'ibv' ),
			'append_note'  => true,
			'steps'        => [
				[
					'title' => __( 'We check availability', 'ibv' ),
					'text'  => __( 'Someone in the Ibiza office looks at your dates and comes straight back to you.', 'ibv' ),
				],
				[
					'title' => __( 'You get a price in writing', 'ibv' ),
					'text'  => __( 'The full cost, exactly what it covers, and the booking terms. Nothing gets added later.', 'ibv' ),
				],
			],
		],
		'concierge'     => [
			'heading'      => __( "We've got your request", 'ibv' ),
			'subheading'   => __( "A copy is on its way to your inbox. We'll come back with options and prices for what you've asked for.", 'ibv' ),
			'contact'      => __( 'Need to change something?', 'ibv' ),
			'append_note'  => false,
			'steps'        => [
				[
					'title' => __( 'We go to the right people', 'ibv' ),
					'text'  => __( "We'll ask the suppliers we've used for years, not whoever comes up first.", 'ibv' ),
				],
				[
					'title' => __( 'You get options and prices', 'ibv' ),
					'text'  => __( 'Nothing is booked until you choose.', 'ibv' ),
				],
			],
		],
		'general'       => [
			'heading'      => __( "Thanks — that's with us", 'ibv' ),
			'subheading'   => __( 'A copy is on its way to your inbox. Someone from the team in Ibiza will come back to you personally', 'ibv' ),
			'contact'      => __( 'Need to change something?', 'ibv' ),
			'append_note'  => true,
			'steps'        => [],
		],
	];
}

/**
 * Resolve the variant row for a type. ACF wins; defaults fill gaps.
 *
 * @param string $type Variant key.
 * @return array{heading:string,subheading:string,contact:string,append_note:bool,steps:array}
 */
function ibv_booking_confirmation_variant( $type ) {
	$defaults = ibv_booking_confirmation_variant_defaults();
	$fallback = $defaults['general'];
	$base     = isset( $defaults[ $type ] ) ? $defaults[ $type ] : $fallback;

	$rows = get_field( 'confirmation_variants' );
	if ( ! is_array( $rows ) ) {
		return $base;
	}

	foreach ( $rows as $row ) {
		$key = isset( $row['variant_key'] ) ? sanitize_key( (string) $row['variant_key'] ) : '';
		if ( $key !== $type ) {
			continue;
		}

		$steps = [];
		if ( ! empty( $row['variant_steps'] ) && is_array( $row['variant_steps'] ) ) {
			foreach ( $row['variant_steps'] as $step ) {
				$title = isset( $step['step_title'] ) ? trim( (string) $step['step_title'] ) : '';
				$text  = isset( $step['step_body'] ) ? trim( (string) $step['step_body'] ) : '';
				if ( '' === $title && '' === $text ) {
					continue;
				}
				$steps[] = [
					'title' => $title,
					'text'  => $text,
				];
			}
		}

		$heading    = isset( $row['variant_heading'] ) ? trim( (string) $row['variant_heading'] ) : '';
		$subheading = isset( $row['variant_subheading'] ) ? trim( (string) $row['variant_subheading'] ) : '';
		$contact    = isset( $row['variant_contact_intro'] ) ? trim( (string) $row['variant_contact_intro'] ) : '';

		return [
			'heading'     => $heading ? $heading : $base['heading'],
			'subheading'  => $subheading ? $subheading : $base['subheading'],
			'contact'     => $contact ? $contact : $base['contact'],
			'append_note' => $base['append_note'],
			'steps'       => $steps,
		];
	}

	return $base;
}

/**
 * Join the editorial lead to the Site Options timing note with an em dash
 * so the promise is one sentence, not two stapled claims.
 *
 * @param string $lead        Variant subheading (no timing phrase).
 * @param bool   $append_note Whether this variant uses the global note.
 * @return string
 */
function ibv_booking_confirmation_compose_subheading( $lead, $append_note ) {
	$lead = trim( (string) $lead );
	if ( ! $append_note ) {
		return $lead;
	}

	$note = trim( ibv_get_response_time_note() );
	if ( '' === $note ) {
		return $lead ? rtrim( $lead, " \t." ) . '.' : '';
	}
	if ( '' === $lead ) {
		return rtrim( $note, " \t." ) . '.';
	}

	return rtrim( $lead, " \t." ) . ' — ' . lcfirst( rtrim( $note, " \t." ) ) . '.';
}

/**
 * Render the URL-driven booking details panel.
 *
 * Whitelists each query param, validates strict shapes (Y-m-d dates,
 * integer guests, real villa post, integer entry id), and renders
 * nothing if none of them survive. The URL is user-mutable, so every
 * input is treated as untrusted.
 */
function ibv_render_booking_details_panel() {
	$villa_id  = isset( $_GET['villa'] ) ? absint( wp_unslash( $_GET['villa'] ) ) : 0;
	$arrival   = isset( $_GET['arrival'] ) ? sanitize_text_field( wp_unslash( $_GET['arrival'] ) ) : '';
	$departure = isset( $_GET['departure'] ) ? sanitize_text_field( wp_unslash( $_GET['departure'] ) ) : '';
	$guests    = isset( $_GET['guests'] ) ? absint( wp_unslash( $_GET['guests'] ) ) : 0;
	$offer     = isset( $_GET['offer'] ) ? sanitize_text_field( wp_unslash( $_GET['offer'] ) ) : '';
	$ref       = isset( $_GET['ref'] ) ? absint( wp_unslash( $_GET['ref'] ) ) : 0;

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
		$dates_label = ibv_core_villa_offers_format_range(
			str_replace( '-', '', $arrival ),
			str_replace( '-', '', $departure )
		);
	}

	if ( ! $villa_name && ! $dates_label && ! $guests && ! $offer && ! $ref ) {
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
				<?php if ( $ref ) : ?>
					<li>
						<strong><?php esc_html_e( 'Reference', 'ibv' ); ?></strong>
						<?php echo esc_html( 'IV-' . $ref ); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</section>
	<?php
}

/**
 * Build a tel: link from a Site Options phone value.
 *
 * @param string $phone Display number.
 * @param string $label Visible suffix, e.g. "UK".
 * @return string Escaped HTML or empty.
 */
function ibv_booking_confirmation_phone_link( $phone, $label ) {
	$phone = trim( (string) $phone );
	$tel   = $phone ? preg_replace( '/[^\d+]/', '', $phone ) : '';
	if ( ! $tel || ! $phone ) {
		return '';
	}

	$visible = $label
		? sprintf(
			/* translators: 1: phone number, 2: region label */
			__( '%1$s (%2$s)', 'ibv' ),
			$phone,
			$label
		)
		: $phone;

	return sprintf(
		'<a href="%s" aria-label="%s">%s</a>',
		esc_url( 'tel:' . $tel ),
		esc_attr( sprintf( /* translators: %s: international phone number */ __( 'Call %s', 'ibv' ), $phone ) ),
		esc_html( $visible )
	);
}

/**
 * Quiet contact line. Intro is per-variant; numbers come from Site Options.
 *
 * @param string $intro Opening sentence.
 */
function ibv_render_booking_confirmation_contact_strip( $intro ) {
	$intro = trim( (string) $intro );
	if ( '' === $intro ) {
		$intro = __( 'Need to change something?', 'ibv' );
	}

	$phone_uk    = trim( (string) get_field( 'phone_uk', 'option' ) );
	$phone_ibiza = trim( (string) get_field( 'phone_ibiza', 'option' ) );
	$whatsapp    = trim( (string) get_field( 'whatsapp_number', 'option' ) );
	$wa_digits   = $whatsapp ? preg_replace( '/[^0-9]/', '', $whatsapp ) : '';
	$wa_url      = $wa_digits ? 'https://wa.me/' . $wa_digits : '';

	$whatsapp_link = '';
	if ( $wa_url ) {
		$whatsapp_link = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a>',
			esc_url( $wa_url ),
			esc_attr__( 'Message us on WhatsApp', 'ibv' ),
			esc_html__( 'WhatsApp', 'ibv' )
		);
	}

	$uk_link    = ibv_booking_confirmation_phone_link( $phone_uk, __( 'UK', 'ibv' ) );
	$ibiza_link = ibv_booking_confirmation_phone_link( $phone_ibiza, __( 'Ibiza', 'ibv' ) );

	$phones = array_values( array_filter( [ $uk_link, $ibiza_link ] ) );
	if ( 2 === count( $phones ) ) {
		$phone_clause = sprintf(
			/* translators: 1: UK tel link, 2: Ibiza tel link */
			__( 'call %1$s or %2$s', 'ibv' ),
			$phones[0],
			$phones[1]
		);
	} elseif ( 1 === count( $phones ) ) {
		$phone_clause = sprintf(
			/* translators: %s: tel link */
			__( 'call %s', 'ibv' ),
			$phones[0]
		);
	} else {
		$phone_clause = '';
	}

	if ( $whatsapp_link && $phone_clause ) {
		$clause = sprintf(
			/* translators: 1: WhatsApp link, 2: phone clause */
			__( 'Reply to that email, message us on %1$s, or %2$s.', 'ibv' ),
			$whatsapp_link,
			$phone_clause
		);
	} elseif ( $whatsapp_link ) {
		$clause = sprintf(
			/* translators: %s: WhatsApp link */
			__( 'Reply to that email, or message us on %s.', 'ibv' ),
			$whatsapp_link
		);
	} elseif ( $phone_clause ) {
		$clause = sprintf(
			/* translators: %s: phone clause */
			__( 'Reply to that email, or %s.', 'ibv' ),
			$phone_clause
		);
	} else {
		$clause = __( 'Reply to that email.', 'ibv' );
	}
	?>
	<section class="ibv-booking-confirmation__contact ibv-section ibv-section--surface-bg ibv-section--rhythm-sm">
		<div class="ibv-container">
			<p class="ibv-booking-confirmation__contact-line">
				<?php echo esc_html( $intro ); ?>
				<?php echo $clause; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from escaped pieces. ?>
			</p>
		</div>
	</section>
	<?php
}

get_header();

while ( have_posts() ) :
	the_post();

	$type    = ibv_booking_confirmation_type();
	$variant = ibv_booking_confirmation_variant( $type );
	$subtitle = ibv_booking_confirmation_compose_subheading(
		$variant['subheading'],
		$variant['append_note']
	);
	?>

	<section class="ibv-booking-confirmation__panel ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-booking-confirmation__panel-inner">
			<span class="ibv-booking-confirmation__icon">
				<?php echo ibv_core_icon( 'check', [ 'size' => 32 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored SVG. ?>
			</span>
			<h1 class="ibv-booking-confirmation__title ibv-font-display">
				<?php echo esc_html( $variant['heading'] ); ?>
			</h1>
			<?php if ( $subtitle ) : ?>
				<p class="ibv-booking-confirmation__subtitle">
					<?php echo esc_html( $subtitle ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<?php
	ibv_render_booking_details_panel();

	if ( ! empty( $variant['steps'] ) ) {
		ibv_core_section_three_step(
			[
				'eyebrow' => '',
				'title'   => '',
				'steps'   => $variant['steps'],
				'surface' => 'bg',
			]
		);
	}

	ibv_render_booking_confirmation_contact_strip( $variant['contact'] );

	// Held default copy — leave in place, but do not pitch concierge
	// back at someone who has just enquired about concierge.
	if ( 'concierge' !== $type ) {
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
	}

endwhile;

get_footer();
