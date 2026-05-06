<?php
/**
 * Section: Special Offers — offer grid.
 *
 * Aggregates active offers across all villas (via the per-villa
 * `villa_offers` repeater), expands to one card per villa-offer
 * pair, sorts by offer start date ascending, and renders them in
 * a 3-up villa-card grid. If no active offers exist, delegates to
 * the empty-state section so the page still has something useful.
 *
 * Owns its own data — the page template just calls this without args.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Special Offers grid.
 */
function ibv_core_section_special_offers_grid() {
	$villas = get_posts(
		[
			'post_type'      => 'villas',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		]
	);

	$today = current_time( 'Ymd' );
	$cards = [];

	foreach ( $villas as $villa_id ) {
		$offers = get_field( 'villa_offers', $villa_id );
		if ( ! is_array( $offers ) ) {
			continue;
		}
		foreach ( $offers as $offer ) {
			$from = (string) ( $offer['offer_date_from'] ?? '' );
			$to   = (string) ( $offer['offer_date_to'] ?? '' );
			$name = trim( (string) ( $offer['offer_name'] ?? '' ) );
			if ( ! $from || ! $to || ! $name || $to < $today ) {
				continue;
			}
			$cards[] = [
				'villa_id' => (int) $villa_id,
				'offer'    => $offer,
			];
		}
	}

	usort(
		$cards,
		static function ( $a, $b ) {
			return strcmp(
				(string) ( $a['offer']['offer_date_from'] ?? '' ),
				(string) ( $b['offer']['offer_date_from'] ?? '' )
			);
		}
	);

	if ( empty( $cards ) ) {
		ibv_core_section_special_offers_empty_state();
		return;
	}

	wp_enqueue_style( 'ibv-section-special-offers-grid' );
	?>
	<section class="ibv-section-special-offers-grid ibv-section ibv-section--surface-bg">
		<div class="ibv-container">
			<div class="ibv-section-special-offers-grid__cards">
				<?php
				foreach ( $cards as $card ) {
					$offer = $card['offer'];
					ibv_core_villa_card(
						[
							'villa'             => $card['villa_id'],
							'variant'           => 'default',
							'offer_dates'       => ibv_core_villa_offers_format_range(
								(string) ( $offer['offer_date_from'] ?? '' ),
								(string) ( $offer['offer_date_to'] ?? '' )
							),
							'offer_headline'    => trim( (string) ( $offer['offer_headline'] ?? '' ) ),
							'offer_description' => trim( (string) ( $offer['offer_description'] ?? '' ) ),
						]
					);
				}
				?>
			</div>
		</div>
	</section>
	<?php
}
