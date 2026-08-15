<?php
/**
 * Component: Villa card.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a villa ID from a post object or ID.
 *
 * @param int|WP_Post $villa Villa.
 * @return int
 */
function ibv_resolve_villa_id( $villa ) {
	if ( $villa instanceof WP_Post ) {
		return (int) $villa->ID;
	}
	return (int) $villa;
}

/**
 * Plain-text card description for a villa.
 *
 * villa_summary is the editorial one-paragraph summary. It is shown in
 * full on the card and again under "Villa Overview" on the single — the
 * field contract is a short para, so the card must not ellipsis-clip it.
 * The long post_content fallback is still trimmed; that body is not
 * written for the card.
 *
 * @param int $villa_id Post ID.
 * @return string
 */
function ibv_villa_excerpt_plain( $villa_id ) {
	$summary = get_field( 'villa_summary', $villa_id );
	if ( $summary ) {
		return trim( wp_strip_all_tags( (string) $summary ) );
	}
	// No post_excerpt branch: the villas CPT dropped excerpt support, so a
	// stale stored excerpt would be invisible and uneditable in admin.
	$post = get_post( $villa_id );
	if ( $post && $post->post_content ) {
		return wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
	}
	return '';
}

/**
 * Primary location term name for pill display.
 *
 * @param int $villa_id Post ID.
 * @return string
 */
function ibv_villa_location_label( $villa_id ) {
	$terms = get_the_terms( $villa_id, 'property_location' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}
	$term = array_shift( $terms );
	return $term ? $term->name : '';
}

/**
 * Format ACF date picker value (Ymd or other) for display.
 *
 * @param string $ymd Raw date from ACF.
 * @return string
 */
function ibv_format_acf_date_display( $ymd ) {
	if ( ! $ymd ) {
		return '';
	}
	if ( strlen( (string) $ymd ) === 8 && ctype_digit( (string) $ymd ) ) {
		$dt = DateTime::createFromFormat( 'Ymd', (string) $ymd );
	} else {
		$dt = date_create( (string) $ymd );
	}
	return $dt ? $dt->format( 'j M Y' ) : '';
}

/**
 * Render a villa card.
 *
 * @param array $args See project brief (Pass 3c-foundation).
 */
function ibv_core_villa_card( $args = [] ) {
	$defaults = [
		'villa'             => 0,
		'variant'           => 'default',
		'cta_label'         => __( 'View Villa', 'ibv' ),
		'cta_url'           => '',
		// Offer fields — populated by the Special Offers grid only.
		// `offer_dates` is a pre-formatted string (date-range formatter
		// lives with the villa-offers component to keep one source of
		// truth for offer date formatting).
		'offer_dates'       => '',
		'offer_headline'    => '',
		'offer_description' => '',
	];
	$args = wp_parse_args( $args, $defaults );

	$villa_id = ibv_resolve_villa_id( $args['villa'] );
	if ( ! $villa_id || 'villas' !== get_post_type( $villa_id ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-card' );
	wp_enqueue_style( 'ibv-button' );

	$variant = in_array( $args['variant'], [ 'default', 'offer' ], true ) ? $args['variant'] : 'default';

	$permalink = $args['cta_url'] ? $args['cta_url'] : get_permalink( $villa_id );

	// Forward an active villa-listing search (date_from/date_to/pax from
	// the current URL) to the detail page so the enquiry panel can prefill
	// and fetch live pricing on arrival.
	$forward_params = [];
	if ( isset( $_GET['date_from'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['date_from'] ) );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
			$forward_params['date_from'] = $raw;
		}
	}
	if ( isset( $_GET['date_to'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['date_to'] ) );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
			$forward_params['date_to'] = $raw;
		}
	}
	if ( isset( $_GET['pax'] ) ) {
		$pax = absint( wp_unslash( $_GET['pax'] ) );
		if ( $pax > 0 ) {
			$forward_params['pax'] = $pax;
		}
	}
	if ( ! empty( $forward_params ) ) {
		$permalink = add_query_arg( $forward_params, $permalink );
	}
	$title     = get_field( 'villa_pretty_name', $villa_id );
	if ( ! $title ) {
		$title = get_the_title( $villa_id );
	}

	$bedrooms  = get_field( 'property_bedrooms', $villa_id );
	$baths     = get_field( 'property_bathrooms', $villa_id );
	$sleeps    = get_field( 'property_sleeps', $villa_id );
	$excerpt   = ibv_villa_excerpt_plain( $villa_id );
	$location  = ibv_villa_location_label( $villa_id );

	$thumb_id = get_post_thumbnail_id( $villa_id );

	$show_offer_row = ( 'offer' === $variant )
		|| (
			'default' === $variant
			&& (
				$args['offer_dates']
				|| $args['offer_headline']
				|| $args['offer_description']
			)
		);

	$root_classes = [
		'ibv-villa-card',
		'ibv-villa-card--' . $variant,
	];

	$property_id     = (string) get_field( 'property_id', $villa_id );
	$indicative_from = get_field( 'villa_indicative_from_price', $villa_id );
	$has_offer       = count( ibv_villa_get_active_offers( $villa_id ) ) > 0;
	?>
	<article
		class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>"
		data-bob-property-id="<?php echo esc_attr( $property_id ); ?>"
		<?php /* No data-price when the ACF from-price is empty — the JS price sort treats a missing attribute as Infinity, so unpriced cards sort last. */ ?>
		<?php echo $indicative_from ? ' data-price="' . esc_attr( (string) (float) $indicative_from ) . '"' : ''; ?>
		<?php echo $has_offer ? ' data-bob-has-offer' : ''; ?>
	>
		<a href="<?php echo esc_url( $permalink ); ?>" class="ibv-villa-card__media">
			<?php
			if ( $thumb_id ) {
				ibv_core_image(
					$thumb_id,
					'ibv-card',
					[
						'class'    => 'ibv-villa-card__image',
						'loading'  => 'lazy',
						'decoding' => 'async',
					]
				);
			}
			?>
			<?php if ( $location ) : ?>
				<span class="ibv-villa-card__location"><?php echo esc_html( $location ); ?></span>
			<?php endif; ?>
		</a>

		<div class="ibv-villa-card__body">
			<h3 class="ibv-villa-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>

			<?php if ( $bedrooms || $baths || $sleeps ) : ?>
				<ul class="ibv-villa-card__facts">
					<?php if ( '' !== $bedrooms && null !== $bedrooms ) : ?>
						<li class="ibv-villa-card__fact" aria-label="<?php echo esc_attr( sprintf( _n( '%d bedroom', '%d bedrooms', (int) $bedrooms, 'ibv' ), (int) $bedrooms ) ); ?>">
							<?php
							echo ibv_core_icon(
								'bed',
								[
									'class' => 'ibv-villa-card__fact-icon',
									'size'  => 14,
								]
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="ibv-villa-card__fact-value"><?php echo esc_html( (int) $bedrooms ); ?></span>
						</li>
					<?php endif; ?>
					<?php if ( '' !== $baths && null !== $baths ) : ?>
						<li class="ibv-villa-card__fact" aria-label="<?php echo esc_attr( sprintf( _n( '%d bathroom', '%d bathrooms', (int) $baths, 'ibv' ), (int) $baths ) ); ?>">
							<?php
							echo ibv_core_icon(
								'bath',
								[
									'class' => 'ibv-villa-card__fact-icon',
									'size'  => 14,
								]
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="ibv-villa-card__fact-value"><?php echo esc_html( (int) $baths ); ?></span>
						</li>
					<?php endif; ?>
					<?php if ( '' !== $sleeps && null !== $sleeps ) : ?>
						<li class="ibv-villa-card__fact" aria-label="<?php echo esc_attr( sprintf( __( 'Sleeps %d', 'ibv' ), (int) $sleeps ) ); ?>">
							<?php
							echo ibv_core_icon(
								'users',
								[
									'class' => 'ibv-villa-card__fact-icon',
									'size'  => 14,
								]
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="ibv-villa-card__fact-value"><?php echo esc_html( (int) $sleeps ); ?></span>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<?php if ( 'default' === $variant ) : ?>
				<hr class="ibv-villa-card__rule" aria-hidden="true" />
			<?php endif; ?>

			<?php if ( $excerpt ) : ?>
				<p class="ibv-villa-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( $show_offer_row ) : ?>
				<div class="ibv-villa-card__offer">
					<?php if ( $args['offer_dates'] || $args['offer_headline'] ) : ?>
						<p class="ibv-villa-card__offer-meta">
							<?php if ( $args['offer_dates'] ) : ?>
								<span class="ibv-villa-card__offer-dates"><?php echo esc_html( $args['offer_dates'] ); ?></span>
							<?php endif; ?>
							<?php if ( $args['offer_dates'] && $args['offer_headline'] ) : ?>
								<span class="ibv-meta-dot" aria-hidden="true"></span>
							<?php endif; ?>
							<?php if ( $args['offer_headline'] ) : ?>
								<span class="ibv-villa-card__offer-headline"><?php echo esc_html( $args['offer_headline'] ); ?></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( $args['offer_description'] ) : ?>
						<p class="ibv-villa-card__offer-desc"><?php echo esc_html( $args['offer_description'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="ibv-villa-card__price">
					<?php /* Bob shell: API may replace amount; ACF villa_indicative_from_price is static fallback. */ ?>
					<?php if ( $indicative_from ) : ?>
						<span class="ibv-villa-card__price-prefix"><?php esc_html_e( 'From', 'ibv' ); ?></span>
						<span class="ibv-villa-card__price-amount" data-bob-from-price="<?php echo esc_attr( (string) $villa_id ); ?>">
							<?php printf( '€%s', esc_html( number_format_i18n( (float) $indicative_from ) ) ); ?>
						</span>
						<span class="ibv-villa-card__price-suffix"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
					<?php else : ?>
						<?php /* No price known: cards show nothing rather than a "Select dates for price" prompt — that nudge lives on the villa detail page (villa-overview), where the date picker it points to actually is. The empty amount and hidden prefix/suffix stay in the DOM so the listing JS can still hydrate and reveal a real API rate. */ ?>
						<span class="ibv-villa-card__price-prefix" hidden><?php esc_html_e( 'From', 'ibv' ); ?></span>
						<span class="ibv-villa-card__price-amount" data-bob-from-price="<?php echo esc_attr( (string) $villa_id ); ?>"></span>
						<span class="ibv-villa-card__price-suffix" hidden><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			ibv_core_button(
				[
					'url'     => $permalink,
					'label'   => $args['cta_label'],
					'variant' => 'secondary',
					'size'    => 'small',
				]
			);
			?>
		</div>
	</article>
	<?php
}
