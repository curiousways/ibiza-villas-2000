<?php
/**
 * Component: Accommodation tile + pair.
 *
 * Single tile: image + content split inside a bordered card. Pair helper
 * renders the listing cross-sell pair from shared Site Options fields.
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
	<article class="ibv-accommodation-tile ibv-surface-card">

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
 * Render the listing cross-sell pair from shared ACF option fields.
 *
 * @param array $args {
 *     @type string $apartments_label Optional CTA label override.
 *     @type string $hotel_label      Optional CTA label override.
 * }
 */
function ibv_core_accommodation_tile_pair( $args = [] ) {
	$defaults = [
		'apartments_label' => __( 'View the apartments', 'ibv' ),
		'hotel_label'      => __( 'View the apartments', 'ibv' ),
	];
	$args = wp_parse_args( $args, $defaults );

	$ap_img = get_field( 'fancy_apartments_image', 'option' );
	$ap_url = get_field( 'fancy_apartments_url', 'option' );

	$ho_img = get_field( 'fancy_hotel_image', 'option' );
	$ho_txt = get_field( 'fancy_hotel_text', 'option' );
	$ho_url = get_field( 'fancy_hotel_url', 'option' );

	if ( empty( $ap_img['ID'] ) && ! $ho_txt && empty( $ho_img['ID'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-accommodation-tile' );
	?>
	<div class="ibv-accommodation-tile-pair">
		<?php if ( ! empty( $ap_img['ID'] ) || $ap_url ) : ?>
			<?php
			ibv_core_accommodation_tile(
				[
					'title'       => __( 'Our apartments', 'ibv' ),
					'description' => __( 'A short walk from the beach and the bars in San Antonio, at a fraction of the price of a villa.', 'ibv' ),
					'cta_url'     => $ap_url ? esc_url( $ap_url ) : '',
					'cta_label'   => $args['apartments_label'],
					'image'       => $ap_img,
				]
			);
			?>
		<?php endif; ?>
		<?php if ( $ho_txt || ! empty( $ho_img['ID'] ) ) : ?>
			<?php
			ibv_core_accommodation_tile(
				[
					'title'       => __( 'Apartments', 'ibv' ),
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
