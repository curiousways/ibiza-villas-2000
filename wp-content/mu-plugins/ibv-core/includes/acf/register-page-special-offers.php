<?php
/**
 * Special Offers page — page template ACF field groups.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Special Offers page template.
 */
function ibv_register_page_special_offers_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_so = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-special-offers.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_so_page_content',
			'title'                 => __( 'Special Offers — page content', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_so_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_so_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_so_hero_title',
					'label' => __( 'Hero title', 'ibv' ),
					'name'  => 'hero_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_so_hero_subtitle',
					'label' => __( 'Hero subtitle', 'ibv' ),
					'name'  => 'hero_subtitle',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_ibv_so_tab_offer_table',
					'label'     => __( 'Offer table', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'          => 'field_ibv_so_offer_table',
					'label'        => __( 'Offers', 'ibv' ),
					'name'         => 'offer_table',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add offer', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_ibv_so_offer_villa',
							'label'         => __( 'Villa', 'ibv' ),
							'name'          => 'villa',
							'type'          => 'post_object',
							'post_type'     => array( 'villas' ),
							'return_format' => 'id',
							'multiple'      => 0,
						),
						array(
							'key'           => 'field_ibv_so_offer_is_weeks_deal',
							'label'         => __( "Show 'This week's deal' badge", 'ibv' ),
							'name'          => 'is_weeks_deal',
							'type'          => 'true_false',
							'ui'            => 1,
							'default_value' => 0,
							'instructions'  => __( 'Adds a yellow badge to this card. Use on at most one row per page.', 'ibv' ),
						),
						array(
							'key'     => 'field_ibv_so_offer_was_price',
							'label'   => __( 'Was price', 'ibv' ),
							'name'    => 'was_price',
							'type'    => 'number',
							'prepend' => '€',
						),
						array(
							'key'     => 'field_ibv_so_offer_now_price',
							'label'   => __( 'Now price', 'ibv' ),
							'name'    => 'now_price',
							'type'    => 'number',
							'prepend' => '€',
						),
						array(
							'key'            => 'field_ibv_so_offer_valid_from',
							'label'          => __( 'Valid from', 'ibv' ),
							'name'           => 'valid_from',
							'type'           => 'date_picker',
							'display_format' => 'd/m/Y',
							'return_format'  => 'Y-m-d',
						),
						array(
							'key'            => 'field_ibv_so_offer_valid_to',
							'label'          => __( 'Valid to', 'ibv' ),
							'name'           => 'valid_to',
							'type'           => 'date_picker',
							'display_format' => 'd/m/Y',
							'return_format'  => 'Y-m-d',
						),
						array(
							'key'           => 'field_ibv_so_offer_footnote',
							'label'         => __( 'Footnote', 'ibv' ),
							'name'          => 'footnote',
							'type'          => 'text',
							'instructions'  => __( "Optional footnote below this card's pricing", 'ibv' ),
						),
						array(
							'key'           => 'field_ibv_so_offer_show_now_asterisk',
							'label'         => __( 'Asterisk after Now price', 'ibv' ),
							'name'          => 'show_now_asterisk',
							'type'          => 'true_false',
							'ui'            => 1,
							'default_value' => 0,
							'instructions'  => __( 'Show * after the Now price (paired with footnote)', 'ibv' ),
						),
					),
				),
			),
			'location'              => $loc_so,
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_so_empty_state',
			'title'                 => __( 'Special Offers — empty state', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_so_empty_tab_main',
					'label'     => __( 'Empty state', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_so_empty_title',
					'label'         => __( 'Title', 'ibv' ),
					'name'          => 'empty_state_title',
					'type'          => 'text',
					'default_value' => __( 'Nothing available', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_so_empty_body',
					'label'         => __( 'Body', 'ibv' ),
					'name'          => 'empty_state_body',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => __( 'No special offers for you right now — get offers by email, check back soon, or browse all villas.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_so_empty_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'empty_state_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_so_empty_browse_cta_url',
					'label' => __( 'Browse all villas URL', 'ibv' ),
					'name'  => 'empty_state_browse_cta_url',
					'type'  => 'url',
				),
			),
			'location'              => $loc_so,
			'menu_order'            => 1,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_page_special_offers_fields', 16 );
