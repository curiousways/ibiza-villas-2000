<?php
/**
 * About page — page template ACF field groups.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the About page template.
 */
function ibv_register_page_about_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_about = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-about.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_about',
			'title'                 => __( 'About page', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_page_about_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_about_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Hero background image. Used at compact height (480px).', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_about_hero_title',
					'label'         => __( 'Hero title', 'ibv' ),
					'name'          => 'hero_title',
					'type'          => 'text',
					'default_value' => __( 'About Ibiza Villas 2000', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_about_hero_subtitle',
					'label'         => __( 'Hero subtitle', 'ibv' ),
					'name'          => 'hero_subtitle',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => __( "Warm, direct intro — who we are, how long we've been going, what makes us different. Not corporate.", 'ibv' ),
				),
				array(
					'key'       => 'field_ibv_page_about_tab_stats',
					'label'     => __( 'Stats', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'          => 'field_ibv_page_about_stats',
					'label'        => __( 'Stats', 'ibv' ),
					'name'         => 'about_stats',
					'type'         => 'repeater',
					'layout'       => 'block',
					'min'          => 4,
					'max'          => 4,
					'button_label' => __( 'Add stat', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'          => 'field_ibv_page_about_stat_eyebrow',
							'label'        => __( 'Eyebrow', 'ibv' ),
							'name'         => 'eyebrow',
							'type'         => 'text',
							'required'     => 1,
							'instructions' => __( "Small label above the value. e.g. 'Established'", 'ibv' ),
						),
						array(
							'key'          => 'field_ibv_page_about_stat_value',
							'label'        => __( 'Value', 'ibv' ),
							'name'         => 'value',
							'type'         => 'text',
							'required'     => 1,
							'instructions' => __( "Headline number or short text. e.g. '2002', '15+', '20min', 'AVAT'", 'ibv' ),
						),
						array(
							'key'          => 'field_ibv_page_about_stat_caption',
							'label'        => __( 'Caption', 'ibv' ),
							'name'         => 'caption',
							'type'         => 'text',
							'required'     => 1,
							'instructions' => __( "Caption beneath the value. e.g. 'Established', 'Villas', 'Response time', 'members'", 'ibv' ),
						),
					),
				),
				array(
					'key'       => 'field_ibv_page_about_tab_story',
					'label'     => __( 'Our Story', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_about_story_eyebrow',
					'label'         => __( 'Eyebrow', 'ibv' ),
					'name'          => 'about_story_eyebrow',
					'type'          => 'text',
					'default_value' => __( 'Our Story', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_page_about_story_title',
					'label' => __( 'Title', 'ibv' ),
					'name'  => 'about_story_title',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_ibv_page_about_story_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'about_story_image',
					'type'          => 'image',
					'return_format' => 'array',
					'required'      => 1,
					'instructions'  => __( 'Image displayed alongside the story entries on desktop.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_about_story_entries',
					'label'        => __( 'Entries', 'ibv' ),
					'name'         => 'about_story_entries',
					'type'         => 'repeater',
					'layout'       => 'block',
					'min'          => 2,
					'button_label' => __( 'Add entry', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'          => 'field_ibv_page_about_story_entry_label',
							'label'        => __( 'Label', 'ibv' ),
							'name'         => 'label',
							'type'         => 'text',
							'instructions' => __( "Short label. e.g. 'Origin', 'Growth', 'Values'", 'ibv' ),
						),
						array(
							'key'          => 'field_ibv_page_about_story_entry_body',
							'label'        => __( 'Body', 'ibv' ),
							'name'         => 'body',
							'type'         => 'textarea',
							'rows'         => 4,
							'instructions' => __( 'Plain text.', 'ibv' ),
						),
					),
				),
			),
			'location'              => $loc_about,
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

add_action( 'acf/init', 'ibv_register_page_about_fields', 15 );
