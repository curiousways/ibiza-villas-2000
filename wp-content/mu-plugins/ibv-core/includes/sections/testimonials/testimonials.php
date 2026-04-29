<?php
/**
 * Section: Testimonials (homepage).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Testimonials grid on teal background.
 */
function ibv_core_section_testimonials() {
	wp_enqueue_style( 'ibv-section-testimonials' );

	$rows = get_field( 'testimonials', 'option' );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}
	?>
	<section class="ibv-section-testimonials ibv-section">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'What our guests say', 'ibv' ),
					'level' => 'h2',
					'align' => 'center',
				]
			);
			?>
			<div class="ibv-section-testimonials__grid ibv-grid ibv-grid--3">
				<?php foreach ( $rows as $row ) : ?>
					<?php if ( empty( $row['quote'] ) ) { continue; } ?>
					<blockquote class="ibv-quote-card">
						<p class="ibv-quote-card__stars" aria-hidden="true">★★★★★</p>
						<p class="ibv-quote-card__quote"><?php echo esc_html( $row['quote'] ); ?></p>
						<?php if ( ! empty( $row['attribution'] ) ) : ?>
							<footer class="ibv-quote-card__attr"><?php echo esc_html( $row['attribution'] ); ?></footer>
						<?php endif; ?>
					</blockquote>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}
