<?php
/**
 * Component: Date range picker (Vanilla Calendar Pro wrapper).
 *
 * Self-hosted dual-calendar range picker. The component renders no markup —
 * its only job is to enqueue the vendored library, the brand theme override,
 * and the init script. Consumers opt-in by adding `data-bob-date-range` to
 * their form and `data-bob-date-from` / `data-bob-date-to` to the paired
 * inputs (which must keep `name="date_from"` / `name="date_to"` for the API
 * contract). The init script finds them on DOMContentLoaded and binds one
 * Calendar instance per form.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the picker's CSS + JS. Call from any component that wraps a
 * `data-bob-date-range` form.
 */
function ibv_core_date_range_picker_enqueue() {
	wp_enqueue_style( 'ibv-vanilla-calendar-pro' );
	wp_enqueue_style( 'ibv-date-range-picker' );
	wp_enqueue_script( 'ibv-vanilla-calendar-pro' );
	wp_enqueue_script( 'ibv-date-range-picker' );
}
