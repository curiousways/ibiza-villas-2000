<?php
/**
 * Component: Section Heading
 *
 * Eyebrow + title + optional lead. Used at the top of every content section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a section heading.
 *
 * @param array $args {
 *     @type string $eyebrow Optional. Small label above the title.
 *     @type string $title   Required (or $eyebrow). Heading text.
 *     @type string $lead    Optional. Intro paragraph below the title (rich text allowed).
 *     @type string $level   Optional. h1–h6. Default 'h2'.
 *     @type string $align   Optional. 'left' | 'center'. Default 'left'.
 *     @type string $class   Optional. Extra class on the root.
 * }
 */
function ibv_core_section_heading( $args = [] ) {
	$defaults = [
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'level'   => 'h2',
		'align'   => 'left',
		'class'   => '',
	];
	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['title'] ) && empty( $args['eyebrow'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-heading' );

	$tag   = in_array( $args['level'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ? $args['level'] : 'h2';
	$align = in_array( $args['align'], [ 'left', 'center' ], true ) ? $args['align'] : 'left';

	$classes = [ 'ibv-section-heading', 'ibv-section-heading--' . $align ];
	if ( ! empty( $args['class'] ) ) {
		$classes[] = sanitize_html_class( $args['class'] );
	}
	?>
	<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
			<p class="ibv-section-heading__eyebrow ibv-text-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $args['title'] ) ) : ?>
			<<?php echo esc_attr( $tag ); ?> class="ibv-section-heading__title"><?php echo esc_html( $args['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
		<?php endif; ?>

		<?php if ( ! empty( $args['lead'] ) ) : ?>
			<div class="ibv-section-heading__lead ibv-prose"><?php echo wp_kses_post( $args['lead'] ); ?></div>
		<?php endif; ?>
	</header>
	<?php
}
