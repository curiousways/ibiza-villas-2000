<?php
/**
 * Villa ACF field group — PHP registration (`acf_add_local_field_group`).
 *
 * Consolidates the six legacy groups (Villa ID / Villa / Villa Required /
 * Villa Information / Villa Prices / Villa Special Offers) into a single
 * group with left-placed tabs, matching the homepage and about-page admin
 * pattern. Every field's original `key` and `name` is preserved so existing
 * villa data continues to load — only the visual organisation changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the villa data-layer field group.
 *
 * Hooks at priority 15 so mu-plugin baseline Site Options
 * (register-options.php) can run first when both use `acf/init`.
 *
 * @since 3b
 */
function ibv_register_villa_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'                   => 'group_ibv_villa',
		'title'                 => __( 'Villa details', 'ibv' ),
		'fields'                => array(

			// ─── Tab: Identity ──────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_identity',
				'label'     => __( 'Identity', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => false,
				'allow_quickedit'      => false,
				'show_column'          => false,
				'show_column_weight'   => 1000,
				'show_column_sortable' => false,
				'key'                  => 'field_5582ae34eee06',
				'label'                => 'Property ID',
				'name'                 => 'property_id',
				'aria-label'           => '',
				'type'                 => 'text',
				'instructions'         => 'The villa\'s ID in Steve\'s availability/booking system — live pricing, availability and the booking form all key off it. Must match the ID Steve holds for this villa exactly; only change it if the villa\'s ID in Steve\'s system has changed.',
				'required'             => 0,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'        => '',
				'placeholder'          => '',
				'prepend'              => '',
				'append'               => '',
				'maxlength'            => '',
				'readonly'             => 0,
				'disabled'             => 0,
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => 0,
				'allow_quickedit'      => 0,
				'show_column'          => 0,
				'show_column_weight'   => 0,
				'show_column_sortable' => false,
				'key'                  => 'field_5849d0c9962f6',
				'label'                => 'Villa Pretty Name',
				'name'                 => 'villa_pretty_name',
				'aria-label'           => '',
				'type'                 => 'text',
				'instructions'         => 'such as "Villa Tom"',
				'required'             => 0,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'        => '',
				'placeholder'          => '',
				'prepend'              => '',
				'append'               => '',
				'maxlength'            => '',
			),
			// additional_title_keyword is retired: only the legacy theme's H1
			// ever appended it; the ibv theme builds the H1 from
			// villa_pretty_name alone. Postmeta deleted (values were legacy
			// SEO strings on draft/for-sale posts; backup in
			// docs/briefs/done/admin-tidy-backups/).
			array(
				'key'          => 'field_ibv_villa_summary',
				'label'        => __( 'Summary', 'ibv' ),
				'name'         => 'villa_summary',
				'type'         => 'textarea',
				'instructions' => __( 'One-paragraph summary, plain text. Shown in full under "Villa Overview" on the villa page (above the collapsible description) and as the villa card description in listings.', 'ibv' ),
				'rows'         => 4,
				'new_lines'    => '',
			),

			// ─── Tab: Key facts ─────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_key_facts',
				'label'     => __( 'Key facts', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => 0,
				'allow_quickedit'      => 0,
				'show_column'          => 0,
				'show_column_weight'   => 0,
				'show_column_sortable' => false,
				'key'                  => 'field_557982a7e2516',
				'label'                => 'Property Sleeps UK',
				'name'                 => 'property_sleeps',
				'aria-label'           => '',
				'type'                 => 'number',
				'instructions'         => '',
				'required'             => 1,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'        => '',
				'placeholder'          => '',
				'prepend'              => '',
				'append'               => '',
				'min'                  => '',
				'max'                  => 40,
				'step'                 => '',
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => false,
				'allow_quickedit'      => false,
				'show_column'          => false,
				'show_column_weight'   => 1000,
				'show_column_sortable' => false,
				'key'                  => 'field_57a9f17acad6c',
				'label'                => 'Property Bedrooms UK',
				'name'                 => 'property_bedrooms',
				'aria-label'           => '',
				'type'                 => 'number',
				'instructions'         => '',
				'required'             => 1,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'        => '',
				'placeholder'          => '',
				'prepend'              => '',
				'append'               => '',
				'min'                  => '',
				'max'                  => '',
				'step'                 => '',
				'readonly'             => 0,
				'disabled'             => 0,
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => false,
				'allow_quickedit'      => false,
				'show_column'          => false,
				'show_column_weight'   => 1000,
				'show_column_sortable' => false,
				'key'                  => 'field_57a9f1df13490',
				'label'                => 'Property Bathrooms UK',
				'name'                 => 'property_bathrooms',
				'aria-label'           => '',
				'type'                 => 'number',
				'instructions'         => '',
				'required'             => 0,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'        => '',
				'placeholder'          => '',
				'prepend'              => '',
				'append'               => '',
				'min'                  => '',
				'max'                  => '',
				'step'                 => '',
				'readonly'             => 0,
				'disabled'             => 0,
			),
			// property_featured is retired: only the legacy theme's featured-
			// properties widget/template ever queried it; nothing on the ibv
			// site does. Postmeta deleted (flag backup in
			// docs/briefs/done/admin-tidy-backups/).
			array(
				'key'          => 'field_ibv_villa_rating_score',
				'label'        => __( 'Rating score', 'ibv' ),
				'name'         => 'villa_rating_score',
				'type'         => 'number',
				'min'          => 0,
				'max'          => 5,
				'step'         => 0.1,
				'instructions' => __( 'Optional. e.g. 4.9', 'ibv' ),
			),
			array(
				'key'   => 'field_ibv_villa_review_count',
				'label' => __( 'Review count', 'ibv' ),
				'name'  => 'villa_review_count',
				'type'  => 'number',
				'min'   => 0,
				'step'  => 1,
			),
			array(
				'key'   => 'field_ibv_villa_review_source_url',
				'label' => __( 'Review source URL', 'ibv' ),
				'name'  => 'villa_review_source_url',
				'type'  => 'url',
			),
			// notice_uk is retired: a required field ("groups of 12 or more…"
			// default) nothing on either theme ever rendered. Postmeta
			// deleted (backup in docs/briefs/done/admin-tidy-backups/).

			// ─── Tab: Content ───────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_content',
				'label'     => __( 'Content', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			// Wayfinding only: the description is the native post editor,
			// which sits below this box. It stays post_content (not an ACF
			// WYSIWYG) deliberately — Yoast analysis, native search,
			// revisions and the_content rendering all read it, and TinyMCE
			// misbehaves when initialised inside a hidden ACF tab panel.
			array(
				'key'     => 'field_ibv_villa_editor_note',
				'label'   => __( 'Description', 'ibv' ),
				'type'    => 'message',
				'message' => __( 'The full villa description is edited in the main editor directly below this box. It shows under "Villa Overview" behind the Read more toggle; the always-visible one-paragraph summary is the Summary field on the Identity tab.', 'ibv' ),
			),

			// ─── Tab: Property facts ────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_property_facts',
				'label'     => __( 'Property facts', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			// The old "Property Location" WYSIWYG (property_summary) is
			// retired: its bullets were migrated into Location tags,
			// Property Facts and Rental licence (see
			// docs/briefs/active/villa-location-facts/).
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => false,
				'allow_quickedit'      => false,
				'show_column'          => false,
				'show_column_weight'   => 1000,
				'show_column_sortable' => false,
				'key'                  => 'field_5582c42f4c3b4',
				'label'                => 'Property Facts',
				'name'                 => 'property_features',
				'aria-label'           => '',
				'type'                 => 'repeater',
				'instructions'         => 'Short amenity facts shown as ✓ chips under "Villa Overview" on the villa page — e.g. "Air conditioning", "Heated pool", "WiFi". Keep each to a few words.',
				'required'             => 0,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'collapsed'            => '',
				'min'                  => 0,
				'max'                  => 0,
				'layout'               => 'table',
				'button_label'         => 'Add fact',
				// The old "Property Facts Icon" select subfield is gone: the
				// front end always renders a uniform ✓ (amenity-ticks), so
				// the icon choice never did anything.
				'sub_fields'           => array(
					array(
						'allow_backendsearch'  => false,
						'show_column_filter'   => false,
						'allow_bulkedit'       => false,
						'allow_quickedit'      => false,
						'show_column'          => false,
						'show_column_weight'   => 1000,
						'show_column_sortable' => false,
						'key'                  => 'field_5582c5514c3b6',
						'label'                => 'Fact',
						'name'                 => 'property_feature_text',
						'aria-label'           => '',
						'type'                 => 'text',
						'instructions'         => '',
						'required'             => 0,
						'conditional_logic'    => 0,
						'wrapper'              => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'        => '',
						'placeholder'          => 'Air conditioning',
						'prepend'              => '',
						'append'               => '',
						'maxlength'            => '',
						'readonly'             => 0,
						'disabled'             => 0,
						'parent_repeater'      => 'field_5582c42f4c3b4',
					),
				),
				'rows_per_page'        => 20,
			),
			// ─── Tab: Media ─────────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_media',
				'label'     => __( 'Media', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => false,
				'allow_quickedit'      => false,
				'show_column'          => false,
				'show_column_weight'   => 1000,
				'show_column_sortable' => false,
				'key'                  => 'field_55799b13e3584',
				'label'                => 'Property Image Gallery',
				'name'                 => 'property_images',
				'aria-label'           => '',
				'type'                 => 'gallery',
				'instructions'         => 'Landscape or mixed-aspect photos. The pop-up viewer holds images at source shape, so portrait shots display correctly here too.',
				'required'             => 0,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'min'                  => '',
				'max'                  => '',
				'preview_size'         => 'thumbnail',
				'library'              => 'all',
				'min_width'            => '',
				'min_height'           => '',
				'min_size'             => '',
				'max_width'            => '',
				'max_height'           => '',
				'max_size'             => '',
				'mime_types'           => '',
				'return_format'        => 'array',
				'insert'               => 'append',
			),

			// ─── Tab: Location ──────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_location',
				'label'     => __( 'Location', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			array(
				'allow_backendsearch'  => false,
				'show_column_filter'   => false,
				'allow_bulkedit'       => false,
				'allow_quickedit'      => false,
				'show_column'          => false,
				'show_column_weight'   => 1000,
				'show_column_sortable' => false,
				'key'                  => 'field_558065ef4f993',
				'label'                => 'Property Map',
				'name'                 => 'property_map',
				'aria-label'           => '',
				'type'                 => 'google_map',
				'instructions'         => '',
				'required'             => 0,
				'conditional_logic'    => 0,
				'wrapper'              => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'center_lat'           => '45.923697',
				'center_lng'           => '6.869433',
				'zoom'                 => 7,
				'height'               => '',
			),
			array(
				'key'          => 'field_ibv_villa_distances',
				'label'        => __( 'Location tags', 'ibv' ),
				'name'         => 'villa_distances',
				'type'         => 'repeater',
				'instructions' => __( 'Shown as map-pin pills under the map on the villa page (the first one also appears next to the location in the villa header). 3–5 short tags, e.g. "3 mins from beach", "10 mins from Ibiza Town".', 'ibv' ),
				'layout'       => 'table',
				'button_label' => __( 'Add tag', 'ibv' ),
				'sub_fields'   => array(
					array(
						'key'             => 'field_ibv_villa_distance_text',
						'label'           => __( 'Tag', 'ibv' ),
						'name'            => 'distance_text',
						'type'            => 'text',
						'placeholder'     => '3 mins from beach',
						'parent_repeater' => 'field_ibv_villa_distances',
					),
				),
			),
			array(
				'key'          => 'field_ibv_villa_rental_licence',
				'label'        => __( 'Rental licence', 'ibv' ),
				'name'         => 'villa_rental_licence',
				'type'         => 'text',
				'instructions' => __( 'Tourist rental licence number, e.g. "ET0405E". Shown as small print under the location tags on the villa page.', 'ibv' ),
				'placeholder'  => 'ET0405E',
			),

			// ─── Tab: Pricing ───────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_pricing',
				'label'     => __( 'Pricing', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			// property_price_from_euros / property_price_to_euros are
			// retired: only the legacy theme's from/to price templates
			// ever rendered them. The new site uses villa_indicative_from_price
			// as the static fallback before live API prices. Postmeta
			// deleted (backup in docs/briefs/done/admin-tidy-backups/).
			array(
				'key'          => 'field_ibv_villa_indicative_from_price',
				'label'        => __( 'Indicative from price (EUR / wk)', 'ibv' ),
				'name'         => 'villa_indicative_from_price',
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'instructions' => __( 'Static fallback before live API prices.', 'ibv' ),
			),

			// ─── Tab: Offers ────────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_offers',
				'label'     => __( 'Offers', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			// Pass 3c-villa-offers — structured per-villa offers repeater.
			// The two legacy fields below feed the (legacy) Special Offers
			// page carousel and remain in place until that page is
			// refactored to aggregate from this repeater.
			array(
				'key'          => 'field_ibv_villa_offers',
				'label'        => __( 'Special Offers', 'ibv' ),
				'name'         => 'villa_offers',
				'type'         => 'repeater',
				'instructions' => __( 'Add active special offers for this villa. Offers automatically disappear from the site after their "Valid to" date passes.', 'ibv' ),
				'min'          => 0,
				'max'          => 0,
				'layout'       => 'block',
				'button_label' => __( 'Add offer', 'ibv' ),
				'sub_fields'   => array(
					array(
						'key'             => 'field_ibv_villa_offer_name',
						'label'           => __( 'Offer name', 'ibv' ),
						'name'            => 'offer_name',
						'type'            => 'text',
						'required'        => 1,
						'instructions'    => __( 'Marketing label, e.g. "Spring 4-night escape". Identifies the offer in enquiries.', 'ibv' ),
						'parent_repeater' => 'field_ibv_villa_offers',
					),
					array(
						'key'             => 'field_ibv_villa_offer_date_from',
						'label'           => __( 'Valid from', 'ibv' ),
						'name'            => 'offer_date_from',
						'type'            => 'date_picker',
						'required'        => 1,
						'display_format'  => 'j M Y',
						'return_format'   => 'Ymd',
						'first_day'       => 1,
						'parent_repeater' => 'field_ibv_villa_offers',
					),
					array(
						'key'             => 'field_ibv_villa_offer_date_to',
						'label'           => __( 'Valid to', 'ibv' ),
						'name'            => 'offer_date_to',
						'type'            => 'date_picker',
						'required'        => 1,
						'display_format'  => 'j M Y',
						'return_format'   => 'Ymd',
						'first_day'       => 1,
						'parent_repeater' => 'field_ibv_villa_offers',
					),
					array(
						'key'             => 'field_ibv_villa_offer_headline',
						'label'           => __( 'Headline', 'ibv' ),
						'name'            => 'offer_headline',
						'type'            => 'text',
						'required'        => 1,
						'maxlength'       => 30,
						'instructions'    => __( 'Short marketing line, ≤30 chars. e.g. "20% off" or "From €1,800/wk".', 'ibv' ),
						'parent_repeater' => 'field_ibv_villa_offers',
					),
					array(
						'key'             => 'field_ibv_villa_offer_description',
						'label'           => __( 'Description', 'ibv' ),
						'name'            => 'offer_description',
						'type'            => 'textarea',
						'rows'            => 3,
						'new_lines'       => 'wpautop',
						'instructions'    => __( 'Optional supporting copy, ~2 sentences.', 'ibv' ),
						'parent_repeater' => 'field_ibv_villa_offers',
					),
				),
			),
			// property_special_offers_text / property_special_offer_display
			// are retired: only the legacy Special Offers carousel used
			// them. The new site reads the structured villa_offers
			// repeater above. Postmeta deleted (backup in
			// docs/briefs/done/admin-tidy-backups/).

			// ─── Tab: Related ───────────────────────────────────────────
			array(
				'key'       => 'field_ibv_villa_tab_related',
				'label'     => __( 'Related', 'ibv' ),
				'type'      => 'tab',
				'placement' => 'left',
			),
			array(
				'key'           => 'field_ibv_villa_similar',
				'label'         => __( 'Similar villas', 'ibv' ),
				'name'          => 'villa_similar',
				'type'          => 'post_object',
				'post_type'     => array( 'villas' ),
				'multiple'      => 1,
				'min'           => 0,
				'max'           => 3,
				'return_format' => 'id',
				'instructions'  => __( 'Manual picks. Empty falls back to latest villas.', 'ibv' ),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'villas',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => array(
			0 => 'discussion',
			1 => 'comments',
			2 => 'categories',
			3 => 'tags',
			4 => 'send-trackbacks',
		),
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => false,
		'display_title'         => '',
		'allow_ai_access'       => false,
		'ai_description'        => '',
	) );
}

add_action( 'acf/init', 'ibv_register_villa_acf_fields', 15 );
