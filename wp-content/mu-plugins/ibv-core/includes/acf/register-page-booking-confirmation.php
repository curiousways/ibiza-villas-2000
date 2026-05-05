<?php
/**
 * Booking Confirmation page — page template ACF field groups.
 *
 * Page composition: confirmation panel (heading + optional subheading)
 * → optional URL-driven booking-details panel (rendered inline by the
 * template, no ACF) → three-step (page-scoped content) → concierge
 * image-text section. The three-step content is page-scoped here so
 * Tina can write copy specific to "after you've enquired" without
 * touching the homepage globals.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fields for the Booking Confirmation page template.
 */
function ibv_register_page_booking_confirmation_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$location = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-booking-confirmation.php',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_booking_confirmation',
			'title'                 => __( 'Booking confirmation page', 'ibv' ),
			'fields'                => array(
				// Confirmation panel.
				array(
					'key'       => 'field_ibv_page_bc_tab_confirmation',
					'label'     => __( 'Confirmation', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_bc_heading',
					'label'         => __( 'Heading', 'ibv' ),
					'name'          => 'confirmation_heading',
					'type'          => 'text',
					'required'      => 1,
					'default_value' => __( 'Booking request received', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_bc_subheading',
					'label'        => __( 'Subheading', 'ibv' ),
					'name'         => 'confirmation_subheading',
					'type'         => 'text',
					'instructions' => __( 'Optional supporting line below the heading.', 'ibv' ),
				),

				// Three-step (page-scoped content).
				array(
					'key'       => 'field_ibv_page_bc_tab_three_step',
					'label'     => __( 'Three-step', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_bc_three_step_eyebrow',
					'label'         => __( 'Eyebrow', 'ibv' ),
					'name'          => 'three_step_eyebrow',
					'type'          => 'text',
					'default_value' => __( 'What happens next', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_bc_three_step_title',
					'label'         => __( 'Title', 'ibv' ),
					'name'          => 'three_step_title',
					'type'          => 'text',
					'default_value' => __( 'Three steps to confirmation', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_bc_three_step_steps',
					'label'        => __( 'Steps', 'ibv' ),
					'name'         => 'three_step_steps',
					'type'         => 'repeater',
					'layout'       => 'block',
					'min'          => 3,
					'max'          => 3,
					'button_label' => __( 'Add step', 'ibv' ),
					'instructions' => __( 'Three steps that describe the post-enquiry journey.', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_ibv_page_bc_three_step_card_title',
							'label' => __( 'Title', 'ibv' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_page_bc_three_step_card_text',
							'label' => __( 'Body', 'ibv' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),

				// Concierge.
				array(
					'key'       => 'field_ibv_page_bc_tab_concierge',
					'label'     => __( 'Concierge', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_bc_concierge_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'concierge_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_ibv_page_bc_concierge_title',
					'label'         => __( 'Title', 'ibv' ),
					'name'          => 'concierge_title',
					'type'          => 'text',
					'default_value' => __( 'Explore concierge services', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_page_bc_concierge_body',
					'label' => __( 'Body', 'ibv' ),
					'name'  => 'concierge_body',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'           => 'field_ibv_page_bc_concierge_cta_label',
					'label'         => __( 'CTA label', 'ibv' ),
					'name'          => 'concierge_cta_label',
					'type'          => 'text',
					'default_value' => __( 'Enquire about a villa', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_page_bc_concierge_cta_url',
					'label' => __( 'CTA URL', 'ibv' ),
					'name'  => 'concierge_cta_url',
					'type'  => 'url',
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

add_action( 'acf/init', 'ibv_register_page_booking_confirmation_fields', 16 );
