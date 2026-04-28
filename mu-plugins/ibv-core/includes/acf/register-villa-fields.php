<?php
/**
 * Villa ACF field groups — PHP registration (`acf_add_local_field_group`).
 *
 * HALT — synced JSON exports were not in this repo snapshot (see Pass 3b brief).
 * Field definitions currently live only in the WordPress database until exports are
 * added and mechanically converted below. Every `acf_add_local_field_group()` call
 * must preserve JSON keys (`field_*`, `group_*`) exactly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers villa-related ACF local field groups after JSON conversions land.
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
