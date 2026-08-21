<?php
/**
 * Hide the leftover Classic editor on page templates that never call the_content().
 *
 * Default `page.php` and the Legal template still render post_content — those
 * keep the box. Everything else is ACF / hardcoded sections.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selectable page templates that do not call the_content().
 *
 * front-page.php is not in this list — WordPress picks it via Settings →
 * Reading, not the template dropdown. That case is page_on_front below.
 *
 * @return string[] Template filenames as stored in `_wp_page_template`.
 */
function ibv_page_templates_without_content() {
	return array(
		'page-about.php',
		'page-accommodation.php',
		'page-booking-confirmation.php',
		'page-builder.php',
		'page-concierge.php',
		'page-contact.php',
		'page-ibiza-guide.php',
		'page-ips.php',
		'page-my-booking.php',
		'page-special-offers.php',
		'page-villa-listing.php',
	);
}

/**
 * Whether this page's template never prints post_content.
 *
 * @param int|\WP_Post|null $post Post. Defaults to the current one.
 * @return bool
 */
function ibv_page_hides_content_editor( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'page' !== $post->post_type ) {
		return false;
	}

	if ( (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
		return true;
	}

	return in_array( get_page_template_slug( $post ), ibv_page_templates_without_content(), true );
}

/**
 * Drop editor support for the current page edit screen only.
 *
 * @param string       $post_type Post type.
 * @param \WP_Post|null $post      Post being edited.
 */
function ibv_hide_content_editor_on_acf_templates( $post_type, $post ) {
	if ( 'page' === $post_type && ibv_page_hides_content_editor( $post ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}
add_action( 'add_meta_boxes', 'ibv_hide_content_editor_on_acf_templates', 10, 2 );
