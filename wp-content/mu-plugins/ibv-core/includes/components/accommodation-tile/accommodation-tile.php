<?php
/**
 * Component: Accommodation tile.
 *
 * Image + content split inside a bordered card. The options helper renders
 * the listing cross-sell from the shared fancy_apartments_* Site Options.
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
 * Render the listing apartments tile from shared ACF option fields.
 *
 * @param array $args {
 *     @type string $cta_label Optional CTA label override.
 * }
 */
function ibv_core_accommodation_tile_from_options( $args = [] ) {
	$defaults = [
		'cta_label' => __( 'View the apartments', 'ibv' ),
	];
	$args = wp_parse_args( $args, $defaults );

	$img = get_field( 'fancy_apartments_image', 'option' );
	$txt = get_field( 'fancy_apartments_text', 'option' );
	$url = get_field( 'fancy_apartments_url', 'option' );

	if ( empty( $img['ID'] ) && ! $txt ) {
		return;
	}

	ibv_core_accommodation_tile(
		[
			'title'       => __( 'Our apartments', 'ibv' ),
			'description' => $txt ? (string) $txt : '',
			'cta_url'     => $url ? esc_url( $url ) : '',
			'cta_label'   => $args['cta_label'],
			'image'       => $img,
		]
	);
}
