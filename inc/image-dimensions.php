<?php
/**
 * Image dimensions for layout stability (CLS fix).
 *
 * Provides width/height for registered image sizes. All names are scoped with iv2000_
 * to avoid conflicts with WordPress core and plugins.
 *
 * @package Ibiza_Villas_2000
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Registered image size dimensions: size slug => [ width, height ].
 *
 * Keys match add_image_size() names used by the theme. Values are [width, height] in pixels.
 * Used to inject dimensions into post thumbnail HTML when missing.
 *
 * @return array<string, array{0: int, 1: int}>
 */
function iv2000_get_registered_image_dimensions() {
	return array(
		'property-featured-image'     => array(420, 280),
		'property-featured-image-ret' => array(840, 560),
		'property-gallery-image'      => array(1024, 685),
		'thumbnail'                   => array(150, 150),
		'medium'                      => array(300, 300),
		'large'                       => array(1024, 1024),
	);
}

/**
 * Add width/height to post thumbnail HTML when missing (prevents layout shift).
 *
 * @param string       $html              Post thumbnail HTML.
 * @param int          $post_id           Post ID.
 * @param int          $post_thumbnail_id  Attachment ID.
 * @param string|array $size              Requested size name or array.
 * @param array        $attr              Image attributes.
 * @return string
 */
function iv2000_post_thumbnail_html_add_dimensions($html, $post_id, $post_thumbnail_id, $size, $attr) {
	if (empty($html) || ! is_string($size)) {
		return $html;
	}

	$dimensions = iv2000_get_registered_image_dimensions();
	if (! isset($dimensions[ $size ])) {
		return $html;
	}

	if (preg_match('/\swidth\s*=/', $html) && preg_match('/\sheight\s*=/', $html)) {
		return $html;
	}

	$w = (int) $dimensions[ $size ][0];
	$h = (int) $dimensions[ $size ][1];
	return preg_replace('/<img\s/', '<img width="' . $w . '" height="' . $h . '" ', $html, 1);
}

add_filter('post_thumbnail_html', 'iv2000_post_thumbnail_html_add_dimensions', 10, 5);
