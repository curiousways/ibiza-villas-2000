<?php
/**
 * Component: Villa special offers.
 *
 * Open, list-led treatment (deliberate deviation from the signed-off
 * Figma card — this brief is the design source): gold tag icon + label
 * header over hairline-separated offer rows, sitting directly on the
 * page surface. Reads the `villa_offers` ACF repeater. An offer is
 * "active" when its `offer_date_to` is today or later (site timezone).
 * Active offers are sorted ascending by `offer_date_from`. If there are
 * no active offers the component renders nothing.
 *
 * One active offer → static block (nothing to collapse, no accordion).
 * Two or more → native <details>/<summary>; open by default at 2,
 * collapsed at 3+ (so multiple offers don't push pricing below the fold).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a villa's active offers from the `villa_offers` ACF repeater.
 *
 * An offer is active when `offer_name`, `offer_date_from` and
 * `offer_date_to` are all non-empty and `offer_date_to` is today or
 * later (site timezone). Single source of truth for the rule — used
 * by the offers accordion, the Special Offers grid, and the villa-card
 * `data-bob-has-offer` marker.
 *
 * @param int $villa_id Villa post ID.
 * @return array[] Active repeater rows sorted by `offer_date_from` ascending.
 */
function ibv_villa_get_active_offers( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return [];
	}

	$offers = get_field( 'villa_offers', $villa_id );
	if ( ! is_array( $offers ) || ! count( $offers ) ) {
		return [];
	}

	$today  = current_time( 'Ymd' );
	$active = [];
	foreach ( $offers as $offer ) {
		$from = (string) ( $offer['offer_date_from'] ?? '' );
		$to   = (string) ( $offer['offer_date_to'] ?? '' );
		$name = trim( (string) ( $offer['offer_name'] ?? '' ) );
		if ( ! $from || ! $to || ! $name || $to < $today ) {
			continue;
		}
		$active[] = $offer;
	}

	usort(
		$active,
		static function ( $a, $b ) {
			return strcmp( (string) $a['offer_date_from'], (string) $b['offer_date_from'] );
		}
	);

	return $active;
}

/**
 * Count published villas with at least one currently-active offer.
 *
 * Drives the "Special offers only (N)" count on the listing filter toggle.
 * Shares ibv_villa_get_active_offers() so the count, the detail accordion,
 * the Special Offers grid and the villa-card `data-bob-has-offer` markers
 * can never disagree. A dozen villas behind full-page caching — a direct
 * loop per render beats managing a date-keyed transient.
 *
 * @return int
 */
function ibv_count_villas_with_active_offers() {
	$villa_ids = get_posts(
		[
			'post_type'      => 'villas',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		]
	);

	$count = 0;
	foreach ( $villa_ids as $villa_id ) {
		if ( count( ibv_villa_get_active_offers( $villa_id ) ) ) {
			$count++;
		}
	}

	return $count;
}

/**
 * Render the offer rows shared by both variants.
 *
 * Headline first, dates second — "20% off" is the message, the range is
 * the qualifier — in DOM order (not CSS `order:`) so reading order
 * matches visual order.
 *
 * @param array[] $offers Active repeater rows.
 */
function ibv_core_villa_offers_list( array $offers ) {
	?>
	<ul class="ibv-villa-offers__list">
		<?php foreach ( $offers as $offer ) : ?>
			<?php
			$headline = trim( (string) ( $offer['offer_headline'] ?? '' ) );
			if ( '' === $headline ) {
				// An offer saved with a name but no headline would otherwise
				// render as a dateline with no message.
				$headline = trim( (string) ( $offer['offer_name'] ?? '' ) );
			}
			$description = trim( (string) ( $offer['offer_description'] ?? '' ) );
			$dates       = ibv_core_villa_offers_format_range(
				(string) $offer['offer_date_from'],
				(string) $offer['offer_date_to']
			);
			?>
			<li class="ibv-villa-offers__item">
				<header class="ibv-villa-offers__item-header">
					<?php if ( $headline ) : ?>
						<p class="ibv-villa-offers__headline"><?php echo esc_html( $headline ); ?></p>
					<?php endif; ?>
					<?php if ( $dates ) : ?>
						<p class="ibv-villa-offers__dates"><?php echo esc_html( $dates ); ?></p>
					<?php endif; ?>
				</header>

				<?php if ( $description ) : ?>
					<div class="ibv-villa-offers__desc"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Render the villa-offers block.
 *
 * @param array $args {
 *     @type int $post_id Required. Villa post ID.
 * }
 */
function ibv_core_villa_offers( $args = [] ) {
	$args    = wp_parse_args( $args, [ 'post_id' => 0 ] );
	$post_id = (int) $args['post_id'];
	if ( ! $post_id ) {
		return;
	}

	$active = ibv_villa_get_active_offers( $post_id );
	if ( ! count( $active ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-offers' );

	$count = count( $active );

	$icon = ibv_core_icon(
		'tag',
		[
			'size'  => 20,
			'class' => 'ibv-villa-offers__icon',
		]
	);

	if ( 1 === $count ) :
		// Static variant: nothing to collapse, so no <details>, no chevron,
		// no count. The label is a heading so the block sits in the document
		// outline between the facts strip and "Villa Overview".
		?>
		<section class="ibv-villa-offers ibv-villa-offers--static">
			<header class="ibv-villa-offers__header">
				<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ibv_core_icon() output. ?>
				<h3 class="ibv-villa-offers__label"><?php esc_html_e( 'Special offer', 'ibv' ); ?></h3>
			</header>
			<?php ibv_core_villa_offers_list( $active ); ?>
		</section>
		<?php
		return;
	endif;

	$open = $count <= 2;

	$count_label = sprintf(
		/* translators: %d: number of active offers */
		esc_html( _n( '%d offer', '%d offers', $count, 'ibv' ) ),
		(int) $count
	);
	?>
	<details class="ibv-villa-offers"<?php echo $open ? ' open' : ''; ?>>
		<summary class="ibv-villa-offers__summary">
			<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ibv_core_icon() output. ?>
			<span class="ibv-villa-offers__label"><?php esc_html_e( 'Special offers', 'ibv' ); ?></span>
			<span class="ibv-villa-offers__count"><?php echo $count_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sprintf above. ?></span>
			<?php
			echo ibv_core_icon( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ibv_core_icon() output.
				'chevron-down',
				[
					'size'  => 20,
					'class' => 'ibv-villa-offers__chevron',
				]
			);
			?>
		</summary>
		<?php ibv_core_villa_offers_list( $active ); ?>
	</details>
	<?php
}

/**
 * Format an offer's date range for display.
 *
 * Same month → "15–22 May 2026". Different months, same year →
 * "28 May – 3 Jun 2026". Different years → "28 Dec 2026 – 3 Jan 2027".
 * Returns empty string if either date is unparseable.
 *
 * @param string $from Stored Ymd.
 * @param string $to   Stored Ymd.
 * @return string
 */
function ibv_core_villa_offers_format_range( $from, $to ) {
	$df = DateTimeImmutable::createFromFormat( 'Ymd', $from );
	$dt = DateTimeImmutable::createFromFormat( 'Ymd', $to );
	if ( ! $df || ! $dt ) {
		return '';
	}

	$from_ts = $df->getTimestamp();
	$to_ts   = $dt->getTimestamp();

	if ( $df->format( 'Y' ) !== $dt->format( 'Y' ) ) {
		return sprintf(
			'%s – %s',
			wp_date( 'j M Y', $from_ts ),
			wp_date( 'j M Y', $to_ts )
		);
	}

	if ( $df->format( 'm' ) !== $dt->format( 'm' ) ) {
		return sprintf(
			'%s – %s',
			wp_date( 'j M', $from_ts ),
			wp_date( 'j M Y', $to_ts )
		);
	}

	return sprintf(
		'%s–%s',
		wp_date( 'j', $from_ts ),
		wp_date( 'j M Y', $to_ts )
	);
}
