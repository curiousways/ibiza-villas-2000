<?php
/**
 * Component: Image and Text Section.
 *
 * Reusable horizontal layout pairing a heading + body + CTA with an image.
 * Used on the homepage for Short Breaks, IPS / responsible tourism, and
 * Meet the Team. Variants: image side (left | right), optional surface
 * modifier, and optional accent rule colour.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render an image-and-text section.
 *
 * @param array $args {
 *     @type string $title       Required with other content. Title text (rendered as h2).
 *     @type string $description Body copy (plain text; wrapped with wpautop + wp_kses_post).
 *     @type string $cta_url     CTA destination.
 *     @type string $cta_label   CTA label.
 *     @type int|array $image    Image ID or ACF image array.
 *     @type string $image_side 'left' | 'right'. Default 'right'.
 *     @type string $surface    Surface modifier slug ('bg' | 'white' | 'tint-teal' | 'tint-gold' | 'tint-blue' | 'forest-green'). Empty string applies no surface (transparent).
 *     @type string $rule_color Optional accent for the rule under the title; empty uses sage-500 from CSS.
 * }
 */
function ibv_core_image_text_section( $args = [] ) {
	$defaults = [
		'title'       => '',
		'description' => '',
		'cta_url'     => '',
		'cta_label'   => '',
		'image'       => null,
		'image_side'  => 'right',
		'surface'     => '',
		'rule_color'  => '',
	];
	$args = wp_parse_args( $args, $defaults );

	if ( ! $args['title'] && ! $args['description'] && ! $args['image'] ) {
		return;
	}

	wp_enqueue_style( 'ibv-image-text-section' );

	$image_side = in_array( $args['image_side'], [ 'left', 'right' ], true ) ? $args['image_side'] : 'right';

	$root_classes = [
		'ibv-image-text-section',
		'ibv-section',
		'ibv-image-text-section--image-' . $image_side,
	];

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
		$root_classes[] = 'ibv-section--surface-' . $args['surface'];
	}

	$inline_styles = [];
	if ( ! empty( $args['rule_color'] ) ) {
		$inline_styles[] = '--ibv-image-text-section-rule: ' . $args['rule_color'];
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>"
	<?php
	if ( $inline_styles ) {
		printf( ' style="%s"', esc_attr( implode( '; ', $inline_styles ) ) );
	}
	?>
	>
		<div class="ibv-container ibv-image-text-section__inner">

			<div class="ibv-image-text-section__content">
				<?php if ( $args['title'] ) : ?>
					<h2 class="ibv-image-text-section__title ibv-font-display"><?php echo esc_html( $args['title'] ); ?></h2>
				<?php endif; ?>

				<hr class="ibv-image-text-section__rule" aria-hidden="true">

				<?php if ( $args['description'] ) : ?>
					<div class="ibv-image-text-section__description">
						<?php echo wp_kses_post( wpautop( $args['description'] ) ); ?>
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

			<?php if ( $args['image'] ) : ?>
				<div class="ibv-image-text-section__media">
					<?php
					ibv_core_image(
						$args['image'],
						'ibv-card',
						[
							'class'    => 'ibv-image-text-section__image',
							'loading'  => 'lazy',
							'decoding' => 'async',
						]
					);
					?>
				</div>
			<?php endif; ?>

		</div>
	</section>
	<?php
}
