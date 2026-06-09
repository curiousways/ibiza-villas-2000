<?php
/**
 * Section: Single testimonial teaser (first homepage testimonial).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Placeholder until editor-controlled field in a later pass.
 */
function ibv_core_section_villa_testimonial_teaser() {
	wp_enqueue_style( 'ibv-section-villa-testimonial-teaser' );

	$rows = get_field( 'testimonials', 'option' );
	if ( ! is_array( $rows ) || ! count( $rows ) || empty( $rows[0]['quote'] ) ) {
		return;
	}

	$row = $rows[0];
	?>
	<section class="ibv-villa-testimonial-teaser" aria-label="<?php esc_attr_e( 'Guest testimonial', 'ibv' ); ?>">
		<blockquote class="ibv-villa-testimonial-teaser__quote">
			<p class="ibv-villa-testimonial-teaser__stars" aria-hidden="true">★★★★★</p>
			<p class="ibv-villa-testimonial-teaser__text"><?php echo esc_html( $row['quote'] ); ?></p>
			<?php if ( ! empty( $row['attribution'] ) ) : ?>
				<footer class="ibv-villa-testimonial-teaser__attr"><?php echo esc_html( $row['attribution'] ); ?></footer>
			<?php endif; ?>
		</blockquote>
	</section>
	<?php
}
