<?php
/**
 * Component: Alternative accommodation (listing page cross-sell).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listing-page panel using same Site Options as fancy-different.
 */
function ibv_core_alternative_accommodation() {
	wp_enqueue_style( 'ibv-alternative-accommodation' );
	wp_enqueue_style( 'ibv-section-heading' );
	wp_enqueue_style( 'ibv-button' );

	$ai_img = get_field( 'fancy_airstream_image', 'option' );
	$ai_txt = get_field( 'fancy_airstream_text', 'option' );
	$ai_url = get_field( 'fancy_airstream_url', 'option' );

	$ho_img = get_field( 'fancy_hotel_image', 'option' );
	$ho_txt = get_field( 'fancy_hotel_text', 'option' );
	$ho_url = get_field( 'fancy_hotel_url', 'option' );

	if ( ! $ai_txt && ! $ho_txt && empty( $ai_img['ID'] ) && empty( $ho_img['ID'] ) ) {
		return;
	}
	?>
	<section class="ibv-alternative-accommodation ibv-section">
		<div class="ibv-container">
			<h2 class="ibv-alternative-accommodation__title"><?php esc_html_e( 'Traveling as couple or small group?', 'ibv' ); ?></h2>
			<p class="ibv-alternative-accommodation__subtitle"><?php esc_html_e( 'We also have licensed apartments and beachside Airstreams.', 'ibv' ); ?></p>
			<div class="ibv-alternative-accommodation__grid ibv-grid ibv-grid--2">
				<?php if ( $ai_txt || ! empty( $ai_img['ID'] ) ) : ?>
					<article class="ibv-alt-acc-card">
						<h3 class="ibv-alt-acc-card__title"><?php esc_html_e( 'Our Airstreams', 'ibv' ); ?></h3>
						<?php if ( $ai_txt ) : ?>
							<div class="ibv-alt-acc-card__text ibv-prose"><?php echo wp_kses_post( wpautop( $ai_txt ) ); ?></div>
						<?php endif; ?>
						<?php if ( $ai_url ) : ?>
							<?php
							ibv_core_button(
								[
									'url'     => esc_url( $ai_url ),
									'label'   => __( 'Find out more', 'ibv' ),
									'variant' => 'secondary',
								]
							);
							?>
						<?php endif; ?>
						<?php if ( ! empty( $ai_img['ID'] ) ) : ?>
							<div class="ibv-alt-acc-card__media">
								<?php ibv_core_image( $ai_img, 'ibv-card', [ 'class' => 'ibv-alt-acc-card__image' ] ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endif; ?>
				<?php if ( $ho_txt || ! empty( $ho_img['ID'] ) ) : ?>
					<article class="ibv-alt-acc-card">
						<h3 class="ibv-alt-acc-card__title"><?php esc_html_e( 'Our Hotel', 'ibv' ); ?></h3>
						<?php if ( $ho_txt ) : ?>
							<div class="ibv-alt-acc-card__text ibv-prose"><?php echo wp_kses_post( wpautop( $ho_txt ) ); ?></div>
						<?php endif; ?>
						<?php if ( $ho_url ) : ?>
							<?php
							ibv_core_button(
								[
									'url'     => esc_url( $ho_url ),
									'label'   => __( 'Find out more', 'ibv' ),
									'variant' => 'secondary',
								]
							);
							?>
						<?php endif; ?>
						<?php if ( ! empty( $ho_img['ID'] ) ) : ?>
							<div class="ibv-alt-acc-card__media">
								<?php ibv_core_image( $ho_img, 'ibv-card', [ 'class' => 'ibv-alt-acc-card__image' ] ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
