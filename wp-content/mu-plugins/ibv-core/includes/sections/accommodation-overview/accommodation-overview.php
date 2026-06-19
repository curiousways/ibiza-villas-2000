<?php
/**
 * Section: Accommodation overview (Hotel / Airstream pages).
 *
 * Presentational main-column block: heading + body prose + free-text fact pills
 * + static "from" price. Reads the Accommodation page ACF (no villa data).
 * Renders inside .ibv-villa-detail__main; the shell owns vertical rhythm so this
 * is NOT a `.ibv-section`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the accommodation overview for the current page.
 */
function ibv_core_section_accommodation_overview() {
	$heading = (string) get_field( 'overview_heading' );
	$body    = (string) get_field( 'overview_body' );

	$pills = array(
		'location'     => trim( (string) get_field( 'fact_location' ) ),
		'sleeps'       => trim( (string) get_field( 'fact_sleeps' ) ),
		'availability' => trim( (string) get_field( 'fact_availability' ) ),
	);
	$pills = array_filter( $pills );

	$from_price = get_field( 'from_price' );
	$price_note = trim( (string) get_field( 'from_price_note' ) );

	if ( ! $heading && ! $body && ! $pills && ( '' === $from_price || null === $from_price ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-accommodation-overview' );
	?>
	<section class="ibv-accommodation-overview">
		<?php if ( $heading || $body ) : ?>
			<div class="ibv-accommodation-overview__intro">
				<?php
				if ( $heading ) {
					ibv_core_section_heading(
						array(
							'title' => $heading,
							'level' => 'h2',
						)
					);
				}
				?>

				<?php if ( $body ) : ?>
					<?php
					$body_id   = wp_unique_id( 'ibv-ao-body-' );
					$use_clamp = mb_strlen( wp_strip_all_tags( $body ) ) > 200;
					?>
					<div class="ibv-accommodation-overview__body-block">
						<div
							id="<?php echo esc_attr( $body_id ); ?>"
							class="ibv-accommodation-overview__body ibv-prose<?php echo $use_clamp ? ' ibv-readmore__content ibv-readmore__content--clamp' : ''; ?>"
						>
							<?php echo wp_kses_post( $body ); ?>
						</div>
						<?php if ( $use_clamp ) : ?>
							<?php
							// Shared "Read more" toggle (assets/js|css/readmore.*): the button
							// names the open-class it toggles on the controlled element.
							wp_enqueue_style( 'ibv-readmore' );
							wp_enqueue_script( 'ibv-readmore' );
							?>
							<button
								type="button"
								class="ibv-readmore__toggle"
								data-ibv-readmore="is-open"
								data-ibv-readmore-less="<?php esc_attr_e( 'Read less', 'ibv' ); ?>"
								aria-expanded="false"
								aria-controls="<?php echo esc_attr( $body_id ); ?>"
							>
								<?php esc_html_e( 'Read more', 'ibv' ); ?>
							</button>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $pills ) : ?>
			<ul class="ibv-accommodation-overview__pills">
				<?php foreach ( $pills as $type => $label ) : ?>
					<li class="ibv-accommodation-overview__pill ibv-accommodation-overview__pill--<?php echo esc_attr( $type ); ?>">
						<?php echo esc_html( $label ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( '' !== $from_price && null !== $from_price ) : ?>
			<div class="ibv-accommodation-overview__price">
				<p class="ibv-accommodation-overview__price-row">
					<span class="ibv-accommodation-overview__price-from"><?php esc_html_e( 'From', 'ibv' ); ?></span>
					<span class="ibv-accommodation-overview__price-amount">&euro;<?php echo esc_html( number_format( (float) $from_price ) ); ?></span>
					<span class="ibv-accommodation-overview__price-unit"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
				</p>
				<?php if ( $price_note ) : ?>
					<p class="ibv-accommodation-overview__price-note"><?php echo esc_html( $price_note ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</section>
	<?php
}
