<?php
/**
 * Section: Three-step process.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Numbered process cards on a gold panel.
 */
function ibv_core_section_three_step() {
	wp_enqueue_style( 'ibv-section-three-step' );

	$eyebrow = get_field( 'three_step_intro_eyebrow' );
	$title   = get_field( 'three_step_intro_title' );
	$steps   = get_field( 'three_step_steps' );

	if ( ! is_array( $steps ) || ! count( $steps ) ) {
		return;
	}

	if ( ! $eyebrow ) {
		$eyebrow = __( 'Simple & Swift', 'ibv' );
	}
	if ( ! $title ) {
		$title = __( 'Our 3-Step Process', 'ibv' );
	}
	?>
	<section class="ibv-section-three-step ibv-section">
		<div class="ibv-container">

			<header class="ibv-section-three-step__header">
				<p class="ibv-section-three-step__eyebrow">
					<?php echo esc_html( $eyebrow ); ?>
				</p>
				<h2 class="ibv-section-three-step__title ibv-font-display">
					<?php echo esc_html( $title ); ?>
				</h2>
				<hr class="ibv-section-three-step__rule" aria-hidden="true">
			</header>

			<div class="ibv-section-three-step__grid">
				<?php
				$i = 0;
				foreach ( $steps as $step ) :
					++$i;
					?>
					<article class="ibv-step-card">
						<span class="ibv-step-card__num ibv-font-display" aria-hidden="true"><?php echo esc_html( (string) $i ); ?></span>
						<?php if ( ! empty( $step['title'] ) ) : ?>
							<h3 class="ibv-step-card__title ibv-font-display">
								<?php echo esc_html( $step['title'] ); ?>
							</h3>
						<?php endif; ?>
						<?php if ( ! empty( $step['text'] ) ) : ?>
							<div class="ibv-step-card__text">
								<?php echo wp_kses_post( wpautop( $step['text'] ) ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php
}
