<?php
/**
 * Ibiza Guide page — page template ACF field group.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Ibiza Guide page template.
 */
function ibv_register_page_ibiza_guide_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$location = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-ibiza-guide.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_ibiza_guide',
			'title'                 => __( 'Ibiza Guide page', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_page_ibiza_guide_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Hero background image. Used at compact height (480px).', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_ibiza_guide_hero_title',
					'label'         => __( 'Hero title', 'ibv' ),
					'name'          => 'hero_title',
					'type'          => 'text',
					'default_value' => __( 'Ibiza Guide', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_ibiza_guide_hero_subtitle',
					'label'         => __( 'Hero subtitle', 'ibv' ),
					'name'          => 'hero_subtitle',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => __( 'Local knowledge, honest recommendations from a team that lives here.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_ibiza_guide_featured_article',
					'label'         => __( 'Featured article', 'ibv' ),
					'name'          => 'ig_featured_article',
					'type'          => 'post_object',
					'post_type'     => array( 'post' ),
					'return_format' => 'id',
					'allow_null'    => 1,
					'instructions'  => __( 'Pick a post to feature in the two-column block above the grid. The picked post is excluded from the grid below.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_ibiza_guide_newsletter_title',
					'label'        => __( 'Newsletter title', 'ibv' ),
					'name'         => 'newsletter_title',
					'type'         => 'text',
					'instructions' => __( 'Title for the in-page newsletter band below the article grid. Leave empty (along with body) to hide the band.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_ibiza_guide_newsletter_body',
					'label'        => __( 'Newsletter body', 'ibv' ),
					'name'         => 'newsletter_body',
					'type'         => 'textarea',
					'rows'         => 3,
					'instructions' => __( 'Body copy for the in-page newsletter band. Leave empty (along with title) to hide the band.', 'ibv' ),
				),
			),
			'location'              => $location,
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

add_action( 'acf/init', 'ibv_register_page_ibiza_guide_fields', 15 );
