<?php
/**
 * Component: Offer panel.
 *
 * Reusable side-by-side panel rendering a villa with Was/Now pricing.
 * Used by the featured-offer section and the special-offers page.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render an offer panel.
 *
 * @param array $args {
 *     @type int|WP_Post $villa             Required. Villa post or ID.
 *     @type float|null  $was_price         Optional. Was price (per week).
 *     @type float|null  $now_price         Optional. Now price (per week).
 *     @type string|null $valid_from        Optional. ACF date (Ymd) or parseable.
 *     @type string|null $valid_to          Optional. ACF date (Ymd) or parseable.
 *     @type string      $cta_label         CTA button label. Default 'View Villa'.
 *     @type string      $cta_url           CTA URL. Default villa permalink.
 *     @type bool        $show_now_asterisk Whether to render '*' after Now amount.
 *     @type string      $footnote          Footnote text rendered below the panel.
 *     @type string      $section_title     Optional in-panel header title (e.g. "This Week's Special Offer").
 *     @type array       $section_cta       Optional in-panel header CTA. ['url' => ..., 'label' => ...]
 * }
 */
function ibv_core_offer_panel( $args = [] ) {
	$defaults = [
		'villa'             => 0,
		'was_price'         => null,
		'now_price'         => null,
		'valid_from'        => null,
		'valid_to'          => null,
		'cta_label'         => __( 'View Villa', 'ibv' ),
		'cta_url'           => '',
		'show_now_asterisk' => false,
		'footnote'          => '',
		'section_title'     => '',
		'section_cta'       => [],
	];
	$args = wp_parse_args( $args, $defaults );

	$vid = ibv_resolve_villa_id( $args['villa'] );
	if ( ! $vid || 'villas' !== get_post_type( $vid ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-offer-panel' );

	$title = get_field( 'villa_pretty_name', $vid );
	if ( ! $title ) {
		$title = get_the_title( $vid );
	}
	$bedrooms  = get_field( 'property_bedrooms', $vid );
	$baths     = get_field( 'property_bathrooms', $vid );
	$sleeps    = get_field( 'property_sleeps', $vid );
	$excerpt   = ibv_villa_excerpt_plain( $vid );
	$location  = ibv_villa_location_label( $vid );
	$thumb_id  = get_post_thumbnail_id( $vid );
	$permalink = $args['cta_url'] ? $args['cta_url'] : get_permalink( $vid );

	$has_section_header = $args['section_title'] || ! empty( $args['section_cta'] );
	?>
	<div class="ibv-offer-panel">
		<?php if ( $has_section_header ) : ?>
			<div class="ibv-offer-panel__header">
				<?php if ( $args['section_title'] ) : ?>
					<h2 class="ibv-offer-panel__section-title ibv-font-display">
						<?php echo esc_html( $args['section_title'] ); ?>
					</h2>
				<?php endif; ?>
				<?php
				if ( ! empty( $args['section_cta'] ) && ! empty( $args['section_cta']['url'] ) && ! empty( $args['section_cta']['label'] ) ) {
					ibv_core_button(
						[
							'url'     => $args['section_cta']['url'],
							'label'   => $args['section_cta']['label'],
							'variant' => 'primary',
							'size'    => 'small',
						]
					);
				}
				?>
			</div>
			<hr class="ibv-offer-panel__divider" aria-hidden="true">
		<?php endif; ?>

		<div class="ibv-offer-panel__layout">
			<a class="ibv-offer-panel__image" href="<?php echo esc_url( $permalink ); ?>">
				<?php
				if ( $thumb_id ) {
					ibv_core_image(
						$thumb_id,
						'ibv-card',
						[
							'class'    => 'ibv-offer-panel__image-img',
							'loading'  => 'lazy',
							'decoding' => 'async',
						]
					);
				}
				?>
				<?php if ( $location ) : ?>
					<span class="ibv-offer-panel__location"><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</a>

			<div class="ibv-offer-panel__body">
				<h3 class="ibv-offer-panel__villa-name ibv-font-display">
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

				<hr class="ibv-villa-card__rule" aria-hidden="true">

				<?php if ( $excerpt ) : ?>
					<p class="ibv-offer-panel__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<?php if ( $args['was_price'] || $args['now_price'] ) : ?>
					<div class="ibv-offer-panel__pricing">
						<?php if ( $args['was_price'] ) : ?>
							<div class="ibv-offer-panel__price ibv-offer-panel__price--was">
								<span class="ibv-offer-panel__price-eyebrow"><?php esc_html_e( 'Was', 'ibv' ); ?></span>
								<div class="ibv-offer-panel__price-line">
									<span class="ibv-offer-panel__price-prefix"><?php esc_html_e( 'From', 'ibv' ); ?></span>
									<span class="ibv-offer-panel__price-amount">€<?php echo esc_html( number_format_i18n( (float) $args['was_price'] ) ); ?></span>
									<span class="ibv-offer-panel__price-suffix"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( $args['now_price'] ) : ?>
							<div class="ibv-offer-panel__price ibv-offer-panel__price--now">
								<span class="ibv-offer-panel__price-eyebrow"><?php esc_html_e( 'Now', 'ibv' ); ?></span>
								<div class="ibv-offer-panel__price-line">
									<span class="ibv-offer-panel__price-prefix"><?php esc_html_e( 'From', 'ibv' ); ?></span>
									<span class="ibv-offer-panel__price-amount" data-bob-from-price="<?php echo esc_attr( (string) $vid ); ?>">€<?php echo esc_html( number_format_i18n( (float) $args['now_price'] ) ); ?><?php if ( $args['show_now_asterisk'] ) : ?><span class="ibv-offer-panel__price-mark" aria-hidden="true">*</span><?php endif; ?></span>
									<span class="ibv-offer-panel__price-suffix"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php
				$from_d = ibv_format_acf_date_display( (string) $args['valid_from'] );
				$to_d   = ibv_format_acf_date_display( (string) $args['valid_to'] );
				if ( $from_d && $to_d ) :
					?>
					<p class="ibv-offer-panel__valid">
						<?php
						printf(
							/* translators: 1: from date, 2: to date */
							esc_html__( 'Valid: %1$s – %2$s', 'ibv' ),
							esc_html( $from_d ),
							esc_html( $to_d )
						);
						?>
					</p>
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
		</div>
	</div>

	<?php if ( $args['footnote'] ) : ?>
		<p class="ibv-offer-panel__footnote"><?php echo esc_html( $args['footnote'] ); ?></p>
	<?php endif; ?>
	<?php
}
