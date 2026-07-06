<?php
/**
 * Legal page — page template ACF field group.
 *
 * Bound to the shared Legals template (page-legal.php). The only template field
 * is the short tab label; body copy is the editor content (the_content), and
 * "Last updated" is the page's modified date — neither needs an ACF field.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Legals page template.
 */
function ibv_register_page_legal_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_legal = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-legal.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_legal',
			'title'                 => __( 'Legal page', 'ibv' ),
			'fields'                => array(
				array(
					'key'          => 'field_ibv_page_legal_tab_label',
					'label'        => __( 'Tab label', 'ibv' ),
					'name'         => 'legal_tab_label',
					'type'         => 'text',
					'instructions' => __( 'Short label for the legal-pages tab nav (e.g. "Website T&Cs"). Falls back to the page title when empty.', 'ibv' ),
				),
			),
			'location'              => $loc_legal,
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_page_legal_fields', 15 );
