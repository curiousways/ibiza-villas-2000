<?php
/**
 * Editor role: Site Options yes, Appearance / menus no;
 * Gravity Forms entries yes, form building / settings no.
 *
 * The retired theme granted Editors `edit_theme_options` so they could
 * reach Appearance → Menus. That write lives on the role in the database
 * and survived the switch to `ibv`. Strip it so Editors cannot edit menus
 * (or Customize / Widgets / Themes). Site Options is opened to Editors
 * separately via `edit_pages` on the ACF options pages.
 *
 * Gravity Forms entry caps are granted at runtime via `user_has_cap` so
 * they are not written into `wp_user_roles`. Any leftover `gform_full_access`
 * (or form-building caps) on the Editor role in the database is denied here
 * so client Editors only get Entries / notes / export.
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

/**
 * Entry-related Gravity Forms capabilities granted to Editors.
 *
 * @return string[]
 */
function ibv_editor_gravity_forms_grant_caps() {
	return array(
		'gravityforms_view_entries',
		'gravityforms_edit_entries',
		'gravityforms_delete_entries',
		'gravityforms_view_entry_notes',
		'gravityforms_edit_entry_notes',
		'gravityforms_export_entries',
	);
}

/**
 * Gravity Forms capabilities Editors must never receive.
 *
 * Includes `gform_full_access` in case an old role write left it on Editor.
 *
 * @return string[]
 */
function ibv_editor_gravity_forms_deny_caps() {
	return array(
		'gform_full_access',
		'gravityforms_create_form',
		'gravityforms_edit_forms',
		'gravityforms_delete_forms',
		'gravityforms_preview_forms',
		'gravityforms_view_settings',
		'gravityforms_edit_settings',
		'gravityforms_uninstall',
		'gravityforms_view_addons',
		'gravityforms_view_updates',
		'gravityforms_system_status',
		'gravityforms_logging',
		'gravityforms_api_settings',
	);
}

/**
 * Grant Editors GF entry caps; deny form building / settings / full access.
 *
 * Runs after Gravity Forms' own `user_has_cap` callback (priority 10).
 *
 * @param bool[]   $allcaps Caps the user currently has.
 * @param string[] $caps    Caps being checked.
 * @param array    $args    Arguments passed to current_user_can().
 * @param WP_User  $user    User object (WP 3.7+).
 * @return bool[]
 */
function ibv_editor_gravity_forms_caps( $allcaps, $caps, $args, $user = null ) {
	if ( ! $user instanceof WP_User ) {
		return $allcaps;
	}

	$roles = (array) $user->roles;
	if ( in_array( 'administrator', $roles, true ) || ! in_array( 'editor', $roles, true ) ) {
		return $allcaps;
	}

	foreach ( ibv_editor_gravity_forms_deny_caps() as $cap ) {
		unset( $allcaps[ $cap ] );
	}

	foreach ( ibv_editor_gravity_forms_grant_caps() as $cap ) {
		$allcaps[ $cap ] = true;
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'ibv_editor_gravity_forms_caps', 20, 4 );
