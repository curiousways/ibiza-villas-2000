<?php
/**
 * My Booking page — no editable body, admin explanation, noindex.
 *
 * page-my-booking.php never calls the_content(). The heading is the page
 * title; the lead and form are hardcoded in ibv_core_section_my_booking().
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the My Booking template field group (notice + hide leftover boxes).
 */
function ibv_register_page_my_booking_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_my_booking',
			'title'                 => __( 'My Booking page', 'ibv' ),
			'fields'                => array(
				array(
					'key'     => 'field_ibv_page_my_booking_note',
					'label'   => __( 'This page', 'ibv' ),
					'type'    => 'message',
					'message' => __( 'Guests use this page to look up a booking. The title above is the heading they see. The form copy and the destination (Steve’s booking system) are built into the template — there is nothing else to edit here. The page is hidden from search engines.', 'ibv' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-my-booking.php',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => array(
				'the_content',
				'excerpt',
				'discussion',
				'comments',
				'send-trackbacks',
				'featured_image',
			),
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}
add_action( 'acf/init', 'ibv_register_page_my_booking_fields', 16 );

/**
 * Whether the current (or given) page uses the My Booking template.
 *
 * @param int|\WP_Post|null $post Optional post. Defaults to the current screen.
 * @return bool
 */
function ibv_is_my_booking_template( $post = null ) {
	$post = get_post( $post );
	return $post
		&& 'page' === $post->post_type
		&& 'page-my-booking.php' === get_page_template_slug( $post );
}

/**
 * Hide the Yoast metabox — indexability is forced in code, not a CMS choice.
 */
function ibv_my_booking_remove_yoast_metabox() {
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen check.
	if ( $post_id && ibv_is_my_booking_template( $post_id ) ) {
		remove_meta_box( 'wpseo_meta', 'page', 'normal' );
	}
}
add_action( 'add_meta_boxes', 'ibv_my_booking_remove_yoast_metabox', 100 );

/**
 * Force noindex on the front-end. Utility page; not a marketing URL.
 *
 * @param array $robots Yoast robots directives.
 * @return array
 */
function ibv_my_booking_robots( $robots ) {
	if ( is_page_template( 'page-my-booking.php' ) ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
	}
	return $robots;
}
add_filter( 'wpseo_robots_array', 'ibv_my_booking_robots' );

/**
 * Keep the page out of the XML sitemap as well as the robots tag.
 *
 * @param int[] $ids Post IDs already excluded.
 * @return int[]
 */
function ibv_my_booking_exclude_from_sitemap( $ids ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'   => '_wp_page_template',
					'value' => 'page-my-booking.php',
				),
			),
		)
	);
	return array_values( array_unique( array_merge( (array) $ids, $pages ) ) );
}
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'ibv_my_booking_exclude_from_sitemap' );
