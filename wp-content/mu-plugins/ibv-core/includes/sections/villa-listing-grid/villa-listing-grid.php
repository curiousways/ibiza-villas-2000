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
	wp_enqueue_style( 'ibv-button' );

	wp_enqueue_script( 'ibv-villa-listing-search' );
	$qs = ibv_get_villa_listing_search_params();
	wp_localize_script(
		'ibv-villa-listing-search',
		'ibvListingSearch',
		[
			'endpoint' => ibv_get_bob_endpoint_url(),
			'params'   => [
				'date_from' => $qs['date_from'],
				'date_to'   => $qs['date_to'],
				'pax'       => $qs['pax'],
			],
			'i18n'     => [
				'showing' => __( 'Showing %d villas', 'ibv' ),
			],
		]
	);

	$listing_root = ibv_get_search_villas_url();
	?>
	<section class="ibv-listing-grid-section ibv-section">
		<div class="ibv-container">
			<?php /* ─────────────────────────────────────────────────────────────
			       BOB API INTEGRATION SHELL — listing grid
			       ─────────────────────────────────────────────────────────────
			       Server renders fallback posts inside [data-bob-listing-grid].
			       JS at villa-listing-grid.js calls the API in search mode and
			       filters/sorts these cards in place.
			       Query params on this page: date_from, date_to, pax (GET).
			       Spec: Notion → IBZ002 → API Integration Spec
			       ──────────────────────────────────────────────────────────── */ ?>
			<div class="ibv-listing-grid-section__toolbar" data-bob-listing-toolbar hidden>
				<ul class="ibv-listing-grid-section__filters">
					<li class="ibv-listing-grid-section__filter ibv-listing-grid-section__filter--link">
						<a href="<?php echo esc_url( $listing_root ); ?>" class="ibv-listing-grid-section__filter-link">
							<?php esc_html_e( 'Short breaks', 'ibv' ); ?>
						</a>
					</li>
					<?php /* TODO #2 follow-up: Offers filter contract still TBD with Steve.
					       Checkbox is rendered disabled until the response flag is confirmed. */ ?>
					<li class="ibv-listing-grid-section__filter">
						<label class="ibv-listing-grid-section__checkbox">
							<input type="checkbox" disabled aria-disabled="true" data-bob-filter-offers>
							<span><?php esc_html_e( 'Offers', 'ibv' ); ?></span>
							<span class="ibv-listing-grid-section__filter-meta"><?php esc_html_e( '(coming soon)', 'ibv' ); ?></span>
						</label>
					</li>
				</ul>
				<p class="ibv-listing-grid-section__count" data-bob-results-count hidden></p>
			</div>
			<div class="ibv-listing-grid ibv-grid ibv-grid--4" data-bob-listing-grid>
				<?php
				$fallback = new WP_Query(
					[
						'post_type'           => 'villas',
						'posts_per_page'      => -1,
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
