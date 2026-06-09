<?php
/**
 * Section: Villa header (name, rating, location/distance pills, facts).
 *
 * Matches Figma node 1:5940 (02b | Villa Detail → Heading 3). Renders
 * inside .ibv-villa-detail__main; the shell owns vertical rhythm so this
 * section is NOT a `.ibv-section`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_section_villa_header( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-detail' );
	wp_enqueue_style( 'ibv-section-villa-header' );

	$title = get_field( 'villa_pretty_name', $villa_id );
	if ( ! $title ) {
		$title = get_the_title( $villa_id );
	}

	$rating = get_field( 'villa_rating_score', $villa_id );
	$rv_ct  = get_field( 'villa_review_count', $villa_id );
	$loc    = ibv_villa_location_label( $villa_id );

	$distance = '';
	$rows     = get_field( 'villa_distances', $villa_id );
	if ( is_array( $rows ) && count( $rows ) ) {
		$distance = ibv_villa_distance_label( $rows[0] );
	}

	$heading_id = wp_unique_id( 'ibv-vh-heading-' );
	?>
	<section class="ibv-villa-header" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<div class="ibv-villa-header__title-cluster">
			<h1 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-header__title">
				<?php echo esc_html( $title ); ?>
			</h1>

			<?php if ( $rating || $rv_ct ) : ?>
				<div class="ibv-villa-header__rating">
					<?php
					if ( $rating ) :
						$rating_value = max( 0.0, min( 5.0, (float) $rating ) );
						$rating_pct   = round( ( $rating_value / 5 ) * 100, 2 );
						?>
						<span
							class="ibv-villa-header__stars"
							role="img"
							aria-label="<?php echo esc_attr( sprintf(
								/* translators: %s: rating out of 5 */
								__( 'Rated %s out of 5', 'ibv' ),
								number_format_i18n( $rating_value, 1 )
							) ); ?>"
						>
							<span class="ibv-villa-header__stars-track" aria-hidden="true">
								<?php for ( $i = 0; $i < 5; $i++ ) {
									ibv_core_the_icon( 'star', [ 'size' => 14, 'class' => 'ibv-villa-header__star' ] );
								} ?>
							</span>
							<span class="ibv-villa-header__stars-fill" aria-hidden="true" style="width: <?php echo esc_attr( $rating_pct . '%' ); ?>">
								<?php for ( $i = 0; $i < 5; $i++ ) {
									ibv_core_the_icon( 'star', [ 'size' => 14, 'class' => 'ibv-villa-header__star' ] );
								} ?>
							</span>
						</span>
					<?php endif; ?>

					<span class="ibv-villa-header__rating-text">
						<?php
						$parts = [];
						if ( $rating ) {
							$parts[] = number_format_i18n( (float) $rating, 1 );
						}
						if ( $rv_ct ) {
							$parts[] = sprintf(
								/* translators: %d: review count */
								_n( '(%d review)', '(%d reviews)', (int) $rv_ct, 'ibv' ),
								(int) $rv_ct
							);
						}
						echo esc_html( implode( ' ', $parts ) );
						?>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $loc || $distance ) : ?>
			<div class="ibv-villa-header__tags">
				<?php if ( $loc ) : ?>
					<span class="ibv-villa-header__tag ibv-villa-header__tag--location"><?php echo esc_html( $loc ); ?></span>
				<?php endif; ?>
				<?php if ( $distance ) : ?>
					<span class="ibv-villa-header__tag ibv-villa-header__tag--distance"><?php echo esc_html( $distance ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php ibv_core_facts_strip( $villa_id, 'icons' ); ?>
	</section>
	<?php
}
