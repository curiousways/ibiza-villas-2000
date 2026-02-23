<?php
/**
 * Image dimensions for layout stability (CLS fix).
 *
 * All size names are generic and dimension-based (iv2000_WxH). Scoped to avoid
 * conflicts with WordPress core and plugins.
 *
 * @package Ibiza_Villas_2000
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Image dimensions by size name (theme-only, dimension-based).
 *
 * Only the sizes this theme registers (iv2000_WxH). Core WP sizes (thumbnail, medium, large)
 * are not listed; WordPress handles those and our filter leaves their HTML unchanged.
 *
 * @return array<string, array{0: int, 1: int}>
 */
function iv2000_get_image_dimensions_by_size() {
	return array(
		'iv2000_420x280'  => array(420, 280),
		'iv2000_840x560'  => array(840, 560),
		'iv2000_1024x685' => array(1024, 685),
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

	$dimensions = iv2000_get_image_dimensions_by_size();
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
