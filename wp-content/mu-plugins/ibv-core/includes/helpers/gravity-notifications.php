<?php
/**
 * Gravity Forms notifications — test reroute, merge-tag aliases, C1 type.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Optional override address for GF notifications.
 *
 * Off unless option `ibv_gf_notification_override` is a valid email.
 * Host is not used — staging and local send to the form To field.
 *
 * @return string Email or empty.
 */
function ibv_gf_notification_override_address() {
	$option = trim( (string) get_option( 'ibv_gf_notification_override', '' ) );
	if ( $option && is_email( $option ) ) {
		return $option;
	}

	return '';
}

/**
 * Whether this request should keep client inboxes and CRM feeds quiet.
 *
 * @return bool
 */
function ibv_gf_should_reroute_notifications() {
	return (bool) ibv_gf_notification_override_address();
}

/**
 * Send every GF notification to the override address while testing.
 *
 * Guest "send to field" notifications are rewritten to a plain email
 * address so a tester's address (or bookings@) never receives a copy.
 * The original To is prefixed onto the subject so we can see where it
 * would have gone.
 *
 * @param array $notification Notification.
 * @param array $form         Form.
 * @param array $entry        Entry.
 * @return array
 */
function ibv_gf_reroute_notification( $notification, $form, $entry ) {
	unset( $form, $entry );
	$to = ibv_gf_notification_override_address();
	if ( ! $to ) {
		return $notification;
	}

	$original = isset( $notification['to'] ) ? (string) $notification['to'] : '';
	$notification['toType'] = 'email';
	$notification['to']     = $to;
	unset( $notification['toField'] );

	$subject = isset( $notification['subject'] ) ? (string) $notification['subject'] : '';
	$notification['subject'] = '[TEST → ' . $to . ', was ' . $original . '] ' . $subject;

	return $notification;
}
add_filter( 'gform_notification', 'ibv_gf_reroute_notification', 10, 3 );

/**
 * Hold Zoho / Campaign Monitor / other add-on feeds while an override is set.
 *
 * @param array  $feeds Feeds about to run.
 * @param array  $entry Entry.
 * @param array  $form  Form.
 * @param object $addon Add-on instance.
 * @return array
 */
function ibv_gf_hold_addon_feeds( $feeds, $entry, $form, $addon ) {
	unset( $entry, $form, $addon );
	if ( ! ibv_gf_should_reroute_notifications() ) {
		return $feeds;
	}
	return [];
}
add_filter( 'gform_addon_pre_process_feeds', 'ibv_gf_hold_addon_feeds', 10, 4 );

/**
 * Admin banner while the override is on.
 */
function ibv_gf_notification_override_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$to = ibv_gf_notification_override_address();
	if ( ! $to ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html(
		sprintf(
			/* translators: %s: override email address */
			__( 'Form emails are being redirected to %s and CRM/newsletter feeds are held. Clear the option ibv_gf_notification_override to restore the form To addresses.', 'ibv' ),
			$to
		)
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'ibv_gf_notification_override_notice' );

/**
 * Villa Enquiry field id for the C1 submission-type tag.
 */
const IBV_GF_SUBMISSION_TYPE_FIELD_ID = 15;

/**
 * Allowed values for the C1 subject-line tag.
 *
 * @return string[]
 */
function ibv_gf_submission_type_values() {
	return [
		'Request to Book',
		'Enquiry',
	];
}

/**
 * Add the hidden Submission type field if the villa form does not have it.
 *
 * Updates the stored form once. Notifications and other fields are left
 * as they are.
 */
function ibv_gf_ensure_submission_type_field() {
	if ( get_option( 'ibv_gf_submission_type_field' ) === (string) IBV_GF_SUBMISSION_TYPE_FIELD_ID ) {
		return;
	}
	if ( ! class_exists( 'GFAPI' ) ) {
		return;
	}

	$form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
	if ( ! $form_id ) {
		return;
	}

	$form = GFAPI::get_form( $form_id );
	if ( ! $form || empty( $form['fields'] ) ) {
		return;
	}

	foreach ( $form['fields'] as $field ) {
		if ( (int) $field->id === IBV_GF_SUBMISSION_TYPE_FIELD_ID ) {
			update_option( 'ibv_gf_submission_type_field', (string) IBV_GF_SUBMISSION_TYPE_FIELD_ID, false );
			return;
		}
	}

	$form['fields'][] = new GF_Field_Hidden(
		[
			'id'    => IBV_GF_SUBMISSION_TYPE_FIELD_ID,
			'label' => 'Submission type',
		]
	);

	$result = GFAPI::update_form( $form, $form_id );
	if ( ! is_wp_error( $result ) ) {
		update_option( 'ibv_gf_submission_type_field', (string) IBV_GF_SUBMISSION_TYPE_FIELD_ID, false );
	}
}
add_action( 'init', 'ibv_gf_ensure_submission_type_field', 30 );

/**
 * Keep the posted submission type to the two allowed labels.
 *
 * @param array $form Form.
 * @return array
 */
function ibv_gf_sanitize_submission_type( $form ) {
	$villa_form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
	if ( ! $villa_form_id || (int) $form['id'] !== $villa_form_id ) {
		return $form;
	}

	$key   = 'input_' . IBV_GF_SUBMISSION_TYPE_FIELD_ID;
	$raw   = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- GF owns the submission nonce.
	$allow = ibv_gf_submission_type_values();
	$_POST[ $key ] = in_array( $raw, $allow, true ) ? $raw : 'Enquiry';

	return $form;
}
add_filter( 'gform_pre_submission_filter', 'ibv_gf_sanitize_submission_type' );

/**
 * Resolve the label-only tags Luke used in the guest confirmation.
 *
 * Gravity Forms needs `{Label:id}`. `{Villa}` and `{First Name}` do not
 * match any field (`Property Name`, `Name`). `{Guests}` does not match
 * `Number of guests`. `{Arrival}` / `{Departure}` match by label, but
 * GF 3 has been leaving those literal as well — pin them to field ids.
 *
 * @param string $text      Text still containing merge tags.
 * @param array  $form      Form.
 * @param array  $entry     Entry.
 * @param bool   $url_encode Unused.
 * @param bool   $esc_html  Unused.
 * @param bool   $nl2br     Unused.
 * @param string $format    Unused.
 * @return string
 */
function ibv_gf_alias_enquiry_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
	unset( $url_encode, $esc_html, $nl2br, $format );
	if ( ! is_string( $text ) || '' === $text || ! is_array( $form ) || ! is_array( $entry ) ) {
		return $text;
	}

	$villa_form_id = (int) get_option( 'ibv_villa_enquiry_form_id' );
	if ( ! $villa_form_id || (int) $form['id'] !== $villa_form_id ) {
		return $text;
	}

	$aliases = [
		'{Villa}'             => rgar( $entry, '1' ),
		'{First Name}'        => rgar( $entry, '2' ),
		'{Name}'              => rgar( $entry, '2' ),
		'{Guests}'            => rgar( $entry, '7' ),
		'{Arrival}'           => rgar( $entry, '5' ),
		'{Departure}'         => rgar( $entry, '6' ),
		'{Submission type}'   => rgar( $entry, (string) IBV_GF_SUBMISSION_TYPE_FIELD_ID ),
		'{Property Name}'     => rgar( $entry, '1' ),
		'{Number of guests}'  => rgar( $entry, '7' ),
	];

	return str_replace( array_keys( $aliases ), array_values( $aliases ), $text );
}
add_filter( 'gform_replace_merge_tags', 'ibv_gf_alias_enquiry_merge_tags', 11, 7 );
