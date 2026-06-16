<?php
/**
 * Gravity Forms — populate the concierge enquiry Service dropdown.
 *
 * Any GF dropdown carrying the CSS class `ibv-gf-concierge-service`
 * (set in the field's Appearance settings in GF admin) has its
 * choices replaced with the Concierge page's `concierge_services`
 * repeater titles. Single source of truth — when the services list
 * changes on the page, the dropdown follows automatically; no
 * editorial sync step.
 *
 * Hooks all three GF render filters so the choices stay correct in
 * front-end render, validation pass, and the GF admin form editor.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Populate any field carrying the `ibv-gf-concierge-service` class
 * with the Concierge page's services list.
 *
 * @param array $form GF form array.
 * @return array
 */
function ibv_gf_populate_concierge_service_dropdown( $form ) {
	if ( ! is_array( $form ) || empty( $form['fields'] ) ) {
		return $form;
	}

	$choices = ibv_gf_concierge_service_choices();
	if ( empty( $choices ) ) {
		return $form;
	}

	foreach ( $form['fields'] as $field ) {
		$class = isset( $field->cssClass ) ? (string) $field->cssClass : '';
		if ( false === strpos( $class, 'ibv-gf-concierge-service' ) ) {
			continue;
		}
		$field->choices       = $choices;
		$field->enableChoiceValue = true;
	}

	return $form;
}

add_filter( 'gform_pre_render', 'ibv_gf_populate_concierge_service_dropdown' );
add_filter( 'gform_pre_validation', 'ibv_gf_populate_concierge_service_dropdown' );
add_filter( 'gform_admin_pre_render', 'ibv_gf_populate_concierge_service_dropdown' );
add_filter( 'gform_pre_submission_filter', 'ibv_gf_populate_concierge_service_dropdown' );

/**
 * Build the GF choices array from the Concierge page's
 * `concierge_services` repeater.
 *
 * Cached per request (the GF filters fire multiple times per render).
 *
 * @return array[] Each row: `[ 'text' => string, 'value' => string ]`.
 */
function ibv_gf_concierge_service_choices() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}

	$cached = array();

	if ( ! function_exists( 'get_field' ) || ! function_exists( 'ibv_get_concierge_page_id' ) ) {
		return $cached;
	}

	$page_id = ibv_get_concierge_page_id();
	if ( ! $page_id ) {
		return $cached;
	}

	$services = get_field( 'concierge_services', $page_id );
	if ( ! is_array( $services ) ) {
		return $cached;
	}

	foreach ( $services as $service ) {
		$title = isset( $service['title'] ) ? trim( (string) $service['title'] ) : '';
		if ( '' === $title ) {
			continue;
		}
		$cached[] = array(
			'text'  => $title,
			'value' => $title,
		);
	}

	return $cached;
}
