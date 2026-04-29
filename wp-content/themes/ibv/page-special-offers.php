<?php
/**
 * Template Name: Special Offers
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'ibv-section-featured-villas' );
wp_enqueue_style( 'ibv-section-heading' );
wp_enqueue_style( 'ibv-section-special-offers-page' );

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="ibv-page ibv-special-offers">
		<div class="ibv-container">
			<header class="ibv-special-offers__hero">
				<?php
				ibv_core_section_heading(
					[
						'eyebrow' => __( 'Save on your dream villa', 'ibv' ),
						'title'   => get_the_title(),
						'level'   => 'h1',
					]
				);
				?>
				<div class="ibv-prose ibv-special-offers__intro">
					<?php the_content(); ?>
				</div>
			</header>

			<?php
			$offers = get_field( 'offer_table' );
			if ( ! empty( $offers ) && is_array( $offers ) ) :
				?>
				<section class="ibv-special-offers__list" aria-label="<?php esc_attr_e( 'Current offers', 'ibv' ); ?>">
					<?php
					foreach ( $offers as $offer ) :
						$villa_raw = $offer['villa'] ?? null;
						$vid       = 0;
						if ( $villa_raw instanceof WP_Post ) {
							$vid = (int) $villa_raw->ID;
						} elseif ( is_numeric( $villa_raw ) ) {
							$vid = (int) $villa_raw;
						}
						if ( ! $vid ) {
							continue;
						}
						$price = isset( $offer['special_offer_price'] ) ? (string) $offer['special_offer_price'] : '';
						$dates = isset( $offer['dates'] ) ? (string) $offer['dates'] : '';
						ibv_core_villa_card(
							[
								'villa'            => $vid,
								'variant'          => 'offer',
								'offer_price_text' => $price,
								'offer_dates_text' => $dates,
								'cta_label'        => __( 'Enquire now', 'ibv' ),
							]
						);
					endforeach;
					?>
				</section>
			<?php else : ?>
				<p class="ibv-empty-state"><?php esc_html_e( 'No special offers available right now.', 'ibv' ); ?></p>
			<?php endif; ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
