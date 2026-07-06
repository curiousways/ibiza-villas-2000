<?php
/**
 * Section: Image + labelled entries.
 *
 * Generic, arg-driven block: a heading + gold rule + a definition list of
 * labelled entries, paired with an image. Sibling to `image-text-section`
 * (image + body + CTA). First consumer is the About "Our Story" section;
 * the IPS "What we do" section reuses it.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render an image-and-entries section.
 *
 * @param array $args {
 *     @type string    $title      Heading (rendered as h2).
 *     @type array     $entries    List of [ 'label' => '', 'body' => '' ] rows.
 *     @type int|array $image      Image ID or ACF image array.
 *     @type string    $image_side 'left' | 'right'. Default 'right'.
 *     @type string    $surface    Surface modifier slug ('bg' | 'white' | 'tint-teal' | 'tint-gold' | 'tint-blue' | 'forest-green'). Default 'bg'.
 *     @type string    $rule_color Optional accent for the rule under the title; empty uses gold from CSS.
 * }
 */
function ibv_core_image_entries_section( array $args = [] ) {
	$defaults = [
		'title'      => '',
		'entries'    => [],
		'image'      => null,
		'image_side' => 'right',
		'surface'    => 'bg',
		'rule_color' => '',
	];
	$args = wp_parse_args( $args, $defaults );

	$entries = is_array( $args['entries'] ) ? $args['entries'] : [];
	$title   = (string) $args['title'];
	$image   = $args['image'];

	if ( ! $entries && ! $title && ! $image ) {
		return;
	}

	wp_enqueue_style( 'ibv-image-entries-section' );

	$image_side = in_array( $args['image_side'], [ 'left', 'right' ], true ) ? $args['image_side'] : 'right';

	$root_classes = [
		'ibv-image-entries-section',
		'ibv-section',
		'ibv-image-entries-section--image-' . $image_side,
	];

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
		$root_classes[] = 'ibv-section--surface-' . $args['surface'];
	}

	$inline_styles = [];
	if ( ! empty( $args['rule_color'] ) ) {
		$inline_styles[] = '--ibv-rule-color: ' . $args['rule_color'];
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>"
	<?php
	if ( $inline_styles ) {
		printf( ' style="%s"', esc_attr( implode( '; ', $inline_styles ) ) );
	}
	?>
	>
		<div class="ibv-container ibv-image-entries-section__inner">
			<div class="ibv-image-entries-section__copy">
				<header class="ibv-image-entries-section__header">
					<?php if ( $title ) : ?>
						<h2 class="ibv-image-entries-section__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
						<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
					<?php endif; ?>
				</header>

				<dl class="ibv-image-entries-section__entries">
					<?php foreach ( $entries as $entry ) :
						$label = (string) ( $entry['label'] ?? '' );
						$body  = (string) ( $entry['body'] ?? '' );
						if ( ! $body ) {
							continue;
						}
						?>
						<div class="ibv-image-entries-section__entry">
							<?php if ( $label ) : ?>
								<dt class="ibv-image-entries-section__label"><?php echo esc_html( $label ); ?></dt>
							<?php endif; ?>
							<dd class="ibv-image-entries-section__body"><?php echo esc_html( $body ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>

			<?php if ( ! empty( $image['ID'] ) ) : ?>
				<div class="ibv-image-entries-section__media">
					<?php
					ibv_core_image(
						$image,
						'ibv-card',
						[ 'class' => 'ibv-image-entries-section__image' ]
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
