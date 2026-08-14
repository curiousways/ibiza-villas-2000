<?php
/**
 * ACF picker queries — never offer media attachments.
 *
 * Bare attachment pages are never linked to on this site, and attachments
 * (mostly untitled images) drown out the real pages in every page_link /
 * post_object / relationship picker. Fields that restrict their post types
 * are unaffected; fields with no restriction get "everything except
 * attachments" instead of everything.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Strip the attachment post type from an ACF picker query.
 *
 * @param array $args WP_Query args ACF is about to run.
 * @return array
 */
function ibv_acf_picker_exclude_media( $args ) {
	if ( empty( $args['post_type'] ) || 'any' === $args['post_type'] ) {
		$args['post_type'] = function_exists( 'acf_get_post_types' ) ? acf_get_post_types() : get_post_types( [ 'public' => true ] );
	}

	$args['post_type'] = array_values( array_diff( (array) $args['post_type'], [ 'attachment' ] ) );

	return $args;
}

add_filter( 'acf/fields/page_link/query', 'ibv_acf_picker_exclude_media' );
add_filter( 'acf/fields/post_object/query', 'ibv_acf_picker_exclude_media' );
add_filter( 'acf/fields/relationship/query', 'ibv_acf_picker_exclude_media' );
