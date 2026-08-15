<?php
/**
 * Section: Contact enquiry — shared layout for concierge + contact pages.
 *
 * Two-column section: a content sidebar (heading + subtitle from the page,
 * plus phone / email / WhatsApp details from Site Options) beside a
 * Gravity Forms enquiry embed. Per-page composition is driven by args:
 * heading level, heading size, optional banner image above the form,
 * optional form-card title, the GF form ID.
 *
 * Consumers:
 *   - Concierge page (h2 + large heading, no banner, no form-card title)
 *   - Contact   page (h1 + display heading, banner image, "Send an enquiry")
 *
 * Contact details always come from Site Options — shared across all pages.
 * Heading, subtitle, banner, form-title, form-id come from the caller.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param array $args {
 *     @type string       $heading        Section heading text.
 *     @type string       $subheading     Section subheading text (under heading).
 *     @type string       $heading_level  'h1' | 'h2'. Default 'h2'.
 *     @type string       $heading_size   'display' (~64px) | 'large' (~44px). Default 'large'.
 *     @type int|array    $banner_image   Optional ACF image (ID or array) shown above the form column.
 *     @type string       $form_title     Optional title rendered above the form (inside the form card).
 *     @type int          $form_id        Gravity Form ID. Required for the form to render.
 * }
 */
function ibv_core_section_contact_enquiry( array $args = [] ) {
	$args = wp_parse_args(
		$args,
		[
			'heading'       => '',
			'subheading'    => '',
			'heading_level' => 'h2',
			'heading_size'  => 'large',
			'banner_image'  => null,
			'form_title'    => '',
			'form_id'       => 0,
		]
	);

	$heading       = (string) $args['heading'];
	$subheading    = (string) $args['subheading'];
	$heading_level = in_array( $args['heading_level'], [ 'h1', 'h2' ], true ) ? $args['heading_level'] : 'h2';
	$heading_size  = in_array( $args['heading_size'], [ 'display', 'large' ], true ) ? $args['heading_size'] : 'large';
	$banner_image  = $args['banner_image'];
	$form_title    = (string) $args['form_title'];
	$form_id       = (int) $args['form_id'];

	$phone_uk    = trim( (string) get_field( 'phone_uk', 'option' ) );
	$phone_ibiza = trim( (string) get_field( 'phone_ibiza', 'option' ) );
	$email       = trim( (string) get_field( 'contact_email', 'option' ) );
	$whatsapp    = trim( (string) get_field( 'whatsapp_number', 'option' ) );

	$has_details = ( '' !== $phone_uk ) || ( '' !== $phone_ibiza ) || ( '' !== $email ) || ( '' !== $whatsapp );
	$has_banner  = ! empty( $banner_image );

	if ( ! $heading && ! $subheading && ! $has_details && ! $form_id ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-contact-enquiry' );

	$iti_utils_url = '';
	if ( $form_id ) {
		wp_enqueue_style( 'ibv-intl-tel-input' );
		wp_enqueue_script( 'ibv-section-contact-enquiry' );
		$iti_utils_url = add_query_arg(
			'ver',
			rawurlencode( IBV_CORE_VERSION ),
			IBV_CORE_URL . 'includes/components/enquiry-panel/vendor/intl-tel-input/js/utils.js'
		);
	}

	// Both office numbers render as their own labelled row (matching the
	// Site Options field labels); either can be left blank in admin.
	$phones = [];
	if ( '' !== $phone_uk ) {
		$phones[] = [
			'label' => __( 'Phone (UK)', 'ibv' ),
			'value' => $phone_uk,
			'href'  => 'tel:' . preg_replace( '/[^\d+]/', '', $phone_uk ),
		];
	}
	if ( '' !== $phone_ibiza ) {
		$phones[] = [
			'label' => __( 'Phone (Ibiza)', 'ibv' ),
			'value' => $phone_ibiza,
			'href'  => 'tel:' . preg_replace( '/[^\d+]/', '', $phone_ibiza ),
		];
	}
	$wa_href = $whatsapp ? 'https://wa.me/' . preg_replace( '/[^\d]/', '', $whatsapp ) : '';

	$title_classes = [ 'ibv-section-contact-enquiry__title', 'ibv-font-display' ];
	$title_classes[] = 'ibv-section-contact-enquiry__title--' . $heading_size;
	?>
	<section
		class="ibv-section-contact-enquiry ibv-section ibv-section--surface-bg"
		<?php if ( $iti_utils_url ) : ?>
			data-iti-utils-url="<?php echo esc_url( $iti_utils_url ); ?>"
		<?php endif; ?>
	>
		<div class="ibv-container">

			<?php if ( $heading || $subheading ) : ?>
				<header class="ibv-section-contact-enquiry__header">
					<?php if ( $heading ) : ?>
						<<?php echo esc_attr( $heading_level ); ?> class="<?php echo esc_attr( implode( ' ', $title_classes ) ); ?>"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $heading_level ); ?>>
					<?php endif; ?>
					<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
					<?php if ( $subheading ) : ?>
						<p class="ibv-section-contact-enquiry__subtitle ibv-font-display"><?php echo esc_html( $subheading ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="ibv-section-contact-enquiry__inner">

				<aside class="ibv-section-contact-enquiry__sidebar">
					<?php if ( $has_details ) : ?>
						<ul class="ibv-section-contact-enquiry__details">
							<?php foreach ( $phones as $phone_row ) : ?>
								<li class="ibv-section-contact-enquiry__detail">
									<span class="ibv-section-contact-enquiry__detail-label"><?php echo esc_html( $phone_row['label'] ); ?></span>
									<a class="ibv-section-contact-enquiry__detail-value" href="<?php echo esc_url( $phone_row['href'] ); ?>"><?php echo esc_html( $phone_row['value'] ); ?></a>
								</li>
							<?php endforeach; ?>
							<?php if ( '' !== $email ) : ?>
								<li class="ibv-section-contact-enquiry__detail">
									<span class="ibv-section-contact-enquiry__detail-label"><?php esc_html_e( 'Email', 'ibv' ); ?></span>
									<a class="ibv-section-contact-enquiry__detail-value" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $whatsapp ) : ?>
								<li class="ibv-section-contact-enquiry__detail">
									<span class="ibv-section-contact-enquiry__detail-label"><?php esc_html_e( 'Chat with us', 'ibv' ); ?></span>
									<a class="ibv-section-contact-enquiry__detail-link" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp', 'ibv' ); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</aside>

				<div class="ibv-section-contact-enquiry__form-column">

					<?php if ( $has_banner ) : ?>
						<div class="ibv-section-contact-enquiry__banner">
							<?php
							ibv_core_image(
								$banner_image,
								'large',
								[
									'class'    => 'ibv-section-contact-enquiry__banner-image',
									'loading'  => 'lazy',
									'decoding' => 'async',
									'alt'      => '',
								]
							);
							?>
						</div>
					<?php endif; ?>

					<?php if ( $form_id ) : ?>
						<div class="ibv-section-contact-enquiry__form-card">
							<?php if ( $form_title ) : ?>
								<h2 class="ibv-section-contact-enquiry__form-title ibv-font-display"><?php echo esc_html( $form_title ); ?></h2>
							<?php endif; ?>
							<?php ibv_core_gravity_form( $form_id ); ?>
						</div>
					<?php endif; ?>

				</div>

			</div>
		</div>
	</section>
	<?php
}
