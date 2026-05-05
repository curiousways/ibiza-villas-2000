<?php
/**
 * Ibiza Villas 2000 Core — editor preferences.
 *
 * The site uses the Classic editor everywhere. CPTs registered by this
 * mu-plugin should opt out via `show_in_rest => false`; this module
 * extends the same policy to the core `post` and `page` types and the
 * widgets screen so the experience is consistent across the admin.
 *
 * If a future brief needs the block editor for a specific post type,
 * narrow `ibv_disable_block_editor()` rather than dropping the filter
 * altogether.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'use_block_editor_for_post_type', 'ibv_disable_block_editor', 100 );
function ibv_disable_block_editor() {
	return false;
}

// Use the classic widgets screen so the admin chrome matches.
add_filter( 'use_widgets_block_editor', '__return_false' );

/**
 * The Classic editor never produces block markup, so the block-library
 * stylesheets are dead weight on every front-end request. Strip them.
 */
add_action( 'wp_enqueue_scripts', 'ibv_dequeue_block_assets', 100 );
function ibv_dequeue_block_assets() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}

/**
 * Trim the Classic editor toolbar to the prose feature set.
 *
 * Removes the four alignment buttons, the Read More tag, and the
 * "Toggle toolbar" (kitchen-sink) button from row 1; empties row 2
 * entirely so the secondary controls (font colour, hr, paste-as-text,
 * indent/outdent, etc.) cannot reappear if a user previously toggled
 * them on. The article-shell design uses a fixed reading column, and
 * the prose CSS doesn't style any of these surfaces.
 */
add_filter( 'mce_buttons', 'ibv_clean_mce_buttons' );
function ibv_clean_mce_buttons( $buttons ) {
	return array_values(
		array_diff(
			(array) $buttons,
			[ 'alignleft', 'aligncenter', 'alignright', 'alignjustify', 'wp_more', 'wp_adv' ]
		)
	);
}
add_filter( 'mce_buttons_2', '__return_empty_array' );

// Defence in depth: strip `align*` classes from images inserted via the
// media modal, in case a saved preference or older option still applies one.
add_filter( 'image_send_to_editor', 'ibv_strip_align_from_inserted_image' );
function ibv_strip_align_from_inserted_image( $html ) {
	return preg_replace( '/\balign(?:left|right|center|none)\b\s*/', '', $html );
}

/**
 * Hide alignment surfaces that aren't reachable via the toolbar filters:
 *  - the Alignment dropdown in the media-modal attachment sidebar
 *  - the alignment buttons on the floating toolbar over a selected image
 *
 * Known leak: the Image Details modal (pencil icon on a selected image)
 * still exposes an Align field — it's rendered by the `wpeditimage`
 * TinyMCE plugin's WindowManager and doesn't share the icon classes
 * targeted below. Acceptable trade-off; the strip of `align*` classes
 * on save (`image_send_to_editor`, above) is the second line of defence
 * for first inserts. Editors who change alignment via the pencil dialog
 * after insertion will still produce an `align*` class in saved content.
 */
add_action( 'admin_head', 'ibv_admin_hide_alignment_ui' );
function ibv_admin_hide_alignment_ui() {
	echo '<style>'
		. '.media-frame .setting[data-setting="align"]{display:none}'
		. '.mce-toolbar .mce-btn:has(.mce-i-alignleft),'
		. '.mce-toolbar .mce-btn:has(.mce-i-aligncenter),'
		. '.mce-toolbar .mce-btn:has(.mce-i-alignright),'
		. '.mce-toolbar .mce-btn:has(.mce-i-alignnone){display:none}'
		. '</style>';
}
