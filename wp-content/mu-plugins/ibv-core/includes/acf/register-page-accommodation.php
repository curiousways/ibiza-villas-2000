<?php
/**
 * Accommodation page — shared page template ACF field group.
 *
 * Bound to the `page-accommodation.php` template (Apartments). These are
 * fixed editorial pages — not villas. Hero / overview / gallery render
 * presentationally from these fields; the enquiry sidebar embeds a Gravity Form.
 *
 * Field names chosen for zero-change reuse of existing components:
 *   - `hero_image` / `hero_title` / `hero_subtitle` → `ibv_core_section_hero()`
 *   - `property_images` (gallery, return array) → `ibv_core_gallery()`
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Accommodation page template.
 */
function ibv_register_page_accommodation_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_accommodation = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-accommodation.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_accommodation',
			'title'                 => __( 'Accommodation page', 'ibv' ),
			'fields'                => array(

				// ─── Tab: Hero ──────────────────────────────────────────
				array(
					'key'       => 'field_ibv_page_accom_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_accom_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Hero background image. Rendered at compact height (480px) with the title + subtitle overlaid.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_accom_hero_title',
					'label'         => __( 'Hero title', 'ibv' ),
					'name'          => 'hero_title',
					'type'          => 'text',
					'default_value' => __( 'Apartments', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_accom_hero_subtitle',
					'label'         => __( 'Hero subtitle', 'ibv' ),
					'name'          => 'hero_subtitle',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => __( 'Our apartments at Aparthotel Marian Ibiza', 'ibv' ),
				),

				// ─── Tab: Overview ──────────────────────────────────────
				array(
					'key'       => 'field_ibv_page_accom_tab_overview',
					'label'     => __( 'Overview', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_accom_overview_heading',
					'label'         => __( 'Overview heading', 'ibv' ),
					'name'          => 'overview_heading',
					'type'          => 'text',
					'default_value' => __( 'About the apartments', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_accom_overview_body',
					'label'        => __( 'Overview body', 'ibv' ),
					'name'         => 'overview_body',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'media_upload' => 0,
					'toolbar'      => 'basic',
				),
				array(
					'key'          => 'field_ibv_page_accom_fact_location',
					'label'        => __( 'Fact pill — location', 'ibv' ),
					'name'         => 'fact_location',
					'type'         => 'text',
					'instructions' => __( 'Free-text pill, e.g. "Cala Martina". Leave empty to hide.', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_page_accom_fact_sleeps',
					'label' => __( 'Fact pill — sleeps', 'ibv' ),
					'name'  => 'fact_sleeps',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_accom_fact_availability',
					'label' => __( 'Fact pill — availability', 'ibv' ),
					'name'  => 'fact_availability',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_ibv_page_accom_from_price',
					'label'        => __( 'From price (EUR / wk)', 'ibv' ),
					'name'         => 'from_price',
					'type'         => 'number',
					'min'          => 0,
					'step'         => 1,
					'instructions' => __( 'Static "from" price. Leave empty to hide the price block.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_accom_from_price_note',
					'label'         => __( 'Price note', 'ibv' ),
					'name'          => 'from_price_note',
					'type'          => 'text',
					'default_value' => __( 'Price varies by season', 'ibv' ),
				),

				// ─── Tab: Gallery ───────────────────────────────────────
				array(
					'key'       => 'field_ibv_page_accom_tab_gallery',
					'label'     => __( 'Gallery', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_accom_property_images',
					'label'         => __( 'Gallery images', 'ibv' ),
					'name'          => 'property_images',
					'type'          => 'gallery',
					'return_format' => 'array',
					'library'       => 'all',
					'insert'        => 'append',
					'instructions'  => __( 'Same component as villa galleries. First image is the on-page hero; the rest fill the pop-up viewer + thumb strip.', 'ibv' ),
				),

				// ─── Tab: Enquiry ───────────────────────────────────────
				array(
					'key'       => 'field_ibv_page_accom_tab_enquiry',
					'label'     => __( 'Enquiry', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_accom_enquiry_heading',
					'label'         => __( 'Enquiry heading', 'ibv' ),
					'name'          => 'enquiry_heading',
					'type'          => 'text',
					'default_value' => __( 'Enquire about the apartments', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_accom_enquiry_form_id',
					'label'         => __( 'Accommodation enquiry Gravity Form ID', 'ibv' ),
					'name'          => 'accommodation_enquiry_gravity_form_id',
					'type'          => 'number',
					'min'           => 0,
					'instructions'  => __(
						'Gravity Form embedded in the enquiry sidebar. Default is the shared "Accommodation Enquiry" form created by the seeder. Leave empty to hide the form.',
						'ibv'
					),
				),
			),
			'location'              => $loc_accommodation,
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

add_action( 'acf/init', 'ibv_register_page_accommodation_fields', 15 );
