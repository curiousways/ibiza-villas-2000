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
 * Testimonials grid on blue tint background.
 */
function ibv_core_section_testimonials() {
	wp_enqueue_style( 'ibv-section-testimonials' );

	$rows = get_field( 'testimonials', 'option' );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}
	?>
	<section class="ibv-section-testimonials ibv-section ibv-section--surface-tint-blue">
		<div class="ibv-container">

			<header class="ibv-section-testimonials__header">
				<h2 class="ibv-section-testimonials__title ibv-font-display">
					<?php esc_html_e( 'What our guests say', 'ibv' ); ?>
				</h2>
				<hr class="ibv-section-testimonials__rule" aria-hidden="true">
			</header>

			<div class="ibv-section-testimonials__grid">
				<?php
				foreach ( $rows as $row ) {
					if ( empty( $row['quote'] ) ) {
						continue;
					}
					ibv_core_quote_card(
						[
							'quote'       => $row['quote'],
							'attribution' => $row['attribution'] ?? '',
						]
					);
				}
				?>
			</div>

		</div>
	</section>
	<?php
}
