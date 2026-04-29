<?php
/**
 * Section: IPS responsible tourism panel.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IPS panel — image + copy.
 */
function ibv_core_section_ips_panel() {
	wp_enqueue_style( 'ibv-section-ips-panel' );

	$img   = get_field( 'ips_image', 'option' );
	$title = get_field( 'ips_title', 'option' );
	$text  = get_field( 'ips_text', 'option' );
	$clab  = get_field( 'ips_cta_label', 'option' );
	$curl  = get_field( 'ips_cta_url', 'option' );

	if ( ! $title && ! $text && empty( $img['ID'] ) ) {
		return;
	}
	?>
	<section class="ibv-section-ips-panel ibv-section">
		<div class="ibv-container ibv-section-ips-panel__layout">
			<?php if ( ! empty( $img['ID'] ) ) : ?>
				<div class="ibv-section-ips-panel__media">
					<?php ibv_core_image( $img, 'ibv-card', [ 'class' => 'ibv-section-ips-panel__image' ] ); ?>
				</div>
			<?php endif; ?>
			<div class="ibv-section-ips-panel__content">
				<?php if ( $title ) : ?>
					<h2 class="ibv-section-ips-panel__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $text ) : ?>
					<div class="ibv-section-ips-panel__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
				<?php endif; ?>
				<?php if ( $clab && $curl ) : ?>
					<?php
					ibv_core_button(
						[
							'url'     => esc_url( $curl ),
							'label'   => $clab,
							'variant' => 'primary',
						]
					);
					?>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
