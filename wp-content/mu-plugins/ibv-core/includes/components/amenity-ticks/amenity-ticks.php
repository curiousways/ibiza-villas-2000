<?php
/**
 * Component: Amenity ticks from native taxonomy `villa_amenity`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_amenity_ticks( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	$terms = get_the_terms( $villa_id, 'villa_amenity' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-amenity-ticks' );
	?>
	<ul class="ibv-amenity-ticks">
		<?php foreach ( $terms as $term ) : ?>
			<li class="ibv-amenity-ticks__item">
				<span class="ibv-amenity-ticks__tick" aria-hidden="true"><?php echo esc_html( '✓' ); ?></span>
				<?php echo esc_html( $term->name ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
