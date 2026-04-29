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
 * Plain-text excerpt for a villa (~30 words).
 *
 * @param int $villa_id Post ID.
 * @return string
 */
function ibv_villa_excerpt_plain( $villa_id ) {
	$summary = get_field( 'property_summary', $villa_id );
	if ( $summary ) {
		return wp_trim_words( wp_strip_all_tags( (string) $summary ), 30, '…' );
	}
	$post = get_post( $villa_id );
	if ( $post && $post->post_excerpt ) {
		return wp_trim_words( wp_strip_all_tags( $post->post_excerpt ), 30, '…' );
	}
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
		'was_price'         => null,
		'now_price'         => null,
		'valid_from'        => null,
		'valid_to'          => null,
		'offer_price_text'  => '',
		'offer_dates_text'  => '',
		'cta_label'         => __( 'View Villa', 'ibv' ),
		'cta_url'           => '',
		'badge'             => '',
	];
	$args = wp_parse_args( $args, $defaults );

	$villa_id = ibv_resolve_villa_id( $args['villa'] );
	if ( ! $villa_id || 'villas' !== get_post_type( $villa_id ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-card' );
	wp_enqueue_style( 'ibv-button' );

	$variant = in_array( $args['variant'], [ 'default', 'offer', 'similar' ], true ) ? $args['variant'] : 'default';

	$permalink = $args['cta_url'] ? $args['cta_url'] : get_permalink( $villa_id );
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

	$root_classes = [
		'ibv-villa-card',
		'ibv-villa-card--' . $variant,
	];
	?>
	<article class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>">
		<?php if ( 'similar' === $variant && ! empty( $args['badge'] ) ) : ?>
			<span class="ibv-villa-card__badge"><?php echo esc_html( $args['badge'] ); ?></span>
		<?php endif; ?>

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

			<ul class="ibv-villa-card__facts">
				<?php if ( '' !== $bedrooms && null !== $bedrooms ) : ?>
					<li>
						<span class="ibv-villa-card__fact-icon" aria-hidden="true">🛏</span>
						<?php
						printf(
							/* translators: %d bedroom count */
							esc_html( _n( '%d bedroom', '%d bedrooms', (int) $bedrooms, 'ibv' ) ),
							(int) $bedrooms
						);
						?>
					</li>
				<?php endif; ?>
				<?php if ( '' !== $baths && null !== $baths ) : ?>
					<li>
						<span class="ibv-villa-card__fact-icon" aria-hidden="true">🛁</span>
						<?php
						printf(
							/* translators: %d bathroom count */
							esc_html( _n( '%d bathroom', '%d bathrooms', (int) $baths, 'ibv' ) ),
							(int) $baths
						);
						?>
					</li>
				<?php endif; ?>
				<?php if ( '' !== $sleeps && null !== $sleeps ) : ?>
					<li>
						<span class="ibv-villa-card__fact-icon" aria-hidden="true">👥</span>
						<?php
						printf(
							/* translators: %d guest count */
							esc_html( __( 'Sleeps %d', 'ibv' ) ),
							(int) $sleeps
						);
						?>
					</li>
				<?php endif; ?>
			</ul>

			<?php if ( $excerpt ) : ?>
				<p class="ibv-villa-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( 'offer' === $variant ) : ?>
				<div class="ibv-villa-card__offer-pricing">
					<?php if ( $args['was_price'] || $args['now_price'] ) : ?>
						<?php if ( $args['was_price'] ) : ?>
							<p class="ibv-villa-card__was">
								<span class="ibv-villa-card__was-label"><?php esc_html_e( 'Was', 'ibv' ); ?></span>
								<span class="ibv-villa-card__was-amount">
									<?php
									printf(
										/* translators: %s formatted price */
										esc_html__( 'From €%s / wk', 'ibv' ),
										esc_html( number_format_i18n( (float) $args['was_price'] ) )
									);
									?>
								</span>
							</p>
						<?php endif; ?>
						<?php if ( $args['now_price'] ) : ?>
							<p class="ibv-villa-card__now">
								<span class="ibv-villa-card__now-label"><?php esc_html_e( 'Now', 'ibv' ); ?></span>
								<span class="ibv-villa-card__now-amount">
									<?php
									printf(
										/* translators: %s formatted price */
										esc_html__( 'From €%s / wk', 'ibv' ),
										esc_html( number_format_i18n( (float) $args['now_price'] ) )
									);
									?>
								</span>
							</p>
						<?php endif; ?>
					<?php elseif ( $args['offer_price_text'] ) : ?>
						<p class="ibv-villa-card__offer-line"><?php echo esc_html( $args['offer_price_text'] ); ?></p>
					<?php endif; ?>
					<?php
					$from_d = ibv_format_acf_date_display( (string) $args['valid_from'] );
					$to_d   = ibv_format_acf_date_display( (string) $args['valid_to'] );
					if ( $from_d && $to_d ) {
						printf(
							'<p class="ibv-villa-card__valid"><span class="ibv-villa-card__valid-label">%s</span> %s – %s</p>',
							esc_html__( 'Valid:', 'ibv' ),
							esc_html( $from_d ),
							esc_html( $to_d )
						);
					} elseif ( $args['offer_dates_text'] ) {
						printf(
							'<p class="ibv-villa-card__valid"><span class="ibv-villa-card__valid-label">%s</span> %s</p>',
							esc_html__( 'Valid:', 'ibv' ),
							esc_html( $args['offer_dates_text'] )
						);
					}
					?>
				</div>
			<?php else : ?>
				<div class="ibv-villa-card__price">
					<?php /* Bob shell: API may replace amount; ACF villa_indicative_from_price is static fallback. */ ?>
					<span class="ibv-villa-card__price-prefix"><?php esc_html_e( 'From', 'ibv' ); ?></span>
					<span class="ibv-villa-card__price-amount" data-bob-from-price="<?php echo esc_attr( (string) $villa_id ); ?>">
						<?php
						$indicative = get_field( 'villa_indicative_from_price', $villa_id );
						if ( $indicative ) {
							printf( '€%s', esc_html( number_format_i18n( (float) $indicative ) ) );
						} else {
							echo '€420';
						}
						?>
					</span>
					<span class="ibv-villa-card__price-suffix"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
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
