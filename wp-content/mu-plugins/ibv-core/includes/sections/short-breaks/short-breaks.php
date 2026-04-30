<?php
/**
 * Section: Short breaks panel.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Short breaks teaser.
 */
function ibv_core_section_short_breaks() {
	wp_enqueue_style( 'ibv-section-short-breaks' );

	$img   = get_field( 'short_breaks_image' );
	$title = get_field( 'short_breaks_title' );
	$text  = get_field( 'short_breaks_text' );
	$cta   = get_field( 'short_breaks_cta_url' );

	if ( ! $title && ! $text && ! $img ) {
		return;
	}
	?>
	<section class="ibv-section-short-breaks ibv-section">
		<div class="ibv-container ibv-section-short-breaks__panel">
			<div class="ibv-section-short-breaks__content">
				<?php if ( $title ) : ?>
					<h2 class="ibv-section-short-breaks__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $text ) : ?>
					<div class="ibv-section-short-breaks__text ibv-prose"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
				<?php endif; ?>
				<?php if ( $cta ) : ?>
					<?php
					ibv_core_button(
						[
							'url'     => esc_url( $cta ),
							'label'   => __( 'Search all short breaks', 'ibv' ),
							'variant' => 'secondary',
						]
					);
					?>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $img['ID'] ) ) : ?>
				<div class="ibv-section-short-breaks__media">
					<?php ibv_core_image( $img, 'ibv-card', [ 'class' => 'ibv-section-short-breaks__image' ] ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
