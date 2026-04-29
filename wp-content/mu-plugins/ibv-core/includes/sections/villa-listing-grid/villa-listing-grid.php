<?php
/**
 * Section: Villa listing grid (Bob shell + server fallback).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders fallback grid; Bob replaces [data-bob-listing-grid] contents.
 */
function ibv_core_section_villa_listing_grid() {
	wp_enqueue_style( 'ibv-section-villa-listing-grid' );
	wp_enqueue_style( 'ibv-villa-card' );
	?>
	<section class="ibv-listing-grid-section ibv-section">
		<div class="ibv-container">
			<?php /* ─────────────────────────────────────────────────────────────
			       BOB API INTEGRATION SHELL — listing grid
			       ─────────────────────────────────────────────────────────────
			       Server renders fallback posts inside [data-bob-listing-grid].
			       Bob replaces inner HTML on API response.
			       Query params on this page: date_from, date_to, pax (GET).
			       Spec: Notion → IBZ002 → API Integration Spec
			       ──────────────────────────────────────────────────────────── */ ?>
			<div class="ibv-listing-grid ibv-grid ibv-grid--3" data-bob-listing-grid>
				<?php
				$fallback = new WP_Query(
					[
						'post_type'           => 'villas',
						'posts_per_page'      => 6,
						'orderby'             => 'menu_order',
						'order'               => 'ASC',
						'no_found_rows'       => true,
						'ignore_sticky_posts' => true,
					]
				);
				while ( $fallback->have_posts() ) :
					$fallback->the_post();
					ibv_core_villa_card( [ 'villa' => get_the_ID() ] );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<?php /* ─────────── END BOB SHELL ─────────── */ ?>
		</div>
	</section>
	<?php
}
