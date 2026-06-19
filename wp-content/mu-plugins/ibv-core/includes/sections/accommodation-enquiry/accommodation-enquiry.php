<?php
/**
 * Section: Accommodation enquiry sidebar (Hotel / Airstream pages).
 *
 * Right-rail enquiry: an embedded Gravity Form (via ibv_core_gravity_form())
 * with our single-field "When" date-range picker grafted on, plus the
 * "Prefer to chat" chrome (WhatsApp / phone / email from Site Options).
 *
 * The picker graft is pure front-end: the embedded GF (seeded by
 * seed-accommodation-form.php) carries an HTML "When" field + two CSS-classed
 * date fields (ibv-drp-from / ibv-drp-to, hidden via CSS); accommodation-
 * enquiry.js wires VanillaCalendarPro to them and re-binds on
 * gform_post_render so it survives GF's AJAX re-render. The phone field gets
 * intl-tel-input, same as contact-enquiry.
 *
 * Renders inside .ibv-villa-detail__sidebar. NOT a `.ibv-section`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Populate the embedded form's hidden "Accommodation" field (inputName
 * `ibv_accommodation`) with the current page title, so the office sees which
 * page (Hotel / Airstream) an enquiry came from. Only the accommodation form
 * carries that input name.
 */
add_filter(
	'gform_field_value_ibv_accommodation',
	static function ( $value ) {
		// Scope to the accommodation template so the page title can never leak
		// into an unrelated form that happens to reuse this input name.
		if ( ! is_page_template( 'page-accommodation.php' ) ) {
			return $value;
		}
		$id = get_queried_object_id();
		return $id ? get_the_title( $id ) : $value;
	}
);

/**
 * Render the accommodation enquiry sidebar for the current page.
 */
function ibv_core_section_accommodation_enquiry() {
	$heading = (string) get_field( 'enquiry_heading' );

	$form_id = (int) get_field( 'accommodation_enquiry_gravity_form_id' );
	if ( ! $form_id ) {
		$form_id = (int) get_option( 'ibv_accommodation_enquiry_form_id' );
	}

	$phone = trim( (string) get_field( 'phone_ibiza', 'option' ) );
	if ( '' === $phone ) {
		$phone = trim( (string) get_field( 'phone_uk', 'option' ) );
	}
	$phone2   = trim( (string) get_field( 'phone_uk', 'option' ) );
	$email    = trim( (string) get_field( 'contact_email', 'option' ) );
	$whatsapp = trim( (string) get_field( 'whatsapp_number', 'option' ) );

	if ( ! $heading && ! $form_id && '' === $phone && '' === $email && '' === $whatsapp ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-accommodation-enquiry' );

	$iti_utils_url = '';
	if ( $form_id ) {
		wp_enqueue_style( 'ibv-date-range-picker' );
		wp_enqueue_style( 'ibv-intl-tel-input' );
		wp_enqueue_script( 'ibv-section-accommodation-enquiry' );
		$iti_utils_url = add_query_arg(
			'ver',
			rawurlencode( IBV_CORE_VERSION ),
			IBV_CORE_URL . 'includes/components/enquiry-panel/vendor/intl-tel-input/js/utils.js'
		);
	}

	$wa_url = $whatsapp ? 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $whatsapp ) : '';
	?>
	<div
		class="ibv-accommodation-enquiry"
		<?php if ( $iti_utils_url ) : ?>
			data-iti-utils-url="<?php echo esc_url( $iti_utils_url ); ?>"
		<?php endif; ?>
	>
		<?php if ( $heading ) : ?>
			<h2 class="ibv-accommodation-enquiry__title ibv-font-display"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $form_id ) : ?>
			<div class="ibv-accommodation-enquiry__form">
				<?php ibv_core_gravity_form( $form_id, [ 'ajax' => true ] ); ?>
			</div>

			<p class="ibv-accommodation-enquiry__response-note">
				<?php esc_html_e( '✓ We respond within 20 minutes within our business hours', 'ibv' ); ?>
			</p>
		<?php endif; ?>

		<hr class="ibv-accommodation-enquiry__divider">

		<div class="ibv-accommodation-enquiry__chat">
			<p class="ibv-accommodation-enquiry__chat-label">
				<?php esc_html_e( 'Prefer to chat?', 'ibv' ); ?>
				<?php if ( $wa_url ) : ?>
					<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp us', 'ibv' ); ?></a>
				<?php endif; ?>
			</p>
			<?php
			foreach ( array( $phone, $phone2 ) as $phone_val ) :
				$phone_val = trim( (string) $phone_val );
				if ( '' === $phone_val ) {
					continue;
				}
				?>
				<span class="ibv-accommodation-enquiry__chat-number"><?php echo esc_html( $phone_val ); ?></span>
				<?php
			endforeach;

			if ( '' !== $email ) :
				$email_safe = sanitize_email( $email );
				if ( $email_safe ) :
					?>
					<a class="ibv-accommodation-enquiry__chat-link" href="<?php echo esc_url( 'mailto:' . $email_safe ); ?>">
						<?php esc_html_e( 'Or send us an email', 'ibv' ); ?>
					</a>
					<?php
				endif;
			endif;
			?>
		</div>
	</div>
	<?php
}
