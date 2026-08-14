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

	// Show 3 random testimonials per render. With WP Rocket the pick is
	// baked into the cached page, so in practice this rotates per cache
	// cycle rather than per visit — accepted trade-off (no JS, no CLS).
	$rows = array_values(
		array_filter(
			$rows,
			static function ( $row ) {
				return ! empty( $row['quote'] );
			}
		)
	);
	if ( ! count( $rows ) ) {
		return;
	}
	shuffle( $rows );
	$rows = array_slice( $rows, 0, 3 );
	?>
	<section class="ibv-section-testimonials ibv-section ibv-section--surface-tint-blue">
		<div class="ibv-container">

			<header class="ibv-section-testimonials__header">
				<h2 class="ibv-section-testimonials__title ibv-font-display">
					<?php esc_html_e( 'What our guests say', 'ibv' ); ?>
				</h2>
				<hr class="ibv-rule ibv-rule--sage" aria-hidden="true">
			</header>

			<div class="ibv-section-testimonials__grid">
				<?php
				foreach ( $rows as $row ) {
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
