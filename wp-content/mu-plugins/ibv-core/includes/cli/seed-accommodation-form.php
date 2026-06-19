<?php
/**
 * One-off seeder — "Accommodation Enquiry" Gravity Form (Hotel + Airstream).
 *
 * This form IS rendered (embedded via `ibv_core_gravity_form()`), so GF owns
 * submit / validation / notification. The accommodation-enquiry section grafts
 * our single-field "When" date-range picker onto it: the form carries an HTML
 * field (the visible "When" pill) plus two CSS-classed date fields
 * (`ibv-drp-from` / `ibv-drp-to`) which are visually hidden and written to by
 * `accommodation-enquiry.js`. No PHP gform_* filters needed — the JS keys off
 * those classes.
 *
 * Field ids are explicit + stable. Idempotent (skips if the title exists).
 * Stores the id in `ibv_accommodation_enquiry_form_id` (convenience default;
 * the page ACF `accommodation_enquiry_gravity_form_id` is the per-page source).
 *
 * Run (Local Site Shell):  wp eval-file wp-content/mu-plugins/ibv-core/includes/cli/seed-accommodation-form.php
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GFAPI' ) ) {
	echo "GFAPI unavailable — is Gravity Forms active?\n";
	return;
}

$title = 'Accommodation Enquiry';

foreach ( GFAPI::get_forms() as $existing ) {
	if ( isset( $existing['title'] ) && $existing['title'] === $title ) {
		update_option( 'ibv_accommodation_enquiry_form_id', (int) $existing['id'] );
		echo 'Already exists — Accommodation Enquiry form id: ' . (int) $existing['id'] . "\n";
		return;
	}
}

// Visible "When" pill markup (single-field range trigger). Hidden GF date
// fields below hold the actual values; accommodation-enquiry.js wires them.
$when_html = '<div class="ibv-accommodation-enquiry__when" data-bob-date-range-anchor>'
	. '<button type="button" class="ibv-accommodation-enquiry__when-trigger" data-bob-date-range-trigger>'
	. '<span class="ibv-accommodation-enquiry__when-value is-empty" data-bob-date-range-display data-placeholder="When">When</span>'
	. '</button>'
	. '<button type="button" class="ibv-accommodation-enquiry__when-clear" data-bob-date-range-clear hidden aria-label="Clear dates"><span aria-hidden="true">&times;</span></button>'
	. '</div>';

$form = array(
	'title'          => $title,
	'description'    => '',
	'labelPlacement' => 'hidden_label',
	'enableHoneypot' => true,
	'button'         => array(
		'type' => 'text',
		'text' => 'Send Enquiry',
	),
	'fields'         => array(
		array( 'id' => 1, 'type' => 'text',     'label' => 'Name',  'placeholder' => 'Name*',  'isRequired' => true ),
		array( 'id' => 2, 'type' => 'email',    'label' => 'Email', 'placeholder' => 'Email*', 'isRequired' => true ),
		array( 'id' => 3, 'type' => 'phone',    'label' => 'Phone', 'placeholder' => 'Phone*', 'phoneFormat' => 'international', 'isRequired' => true ),
		// Date-range picker graft — REQUIRED by accommodation-enquiry.js: the
		// HTML "When" field (#8) is the visible trigger; the two date fields
		// (#4/#5) MUST keep the ibv-drp-from / ibv-drp-to classes (the JS binds
		// to `.ibv-drp-from input` / `.ibv-drp-to input`). Removing/renaming
		// them silently disables the picker. They are intentionally optional.
		array( 'id' => 8, 'type' => 'html',      'label' => 'Dates', 'content' => $when_html ),
		array( 'id' => 4, 'type' => 'date',      'label' => 'Arrival',   'dateType' => 'datefield', 'dateFormat' => 'ymd_dash', 'isRequired' => false, 'cssClass' => 'ibv-drp-from ibv-drp-hidden' ),
		array( 'id' => 5, 'type' => 'date',      'label' => 'Departure', 'dateType' => 'datefield', 'dateFormat' => 'ymd_dash', 'isRequired' => false, 'cssClass' => 'ibv-drp-to ibv-drp-hidden' ),
		array( 'id' => 6, 'type' => 'number',    'label' => 'Number of guests', 'placeholder' => 'Number of guests (optional)' ),
		array( 'id' => 7, 'type' => 'textarea',  'label' => 'Message', 'placeholder' => 'Message' ),
		array( 'id' => 9, 'type' => 'hidden',    'label' => 'Accommodation', 'allowsPrepopulate' => true, 'inputName' => 'ibv_accommodation' ),
	),
	'notifications'  => array(
		uniqid( 'ibv', true ) => array(
			'id'       => uniqid( 'ibv', true ),
			'isActive' => true,
			'name'     => 'Admin Notification',
			'event'    => 'form_submission',
			'toType'   => 'email',
			'to'       => 'bookings@ibizavillas2000.com',
			'replyTo'  => '{Email:2}',
			'subject'  => 'Accommodation Enquiry - {Name:1}',
			'message'  => '{all_fields}',
		),
	),
);

$result = GFAPI::add_form( $form );

if ( is_wp_error( $result ) ) {
	echo 'Error creating Accommodation Enquiry form: ' . $result->get_error_message() . "\n";
	return;
}

update_option( 'ibv_accommodation_enquiry_form_id', (int) $result );
echo 'Created Accommodation Enquiry form id: ' . (int) $result . "\n";
