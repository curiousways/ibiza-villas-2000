<?php
/**
 * Section: Villa overview (summary, amenities, from-price).
 *
 * Title / rating / location / facts moved to `villa-header` section.
 * The "Villa Overview" <h2> + redesigned description land in a later brief.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_section_villa_overview( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-detail' );
	wp_enqueue_style( 'ibv-section-villa-overview' );
	wp_enqueue_style( 'ibv-section-heading' );

	$summary    = get_field( 'property_summary', $villa_id );
	$indicative = get_field( 'villa_indicative_from_price', $villa_id );

	$summary_id  = wp_unique_id( 'ibv-vo-summary-' );
	$plain_len   = $summary ? mb_strlen( wp_strip_all_tags( (string) $summary ) ) : 0;
	$use_clamp   = $plain_len > 200;
	// Accessible name reintroduced by the later overview-redesign brief
	// when the "Villa Overview" <h2> (Figma 1:5978) lands.
	?>
	<section class="ibv-villa-overview">
		<?php if ( $summary ) : ?>
			<div class="ibv-villa-overview__summary-block">
				<div
					id="<?php echo esc_attr( $summary_id ); ?>"
					class="ibv-villa-overview__summary ibv-prose<?php echo $use_clamp ? ' ibv-villa-overview__summary--clamp' : ''; ?>"
				>
					<?php echo wp_kses_post( $summary ); ?>
				</div>
				<?php if ( $use_clamp ) : ?>
					<button
						type="button"
						class="ibv-villa-overview__read-more"
						data-ibv-readmore
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $summary_id ); ?>"
					>
						<?php esc_html_e( 'Read more', 'ibv' ); ?>
					</button>
					<?php
					wp_enqueue_script( 'ibv-villa-overview' );
					$read_less = esc_js( __( 'Read less', 'ibv' ) );
					$read_more = esc_js( __( 'Read more', 'ibv' ) );
					$inline    = 'document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll("[data-ibv-readmore]").forEach(function(btn){var id=btn.getAttribute("aria-controls");var el=id?document.getElementById(id):null;if(!el)return;btn.addEventListener("click",function(){var open=el.classList.toggle("ibv-villa-overview__summary--open");btn.setAttribute("aria-expanded",open?"true":"false");btn.textContent=open?"' . $read_less . '":"' . $read_more . '";});});});';
					wp_add_inline_script( 'ibv-villa-overview', $inline );
					?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php ibv_core_amenity_ticks( $villa_id ); ?>

		<?php if ( $indicative ) : ?>
			<div class="ibv-villa-overview__from-price">
				<?php
				printf(
					/* translators: 1: formatted EUR amount */
					esc_html__( 'From €%1$s / wk · Price varies by season', 'ibv' ),
					esc_html( number_format_i18n( (float) $indicative ) )
				);
				?>
			</div>
		<?php endif; ?>
	</section>
	<?php
}
