<?php
/**
 * Section: Villa location (heading + retained map + distance pills).
 *
 * Matches Figma node 1:5998 (02b | Villa Detail → Location). The map
 * itself is rendered by `ibv_core_villa_map()` and is intentionally left
 * untouched by this section.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_section_villa_location( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	// Presence check — don't render an orphan heading.
	$map     = get_field( 'property_map', $villa_id );
	$has_map = is_array( $map )
		&& isset( $map['lat'], $map['lng'] )
		&& is_numeric( $map['lat'] )
		&& is_numeric( $map['lng'] );

	$rows         = get_field( 'villa_distances', $villa_id );
	$has_distance = is_array( $rows ) && count( $rows ) > 0;

	// `property_summary` is the ACF WYSIWYG labelled "Property Location" —
	// distances detail, car notes, rental licence. Renders below the ticks
	// so it lives in the same place its label points at.
	$location_text     = get_field( 'property_summary', $villa_id );
	$has_location_text = $location_text && '' !== trim( wp_strip_all_tags( (string) $location_text ) );

	if ( ! $has_map && ! $has_distance && ! $has_location_text ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-detail' );
	wp_enqueue_style( 'ibv-section-villa-location' );
	?>
	<section class="ibv-villa-location">
		<?php
		// Rendered inside .ibv-villa-detail__main which is itself inside
		// .ibv-container, so we deliberately don't nest another container
		// here — adds an extra 24px inset and misaligns vs header/overview.
		ibv_core_section_heading(
			[
				'title' => __( 'Location', 'ibv' ),
				'level' => 'h2',
			]
		);

		ibv_core_villa_map( $villa_id );
		ibv_core_distance_ticks( $villa_id );
		?>

		<?php if ( $has_location_text ) : ?>
			<div class="ibv-villa-location__detail ibv-prose">
				<?php echo wp_kses_post( $location_text ); ?>
			</div>
		<?php endif; ?>
	</section>
	<?php
}
