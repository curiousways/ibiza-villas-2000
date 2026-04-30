<?php
/**
 * Front Page (homepage) ACF field group — page-level fields.
 *
 * Targets whichever page is set as the Front Page in Settings → Reading
 * (`page_type == front_page`). We use that rule instead of `page_template`
 * because WordPress selects `front-page.php` from that setting — no separate
 * “home” template slug to maintain.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Page → Home field group.
 */
function ibv_register_page_home_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc_front_page = array(
		array(
			array(
				'param'    => 'page_type',
				'operator' => '==',
				'value'    => 'front_page',
			),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ibv_page_home',
			'title'                 => __( 'Homepage content', 'ibv' ),
			'fields'                => array(
				array(
					'key'       => 'field_ibv_page_home_tab_hero',
					'label'     => __( 'Hero', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_home_hero_image',
					'label'         => __( 'Hero image', 'ibv' ),
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_page_home_hero_title',
					'label' => __( 'Hero title', 'ibv' ),
					'name'  => 'hero_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_hero_subtitle',
					'label' => __( 'Hero subtitle', 'ibv' ),
					'name'  => 'hero_subtitle',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_featured_villas',
					'label'     => __( 'Featured villas', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_home_featured_villas',
					'label'         => __( 'Featured villas', 'ibv' ),
					'name'          => 'featured_villas',
					'type'          => 'post_object',
					'post_type'     => array( 'villas' ),
					'multiple'      => true,
					'min'           => 0,
					'max'           => 8,
					'return_format' => 'id',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_trust_strip',
					'label'     => __( 'Trust strip', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'        => 'field_ibv_page_home_trust_strip',
					'label'      => __( 'Trust strip', 'ibv' ),
					'name'       => 'trust_strip',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'           => 'field_ibv_page_home_trust_logo',
							'label'         => __( 'Logo', 'ibv' ),
							'name'          => 'logo',
							'type'          => 'image',
							'return_format' => 'array',
						),
						array(
							'key'   => 'field_ibv_page_home_trust_label',
							'label' => __( 'Label', 'ibv' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_page_home_trust_subtext',
							'label' => __( 'Subtext', 'ibv' ),
							'name'  => 'subtext',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_page_home_trust_url',
							'label' => __( 'URL (optional)', 'ibv' ),
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),
				array(
					'key'       => 'field_ibv_page_home_tab_weekly_offer',
					'label'     => __( 'Weekly offer', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_home_weekly_offer_villa',
					'label'         => __( 'Weekly offer villa', 'ibv' ),
					'name'          => 'weekly_offer_villa',
					'type'          => 'post_object',
					'post_type'     => array( 'villas' ),
					'return_format' => 'id',
					'multiple'      => 0,
				),
				array(
					'key'   => 'field_ibv_page_home_weekly_offer_was_price',
					'label' => __( 'Weekly offer “was” price (€)', 'ibv' ),
					'name'  => 'weekly_offer_was_price',
					'type'  => 'number',
				),
				array(
					'key'   => 'field_ibv_page_home_weekly_offer_now_price',
					'label' => __( 'Weekly offer “now” price (€)', 'ibv' ),
					'name'  => 'weekly_offer_now_price',
					'type'  => 'number',
				),
				array(
					'key'            => 'field_ibv_page_home_weekly_offer_valid_from',
					'label'          => __( 'Weekly offer valid from', 'ibv' ),
					'name'           => 'weekly_offer_valid_from',
					'type'           => 'date_picker',
					'display_format' => 'd/m/Y',
					'return_format'  => 'Ymd',
				),
				array(
					'key'            => 'field_ibv_page_home_weekly_offer_valid_to',
					'label'          => __( 'Weekly offer valid to', 'ibv' ),
					'name'           => 'weekly_offer_valid_to',
					'type'           => 'date_picker',
					'display_format' => 'd/m/Y',
					'return_format'  => 'Ymd',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_short_breaks',
					'label'     => __( 'Short breaks', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_home_short_breaks_image',
					'label'         => __( 'Short breaks image', 'ibv' ),
					'name'          => 'short_breaks_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_page_home_short_breaks_title',
					'label' => __( 'Short breaks title', 'ibv' ),
					'name'  => 'short_breaks_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_short_breaks_text',
					'label' => __( 'Short breaks text', 'ibv' ),
					'name'  => 'short_breaks_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_page_home_short_breaks_cta_url',
					'label' => __( 'Short breaks CTA URL', 'ibv' ),
					'name'  => 'short_breaks_cta_url',
					'type'  => 'url',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_why_iv2000',
					'label'     => __( 'Why IV2000', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'        => 'field_ibv_page_home_why_pillars',
					'label'      => __( 'Why IV2000 pillars', 'ibv' ),
					'name'       => 'why_pillars',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'           => 'field_ibv_page_home_why_icon',
							'label'         => __( 'Icon', 'ibv' ),
							'name'          => 'icon',
							'type'          => 'image',
							'return_format' => 'array',
						),
						array(
							'key'   => 'field_ibv_page_home_why_title',
							'label' => __( 'Title', 'ibv' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_page_home_why_text',
							'label' => __( 'Text', 'ibv' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'key'       => 'field_ibv_page_home_tab_fancy_different',
					'label'     => __( 'Fancy different (intro)', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'   => 'field_ibv_page_home_fancy_different_intro',
					'label' => __( 'Fancy different intro', 'ibv' ),
					'name'  => 'fancy_different_intro',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_ips',
					'label'     => __( 'IPS panel', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_home_ips_image',
					'label'         => __( 'IPS panel image', 'ibv' ),
					'name'          => 'ips_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_page_home_ips_title',
					'label' => __( 'IPS title', 'ibv' ),
					'name'  => 'ips_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_ips_text',
					'label' => __( 'IPS text', 'ibv' ),
					'name'  => 'ips_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_page_home_ips_cta_label',
					'label' => __( 'IPS CTA label', 'ibv' ),
					'name'  => 'ips_cta_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_ips_cta_url',
					'label' => __( 'IPS CTA URL', 'ibv' ),
					'name'  => 'ips_cta_url',
					'type'  => 'url',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_three_step',
					'label'     => __( 'Three steps', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'   => 'field_ibv_page_home_three_step_intro_eyebrow',
					'label' => __( '3-step eyebrow', 'ibv' ),
					'name'  => 'three_step_intro_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_three_step_intro_title',
					'label' => __( '3-step title', 'ibv' ),
					'name'  => 'three_step_intro_title',
					'type'  => 'text',
				),
				array(
					'key'        => 'field_ibv_page_home_three_step_steps',
					'label'      => __( '3-step cards', 'ibv' ),
					'name'       => 'three_step_steps',
					'type'       => 'repeater',
					'layout'     => 'block',
					'sub_fields' => array(
						array(
							'key'   => 'field_ibv_page_home_three_step_card_title',
							'label' => __( 'Title', 'ibv' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_ibv_page_home_three_step_card_text',
							'label' => __( 'Text', 'ibv' ),
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'key'       => 'field_ibv_page_home_tab_guide',
					'label'     => __( 'Ibiza guide', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'   => 'field_ibv_page_home_guide_intro',
					'label' => __( 'Ibiza guide intro', 'ibv' ),
					'name'  => 'guide_intro',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_ibv_page_home_guide_articles',
					'label'         => __( 'Guide articles', 'ibv' ),
					'name'          => 'guide_articles',
					'type'          => 'post_object',
					'post_type'     => array( 'post' ),
					'multiple'      => true,
					'min'           => 0,
					'max'           => 6,
					'return_format' => 'id',
				),
				array(
					'key'   => 'field_ibv_page_home_guide_view_all_url',
					'label' => __( 'Guide view-all link', 'ibv' ),
					'name'  => 'guide_view_all_url',
					'type'  => 'page_link',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_meet_team',
					'label'     => __( 'Meet the team', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'           => 'field_ibv_page_home_meet_team_image',
					'label'         => __( 'Meet the team image', 'ibv' ),
					'name'          => 'meet_team_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'   => 'field_ibv_page_home_meet_team_title',
					'label' => __( 'Meet the team title', 'ibv' ),
					'name'  => 'meet_team_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_meet_team_text',
					'label' => __( 'Meet the team text', 'ibv' ),
					'name'  => 'meet_team_text',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => 'field_ibv_page_home_meet_team_cta_label',
					'label' => __( 'Meet the team CTA label', 'ibv' ),
					'name'  => 'meet_team_cta_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_meet_team_cta_url',
					'label' => __( 'Meet the team CTA page', 'ibv' ),
					'name'  => 'meet_team_cta_url',
					'type'  => 'page_link',
				),
				array(
					'key'       => 'field_ibv_page_home_tab_newsletter',
					'label'     => __( 'Newsletter (footer)', 'ibv' ),
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'   => 'field_ibv_page_home_newsletter_intro',
					'label' => __( 'Newsletter intro', 'ibv' ),
					'name'  => 'newsletter_intro',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_ibv_page_home_newsletter_form_id',
					'label' => __( 'Newsletter Gravity Form ID', 'ibv' ),
					'name'  => 'newsletter_form_id',
					'type'  => 'number',
					'min'   => 0,
				),
			),
			'location'              => $loc_front_page,
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}

add_action( 'acf/init', 'ibv_register_page_home_fields', 16 );
