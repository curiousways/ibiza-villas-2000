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
			$text = isset( $row['distance_text'] ) ? (string) $row['distance_text'] : '';
			if ( '' === trim( $text ) ) {
				continue;
			}
			?>
			<li class="ibv-distance-ticks__item">
				<span class="ibv-distance-ticks__icon" aria-hidden="true">📍</span>
				<?php echo esc_html( $text ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
