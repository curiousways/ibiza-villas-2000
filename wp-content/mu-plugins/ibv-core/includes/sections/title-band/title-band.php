<?php
/**
 * Section: Title band.
 *
 * Generic, arg-driven text-only page header — a label + optional meta line on a
 * chosen surface, no image. Page-builder-ready (named for what it is, not the
 * page that first needed it). First consumer is the Legals template
 * ("Legals" + "Last updated: {date}").
 *
 * The band label is a styled non-heading; the document <h1> belongs to the
 * page content, not here (see the Legals template).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a title band.
 *
 * @param array $args {
 *     @type string $title   Band label (styled non-heading).
 *     @type string $meta    Optional small meta line beneath the label.
 *     @type string $surface Surface modifier slug ('bg' | 'white' | 'tint-teal' | 'tint-gold' | 'tint-blue' | 'forest-green'). Default 'bg'.
 * }
 */
function ibv_core_title_band( array $args = [] ) {
	$defaults = [
		'title'   => '',
		'meta'    => '',
		'surface' => 'bg',
	];
	$args = wp_parse_args( $args, $defaults );

	$title = (string) $args['title'];
	$meta  = (string) $args['meta'];

	if ( ! $title && ! $meta ) {
		return;
	}

	wp_enqueue_style( 'ibv-title-band' );

	$root_classes = [ 'ibv-title-band', 'ibv-section' ];

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
		$root_classes[] = 'ibv-section--surface-' . $args['surface'];
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>">
		<div class="ibv-container ibv-title-band__inner">
			<?php if ( $title ) : ?>
				<p class="ibv-title-band__title ibv-font-display"><?php echo esc_html( $title ); ?></p>
			<?php endif; ?>
			<?php if ( $meta ) : ?>
				<p class="ibv-title-band__meta"><?php echo esc_html( $meta ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
