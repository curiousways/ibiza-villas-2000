<?php
/**
 * Section: Weekly special offer.
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

	$vid = (int) get_field( 'weekly_offer_villa', 'option' );
	if ( ! $vid ) {
		return;
	}

	$was   = get_field( 'weekly_offer_was_price', 'option' );
	$now   = get_field( 'weekly_offer_now_price', 'option' );
	$vfrom = get_field( 'weekly_offer_valid_from', 'option' );
	$vto   = get_field( 'weekly_offer_valid_to', 'option' );
	?>
	<section class="ibv-section-weekly-offer ibv-section ibv-section--alt">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( "This week's special offer", 'ibv' ),
					'level' => 'h2',
				]
			);
			?>
			<p class="ibv-section-weekly-offer__subcta">
				<a class="ibv-u-text-link" href="<?php echo esc_url( ibv_get_special_offers_url() ); ?>"><?php esc_html_e( 'Search all special offers', 'ibv' ); ?></a>
			</p>
			<div class="ibv-section-weekly-offer__card">
				<span class="ibv-section-weekly-offer__ribbon"><?php esc_html_e( "This week's deal", 'ibv' ); ?></span>
				<?php
				ibv_core_villa_card(
					[
						'villa'      => $vid,
						'variant'    => 'offer',
						'was_price'  => $was ? (float) $was : null,
						'now_price'  => $now ? (float) $now : null,
						'valid_from' => $vfrom ? (string) $vfrom : null,
						'valid_to'   => $vto ? (string) $vto : null,
						'cta_label'  => __( 'Enquire', 'ibv' ),
					]
				);
				?>
			</div>
		</div>
	</section>
	<?php
}
