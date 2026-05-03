<?php
/**
 * Component: Accommodation tile + pair.
 *
 * Single tile: image + content split inside a bordered card. Pair helper
 * renders airstream + hotel teasers from shared Site Options fields.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a single accommodation tile.
 *
 * @param array $args {
 *     @type string    $title       Tile title.
 *     @type string    $description Body text.
 *     @type string    $cta_url     CTA destination.
 *     @type string    $cta_label   CTA label.
 *     @type int|array $image       Image ID or ACF image array.
 * }
 */
function ibv_core_accommodation_tile( $args = [] ) {
	$defaults = [
		'title'       => '',
		'description' => '',
		'cta_url'     => '',
		'cta_label'   => '',
		'image'       => null,
	];
	$args = wp_parse_args( $args, $defaults );

	if ( ! $args['title'] && ! $args['description'] && ! $args['image'] ) {
		return;
	}

	wp_enqueue_style( 'ibv-accommodation-tile' );
	?>
	<article class="ibv-accommodation-tile">

		<?php if ( $args['image'] ) : ?>
			<div class="ibv-accommodation-tile__media">
				<?php
				ibv_core_image(
					$args['image'],
					'ibv-card',
					[
						'class'    => 'ibv-accommodation-tile__image',
						'loading'  => 'lazy',
						'decoding' => 'async',
					]
				);
				?>
			</div>
		<?php endif; ?>

		<div class="ibv-accommodation-tile__body">
			<?php if ( $args['title'] ) : ?>
				<h3 class="ibv-accommodation-tile__title ibv-font-display">
					<?php echo esc_html( $args['title'] ); ?>
				</h3>
				<hr class="ibv-accommodation-tile__rule" aria-hidden="true">
			<?php endif; ?>

			<?php if ( $args['description'] ) : ?>
				<div class="ibv-accommodation-tile__description">
					<?php echo wp_kses_post( wpautop( (string) $args['description'] ) ); ?>
				</div>
			<?php endif; ?>

			<?php
			if ( $args['cta_url'] && $args['cta_label'] ) {
				ibv_core_button(
					[
						'url'     => $args['cta_url'],
						'label'   => $args['cta_label'],
						'variant' => 'primary',
						'size'    => 'small',
					]
				);
			}
			?>
		</div>

	</article>
	<?php
}

/**
 * Render the airstream + hotel pair from shared ACF option fields.
 *
 * @param array $args {
 *     @type string $airstream_label Optional CTA label override.
 *     @type string $hotel_label     Optional CTA label override.
 * }
 */
function ibv_core_accommodation_tile_pair( $args = [] ) {
	$defaults = [
		'airstream_label' => __( 'View Airstreams', 'ibv' ),
		'hotel_label'     => __( 'View Hotel', 'ibv' ),
	];
	$args = wp_parse_args( $args, $defaults );

	$ai_img = get_field( 'fancy_airstream_image', 'option' );
	$ai_txt = get_field( 'fancy_airstream_text', 'option' );
	$ai_url = get_field( 'fancy_airstream_url', 'option' );

	$ho_img = get_field( 'fancy_hotel_image', 'option' );
	$ho_txt = get_field( 'fancy_hotel_text', 'option' );
	$ho_url = get_field( 'fancy_hotel_url', 'option' );

	if ( ! $ai_txt && ! $ho_txt && empty( $ai_img['ID'] ) && empty( $ho_img['ID'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-accommodation-tile' );
	?>
	<div class="ibv-accommodation-tile-pair">
		<?php if ( $ai_txt || ! empty( $ai_img['ID'] ) ) : ?>
			<?php
			ibv_core_accommodation_tile(
				[
					'title'       => __( 'Our Airstreams', 'ibv' ),
					'description' => $ai_txt ? (string) $ai_txt : '',
					'cta_url'     => $ai_url ? esc_url( $ai_url ) : '',
					'cta_label'   => $args['airstream_label'],
					'image'       => $ai_img,
				]
			);
			?>
		<?php endif; ?>
		<?php if ( $ho_txt || ! empty( $ho_img['ID'] ) ) : ?>
			<?php
			ibv_core_accommodation_tile(
				[
					'title'       => __( 'Our Hotel', 'ibv' ),
					'description' => $ho_txt ? (string) $ho_txt : '',
					'cta_url'     => $ho_url ? esc_url( $ho_url ) : '',
					'cta_label'   => $args['hotel_label'],
					'image'       => $ho_img,
				]
			);
			?>
		<?php endif; ?>
	</div>
	<?php
}
