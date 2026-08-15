<?php
/**
 * Move the native post editor into the villa field group's Content tab.
 *
 * The Villa field group sits acf_after_title, which leaves the classic
 * editor (#postdivrich) dangling below the whole box. A message field
 * (#ibv-villa-editor-slot) under the Content tab marks where it belongs;
 * this script relocates the editor into that slot.
 *
 * Printed at admin_print_footer_scripts priority 1 so the move happens
 * before TinyMCE initialises (_WP_Editors::editor_js runs at 50) —
 * moving an initialised TinyMCE iframe wipes its content in some
 * browsers. ACF's tab show/hide then applies to the editor for free,
 * since it lives inside the tab's field wrapper. If JS is off the
 * editor simply stays below the box.
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

add_action(
	'admin_print_footer_scripts',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'villas' !== $screen->post_type || 'post' !== $screen->base ) {
			return;
		}
		?>
		<script>
			( function () {
				var slot   = document.querySelector( '#ibv-villa-editor-slot .acf-input' );
				var editor = document.getElementById( 'postdivrich' );
				if ( slot && editor ) {
					slot.appendChild( editor );
				}
			} )();
		</script>
		<?php
	},
	1
);
