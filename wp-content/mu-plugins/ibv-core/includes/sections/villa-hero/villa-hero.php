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
		?>
	</div>
	<?php
}
