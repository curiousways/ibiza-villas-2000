<?php
/**
 * Section: Villa Listing Hero.
 *
 * Title and description. The main collection also gets the search form
 * and short-breaks line. Filtered pages (Location or Minimum sleeps) do not.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_villa_listing_hero() {
	wp_enqueue_style( 'ibv-section-villa-listing-hero' );

	$page_id = get_the_ID();
	if ( ! $page_id ) {
		return;
	}

	$title       = get_the_title( $page_id );
	$description = (string) get_field( 'listing_description', $page_id );
	$is_area     = ibv_is_filtered_villa_listing( $page_id );
	?>
	<section class="ibv-section-villa-listing-hero ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-section-villa-listing-hero__inner">
			<div class="ibv-section-villa-listing-hero__copy">
				<?php if ( $title ) : ?>
					<h1 class="ibv-section-villa-listing-hero__title ibv-font-display">
						<?php echo esc_html( $title ); ?>
					</h1>
				<?php endif; ?>

				<span class="ibv-rule ibv-rule--gold" aria-hidden="true"></span>

				<?php if ( $description ) : ?>
					<p class="ibv-section-villa-listing-hero__intro"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>

				<?php if ( ! $is_area ) : ?>
					<div class="ibv-section-villa-listing-hero__search">
						<?php ibv_core_hero_search(); ?>
					</div>

					<?php
					// Directly under the date picker it references — the moment
					// someone hesitates over "Add dates" for a short stay.
					ibv_the_short_breaks_statement( 'ibv-section-villa-listing-hero__short-breaks' );
					?>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
