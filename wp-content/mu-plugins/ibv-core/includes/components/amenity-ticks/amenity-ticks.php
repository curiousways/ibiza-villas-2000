<?php
/**
 * Component: Amenity ticks from the `property_features` ACF repeater.
 *
 * Each row's `property_feature_text` becomes a pill; the row's
 * `property_feature_icon` is intentionally ignored — the design uses a
 * uniform ✓ glyph.
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

	$rows = get_field( 'property_features', $villa_id );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}

	$labels = [];
	foreach ( $rows as $row ) {
		$label = isset( $row['property_feature_text'] ) ? trim( (string) $row['property_feature_text'] ) : '';
		if ( '' !== $label ) {
			$labels[] = $label;
		}
	}

	if ( empty( $labels ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-amenity-ticks' );
	?>
	<ul class="ibv-amenity-ticks">
		<?php foreach ( $labels as $label ) : ?>
			<li class="ibv-amenity-ticks__item">
				<span class="ibv-amenity-ticks__tick" aria-hidden="true"><?php echo esc_html( '✓' ); ?></span>
				<?php echo esc_html( $label ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
