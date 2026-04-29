<?php
/**
 * Villa CPT admin list — column control.
 *
 * Replaces noisy ACF auto-columns with a fixed set. Yoast columns pass through.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run last: ACF and other plugins append columns on this hook at lower priorities.
 * At 99, ACF still ran after us and merged unwanted columns back in.
 */
add_filter( 'manage_villas_posts_columns', 'ibv_villa_admin_columns', PHP_INT_MAX );

/**
 * @param string[] $columns Column slug => title.
 * @return string[]
 */
function ibv_villa_admin_columns( $columns ) {
	$cb = isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox" />';

	$yoast_keys = array_filter(
		array_keys( $columns ),
		function ( $key ) {
			return 0 === strpos( $key, 'wpseo' );
		}
	);
	$yoast_cols = array_intersect_key( $columns, array_flip( $yoast_keys ) );

	$base = array(
		'cb'                => $cb,
		'title'             => __( 'Title', 'ibv' ),
		'villa_pretty_name' => __( 'Pretty name', 'ibv' ),
		'sleeps'            => __( 'Sleeps', 'ibv' ),
		'bedrooms'          => __( 'Beds', 'ibv' ),
		'bathrooms'         => __( 'Baths', 'ibv' ),
	);

	$taxonomies = array(
		'taxonomy-property_location' => __( 'Location', 'ibv' ),
		'taxonomy-villa_amenity'     => __( 'Amenities', 'ibv' ),
		'taxonomy-villa_poi'         => __( 'POIs', 'ibv' ),
	);

	$tail = array(
		'date' => __( 'Date', 'ibv' ),
	);

	return array_merge( $base, $taxonomies, $yoast_cols, $tail );
}

add_action( 'manage_villas_posts_custom_column', 'ibv_villa_admin_column_render', 10, 2 );
function ibv_villa_admin_column_render( $column, $post_id ) {
	switch ( $column ) {
		case 'villa_pretty_name':
			$value = get_field( 'villa_pretty_name', $post_id );
			echo $value ? esc_html( $value ) : '—';
			break;

		case 'sleeps':
			$value = get_field( 'property_sleeps', $post_id );
			echo ( '' !== $value && null !== $value ) ? esc_html( (string) $value ) : '—';
			break;

		case 'bedrooms':
			$value = get_field( 'property_bedrooms', $post_id );
			echo ( '' !== $value && null !== $value ) ? esc_html( (string) $value ) : '—';
			break;

		case 'bathrooms':
			$value = get_field( 'property_bathrooms', $post_id );
			echo ( '' !== $value && null !== $value ) ? esc_html( (string) $value ) : '—';
			break;
	}
}

add_filter( 'manage_edit-villas_sortable_columns', 'ibv_villa_admin_sortable_columns' );
function ibv_villa_admin_sortable_columns( $columns ) {
	$columns['villa_pretty_name'] = 'villa_pretty_name';
	$columns['sleeps']            = 'property_sleeps';
	$columns['bedrooms']          = 'property_bedrooms';
	$columns['bathrooms']         = 'property_bathrooms';
	return $columns;
}

add_action( 'pre_get_posts', 'ibv_villa_admin_orderby' );
function ibv_villa_admin_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-villas' !== $screen->id ) {
		return;
	}

	$orderby = $query->get( 'orderby' );
	if ( ! $orderby ) {
		return;
	}

	$meta_map = array(
		'villa_pretty_name'  => 'villa_pretty_name',
		'property_sleeps'    => 'property_sleeps',
		'property_bedrooms'  => 'property_bedrooms',
		'property_bathrooms' => 'property_bathrooms',
	);

	if ( ! isset( $meta_map[ $orderby ] ) ) {
		return;
	}

	$query->set( 'meta_key', $meta_map[ $orderby ] );
	$query->set(
		'orderby',
		'villa_pretty_name' === $orderby ? 'meta_value' : 'meta_value_num'
	);
}
