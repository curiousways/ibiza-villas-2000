<?php
/**
 * Component: Image
 *
 * Wraps wp_get_attachment_image() with sensible defaults and supports
 * either an attachment ID or an ACF image array.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a responsive image.
 *
 * @param int|array $image      Attachment ID, or ACF image array (with 'ID' key).
 * @param string    $size       Registered image size. Default 'large'.
 * @param array     $attributes Extra HTML attributes (class, alt override, sizes, loading, decoding).
 */
function ibv_core_image( $image, $size = 'large', $attributes = [] ) {
	$id = 0;
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		$id = (int) $image['ID'];
	} elseif ( is_numeric( $image ) ) {
		$id = (int) $image;
	}
	if ( ! $id ) {
		return;
	}

	$defaults = [
		'class'    => 'ibv-image',
		'loading'  => 'lazy',
		'decoding' => 'async',
	];
	$attrs = wp_parse_args( $attributes, $defaults );

	// Eager images must not inherit WP's default sizes="(max-width: Npx) 100vw, Npx".
	// Callers that know the layout pass sizes themselves; otherwise omit the attribute.
	// Lazy images keep WP's sizes (including the sizes="auto, …" prefix).
	$omit_default_sizes = ( 'eager' === $attrs['loading'] && ! array_key_exists( 'sizes', $attributes ) );
	if ( $omit_default_sizes ) {
		$attrs['sizes'] = '__ibv_omit_sizes__';
		$strip_sizes    = static function ( $img_attr ) use ( &$strip_sizes ) {
			if ( isset( $img_attr['sizes'] ) && '__ibv_omit_sizes__' === $img_attr['sizes'] ) {
				unset( $img_attr['sizes'] );
			}
			remove_filter( 'wp_get_attachment_image_attributes', $strip_sizes, 99 );
			return $img_attr;
		};
		add_filter( 'wp_get_attachment_image_attributes', $strip_sizes, 99 );
	}

	// wp_get_attachment_image handles escaping internally for the attribute set.
	echo wp_get_attachment_image( $id, $size, false, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	if ( $omit_default_sizes ) {
		remove_filter( 'wp_get_attachment_image_attributes', $strip_sizes, 99 );
	}
}
