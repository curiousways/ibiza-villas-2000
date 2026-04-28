<?php
/**
 * Villa ACF field groups — PHP registration (`acf_add_local_field_group`).
 *
 * HALT — source JSON was not present in this repository workspace
 * (`themes/ibiza-villas-2000/acf-json/`). The audit notes field groups live in
 * the WP database / admin sync workflow; no `group_*.json` exports are versioned
 * here yet.
 *
 * Until JSON exports exist in git, this callback intentionally registers **no**
 * groups. Villa field data continues to resolve via definitions already stored in
 * the database (`acf-post` entities). Passing 3b pre-flight requires:
 * WP Admin → ACF → Field Groups → Sync (or Export) → commit JSON under
 * `themes/ibiza-villas-2000/acf-json/` (or paste here), then convert each blob to
 * `acf_add_local_field_group()` **without renaming any keys** (`field_*`,
 * `group_*`).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers villa-related ACF local field groups once JSON conversion is merged.
 *
 * Hooks at priority 15 so mu-plugin baseline Site Options (register-options.php)
 * can run first when both use `acf/init`.
 */
function ibv_register_villa_acf_fields() {

	/*
	Example (after JSON exists):
	foreach ( ibv_core_get_villa_field_group_arrays() as $group ) {
		acf_add_local_field_group( $group );
	}
	*/
}

add_action( 'acf/init', 'ibv_register_villa_acf_fields', 15 );
