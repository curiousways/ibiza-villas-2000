<?php
/**
 * Section: Why IV2000 pillars.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Three pillars on dark teal.
 */
function ibv_core_section_why_iv2000() {
	wp_enqueue_style( 'ibv-section-why-iv2000' );

	$pillars = get_field( 'why_pillars' );
	if ( ! is_array( $pillars ) || ! count( $pillars ) ) {
		return;
	}
	?>
	<section class="ibv-section-why-iv2000 ibv-section">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'Why Ibiza Villas 2000?', 'ibv' ),
					'lead'  => __( 'Value, Service, Trust — the three pillars behind every booking.', 'ibv' ),
					'level' => 'h2',
					'align' => 'center',
					'class' => 'ibv-section-why-iv2000__heading',
				]
			);
			?>
			<div class="ibv-section-why-iv2000__grid ibv-grid ibv-grid--3">
				<?php foreach ( $pillars as $p ) : ?>
					<div class="ibv-pillar-card">
						<?php if ( ! empty( $p['icon']['ID'] ) ) : ?>
							<div class="ibv-pillar-card__icon">
								<?php ibv_core_image( $p['icon'], 'thumbnail', [ 'class' => 'ibv-pillar-card__icon-img' ] ); ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $p['title'] ) ) : ?>
							<h3 class="ibv-pillar-card__title"><?php echo esc_html( $p['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $p['text'] ) ) : ?>
							<div class="ibv-pillar-card__text"><?php echo wp_kses_post( wpautop( $p['text'] ) ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}
