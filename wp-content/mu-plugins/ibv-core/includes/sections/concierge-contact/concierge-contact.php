<?php
/**
 * Section: Concierge contact (Figma node 1:7008).
 *
 * Two-column layout: a content sidebar (heading + subtitle from the page,
 * plus phone / email / WhatsApp details from Site Options) beside the
 * Gravity Forms enquiry embed. The heading, subtitle, and enquiry form ID
 * are Concierge-page content; the contact details remain global. Must be
 * called within the Concierge page loop so `get_field()` resolves to the page.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_concierge_contact() {
	$title    = (string) get_field( 'contact_title' );
	$subtitle = (string) get_field( 'contact_subtitle' );

	$phone      = trim( (string) get_field( 'phone_ibiza', 'option' ) );
	if ( '' === $phone ) {
		$phone = trim( (string) get_field( 'phone_uk', 'option' ) );
	}
	$email     = trim( (string) get_field( 'contact_email', 'option' ) );
	$whatsapp  = trim( (string) get_field( 'whatsapp_number', 'option' ) );
	$form_id   = (int) get_field( 'concierge_enquiry_gravity_form_id' );

	$has_details = ( '' !== $phone ) || ( '' !== $email ) || ( '' !== $whatsapp );

	if ( ! $title && ! $subtitle && ! $has_details && ! $form_id ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-concierge-contact' );

	// Normalise tel: href (strip spaces; keep the leading +).
	$phone_href = $phone ? 'tel:' . preg_replace( '/[^\d+]/', '', $phone ) : '';
	$wa_href    = $whatsapp ? 'https://wa.me/' . preg_replace( '/[^\d]/', '', $whatsapp ) : '';
	?>
	<section class="ibv-section-concierge-contact ibv-section ibv-section--surface-bg">
		<div class="ibv-container">

			<?php if ( $title || $subtitle ) : ?>
				<header class="ibv-section-concierge-contact__header">
					<?php if ( $title ) : ?>
						<h2 class="ibv-section-concierge-contact__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
					<?php if ( $subtitle ) : ?>
						<p class="ibv-section-concierge-contact__subtitle ibv-font-display"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="ibv-section-concierge-contact__inner">

				<aside class="ibv-section-concierge-contact__sidebar">
					<?php if ( $has_details ) : ?>
						<ul class="ibv-section-concierge-contact__details">
							<?php if ( '' !== $phone ) : ?>
								<li class="ibv-section-concierge-contact__detail">
									<span class="ibv-section-concierge-contact__detail-label"><?php esc_html_e( 'Phone', 'ibv' ); ?></span>
									<a class="ibv-section-concierge-contact__detail-value" href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $email ) : ?>
								<li class="ibv-section-concierge-contact__detail">
									<span class="ibv-section-concierge-contact__detail-label"><?php esc_html_e( 'Email', 'ibv' ); ?></span>
									<a class="ibv-section-concierge-contact__detail-value" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
								</li>
							<?php endif; ?>
							<?php if ( '' !== $whatsapp ) : ?>
								<li class="ibv-section-concierge-contact__detail">
									<span class="ibv-section-concierge-contact__detail-label"><?php esc_html_e( 'Chat with us', 'ibv' ); ?></span>
									<a class="ibv-section-concierge-contact__detail-link" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp', 'ibv' ); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</aside>

				<?php if ( $form_id ) : ?>
					<div class="ibv-section-concierge-contact__form">
						<?php ibv_core_gravity_form( $form_id ); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</section>
	<?php
}
