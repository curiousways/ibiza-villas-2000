<?php
/**
 * Villa description editor tweaks.
 *
 * The native editor stays in its default position below the "Villa
 * details" ACF box. We previously relocated it into the Content tab,
 * but TinyMCE initialising inside a hidden tab panel misbehaves
 * (miscomputed iframe height, glitchy toolbar), and post_content needs
 * to stay post_content — Yoast analysis, native search, revisions and
 * the_content rendering all depend on it. A message field on the
 * Content tab points editors below instead.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// No "Add Media" on the villa description: images belong in the Media
// tab's gallery, not uploaded ad hoc into the_content.
add_filter(
	'wp_editor_settings',
	function ( $settings, $editor_id ) {
		if ( 'content' === $editor_id && 'villas' === get_post_type() ) {
			$settings['media_buttons'] = false;
		}
		return $settings;
	},
	10,
	2
);
