<?php
/**
 * 404 template.
 *
 * Composition only: standard chrome + the shared hero section with
 * content-overrides sourced from Site Options ("404 page (shared)"),
 * falling back to hardcoded copy so the page never renders empty.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$error404_image    = get_field( 'error404_image', 'option' );
$error404_title    = (string) get_field( 'error404_title', 'option' );
$error404_subtitle = (string) get_field( 'error404_subtitle', 'option' );

// The image override passes 0 (not null) when unset — null would fall back to
// the page-field ACF lookup, and a 404 has no post to read. With 0 the hero
// renders on its forest-green fallback, which is the correct degraded state.
ibv_core_section_hero(
	[
		'image'    => $error404_image ? $error404_image : 0,
		'title'    => $error404_title ? $error404_title : __( 'It appears this page has gone off-season', 'ibv' ),
		'subtitle' => $error404_subtitle ? $error404_subtitle : __( 'We\'re afraid something has gone wrong with this link.', 'ibv' ),
		'cta'      => [
			'url'     => ibv_get_search_villas_url(),
			'label'   => __( 'Browse the villas', 'ibv' ),
			'variant' => 'primary',
		],
		'cta_aside' => [
			'label' => __( 'Get in touch', 'ibv' ),
			'url'   => ibv_get_contact_page_url(),
		],
	]
);

get_footer();
