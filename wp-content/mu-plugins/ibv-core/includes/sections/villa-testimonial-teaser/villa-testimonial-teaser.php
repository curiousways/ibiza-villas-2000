<?php
/**
 * Section: Villa testimonial teaser ("What our guests say").
 *
 * Matches Figma node 1:6040 (02b | Villa Detail → Testimonials). Deep
 * blue band with a serif heading + accent rule, holding an off-white
 * rounded card with 5 gold star icons, a quote, and an attribution.
 *
 * Data: Site Options `testimonials` repeater, first row (shared with the
 * homepage testimonials slider — same quote on every villa, by design).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_villa_testimonial_teaser() {
	$rows = get_field( 'testimonials', 'option' );
	if ( ! is_array( $rows ) || ! count( $rows ) || empty( $rows[0]['quote'] ) ) {
		return;
	}

	$row = $rows[0];

	wp_enqueue_style( 'ibv-section-villa-testimonial-teaser' );

	$heading_id = wp_unique_id( 'ibv-testimonial-heading-' );
	?>
	<section class="ibv-villa-testimonial-teaser ibv-section ibv-section--surface-blue" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<div class="ibv-container">
			<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-testimonial-teaser__heading ibv-font-display">
				<?php esc_html_e( 'What our guests say', 'ibv' ); ?>
			</h2>
			<hr class="ibv-rule ibv-rule--sage ibv-villa-testimonial-teaser__rule" aria-hidden="true">

			<blockquote class="ibv-villa-testimonial-teaser__card">
				<span
					class="ibv-villa-testimonial-teaser__stars"
					role="img"
					aria-label="<?php esc_attr_e( 'Rated 5 out of 5', 'ibv' ); ?>"
				>
					<?php for ( $i = 0; $i < 5; $i++ ) {
						ibv_core_the_icon( 'star', [ 'size' => 14, 'class' => 'ibv-villa-testimonial-teaser__star' ] );
					} ?>
				</span>
				<p class="ibv-villa-testimonial-teaser__text"><?php echo esc_html( $row['quote'] ); ?></p>
				<?php if ( ! empty( $row['attribution'] ) ) : ?>
					<footer class="ibv-villa-testimonial-teaser__attr"><?php echo esc_html( $row['attribution'] ); ?></footer>
				<?php endif; ?>
			</blockquote>
		</div>
	</section>
	<?php
}
