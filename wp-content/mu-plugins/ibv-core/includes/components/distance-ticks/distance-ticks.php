<?php
/**
 * Component: Distance ticks repeater.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_distance_ticks( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	$rows = get_field( 'villa_distances', $villa_id );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-distance-ticks' );
	?>
	<ul class="ibv-distance-ticks">
		<?php foreach ( $rows as $row ) : ?>
			<?php
			$label = ibv_villa_distance_label( $row );
			if ( '' === $label ) {
				continue;
			}
			?>
			<li class="ibv-distance-ticks__item">
				<?php ibv_core_the_icon( 'map-pin', [ 'size' => 14, 'class' => 'ibv-distance-ticks__icon' ] ); ?>
				<span class="ibv-distance-ticks__text"><?php echo esc_html( $label ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
