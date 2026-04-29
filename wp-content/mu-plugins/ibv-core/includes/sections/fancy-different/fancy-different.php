<?php
/**
 * Section: Fancy something different (Airstream + Hotel).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Two-column teaser.
 */
function ibv_core_section_fancy_different() {
	wp_enqueue_style( 'ibv-section-fancy-different' );

	$intro = get_field( 'fancy_different_intro', 'option' );
	if ( ! $intro ) {
		$intro = __( 'Fancy something a bit different?', 'ibv' );
	}

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
	<section class="ibv-section-fancy-different ibv-section ibv-section--alt">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => $intro,
					'level' => 'h2',
				]
			);
			?>
			<div class="ibv-section-fancy-different__grid">
				<?php if ( $ai_txt || ! empty( $ai_img['ID'] ) ) : ?>
					<article class="ibv-fancy-card">
						<h3 class="ibv-fancy-card__title"><?php esc_html_e( 'Our Airstreams', 'ibv' ); ?></h3>
						<?php if ( $ai_txt ) : ?>
							<div class="ibv-fancy-card__text"><?php echo wp_kses_post( wpautop( $ai_txt ) ); ?></div>
						<?php endif; ?>
						<?php if ( $ai_url ) : ?>
							<?php
							ibv_core_button(
								[
									'url'   => esc_url( $ai_url ),
									'label' => __( 'Find out more', 'ibv' ),
									'variant' => 'secondary',
								]
							);
							?>
						<?php endif; ?>
						<?php if ( ! empty( $ai_img['ID'] ) ) : ?>
							<div class="ibv-fancy-card__media">
								<?php ibv_core_image( $ai_img, 'ibv-card', [ 'class' => 'ibv-fancy-card__image' ] ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endif; ?>
				<?php if ( $ho_txt || ! empty( $ho_img['ID'] ) ) : ?>
					<article class="ibv-fancy-card">
						<h3 class="ibv-fancy-card__title"><?php esc_html_e( 'Our Hotel', 'ibv' ); ?></h3>
						<?php if ( $ho_txt ) : ?>
							<div class="ibv-fancy-card__text"><?php echo wp_kses_post( wpautop( $ho_txt ) ); ?></div>
						<?php endif; ?>
						<?php if ( $ho_url ) : ?>
							<?php
							ibv_core_button(
								[
									'url'   => esc_url( $ho_url ),
									'label' => __( 'Find out more', 'ibv' ),
									'variant' => 'secondary',
								]
							);
							?>
						<?php endif; ?>
						<?php if ( ! empty( $ho_img['ID'] ) ) : ?>
							<div class="ibv-fancy-card__media">
								<?php ibv_core_image( $ho_img, 'ibv-card', [ 'class' => 'ibv-fancy-card__image' ] ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
