<?php
/**
 * Editor role: Site Options yes, Appearance / menus no.
 *
 * The retired theme granted Editors `edit_theme_options` so they could
 * reach Appearance → Menus. That write lives on the role in the database
 * and survived the switch to `ibv`. Strip it so Editors cannot edit menus
 * (or Customize / Widgets / Themes). Site Options is opened to Editors
 * separately via `edit_pages` on the ACF options pages.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'ibv_revoke_editor_theme_options' );
function ibv_revoke_editor_theme_options() {
	$role = get_role( 'editor' );
	if ( $role && $role->has_cap( 'edit_theme_options' ) ) {
		$role->remove_cap( 'edit_theme_options' );
	}
}
