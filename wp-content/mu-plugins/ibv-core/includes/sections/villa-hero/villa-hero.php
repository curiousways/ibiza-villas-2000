<?php
/**
 * Section: Villa hero (featured image).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_section_villa_hero( $villa_id ) {
	$villa_id   = (int) $villa_id;
	$thumb_id   = get_post_thumbnail_id( $villa_id );
	if ( ! $thumb_id ) {
		return;
	}

	// Same open-the-gallery contract as the gallery component's own
	// triggers (data-ibv-gallery-open): the pop-up viewer only exists when
	// the villa has 2+ gallery images, so gate the button the same way.
	$gallery_rows = get_field( 'property_images', $villa_id );
	$has_gallery  = is_array( $gallery_rows ) && count( $gallery_rows ) > 1;

	wp_enqueue_style( 'ibv-villa-detail' );
	wp_enqueue_style( 'ibv-section-villa-hero' );
	?>
	<div class="ibv-villa-hero">
		<?php
		ibv_core_image(
			$thumb_id,
			'ibv-hero',
			[
				'class'    => 'ibv-villa-hero__image',
				'loading'  => 'eager',
				'decoding' => 'async',
			]
		);
		if ( $has_gallery ) {
			ibv_core_button(
				[
					'tag'        => 'button',
					'type'       => 'button',
					'label'      => __( 'View all photos', 'ibv' ),
					// Solid white: reliable contrast over unknown hero photos
					// (the outlined inverse variant vanished on light images).
					'variant'    => 'primary-inverse',
					'size'       => 'small',
					'arrow'      => false,
					'class'      => 'ibv-villa-hero__gallery-open',
					'attributes' => [
						'data-ibv-gallery-open' => '0',
					],
				]
			);
		}
		?>
	</div>
	<?php
}
