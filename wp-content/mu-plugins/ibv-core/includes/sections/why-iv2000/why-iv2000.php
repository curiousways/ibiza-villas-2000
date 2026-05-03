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

			<header class="ibv-section-why-iv2000__header">
				<h2 class="ibv-section-why-iv2000__title ibv-font-display">
					<?php esc_html_e( 'Why Ibiza Villas 2000?', 'ibv' ); ?>
				</h2>
				<hr class="ibv-section-why-iv2000__rule" aria-hidden="true">
				<p class="ibv-section-why-iv2000__lead">
					<?php esc_html_e( 'Two decades of experience providing the most authentic island stays.', 'ibv' ); ?>
				</p>
			</header>

			<div class="ibv-section-why-iv2000__grid">
				<?php foreach ( $pillars as $p ) : ?>
					<article class="ibv-pillar-card">
						<?php if ( ! empty( $p['icon']['ID'] ) ) : ?>
							<div class="ibv-pillar-card__icon">
								<?php
								ibv_core_image(
									$p['icon'],
									'thumbnail',
									[
										'class'    => 'ibv-pillar-card__icon-img',
										'loading'  => 'lazy',
										'decoding' => 'async',
									]
								);
								?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $p['title'] ) ) : ?>
							<h3 class="ibv-pillar-card__title ibv-font-display">
								<?php echo esc_html( $p['title'] ); ?>
							</h3>
							<hr class="ibv-pillar-card__rule" aria-hidden="true">
						<?php endif; ?>
						<?php if ( ! empty( $p['text'] ) ) : ?>
							<div class="ibv-pillar-card__text">
								<?php echo wp_kses_post( wpautop( $p['text'] ) ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php
}
