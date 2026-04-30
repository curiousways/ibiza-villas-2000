<?php
/**
 * Section: Meet the team teaser.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meet the team block — image + copy.
 */
function ibv_core_section_meet_team_teaser() {
	wp_enqueue_style( 'ibv-section-meet-team-teaser' );

	$img   = get_field( 'meet_team_image' );
	$title = get_field( 'meet_team_title' );
	$text  = get_field( 'meet_team_text' );
	$clab  = get_field( 'meet_team_cta_label' );
	$curl  = get_field( 'meet_team_cta_url' );

	if ( ! $title && ! $text && empty( $img['ID'] ) ) {
		return;
	}
	?>
	<section class="ibv-section-meet-team-teaser ibv-section ibv-section--alt">
		<div class="ibv-container ibv-section-meet-team-teaser__layout">
			<?php if ( ! empty( $img['ID'] ) ) : ?>
				<div class="ibv-section-meet-team-teaser__media">
					<?php ibv_core_image( $img, 'ibv-card', [ 'class' => 'ibv-section-meet-team-teaser__image' ] ); ?>
				</div>
			<?php endif; ?>
			<div class="ibv-section-meet-team-teaser__content">
				<?php if ( $title ) : ?>
					<h2 class="ibv-section-meet-team-teaser__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $text ) : ?>
					<div class="ibv-section-meet-team-teaser__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
				<?php endif; ?>
				<?php if ( $clab && $curl ) : ?>
					<?php
					ibv_core_button(
						[
							'url'     => esc_url( $curl ),
							'label'   => $clab,
							'variant' => 'secondary',
						]
					);
					?>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
