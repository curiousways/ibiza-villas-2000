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

	$is_searching = '' !== $qs['date_from'] && '' !== $qs['date_to'] && '' !== $qs['pax'];
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

	// The active-search pill is server-rendered, not revealed by JS on API
	// response: it is the tallest toolbar item, so a late reveal changes the
	// sticky toolbar's height mid-view. Its label is pure URL state
	// (date_from / date_to / pax) — the API has nothing to add.
	$selected_label = '';
	if ( $is_searching ) {
		$range = ibv_villa_listing_format_range( $qs['date_from'], $qs['date_to'] );
		if ( '' !== $range ) {
			$pax = max( 1, (int) $qs['pax'] );
			/* translators: %d: number of guests. */
			$selected_label = $range . ' · ' . sprintf( _n( '%d guest', '%d guests', $pax, 'ibv' ), $pax );
		}
	}

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
			<div class="ibv-listing-grid-section__toolbar" data-bob-listing-toolbar>
				<ul class="ibv-listing-grid-section__filters">
					<li class="ibv-listing-grid-section__filter" data-bob-selected-dates<?php echo '' === $selected_label ? ' hidden' : ''; ?>>
						<span class="ibv-listing-grid-section__dates">
							<span data-bob-selected-dates-label><?php echo esc_html( $selected_label ); ?></span>
							<a
								class="ibv-listing-grid-section__dates-clear"
								href="<?php echo esc_url( $listing_root ); ?>"
								aria-label="<?php esc_attr_e( 'Clear selected dates', 'ibv' ); ?>"
							>&times;</a>
						</span>
					</li>
					<li class="ibv-listing-grid-section__filter ibv-listing-grid-section__filter--link">
						<a href="<?php echo esc_url( $listing_root ); ?>" class="ibv-listing-grid-section__filter-link">
							<?php esc_html_e( 'Short breaks', 'ibv' ); ?>
						</a>
					</li>
					<li class="ibv-listing-grid-section__filter">
						<label class="ibv-listing-grid-section__checkbox">
							<input type="checkbox" data-bob-filter-offers>
							<span><?php esc_html_e( 'Offers', 'ibv' ); ?></span>
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

/**
 * Format a YYYY-MM-DD pair as a compact human-readable range, e.g.
 * "22 May – 29", "22 May – 4 Jun", "30 Dec – 4 Jan 2027". Mirrors
 * formatRangeForDisplay() in date-range-picker.js (en-GB Intl output) so the
 * toolbar pill never disagrees with the search widget's display input.
 * Returns '' when either value is not a real Y-m-d date.
 *
 * @param string $from_ymd Start date, YYYY-MM-DD.
 * @param string $to_ymd   End date, YYYY-MM-DD.
 * @return string
 */
function ibv_villa_listing_format_range( $from_ymd, $to_ymd ) {
	$from = DateTimeImmutable::createFromFormat( '!Y-m-d', (string) $from_ymd );
	$to   = DateTimeImmutable::createFromFormat( '!Y-m-d', (string) $to_ymd );

	// Round-trip check rejects rollover dates (2026-02-31) and loose
	// formats (2026-5-2) that createFromFormat silently accepts.
	if ( ! $from || ! $to || $from->format( 'Y-m-d' ) !== $from_ymd || $to->format( 'Y-m-d' ) !== $to_ymd ) {
		return '';
	}

	// PHP's M prints "Sep" where en-GB Intl prints "Sept" — patch the one
	// month that differs.
	$month_day = static function ( DateTimeImmutable $d ) {
		return str_replace( 'Sep', 'Sept', $d->format( 'j M' ) );
	};

	if ( $from->format( 'Y-m' ) === $to->format( 'Y-m' ) ) {
		return $month_day( $from ) . ' – ' . $to->format( 'j' );
	}
	if ( $from->format( 'Y' ) === $to->format( 'Y' ) ) {
		return $month_day( $from ) . ' – ' . $month_day( $to );
	}
	return $month_day( $from ) . ' – ' . $month_day( $to ) . ' ' . $to->format( 'Y' );
}
