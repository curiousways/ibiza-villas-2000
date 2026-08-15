<?php
/**
 * Booking Confirmation page — page template ACF field groups.
 *
 * Copy is per-variant (villa / accommodation / concierge / general).
 * The Gravity Forms confirmation appends ?type=; an unknown or missing
 * type falls back to general. Steps are optional (0–3) so a contact
 * confirmation can ship with no process cards.
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
				array(
					'key'       => 'field_ibv_page_bc_tab_variants',
					'label'     => __( 'Variants', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'          => 'field_ibv_page_bc_variants',
					'label'        => __( 'Confirmation variants', 'ibv' ),
					'name'         => 'confirmation_variants',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add variant', 'ibv' ),
					'instructions' => __( 'One row per form type. The URL ?type= value picks the row. Unknown or missing types use “general”. Subheading is the lead only — the Site Options response-time note is joined on the page (except concierge). Steps are optional; leave empty to hide the cards.', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_ibv_page_bc_variant_key',
							'label'         => __( 'Variant key', 'ibv' ),
							'name'          => 'variant_key',
							'type'          => 'select',
							'choices'       => array(
								'villa'          => 'villa',
								'accommodation'  => 'accommodation',
								'concierge'      => 'concierge',
								'general'        => 'general',
							),
							'required'      => 1,
							'return_format' => 'value',
						),
						array(
							'key'  => 'field_ibv_page_bc_variant_heading',
							'label' => __( 'Heading', 'ibv' ),
							'name'  => 'variant_heading',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_ibv_page_bc_variant_subheading',
							'label'        => __( 'Subheading', 'ibv' ),
							'name'         => 'variant_subheading',
							'type'         => 'textarea',
							'rows'         => 2,
							'instructions' => __( 'Lead only. Timing is composed from Site Options → Response-time note, except on concierge.', 'ibv' ),
						),
						array(
							'key'          => 'field_ibv_page_bc_variant_contact_intro',
							'label'        => __( 'Contact strip intro', 'ibv' ),
							'name'         => 'variant_contact_intro',
							'type'         => 'text',
							'instructions' => __( 'Opening of the quiet contact line. Phone and WhatsApp come from Site Options.', 'ibv' ),
						),
						array(
							'key'          => 'field_ibv_page_bc_variant_steps',
							'label'        => __( 'Steps', 'ibv' ),
							'name'         => 'variant_steps',
							'type'         => 'repeater',
							'layout'       => 'block',
							'min'          => 0,
							'max'          => 3,
							'button_label' => __( 'Add step', 'ibv' ),
							'sub_fields'   => array(
								array(
									'key'   => 'field_ibv_page_bc_variant_step_title',
									'label' => __( 'Title', 'ibv' ),
									'name'  => 'step_title',
									'type'  => 'text',
								),
								array(
									'key'   => 'field_ibv_page_bc_variant_step_body',
									'label' => __( 'Body', 'ibv' ),
									'name'  => 'step_body',
									'type'  => 'textarea',
									'rows'  => 3,
								),
							),
						),
					),
				),

				// Concierge module (held copy — do not treat as live CTA copy).
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
					'key'          => 'field_ibv_page_bc_concierge_cta_url',
					'label'        => __( 'CTA URL', 'ibv' ),
					'name'         => 'concierge_cta_url',
					'type'         => 'page_link',
					'instructions' => __( 'Pick the page the button links to. Empty URL hides the button.', 'ibv' ),
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
