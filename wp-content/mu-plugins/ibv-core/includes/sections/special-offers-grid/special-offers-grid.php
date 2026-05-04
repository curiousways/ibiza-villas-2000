<?php
/**
 * Section: Special Offers — offer grid.
 *
 * Renders a 3-up grid of villa cards (default variant; structured offer
 * row fields when present) from the page-level offer_table repeater.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param array $offers ACF offer_table repeater rows.
 */
function ibv_core_section_special_offers_grid( $offers ) {
	if ( empty( $offers ) || ! is_array( $offers ) ) {
		return;
	}
	wp_enqueue_style( 'ibv-section-special-offers-grid' );
	?>
	<section class="ibv-section-special-offers-grid ibv-section ibv-section--surface-bg">
		<div class="ibv-container">
			<div class="ibv-section-special-offers-grid__cards">
				<?php
				foreach ( $offers as $offer ) {
					$vid = ibv_resolve_villa_id( $offer['villa'] ?? 0 );
					if ( ! $vid ) {
						continue;
					}
					$is_weeks_deal = ! empty( $offer['is_weeks_deal'] );
					$footnote      = isset( $offer['footnote'] ) ? trim( (string) $offer['footnote'] ) : '';
					ibv_core_villa_card(
						[
							'villa'             => $vid,
							'variant'           => 'default',
							'badge'             => $is_weeks_deal ? __( "This week's deal", 'ibv' ) : '',
							'badge_variant'     => 'gold',
							'was_price'         => isset( $offer['was_price'] ) && '' !== $offer['was_price'] && null !== $offer['was_price'] ? (float) $offer['was_price'] : null,
							'now_price'         => isset( $offer['now_price'] ) && '' !== $offer['now_price'] && null !== $offer['now_price'] ? (float) $offer['now_price'] : null,
							'valid_from'        => isset( $offer['valid_from'] ) ? (string) $offer['valid_from'] : '',
							'valid_to'          => isset( $offer['valid_to'] ) ? (string) $offer['valid_to'] : '',
							'footnote'          => $footnote,
							'show_now_asterisk' => ! empty( $offer['show_now_asterisk'] ),
							'cta_label'         => __( 'Enquire now', 'ibv' ),
						]
					);
				}
				?>
			</div>
		</div>
	</section>
	<?php
}
