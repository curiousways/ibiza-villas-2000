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
 *     @type string $variant    Optional. 'primary' | 'secondary' | 'ghost'. Default 'primary'.
 *     @type string $size       Optional. 'small' | 'medium' | 'large'. Default 'medium'.
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

	$allowed_variants = [ 'primary', 'secondary', 'ghost' ];
	$allowed_sizes    = [ 'small', 'medium', 'large' ];

	$variant = in_array( $args['variant'], $allowed_variants, true ) ? $args['variant'] : 'primary';
	$size    = in_array( $args['size'], $allowed_sizes, true ) ? $args['size'] : 'medium';

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
			'<button class="%1$s" type="%2$s"%3$s>%4$s</button>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $args['type'] ),
			$extra_attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr per attribute.
			esc_html( $args['label'] )
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
		'<a class="%1$s" href="%2$s"%3$s%4$s%5$s>%6$s</a>',
		esc_attr( implode( ' ', $classes ) ),
		esc_url( $args['url'] ),
		$target_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$rel_attr,    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$extra_attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html( $args['label'] )
	);
}
