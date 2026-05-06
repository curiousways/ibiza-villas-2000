<?php
/**
 * Front page (homepage).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

ibv_core_section_hero(
	[
		'after_copy' => 'ibv_core_hero_search',
	]
);
ibv_core_section_trust_strip();
ibv_core_section_featured_villas();
ibv_core_section_featured_offer();
ibv_core_section_short_breaks();
ibv_core_section_why_iv2000();
ibv_core_section_fancy_different();
ibv_core_section_ips_panel();
ibv_core_section_three_step();
ibv_core_section_ibiza_guide_preview();
ibv_core_section_meet_team_teaser();
ibv_core_section_testimonials();

get_footer();
