<?php
/**
 * Ibiza Preservation Society page — page template ACF field groups.
 *
 * Bound to the IPS page template (page-ips.php). Every consuming section is
 * arg-driven, so field names are free; the template reads these and passes
 * them straight into hero / image-entries-section / image-text-section.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the IPS page template.
 */
function ibv_register_page_ips_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_ips = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-ips.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_ips',
			'title'                 => __( 'Ibiza Preservation Society page', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_page_ips_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_ips_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Hero background image. Used at compact height (480px).', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_ips_hero_title',
					'label'         => __( 'Hero title', 'ibv' ),
					'name'          => 'hero_title',
					'type'          => 'text',
					'default_value' => __( 'Ibiza Preservation Society', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_ips_hero_subtitle',
					'label'         => __( 'Hero subtitle', 'ibv' ),
					'name'          => 'hero_subtitle',
					'type'          => 'textarea',
					'rows'          => 2,
				),
				array(
					'key'       => 'field_ibv_page_ips_tab_wwd',
					'label'     => __( 'What we do', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_ips_wwd_title',
					'label'         => __( 'Title', 'ibv' ),
					'name'          => 'ips_wwd_title',
					'type'          => 'text',
					'default_value' => __( 'What we do', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_ips_wwd_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'ips_wwd_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Image displayed alongside the entries on desktop (right).', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_ips_wwd_entries',
					'label'        => __( 'Entries', 'ibv' ),
					'name'         => 'ips_wwd_entries',
					'type'         => 'repeater',
					'layout'       => 'block',
					'min'          => 1,
					'button_label' => __( 'Add entry', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'          => 'field_ibv_page_ips_wwd_entry_label',
							'label'        => __( 'Label', 'ibv' ),
							'name'         => 'label',
							'type'         => 'text',
							'instructions' => __( 'Short label for the row.', 'ibv' ),
						),
						array(
							'key'   => 'field_ibv_page_ips_wwd_entry_body',
							'label' => __( 'Body', 'ibv' ),
							'name'  => 'body',
							'type'  => 'textarea',
							'rows'  => 4,
						),
					),
				),
				array(
					'key'       => 'field_ibv_page_ips_tab_fom',
					'label'     => __( 'Find out more', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'   => 'field_ibv_page_ips_fom_title',
					'label' => __( 'Title', 'ibv' ),
					'name'  => 'ips_fom_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_ips_fom_description',
					'label' => __( 'Description', 'ibv' ),
					'name'  => 'ips_fom_description',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'           => 'field_ibv_page_ips_fom_cta_label',
					'label'         => __( 'CTA label', 'ibv' ),
					'name'          => 'ips_fom_cta_label',
					'type'          => 'text',
				),
				array(
					'key'           => 'field_ibv_page_ips_fom_cta_url',
					'label'         => __( 'CTA URL', 'ibv' ),
					'name'          => 'ips_fom_cta_url',
					'type'          => 'url',
					'default_value' => 'https://ibizapreservation.org',
				),
				array(
					'key'           => 'field_ibv_page_ips_fom_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'ips_fom_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Image displayed alongside the copy on desktop (left).', 'ibv' ),
				),
			),
			'location'              => $loc_ips,
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

add_action( 'acf/init', 'ibv_register_page_ips_fields', 15 );
