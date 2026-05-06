<?php
/**
 * Section: About — stats bar.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_about_stats() {
	$stats = get_field( 'about_stats' );
	if ( ! is_array( $stats ) || ! count( $stats ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-about-stats' );
	?>
	<section class="ibv-section-about-stats ibv-section ibv-section--surface-blue ibv-section--rhythm-sm">
		<div class="ibv-container">
			<ul class="ibv-section-about-stats__grid">
				<?php foreach ( $stats as $stat ) : ?>
					<?php
					$eyebrow = (string) ( $stat['eyebrow'] ?? '' );
					$value   = (string) ( $stat['value'] ?? '' );
					$caption = (string) ( $stat['caption'] ?? '' );
					if ( ! $value ) {
						continue;
					}
					?>
					<li class="ibv-section-about-stats__tile">
						<?php if ( $eyebrow ) : ?>
							<p class="ibv-section-about-stats__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<p class="ibv-section-about-stats__value ibv-font-display"><?php echo esc_html( $value ); ?></p>
						<?php if ( $caption ) : ?>
							<p class="ibv-section-about-stats__caption"><?php echo esc_html( $caption ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}
