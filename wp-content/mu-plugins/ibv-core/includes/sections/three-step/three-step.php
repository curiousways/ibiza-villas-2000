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
 * Numbered process cards.
 */
function ibv_core_section_three_step() {
	wp_enqueue_style( 'ibv-section-three-step' );

	$eyebrow = get_field( 'three_step_intro_eyebrow' );
	$title   = get_field( 'three_step_intro_title' );
	$steps   = get_field( 'three_step_steps' );

	if ( ! is_array( $steps ) || ! count( $steps ) ) {
		return;
	}
	?>
	<section class="ibv-section-three-step ibv-section ibv-section--alt">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'eyebrow' => $eyebrow ? $eyebrow : '',
					'title'   => $title ? $title : __( 'Our 3-Step Process', 'ibv' ),
					'lead'    => __( 'Reassurance that the process is simple and fast.', 'ibv' ),
					'level'   => 'h2',
					'align'   => 'center',
				]
			);
			?>
			<div class="ibv-section-three-step__grid ibv-grid ibv-grid--3">
				<?php
				$i = 0;
				foreach ( $steps as $step ) :
					++$i;
					?>
					<div class="ibv-step-card">
						<span class="ibv-step-card__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i ) ); ?></span>
						<?php if ( ! empty( $step['title'] ) ) : ?>
							<h3 class="ibv-step-card__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $step['text'] ) ) : ?>
							<div class="ibv-step-card__text"><?php echo wp_kses_post( wpautop( $step['text'] ) ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="ibv-section-three-step__cta">
				<?php
				ibv_core_button(
					[
						'url'     => ibv_get_search_villas_url(),
						'label'   => __( 'Search Villas', 'ibv' ),
						'variant' => 'primary',
					]
				);
				?>
			</p>
		</div>
	</section>
	<?php
}
