<?php
/**
 * Component: Facts strip (bedrooms / bathrooms / sleeps).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render bedrooms / bathrooms / sleeps list.
 *
 * @param int    $villa_id Post ID.
 * @param string $variant  horizontal|compact.
 */
function ibv_core_facts_strip( $villa_id, $variant = 'horizontal' ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	$bedrooms = get_field( 'property_bedrooms', $villa_id );
	$baths    = get_field( 'property_bathrooms', $villa_id );
	$sleeps   = get_field( 'property_sleeps', $villa_id );

	if ( ( '' === $bedrooms || null === $bedrooms ) && ( '' === $baths || null === $baths ) && ( '' === $sleeps || null === $sleeps ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-facts-strip' );

	$variant_class = 'compact' === $variant ? 'compact' : 'horizontal';
	?>
	<ul class="ibv-facts-strip ibv-facts-strip--<?php echo esc_attr( $variant_class ); ?>">
		<?php if ( '' !== $bedrooms && null !== $bedrooms ) : ?>
			<li class="ibv-facts-strip__item">
				<span class="ibv-facts-strip__icon" aria-hidden="true">🛏</span>
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
			<li class="ibv-facts-strip__item">
				<span class="ibv-facts-strip__icon" aria-hidden="true">🛁</span>
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
			<li class="ibv-facts-strip__item">
				<span class="ibv-facts-strip__icon" aria-hidden="true">👥</span>
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
	<?php
}
