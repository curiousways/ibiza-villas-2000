<?php
/**
 * Section: Weekly special offer.
 *
 * Homepage section that wraps the offer panel with the section header
 * ("This Week's Special Offer" + "Search all Special Offers" CTA).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Weekly offer block + CTA to special offers.
 */
function ibv_core_section_weekly_offer() {
	wp_enqueue_style( 'ibv-section-weekly-offer' );

	$vid = (int) get_field( 'weekly_offer_villa' );
	if ( ! $vid ) {
		return;
	}

	$was   = get_field( 'weekly_offer_was_price' );
	$now   = get_field( 'weekly_offer_now_price' );
	$vfrom = get_field( 'weekly_offer_valid_from' );
	$vto   = get_field( 'weekly_offer_valid_to' );
	?>
	<section class="ibv-section-weekly-offer ibv-section">
		<div class="ibv-container">
			<?php
			ibv_core_offer_panel(
				[
					'villa'         => $vid,
					'was_price'     => $was ? (float) $was : null,
					'now_price'     => $now ? (float) $now : null,
					'valid_from'    => $vfrom ? (string) $vfrom : null,
					'valid_to'      => $vto ? (string) $vto : null,
					'cta_label'     => __( 'View Villa', 'ibv' ),
					'section_title' => __( "This Week's Special Offer", 'ibv' ),
					'section_cta'   => [
						'url'   => ibv_get_special_offers_url(),
						'label' => __( 'Search all Special Offers', 'ibv' ),
					],
				]
			);
			?>
		</div>
	</section>
	<?php
}
