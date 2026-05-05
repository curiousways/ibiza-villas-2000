<?php
/**
 * Post-level ACF — pull-quote group + related-articles picker.
 *
 * Bound to all `post` post type entries. Pull quote sub-fields keep
 * editorial UI tidy; related_articles is registered now and consumed
 * by brief 02 (related articles section).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the post post type.
 */
function ibv_register_post_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$location = array(
		array(
			array(
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => 'post',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_post_fields',
			'title'                 => __( 'Article options', 'ibv' ),
			'fields'                => array(
				array(
					'key'        => 'field_ibv_post_pull_quote',
					'label'      => __( 'Pull Quote', 'ibv' ),
					'name'       => 'pull_quote',
					'type'       => 'group',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'   => 'field_ibv_post_pull_quote_text',
							'label' => __( 'Quote text', 'ibv' ),
							'name'  => 'quote_text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'field_ibv_post_pull_quote_author_name',
							'label' => __( 'Author name', 'ibv' ),
							'name'  => 'author_name',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_post_pull_quote_author_title',
							'label' => __( 'Author title', 'ibv' ),
							'name'  => 'author_title',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'           => 'field_ibv_post_related_articles',
					'label'         => __( 'Related Articles', 'ibv' ),
					'name'          => 'related_articles',
					// Relationship (not post_object): native min/max enforcement
					// and drag-to-reorder, which matters here since pick order
					// is the rendered order.
					'type'          => 'relationship',
					'post_type'     => array( 'post' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
					'min'           => 0,
					'max'           => 3,
					'instructions'  => __( 'Up to three posts to feature in the related-articles block at the bottom of the article. Drag to reorder. Leave empty to fall back to category-related posts.', 'ibv' ),
				),
			),
			'location'              => $location,
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_post_fields', 15 );
