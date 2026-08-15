<?php
/**
 * Page Builder — page template ACF field group.
 *
 * Bound to the Page Builder template (page-builder.php). One flexible content
 * field, `builder_sections`, whose layouts each map 1:1 onto an existing
 * ibv-core section render function; the template's switch is the only glue.
 *
 * Extending this: a new layout is one entry in `$layouts` here plus one `case`
 * in `themes/ibv/page-builder.php`. A section earns a layout only once it is
 * parametrised and context-free — see
 * `docs/briefs/done/feature-page-builder-template.md`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Surface choices exposed to editors.
 *
 * Mirrors the six slugs every surface-aware section validates against (see
 * `assets/css/sections.css` and each section's `$valid_surfaces`). Labels are
 * plain English — editors never see the slug.
 *
 * @return array<string,string>
 */
function ibv_page_builder_surface_choices() {
	return array(
		'bg'           => __( 'Off-white (page default)', 'ibv' ),
		'white'        => __( 'White', 'ibv' ),
		'tint-teal'    => __( 'Teal tint', 'ibv' ),
		'tint-gold'    => __( 'Gold tint', 'ibv' ),
		'tint-blue'    => __( 'Blue tint', 'ibv' ),
		'forest-green' => __( 'Forest green (dark)', 'ibv' ),
	);
}

/**
 * Register the Page Builder field group.
 */
function ibv_register_page_builder_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_builder = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-builder.php',
			),
		),
	);

	$surfaces = ibv_page_builder_surface_choices();

	// Every layout is keyed by its layout key so the array reads the same way
	// an ACF export would. `name` is what `get_row_layout()` returns and is the
	// contract with the template's switch — do not rename one without the other.
	$layouts = array(

		'layout_ibv_page_builder_image_entries' => array(
			'key'        => 'layout_ibv_page_builder_image_entries',
			'name'       => 'image_entries',
			'label'      => __( 'Image + entries', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'   => 'field_ibv_page_builder_image_entries_title',
					'label' => __( 'Title', 'ibv' ),
					'name'  => 'title',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_ibv_page_builder_image_entries_entries',
					'label'        => __( 'Entries', 'ibv' ),
					'name'         => 'entries',
					'type'         => 'repeater',
					'layout'       => 'block',
					'min'          => 1,
					'button_label' => __( 'Add entry', 'ibv' ),
					'sub_fields'   => array(
						array(
							'key'          => 'field_ibv_page_builder_image_entries_entry_label',
							'label'        => __( 'Label', 'ibv' ),
							'name'         => 'label',
							'type'         => 'text',
							'instructions' => __( 'Short label for the row.', 'ibv' ),
						),
						array(
							'key'   => 'field_ibv_page_builder_image_entries_entry_body',
							'label' => __( 'Body', 'ibv' ),
							'name'  => 'body',
							'type'  => 'textarea',
							'rows'  => 4,
						),
					),
				),
				array(
					'key'           => 'field_ibv_page_builder_image_entries_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Image displayed alongside the entries on desktop.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_builder_image_entries_image_side',
					'label'         => __( 'Image side', 'ibv' ),
					'name'          => 'image_side',
					'type'          => 'select',
					'choices'       => array(
						'right' => __( 'Right', 'ibv' ),
						'left'  => __( 'Left', 'ibv' ),
					),
					'default_value' => 'right',
					'allow_null'    => 0,
					'instructions'  => __( 'Desktop only — the image always stacks below the copy on mobile.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_builder_image_entries_surface',
					'label'         => __( 'Background', 'ibv' ),
					'name'          => 'surface',
					'type'          => 'select',
					'choices'       => $surfaces,
					'default_value' => 'bg',
					'allow_null'    => 0,
				),
			),
		),

		'layout_ibv_page_builder_image_text' => array(
			'key'        => 'layout_ibv_page_builder_image_text',
			'name'       => 'image_text',
			'label'      => __( 'Image + text with CTA', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'   => 'field_ibv_page_builder_image_text_title',
					'label' => __( 'Title', 'ibv' ),
					'name'  => 'title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_builder_image_text_description',
					'label' => __( 'Description', 'ibv' ),
					'name'  => 'description',
					'type'  => 'textarea',
					'rows'  => 5,
				),
				array(
					'key'          => 'field_ibv_page_builder_image_text_cta_label',
					'label'        => __( 'CTA label', 'ibv' ),
					'name'         => 'cta_label',
					'type'         => 'text',
					'instructions' => __( 'The button only appears when both a label and a page are set.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_builder_image_text_cta_page',
					'label'        => __( 'CTA page', 'ibv' ),
					'name'         => 'cta_page',
					'type'         => 'page_link',
					'multiple'     => 0,
					'allow_null'   => 1,
					'instructions' => __( 'Pick the page the button links to.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_builder_image_text_image',
					'label'         => __( 'Image', 'ibv' ),
					'name'          => 'image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_ibv_page_builder_image_text_image_side',
					'label'         => __( 'Image side', 'ibv' ),
					'name'          => 'image_side',
					'type'          => 'select',
					'choices'       => array(
						'right' => __( 'Right', 'ibv' ),
						'left'  => __( 'Left', 'ibv' ),
					),
					'default_value' => 'right',
					'allow_null'    => 0,
					'instructions'  => __( 'Desktop only — the image always stacks below the copy on mobile.', 'ibv' ),
				),
				array(
					// This section's own default is "no surface"; `none` is the
					// editor-facing spelling of that and the template maps it
					// back to an empty string.
					'key'           => 'field_ibv_page_builder_image_text_surface',
					'label'         => __( 'Background', 'ibv' ),
					'name'          => 'surface',
					'type'          => 'select',
					'choices'       => array_merge(
						array( 'none' => __( 'None (inherits the page background)', 'ibv' ) ),
						$surfaces
					),
					'default_value' => 'none',
					'allow_null'    => 0,
				),
			),
		),

		'layout_ibv_page_builder_title_band' => array(
			'key'        => 'layout_ibv_page_builder_title_band',
			'name'       => 'title_band',
			'label'      => __( 'Title band', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_ibv_page_builder_title_band_title',
					'label'        => __( 'Title', 'ibv' ),
					'name'         => 'title',
					'type'         => 'text',
					'instructions' => __( 'Styled label, not a heading — the page heading is the hero title.', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_builder_title_band_meta',
					'label'        => __( 'Meta line', 'ibv' ),
					'name'         => 'meta',
					'type'         => 'text',
					'instructions' => __( 'Optional small line beneath the label (e.g. "Last updated: March 2026").', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_builder_title_band_surface',
					'label'         => __( 'Background', 'ibv' ),
					'name'          => 'surface',
					'type'          => 'select',
					'choices'       => $surfaces,
					'default_value' => 'bg',
					'allow_null'    => 0,
				),
			),
		),

		'layout_ibv_page_builder_prose' => array(
			'key'        => 'layout_ibv_page_builder_prose',
			'name'       => 'prose',
			'label'      => __( 'Text block', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_ibv_page_builder_prose_content',
					'label'        => __( 'Content', 'ibv' ),
					'name'         => 'content',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
					'delay'        => 0,
					'instructions' => __( 'Free text in a narrow reading column — same typography as the legal pages and article bodies.', 'ibv' ),
				),
				array(
					'key'           => 'field_ibv_page_builder_prose_surface',
					'label'         => __( 'Background', 'ibv' ),
					'name'          => 'surface',
					'type'          => 'select',
					'choices'       => $surfaces,
					'default_value' => 'bg',
					'allow_null'    => 0,
				),
			),
		),

		'layout_ibv_page_builder_three_step' => array(
			'key'        => 'layout_ibv_page_builder_three_step',
			'name'       => 'three_step',
			'label'      => __( 'Three-step process', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_three_step_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. The eyebrow, title and three steps are managed in Site Options.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_trust_strip' => array(
			'key'        => 'layout_ibv_page_builder_trust_strip',
			'name'       => 'trust_strip',
			'label'      => __( 'Trust logos strip', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_trust_strip_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. <strong>Heads up:</strong> the logos are stored on the Home page, not in Site Options, so this band currently renders nothing on a Page Builder page. Leave it out until the logos are moved to Site Options.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_testimonials' => array(
			'key'        => 'layout_ibv_page_builder_testimonials',
			'name'       => 'testimonials',
			'label'      => __( 'Testimonials', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_testimonials_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. The quotes are managed in Site Options → Shared content.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_newsletter_cta' => array(
			'key'        => 'layout_ibv_page_builder_newsletter_cta',
			'name'       => 'newsletter_cta',
			'label'      => __( 'Newsletter sign-up', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_newsletter_cta_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. The copy and the form are managed in Site Options.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_short_breaks' => array(
			'key'        => 'layout_ibv_page_builder_short_breaks',
			'name'       => 'short_breaks',
			'label'      => __( 'Short breaks band', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_short_breaks_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'The image, copy and link are managed in Site Options → Short breaks (shared). Only the background is set here.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
				array(
					'key'           => 'field_ibv_page_builder_short_breaks_surface',
					'label'         => __( 'Background', 'ibv' ),
					'name'          => 'surface',
					'type'          => 'select',
					'choices'       => $surfaces,
					'default_value' => 'bg',
					'allow_null'    => 0,
				),
			),
		),

		'layout_ibv_page_builder_meet_team_teaser' => array(
			'key'        => 'layout_ibv_page_builder_meet_team_teaser',
			'name'       => 'meet_team_teaser',
			'label'      => __( 'Meet the team teaser', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_meet_team_teaser_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. <strong>Heads up:</strong> the image and copy are stored on the Home page, not in Site Options, so this band currently renders nothing on a Page Builder page. Leave it out until the fields are moved to Site Options.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_concierge_cross_sell' => array(
			'key'        => 'layout_ibv_page_builder_concierge_cross_sell',
			'name'       => 'concierge_cross_sell',
			'label'      => __( 'Concierge cross-sell', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_ibv_page_builder_concierge_cross_sell_heading',
					'label'        => __( 'Heading override', 'ibv' ),
					'name'         => 'heading_override',
					'type'         => 'text',
					'instructions' => __( 'Optional. Leave empty to use the heading from Site Options → Shared content. The image, body and button always come from Site Options.', 'ibv' ),
				),
			),
		),

		'layout_ibv_page_builder_ibiza_guide_preview' => array(
			'key'        => 'layout_ibv_page_builder_ibiza_guide_preview',
			'name'       => 'ibiza_guide_preview',
			'label'      => __( 'Ibiza Guide preview', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_ibiza_guide_preview_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. The hand-picked articles live on the Home page, so on a Page Builder page this shows the three most recent articles under the heading "Our Ibiza Guide", with no "View all" button.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_why_iv2000' => array(
			'key'        => 'layout_ibv_page_builder_why_iv2000',
			'name'       => 'why_iv2000',
			'label'      => __( 'Why Ibiza Villas 2000', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_why_iv2000_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. <strong>Heads up:</strong> the pillars are stored on the Home page, not in Site Options, so this band currently renders nothing on a Page Builder page. Leave it out until the pillars are moved to Site Options.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_fancy_different' => array(
			'key'        => 'layout_ibv_page_builder_fancy_different',
			'name'       => 'fancy_different',
			'label'      => __( 'Fancy something different', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_fancy_different_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. Renders the apartments teaser (image and page from Site Options); the heading falls back to "Fancy something a bit different?" outside the Home page.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),

		'layout_ibv_page_builder_featured_villas' => array(
			'key'        => 'layout_ibv_page_builder_featured_villas',
			'name'       => 'featured_villas',
			'label'      => __( 'Featured villas', 'ibv' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'       => 'field_ibv_page_builder_featured_villas_note',
					'label'     => '',
					'name'      => 'note',
					'type'      => 'message',
					'message'   => __( 'No settings. The hand-picked villas live on the Home page, so on a Page Builder page this shows the four most recently added villas.', 'ibv' ),
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_builder',
			'title'                 => __( 'Page Builder', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_page_builder_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_builder_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'instructions'  => __( 'Hero background image. Used at compact height (480px).', 'ibv' ),
				),
				array(
					'key'          => 'field_ibv_page_builder_hero_title',
					'label'        => __( 'Hero title', 'ibv' ),
					'name'         => 'hero_title',
					'type'         => 'text',
					'instructions' => __( 'The page heading. Leave empty for an image-only hero.', 'ibv' ),
				),
				array(
					'key'   => 'field_ibv_page_builder_hero_subtitle',
					'label' => __( 'Hero subtitle', 'ibv' ),
					'name'  => 'hero_subtitle',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'       => 'field_ibv_page_builder_tab_sections',
					'label'     => __( 'Sections', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'          => 'field_ibv_page_builder_sections',
					'label'        => __( 'Sections', 'ibv' ),
					'name'         => 'builder_sections',
					'type'         => 'flexible_content',
					'button_label' => __( 'Add section', 'ibv' ),
					'instructions' => __( 'Sections render top to bottom, below the hero. Drag to reorder. An empty list is fine — the page is then hero-only.', 'ibv' ),
					'layouts'      => $layouts,
				),
			),
			'location'              => $loc_builder,
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

add_action( 'acf/init', 'ibv_register_page_builder_fields', 15 );

/**
 * Append the section's own title to its collapsed label in the builder.
 *
 * Without this a stack of collapsed rows reads "Image + entries / Image +
 * entries / Text block" and the editor has to open each one to find anything.
 *
 * @param string $title  Default layout title (HTML).
 * @param array  $field  Parent flexible content field.
 * @param array  $layout Layout settings.
 * @param int    $i      Row index.
 * @return string
 */
function ibv_page_builder_layout_title( $title, $field, $layout, $i ) {
	unset( $field, $layout, $i );

	$summary = '';

	// Whichever of these the layout happens to have; layouts with neither keep
	// the bare label.
	foreach ( array( 'title', 'heading_override' ) as $name ) {
		$value = get_sub_field( $name );
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$summary = trim( $value );
			break;
		}
	}

	if ( '' === $summary ) {
		return $title;
	}

	return $title . ' — <span style="font-weight:400">' . esc_html( wp_trim_words( $summary, 8, '…' ) ) . '</span>';
}

add_filter( 'acf/fields/flexible_content/layout_title/name=builder_sections', 'ibv_page_builder_layout_title', 10, 4 );
