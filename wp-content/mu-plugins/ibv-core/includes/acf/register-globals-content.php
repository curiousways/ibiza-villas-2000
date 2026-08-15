<?php
/**
 * Site Options — shared editorial content (featured offer, short breaks, newsletter form ID).
 *
 * Single source of truth for sections reused across homepage and Special Offers.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register globals content field groups on the Site Options page.
 */
function ibv_register_globals_content_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc = static function ( $slug ) {
		return array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => $slug,
				),
			),
		);
	};

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_global_featured_offer',
			'title'                 => __( 'Featured offer (shared)', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_global_featured_offer_villa',
					'label'         => __( 'Villa', 'ibv' ),
					'name'          => 'featured_offer_villa',
					'type'          => 'post_object',
					'post_type'     => array( 'villas' ),
					'return_format' => 'id',
					'multiple'      => 0,
				),
				array(
					'key'     => 'field_ibv_global_featured_offer_was_price',
					'label'   => __( 'Was price', 'ibv' ),
					'name'    => 'featured_offer_was_price',
					'type'    => 'number',
					'prepend' => '€',
				),
				array(
					'key'     => 'field_ibv_global_featured_offer_now_price',
					'label'   => __( 'Now price', 'ibv' ),
					'name'    => 'featured_offer_now_price',
					'type'    => 'number',
					'prepend' => '€',
				),
				array(
					'key'            => 'field_ibv_global_featured_offer_valid_from',
					'label'          => __( 'Valid from', 'ibv' ),
					'name'           => 'featured_offer_valid_from',
					'type'           => 'date_picker',
					'display_format' => 'd/m/Y',
					'return_format'  => 'Ymd',
				),
				array(
					'key'            => 'field_ibv_global_featured_offer_valid_to',
					'label'          => __( 'Valid to', 'ibv' ),
					'name'           => 'featured_offer_valid_to',
					'type'           => 'date_picker',
					'display_format' => 'd/m/Y',
					'return_format'  => 'Ymd',
					'instructions'   => __( 'The featured offer block hides itself everywhere once this date has passed. Leave empty to keep it up indefinitely.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_global_featured_offer_show_now_asterisk',
					'label'         => __( 'Show asterisk after Now price', 'ibv' ),
					'name'          => 'featured_offer_show_now_asterisk',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
					'instructions'  => __( 'Show * after the Now price (footnote below).', 'ibv' ),
				),
				array(
					'key'            => 'field_ibv_global_featured_offer_footnote',
					'label'          => __( 'Footnote', 'ibv' ),
					'name'           => 'featured_offer_footnote',
					'type'           => 'text',
					'instructions'   => __( 'Optional footnote below the panel (e.g. "There may be additional costs").', 'ibv' ),
				),
			),
			'location'              => $loc( 'ibv-options-featured-offer' ),
			'menu_order'            => 3,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_global_short_breaks',
			'title'                 => __( 'Short breaks (shared)', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_global_short_breaks_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'short_breaks_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_global_short_breaks_title',
					'label' => __( 'Title', 'ibv' ),
					'name'  => 'short_breaks_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_global_short_breaks_text',
					'label' => __( 'Text', 'ibv' ),
					'name'  => 'short_breaks_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
			),
			// Lives on the Shared content page (own metabox below the
			// testimonials/cross-sell group) — too small for its own page.
			// Same 'options' post_id, so stored values are untouched.
			'location'              => $loc( 'ibv-options-shared-content' ),
			'menu_order'            => 4,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_global_newsletter',
			'title'                 => __( 'Newsletter (global)', 'ibv' ),
			'fields'                => array(
				// Intro/body moved here from the Global group when the
				// options were split into sub-pages — same field keys and
				// names, so stored values carried over untouched.
				array(
					'key'            => 'field_ibv_global_newsletter_intro',
					'label'          => __( 'Newsletter intro', 'ibv' ),
					'name'           => 'newsletter_intro',
					'type'           => 'text',
					'instructions'   => __( 'Title above the newsletter form. Renders in the global footer.', 'ibv' ),
				),
				array(
					'key'            => 'field_ibv_global_newsletter_body',
					'label'          => __( 'Newsletter body', 'ibv' ),
					'name'           => 'newsletter_body',
					'type'           => 'textarea',
					'rows'           => 2,
					'instructions'   => __( 'Subtitle line beneath the newsletter title. Defaults to "Sign up to receive marketing from Ibiza Villas 2000" if empty.', 'ibv' ),
				),
				array(
					'key'            => 'field_ibv_global_newsletter_gravity_form_id',
					'label'          => __( 'Newsletter Gravity Form ID', 'ibv' ),
					'name'           => 'newsletter_gravity_form_id',
					'type'           => 'number',
					'min'            => 0,
					'instructions'   => __( 'Gravity Form ID used by all newsletter forms across the site (footer, empty states, etc.). Set once here.', 'ibv' ),
				),
			),
			'location'              => $loc( 'ibv-options-newsletter' ),
			'menu_order'            => 5,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);

	// Field names use the `error404_` prefix (matching WP's `error404` body
	// class) rather than a leading digit. No CTA fields — the "Go back" button
	// is fixed chrome, not editorial.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_global_error404',
			'title'                 => __( '404 page (shared)', 'ibv' ),
			'fields'                => array(
				array(
					'key'           => 'field_ibv_global_error404_image',
					'label'         => __( 'Background image', 'ibv' ),
					'name'          => 'error404_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Full-bleed photo behind the 404 message. Landscape, ideally 2560px+ wide.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_global_error404_title',
					'label'        => __( 'Title', 'ibv' ),
					'name'         => 'error404_title',
					'type'         => 'text',
					'instructions' => __( 'Falls back to "It appears this page has gone off-season" when empty.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_global_error404_subtitle',
					'label'        => __( 'Sub-line', 'ibv' ),
					'name'         => 'error404_subtitle',
					'type'         => 'text',
					'instructions' => __( 'Falls back to "We\'re afraid something has gone wrong with this link." when empty.', 'ibv' ),
				),
			),
			'location'              => $loc( 'ibv-options-404' ),
			'menu_order'            => 6,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => false,
		)
	);
}

add_action( 'acf/init', 'ibv_register_globals_content_fields', 16 );
