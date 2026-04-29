<?php
/**
 * Section: Villa overview (title, rating, summary, facts, amenities, price).
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

	$title = get_field( 'villa_pretty_name', $villa_id );
	if ( ! $title ) {
		$title = get_the_title( $villa_id );
	}

	$rating  = get_field( 'villa_rating_score', $villa_id );
	$rv_ct   = get_field( 'villa_review_count', $villa_id );
	$rv_url  = get_field( 'villa_review_source_url', $villa_id );
	$loc     = ibv_villa_location_label( $villa_id );
	$summary = get_field( 'property_summary', $villa_id );

	$first_distance = '';
	$rows           = get_field( 'villa_distances', $villa_id );
	if ( is_array( $rows ) && count( $rows ) ) {
		$first = $rows[0];
		if ( ! empty( $first['distance_text'] ) ) {
			$first_distance = (string) $first['distance_text'];
		}
	}

	$indicative = get_field( 'villa_indicative_from_price', $villa_id );

	$summary_id  = wp_unique_id( 'ibv-vo-summary-' );
	$heading_id  = wp_unique_id( 'ibv-vo-heading-' );
	$plain_len   = $summary ? mb_strlen( wp_strip_all_tags( (string) $summary ) ) : 0;
	$use_clamp   = $plain_len > 200;
	?>
	<section class="ibv-villa-overview" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<h1 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-overview__title"><?php echo esc_html( $title ); ?></h1>

		<?php if ( $rating || $rv_ct ) : ?>
			<div class="ibv-villa-overview__rating">
				<?php if ( $rating ) : ?>
					<span class="ibv-villa-overview__score"><?php echo esc_html( number_format_i18n( (float) $rating, 1 ) ); ?></span>
				<?php endif; ?>
				<?php if ( $rv_ct && $rv_url ) : ?>
					<a class="ibv-villa-overview__reviews-link" href="<?php echo esc_url( $rv_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php
						printf(
							/* translators: %d: review count */
							esc_html( _n( '%d review', '%d reviews', (int) $rv_ct, 'ibv' ) ),
							(int) $rv_ct
						);
						?>
					</a>
				<?php elseif ( $rv_ct ) : ?>
					<span class="ibv-villa-overview__reviews-count">
						<?php
						printf(
							esc_html( _n( '%d review', '%d reviews', (int) $rv_ct, 'ibv' ) ),
							(int) $rv_ct
						);
						?>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $loc || $first_distance ) : ?>
			<p class="ibv-villa-overview__location-row">
				<?php if ( $loc ) : ?>
					<span class="ibv-villa-overview__location-pill"><?php echo esc_html( $loc ); ?></span>
				<?php endif; ?>
				<?php if ( $first_distance ) : ?>
					<span class="ibv-villa-overview__distance-note"><?php echo esc_html( $first_distance ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<?php ibv_core_facts_strip( $villa_id, 'horizontal' ); ?>

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
