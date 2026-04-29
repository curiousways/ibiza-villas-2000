<?php
/**
 * Disable WordPress comments (front + admin).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'ibv_disable_comments_post_type_support', 100 );
function ibv_disable_comments_post_type_support() {
	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}

add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

add_filter( 'comments_array', '__return_empty_array', 10, 2 );

add_action( 'admin_menu', 'ibv_disable_comments_admin_menu' );
function ibv_disable_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}

add_action( 'admin_init', 'ibv_disable_comments_admin_redirect' );
function ibv_disable_comments_admin_redirect() {
	global $pagenow;
	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}

add_action( 'admin_init', 'ibv_disable_comments_dashboard' );
function ibv_disable_comments_dashboard() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}

add_action( 'wp_before_admin_bar_render', 'ibv_disable_comments_admin_bar' );
function ibv_disable_comments_admin_bar() {
	global $wp_admin_bar;
	if ( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'comments' );
	}
}
