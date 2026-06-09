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
 *
 * Default behaviour reads from Site Options globals (homepage / About).
 * Pages can override per-arg by passing explicit values; pass `null`
 * (or omit) to inherit a global. The fallback hardcoded defaults
 * ("Simple & Swift" / "Our 3-Step Process") only apply on the globals
 * path, so a page-scoped value won't get overwritten.
 *
 * @param array $args {
 *     @type string|null $eyebrow Optional eyebrow override; null reads global.
 *     @type string|null $title   Optional title override; null reads global.
 *     @type array|null  $steps   Optional steps array (each row: title, text); null reads global.
 * }
 */
function ibv_core_section_three_step( $args = [] ) {
	$args = wp_parse_args(
		$args,
		[
			'eyebrow' => null,
			'title'   => null,
			'steps'   => null,
			'surface' => null,
		]
	);

	wp_enqueue_style( 'ibv-section-three-step' );

	$eyebrow = $args['eyebrow'];
	if ( null === $eyebrow ) {
		$eyebrow = get_field( 'three_step_intro_eyebrow', 'option' );
		if ( ! $eyebrow ) {
			$eyebrow = __( 'Simple & Swift', 'ibv' );
		}
	}

	$title = $args['title'];
	if ( null === $title ) {
		$title = get_field( 'three_step_intro_title', 'option' );
		if ( ! $title ) {
			$title = __( 'Our 3-Step Process', 'ibv' );
		}
	}

	$steps = $args['steps'];
	if ( null === $steps ) {
		$steps = get_field( 'three_step_steps', 'option' );
	}

	if ( ! is_array( $steps ) || ! count( $steps ) ) {
		return;
	}

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	$surface        = is_string( $args['surface'] ) && in_array( $args['surface'], $valid_surfaces, true )
		? $args['surface']
		: 'tint-gold';

	$has_header = (bool) $eyebrow || (bool) $title;
	?>
	<section class="ibv-section-three-step ibv-section ibv-section--surface-<?php echo esc_attr( $surface ); ?>">
		<div class="ibv-container">

			<?php if ( $has_header ) : ?>
				<header class="ibv-section-three-step__header">
					<?php if ( $eyebrow ) : ?>
						<p class="ibv-section-three-step__eyebrow">
							<?php echo esc_html( $eyebrow ); ?>
						</p>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h2 class="ibv-section-three-step__title ibv-font-display">
							<?php echo esc_html( $title ); ?>
						</h2>
					<?php endif; ?>
					<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
				</header>
			<?php endif; ?>

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
