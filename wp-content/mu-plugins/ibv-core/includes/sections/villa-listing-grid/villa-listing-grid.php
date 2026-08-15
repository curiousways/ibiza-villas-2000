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

	// Same three-param test as isProbe in villa-listing-grid.js: when a dated
	// search is active, first paint shows skeletons instead of the unfiltered
	// fallback catalog. JS clears the modifier on API response or failure.
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
			'probe'    => ibv_villa_listing_probe_params(),
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
	<section id="results" class="ibv-listing-grid-section ibv-section">
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
					<?php
					// Zero active offers → no toggle at all: a filter that can
					// only empty the grid looks broken and teaches visitors to
					// distrust the other filters.
					$offers_count = ibv_count_villas_with_active_offers();
					if ( $offers_count > 0 ) :
						?>
					<li class="ibv-listing-grid-section__filter">
						<span class="ibv-offers-toggle">
							<?php
							// Styled NATIVE checkbox (not a div "switch"): keyboard,
							// screen-reader semantics and form behaviour for free.
							// data-bob-filter-offers stays — villa-listing-grid.js
							// and the e2e specs bind to it.
							?>
							<input
								type="checkbox"
								id="ibv-filter-offers"
								class="ibv-offers-toggle__input"
								data-bob-filter-offers
							>
							<label class="ibv-offers-toggle__label" for="ibv-filter-offers">
								<span class="ibv-offers-toggle__switch" aria-hidden="true"></span>
								<span class="ibv-offers-toggle__text">
									<?php esc_html_e( 'Special offers only', 'ibv' ); ?>
									<?php // Inside the <label>: part of the accessible name ("Special offers only (4)"). ?>
									<span class="ibv-offers-toggle__count">(<?php echo esc_html( (string) $offers_count ); ?>)</span>
								</span>
							</label>
						</span>
					</li>
					<?php endif; ?>
				</ul>
				<p class="ibv-listing-grid-section__count" data-bob-results-count hidden></p>
			</div>
			<?php
			// Without a dated search the probe fetch will overwrite the static
			// ACF "from" prices with live rates — --price-pending masks the
			// amounts until then so two different numbers never flash in
			// sequence. JS clears it on probe response or failure.
			?>
			<?php
			// Stack: grid and skeleton row occupy the same named grid area,
			// overlapping instead of flowing — the stack is as tall as the
			// taller child, so the page height never pumps when the skeleton
			// cross-fades away over the entering cards.
			?>
			<div class="ibv-listing-stack">
			<div
				class="ibv-listing-grid ibv-grid ibv-grid--4<?php echo $is_searching ? ' ibv-listing-grid--searching' : ' ibv-listing-grid--price-pending'; ?>"
				data-bob-listing-grid
				<?php echo $is_searching ? 'aria-busy="true"' : ''; ?>
			>
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
			<?php
			if ( $is_searching ) {
				// One self-clipping row: shows however many cells fit a single
				// grid row at the current width (see .ibv-villa-skeleton-row in
				// villa-listing-grid.css), so the stack never reserves more
				// than one row of height. Placed after the grid so the
				// --searching adjacent-sibling selector can reveal it.
				?>
				<div class="ibv-villa-skeleton-row" data-bob-skeleton aria-hidden="true">
					<?php for ( $i = 0; $i < 6; $i++ ) : ?>
						<div class="ibv-villa-skeleton">
							<div class="ibv-villa-skeleton__media"></div>
							<div class="ibv-villa-skeleton__line ibv-villa-skeleton__line--title"></div>
							<div class="ibv-villa-skeleton__line ibv-villa-skeleton__line--meta"></div>
							<div class="ibv-villa-skeleton__line ibv-villa-skeleton__line--price"></div>
						</div>
					<?php endfor; ?>
				</div>
				<?php
			}
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

/**
 * Probe-mode search window: a default future week (+30 to +37 days, 2 pax)
 * used to fetch a real "from" weekly rate when no dated search is active.
 * Computed in UTC to mirror defaultProbeRange() in villa-listing-grid.js,
 * and handed to JS via ibvListingSearch.probe so the runtime fetch URL is
 * byte-identical to the <head> preload below.
 *
 * @return array{date_from: string, date_to: string, pax: string}
 */
function ibv_villa_listing_probe_params() {
	return [
		'date_from' => gmdate( 'Y-m-d', time() + 30 * DAY_IN_SECONDS ),
		'date_to'   => gmdate( 'Y-m-d', time() + 37 * DAY_IN_SECONDS ),
		'pax'       => '2',
	];
}

/**
 * Head-start the availability call. On the listing page, preload the API
 * request from <head> — the dated search when one is in the URL, the probe
 * window otherwise — so the network round trip overlaps HTML parsing
 * instead of starting at DOMContentLoaded. In probe mode this directly
 * shortens the --price-pending mask on the card prices. The fetch in
 * villa-listing-grid.js attaches to the in-flight response — its URL, mode
 * and credentials must keep matching this hint or the browser fetches twice.
 */
function ibv_villa_listing_preload_availability() {
	if ( ! is_page_template( 'page-villa-listing.php' ) ) {
		return;
	}

	$qs = ibv_get_villa_listing_search_params();
	if ( '' === $qs['date_from'] || '' === $qs['date_to'] || '' === $qs['pax'] ) {
		$qs = ibv_villa_listing_probe_params();
	}

	// Built field-by-field with rawurlencode so the string is byte-identical
	// to buildUrl() in villa-listing-grid.js (same key order, same encoding).
	$url = ibv_get_bob_endpoint_url()
		. '?date_from=' . rawurlencode( $qs['date_from'] )
		. '&date_to=' . rawurlencode( $qs['date_to'] )
		. '&pax=' . rawurlencode( $qs['pax'] );

	printf( '<link rel="preload" href="%s" as="fetch" crossorigin="anonymous">' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'ibv_villa_listing_preload_availability', 2 );
