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

	// wp_get_attachment_image handles escaping internally for the attribute set.
	echo wp_get_attachment_image( $id, $size, false, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
