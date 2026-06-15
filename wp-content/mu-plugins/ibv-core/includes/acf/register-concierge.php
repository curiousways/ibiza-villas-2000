<?php
/**
 * Site Options — concierge cross-sell field group.
 *
 * The image-text band reused across villa detail / booking confirmation
 * (Pass 3c-detail): heading / body / image / CTA pair. Genuinely shared
 * across pages, so it stays global.
 *
 * (The Concierge page's services repeater + enquiry form ID used to live
 * here too; they now live on the Concierge page template itself —
 * see register-page-concierge.php.)
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the concierge cross-sell field group on Site Options.
 */
function ibv_register_concierge_site_options() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_concierge',
			'title'                 => __( 'Concierge cross-sell', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_concierge_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'concierge_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_concierge_heading',
					'label' => __( 'Heading', 'ibv' ),
					'name'  => 'concierge_heading',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_concierge_body',
					'label' => __( 'Body', 'ibv' ),
					'name'  => 'concierge_body',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_concierge_cta_label',
					'label' => __( 'CTA label', 'ibv' ),
					'name'  => 'concierge_cta_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_concierge_cta_url',
					'label' => __( 'CTA page', 'ibv' ),
					'name'  => 'concierge_cta_url',
					'type'  => 'page_link',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ibv-site-options',
					),
				),
			),
			'menu_order'            => 5,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_concierge_site_options', 15 );
