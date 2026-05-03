<?php
/**
 * Section: Meet the team teaser.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meet the team block — image + copy via shared image-text-section (image left).
 */
function ibv_core_section_meet_team_teaser() {
	$img   = get_field( 'meet_team_image' );
	$title = get_field( 'meet_team_title' );
	$text  = get_field( 'meet_team_text' );
	$clab  = get_field( 'meet_team_cta_label' );
	$curl  = get_field( 'meet_team_cta_url' );

	$has_image = ! empty( $img['ID'] ) || ( is_numeric( $img ) && (int) $img > 0 );

	if ( ! $title && ! $text && ! $has_image ) {
		return;
	}

	ibv_core_image_text_section(
		[
			'title'       => $title,
			'description' => $text,
			'cta_url'     => $curl ? esc_url( $curl ) : '',
			'cta_label'   => $clab,
			'image'       => $img,
			'image_side'  => 'left',
			// No background — sits on the page off-white directly
		]
	);
}
