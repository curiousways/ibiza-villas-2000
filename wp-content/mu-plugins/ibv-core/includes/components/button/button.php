<?php
/**
 * Component: Button
 *
 * Single point of truth for `<a>` and `<button>`-styled buttons.
 * Always use this helper — never raw `<a class="ibv-button">`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a button.
 *
 * @param array $args {
 *     @type string $url        Required for link buttons. The destination URL.
 *     @type string $label      Required. The button label.
 *     @type string $variant    Optional. 'primary' | 'secondary' | 'ghost' | 'primary-inverse' | 'secondary-inverse'. Default 'primary'.
 *     @type string $size       Optional. 'small' | 'medium' | 'large'. Default 'medium'.
 *     @type bool|null $arrow   Optional. null = variant default (on for primary/secondary and inverse pair, off for ghost). Bool overrides.
 *     @type string $target     Optional. e.g. '_blank'.
 *     @type string $rel        Optional. e.g. 'noopener noreferrer'.
 *     @type string $tag        Optional. 'a' (default) or 'button'.
 *     @type string $type       Optional. Button type when tag is 'button'. Default 'button'.
 *     @type array  $attributes Optional. Extra HTML attributes as key => value pairs.
 *     @type string $class      Optional. Extra CSS classes appended to the BEM root.
 * }
 */
function ibv_core_button( $args = [] ) {
	$defaults = [
		'url'        => '',
		'label'      => '',
		'variant'    => 'primary',
		'size'       => 'medium',
		'arrow'      => null,
		'target'     => '',
		'rel'        => '',
		'tag'        => 'a',
		'type'       => 'button',
		'attributes' => [],
		'class'      => '',
	];
	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['label'] ) ) {
		return;
	}
	if ( 'a' === $args['tag'] && empty( $args['url'] ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-button' );

	$allowed_variants = [ 'primary', 'secondary', 'ghost', 'primary-inverse', 'secondary-inverse' ];
	$allowed_sizes    = [ 'small', 'medium', 'large' ];

	$variant = in_array( $args['variant'], $allowed_variants, true ) ? $args['variant'] : 'primary';
	$size    = in_array( $args['size'], $allowed_sizes, true ) ? $args['size'] : 'medium';

	$variants_with_arrow = [ 'primary', 'secondary', 'primary-inverse', 'secondary-inverse' ];
	$show_arrow          = is_bool( $args['arrow'] )
		? $args['arrow']
		: in_array( $variant, $variants_with_arrow, true );

	$arrow_svg = '';
	if ( $show_arrow ) {
		$arrow_svg = '<svg class="ibv-button__arrow" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false"><path d="M3 9h12M11 5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	$classes = [
		'ibv-button',
		'ibv-button--' . $variant,
		'ibv-button--' . $size,
	];
	if ( ! empty( $args['class'] ) ) {
		$classes[] = sanitize_html_class( $args['class'] );
	}

	$extra_attrs = '';
	foreach ( (array) $args['attributes'] as $key => $value ) {
		$extra_attrs .= sprintf( ' %s="%s"', esc_attr( sanitize_key( $key ) ), esc_attr( $value ) );
	}

	if ( 'button' === $args['tag'] ) {
		printf(
			'<button class="%1$s" type="%2$s"%3$s>%4$s%5$s</button>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $args['type'] ),
			$extra_attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr per attribute.
			esc_html( $args['label'] ),
			$arrow_svg // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG built above.
		);
		return;
	}

	$target_attr = $args['target'] ? sprintf( ' target="%s"', esc_attr( $args['target'] ) ) : '';

	// Auto-add rel="noopener noreferrer" on _blank if rel not explicitly set.
	if ( '_blank' === $args['target'] && empty( $args['rel'] ) ) {
		$args['rel'] = 'noopener noreferrer';
	}
	$rel_attr = $args['rel'] ? sprintf( ' rel="%s"', esc_attr( $args['rel'] ) ) : '';

	printf(
		'<a class="%1$s" href="%2$s"%3$s%4$s%5$s>%6$s%7$s</a>',
		esc_attr( implode( ' ', $classes ) ),
		esc_url( $args['url'] ),
		$target_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$rel_attr,    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$extra_attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html( $args['label'] ),
		$arrow_svg    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG built above.
	);
}
