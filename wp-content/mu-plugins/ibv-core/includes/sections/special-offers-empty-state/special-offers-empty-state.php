<?php
/**
 * Section: Special Offers — empty state.
 *
 * Rendered when offer_table is empty. Image-text layout with email
 * capture (newsletter helper) + “Browse all villas” CTA.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Empty state: copy + newsletter embed + CTA; optional image.
 */
function ibv_core_section_special_offers_empty_state() {
	wp_enqueue_style( 'ibv-section-special-offers-empty-state' );

	$title = get_field( 'empty_state_title' );
	$title = is_string( $title ) ? trim( $title ) : '';
	if ( '' === $title ) {
		$title = __( 'Nothing available', 'ibv' );
	}

	$body = get_field( 'empty_state_body' );
	$body = is_string( $body ) ? trim( $body ) : '';
	if ( '' === $body ) {
		$body = __( 'No special offers for you right now — get offers by email, check back soon, or browse all villas.', 'ibv' );
	}

	$image   = get_field( 'empty_state_image' );
	$cta_url = get_field( 'empty_state_browse_cta_url' );
	$cta_url = is_string( $cta_url ) ? trim( $cta_url ) : '';
	?>
	<section class="ibv-section-special-offers-empty-state ibv-section ibv-section--surface-tint-teal">
		<div class="ibv-container ibv-section-special-offers-empty-state__inner">
			<div class="ibv-section-special-offers-empty-state__copy">
				<h2 class="ibv-section-special-offers-empty-state__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
				<hr class="ibv-rule ibv-rule--sage ibv-section-special-offers-empty-state__rule" aria-hidden="true">
				<p class="ibv-section-special-offers-empty-state__body"><?php echo esc_html( $body ); ?></p>
				<?php
				ibv_core_newsletter_form(
					[
						'variant' => 'empty-state',
					]
				);
				if ( $cta_url ) {
					ibv_core_button(
						[
							'url'     => $cta_url,
							'label'   => __( 'Browse all villas', 'ibv' ),
							'variant' => 'secondary',
						]
					);
				}
				?>
			</div>
			<?php if ( is_array( $image ) && ! empty( $image['ID'] ) ) : ?>
				<div class="ibv-section-special-offers-empty-state__media">
					<?php
					ibv_core_image(
						$image,
						'ibv-card',
						[
							'class' => 'ibv-section-special-offers-empty-state__image',
						]
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
