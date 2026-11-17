<?php
/**
 * Form health — attempt log, daily alerts, health endpoint.
 *
 * Logs Gravity Forms attempts without personal data. Alerts
 * support@curiousways.com when submissions look broken.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Alert recipient. */
const IBV_FORM_HEALTH_ALERT_EMAIL = 'support@curiousways.com';

/** Schema version for dbDelta upgrades. */
const IBV_FORM_HEALTH_DB_VERSION = 1;

/** Days to retain event rows. */
const IBV_FORM_HEALTH_RETENTION_DAYS = 90;

/** Browser REST: max events per salted IP hash per hour. */
const IBV_FORM_HEALTH_RATE_LIMIT = 60;

/** Lost submissions: minimum count before alert. */
const IBV_FORM_HEALTH_LOST_MIN = 3;

/** Lost submissions: share of clicks (0–1). */
const IBV_FORM_HEALTH_LOST_RATIO = 0.30;

/** Validation wall: minimum clicks. */
const IBV_FORM_HEALTH_VALIDATION_MIN_CLICKS = 5;

/** Validation wall: share ending in validation_failed. */
const IBV_FORM_HEALTH_VALIDATION_RATIO = 0.50;

/** JS/AJAX errors: minimum count. */
const IBV_FORM_HEALTH_JS_ERROR_MIN = 3;

/** Quiet check: hours without a submitted enquiry. */
const IBV_FORM_HEALTH_QUIET_HOURS = 72;

/**
 * All monitored Gravity Form IDs (live export 17 Nov 2026).
 *
 * @return int[]
 */
function ibv_form_health_form_ids() {
	$ids = array( 32, 33, 34, 35, 36 );
	/**
	 * Filter monitored form IDs.
	 *
	 * @param int[] $ids Form IDs.
	 */
	return array_map( 'intval', (array) apply_filters( 'ibv_form_health_form_ids', $ids ) );
}

/**
 * Enquiry forms used for lost/validation/quiet alerting (not newsletter).
 *
 * @return int[]
 */
function ibv_form_health_enquiry_form_ids() {
	$ids = array( 33, 34, 35, 36 );
	/**
	 * Filter enquiry form IDs for alerting.
	 *
	 * @param int[] $ids Form IDs.
	 */
	return array_map( 'intval', (array) apply_filters( 'ibv_form_health_enquiry_form_ids', $ids ) );
}

/**
 * Allowed event type strings.
 *
 * @return string[]
 */
function ibv_form_health_event_types() {
	return array(
		'rendered',
		'submit_clicked',
		'validation_failed',
		'submitted',
		'spam',
		'recaptcha_error',
		'notification_failed',
		'js_error',
		'ajax_error',
		'fatal',
	);
}

/**
 * Browser-originated event types (REST whitelist).
 *
 * @return string[]
 */
function ibv_form_health_browser_event_types() {
	return array( 'rendered', 'submit_clicked', 'js_error', 'ajax_error' );
}

/**
 * Whether a form ID is monitored.
 *
 * @param int $form_id Form ID.
 * @return bool
 */
function ibv_form_health_is_scoped( $form_id ) {
	return in_array( (int) $form_id, ibv_form_health_form_ids(), true );
}

/**
 * Table name with blog prefix.
 *
 * @return string
 */
function ibv_form_health_table() {
	global $wpdb;
	return $wpdb->prefix . 'ibv_form_events';
}

/**
 * Create or upgrade the events table.
 */
function ibv_form_health_maybe_install() {
	$installed = (int) get_option( 'ibv_form_health_db_version', 0 );
	if ( $installed >= IBV_FORM_HEALTH_DB_VERSION ) {
		return;
	}

	global $wpdb;
	$table   = ibv_form_health_table();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		created_at datetime NOT NULL,
		form_id int(10) unsigned NOT NULL,
		event varchar(32) NOT NULL,
		detail varchar(255) NOT NULL DEFAULT '',
		page_path varchar(191) NOT NULL DEFAULT '',
		PRIMARY KEY  (id),
		KEY form_event_time (form_id, event, created_at)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'ibv_form_health_db_version', IBV_FORM_HEALTH_DB_VERSION, true );
}
add_action( 'plugins_loaded', 'ibv_form_health_maybe_install', 20 );

/**
 * Insert one event row. No personal data.
 *
 * @param int    $form_id   Form ID.
 * @param string $event     Event type.
 * @param string $detail    Short detail.
 * @param string $page_path Path only.
 * @return bool
 */
function ibv_form_health_log( $form_id, $event, $detail = '', $page_path = '' ) {
	$form_id = (int) $form_id;
	$event   = sanitize_key( (string) $event );

	if ( ! ibv_form_health_is_scoped( $form_id ) ) {
		return false;
	}
	if ( ! in_array( $event, ibv_form_health_event_types(), true ) ) {
		return false;
	}

	$detail    = ibv_form_health_sanitize_detail( $detail );
	$page_path = ibv_form_health_sanitize_path( $page_path );
	if ( '' === $page_path ) {
		$page_path = ibv_form_health_current_path();
	}

	global $wpdb;
	$table = ibv_form_health_table();
	$ok    = $wpdb->insert(
		$table,
		array(
			'created_at' => gmdate( 'Y-m-d H:i:s' ),
			'form_id'    => $form_id,
			'event'      => $event,
			'detail'     => $detail,
			'page_path'  => $page_path,
		),
		array( '%s', '%d', '%s', '%s', '%s' )
	);

	return false !== $ok;
}

/**
 * Truncate and strip control characters from detail.
 *
 * @param string $detail Raw detail.
 * @return string
 */
function ibv_form_health_sanitize_detail( $detail ) {
	$detail = wp_strip_all_tags( (string) $detail );
	$detail = preg_replace( '/[\x00-\x1F\x7F]/', '', $detail );
	$detail = trim( $detail );
	if ( strlen( $detail ) > 255 ) {
		$detail = substr( $detail, 0, 252 ) . '...';
	}
	return $detail;
}

/**
 * Path-only from a URL or path string.
 *
 * @param string $raw URL or path.
 * @return string
 */
function ibv_form_health_sanitize_path( $raw ) {
	$raw = (string) $raw;
	if ( '' === $raw ) {
		return '';
	}
	$path = $raw;
	if ( preg_match( '#^https?://#i', $raw ) ) {
		$parsed = wp_parse_url( $raw );
		$path   = isset( $parsed['path'] ) ? (string) $parsed['path'] : '/';
	}
	$path = '/' . ltrim( $path, '/' );
	$path = preg_replace( '#\?.*$#', '', $path );
	$path = preg_replace( '#\#.*$#', '', $path );
	$path = sanitize_text_field( $path );
	if ( strlen( $path ) > 191 ) {
		$path = substr( $path, 0, 191 );
	}
	return $path ? $path : '/';
}

/**
 * Current request path (front-end).
 *
 * @return string
 */
function ibv_form_health_current_path() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	return ibv_form_health_sanitize_path( $uri );
}

/**
 * Salted hash of the remote IP for rate limiting only (never stored in the table).
 *
 * @return string
 */
function ibv_form_health_ip_hash() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$ip = preg_replace( '/[^0-9a-fA-F:\.]/', '', $ip );
	return hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
}

/* ── Server hooks ─────────────────────────────────────────────────── */

/**
 * Form ID currently being processed (for fatal shutdown logging).
 *
 * @var int
 */
$GLOBALS['ibv_form_health_active_form_id'] = 0;

/**
 * Arm fatal capture when an in-scope form submission starts.
 *
 * Hooks early on POST submit and again at validation so AJAX and classic
 * paths both register the shutdown handler before GF work can fatal.
 *
 * @param int $form_id Form ID.
 */
function ibv_form_health_arm_fatal_capture( $form_id ) {
	$form_id = (int) $form_id;
	if ( ! ibv_form_health_is_scoped( $form_id ) ) {
		return;
	}
	$GLOBALS['ibv_form_health_active_form_id'] = $form_id;
	static $registered = false;
	if ( $registered ) {
		return;
	}
	$registered = true;
	register_shutdown_function( 'ibv_form_health_shutdown_fatal' );
}

/**
 * On front-end init: if GF is submitting an in-scope form, arm capture.
 */
function ibv_form_health_maybe_arm_from_post() {
	if ( empty( $_POST['gform_submit'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- presence check only; GF verifies.
		return;
	}
	$form_id = absint( wp_unslash( $_POST['gform_submit'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	ibv_form_health_arm_fatal_capture( $form_id );
}
add_action( 'init', 'ibv_form_health_maybe_arm_from_post', 1 );

/**
 * Shutdown: log PHP fatals that occurred while an in-scope form was active.
 *
 * Detail is basename + line + error type only — no message (may contain
 * request data).
 */
function ibv_form_health_shutdown_fatal() {
	$form_id = isset( $GLOBALS['ibv_form_health_active_form_id'] )
		? (int) $GLOBALS['ibv_form_health_active_form_id']
		: 0;
	if ( ! $form_id || ! ibv_form_health_is_scoped( $form_id ) ) {
		return;
	}
	$err = error_get_last();
	if ( ! is_array( $err ) || empty( $err['type'] ) ) {
		return;
	}
	$fatal_types = array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR );
	if ( ! in_array( (int) $err['type'], $fatal_types, true ) ) {
		return;
	}
	$file = isset( $err['file'] ) ? (string) $err['file'] : '';
	$line = isset( $err['line'] ) ? (int) $err['line'] : 0;
	$base = $file ? wp_basename( $file ) : 'unknown';
	$detail = sprintf( '%s:%d type=%d', $base, $line, (int) $err['type'] );
	ibv_form_health_log( $form_id, 'fatal', $detail );
}

/**
 * Log validation failures (field IDs + labels only).
 *
 * @param array $validation_result GF validation result.
 * @return array
 */
function ibv_form_health_on_validation( $validation_result ) {
	$form    = isset( $validation_result['form'] ) ? $validation_result['form'] : array();
	$form_id = isset( $form['id'] ) ? (int) $form['id'] : 0;
	ibv_form_health_arm_fatal_capture( $form_id );

	if ( empty( $validation_result['is_valid'] ) ) {
		if ( ibv_form_health_is_scoped( $form_id ) ) {
			$parts = array();
			$fields = isset( $form['fields'] ) && is_array( $form['fields'] ) ? $form['fields'] : array();
			foreach ( $fields as $field ) {
				if ( empty( $field->failed_validation ) ) {
					continue;
				}
				// Never log honeypot name/value — only that a field failed by ID.
				if ( ! empty( $field->type ) && 'honeypot' === $field->type ) {
					continue;
				}
				$fid   = isset( $field->id ) ? (int) $field->id : 0;
				$label = isset( $field->label ) ? sanitize_text_field( (string) $field->label ) : '';
				$label = preg_replace( '/[,\s]+/', ' ', $label );
				$parts[] = $fid . ':' . $label;
			}
			ibv_form_health_log( $form_id, 'validation_failed', implode( ',', $parts ) );
		}
	}
	return $validation_result;
}
add_filter( 'gform_validation', 'ibv_form_health_on_validation', 99, 1 );

/**
 * Log successful submit, spam, and reCAPTCHA status strings.
 *
 * @param array $entry Entry.
 * @param array $form  Form.
 */
function ibv_form_health_on_after_submission( $entry, $form ) {
	$form_id = isset( $form['id'] ) ? (int) $form['id'] : 0;
	if ( ! ibv_form_health_is_scoped( $form_id ) ) {
		return;
	}

	$status = isset( $entry['status'] ) ? (string) $entry['status'] : '';
	$score  = ibv_form_health_recaptcha_score_from_entry( $entry );

	if ( '' !== $score && ! is_numeric( $score ) ) {
		// Status strings from the reCAPTCHA add-on (disconnected, disabled, …).
		$skip_status = array( 'disabled', 'not applicable (v2)' );
		if ( ! in_array( $score, $skip_status, true ) ) {
			ibv_form_health_log( $form_id, 'recaptcha_error', $score );
		}
	}

	if ( 'spam' === $status ) {
		$detail = ibv_form_health_spam_detail( $entry, $score );
		ibv_form_health_log( $form_id, 'spam', $detail );
		return;
	}

	ibv_form_health_log( $form_id, 'submitted', '' );
}
add_action( 'gform_after_submission', 'ibv_form_health_on_after_submission', 20, 2 );

/**
 * reCAPTCHA add-on entry meta key: {slug}_score → gravityformsrecaptcha_score.
 *
 * @param array $entry Entry.
 * @return string Score or status string, or empty.
 */
function ibv_form_health_recaptcha_score_from_entry( $entry ) {
	$key = 'gravityformsrecaptcha_score';
	if ( isset( $entry[ $key ] ) && '' !== $entry[ $key ] && null !== $entry[ $key ] ) {
		return (string) $entry[ $key ];
	}
	$entry_id = isset( $entry['id'] ) ? (int) $entry['id'] : 0;
	if ( $entry_id && function_exists( 'gform_get_meta' ) ) {
		$meta = gform_get_meta( $entry_id, $key );
		if ( false !== $meta && null !== $meta && '' !== $meta ) {
			return (string) $meta;
		}
	}
	return '';
}

/**
 * Short spam reason without PII.
 *
 * @param array  $entry Entry.
 * @param string $score reCAPTCHA score/status.
 * @return string
 */
function ibv_form_health_spam_detail( $entry, $score ) {
	$bits = array();
	if ( '' !== $score ) {
		$bits[] = 'recaptcha:' . $score;
	}
	// GF may mark honeypot via entry notes; avoid field names.
	if ( ! empty( $entry['status'] ) ) {
		$bits[] = 'status:spam';
	}
	return implode( ';', $bits );
}

/**
 * GF notification send failure.
 *
 * Hook args (GF common.php): WP_Error $error, array $email details, array $entry.
 * Docs: https://docs.gravityforms.com/gform_send_email_failed/
 *
 * @param WP_Error|string $error Error object or message.
 * @param array           $email Failed message details (to, subject, message, …).
 * @param array           $entry Entry.
 */
function ibv_form_health_on_send_email_failed( $error, $email, $entry ) {
	$form_id = is_array( $entry ) && isset( $entry['form_id'] ) ? (int) $entry['form_id'] : 0;
	if ( ! ibv_form_health_is_scoped( $form_id ) ) {
		return;
	}
	$msg = is_wp_error( $error ) ? $error->get_error_message() : (string) $error;
	$msg = sanitize_text_field( $msg );
	$to  = is_array( $email ) && isset( $email['to'] ) ? (string) $email['to'] : '';
	$name = is_array( $email ) && isset( $email['name'] ) ? sanitize_text_field( (string) $email['name'] ) : 'notification';
	$domain = ibv_form_health_email_domain( $to );
	ibv_form_health_log( $form_id, 'notification_failed', $name . '|' . $domain . '|' . $msg );
}
add_action( 'gform_send_email_failed', 'ibv_form_health_on_send_email_failed', 10, 3 );

/**
 * Extract domain from a To address (no local-part).
 *
 * @param string $to Raw To.
 * @return string
 */
function ibv_form_health_email_domain( $to ) {
	$to = trim( (string) $to );
	if ( preg_match( '/@([a-z0-9.\-]+)/i', $to, $m ) ) {
		return strtolower( $m[1] );
	}
	return '';
}

/**
 * Catch wp_mail failures that look like GF notification mail.
 *
 * @param WP_Error $error Error.
 */
function ibv_form_health_on_wp_mail_failed( $error ) {
	if ( ! is_wp_error( $error ) ) {
		return;
	}
	$data = $error->get_error_data();
	$to   = '';
	if ( is_array( $data ) && isset( $data['to'] ) ) {
		$to = is_array( $data['to'] ) ? implode( ',', $data['to'] ) : (string) $data['to'];
	}
	$subject = is_array( $data ) && isset( $data['subject'] ) ? (string) $data['subject'] : '';
	// Only when GF left a fingerprint on the subject/headers, or a recent
	// submission is in progress — otherwise ignore unrelated mail.
	$is_gf = ( false !== stripos( $subject, 'gravity' ) );
	if ( is_array( $data ) && ! empty( $data['headers'] ) ) {
		$headers = is_array( $data['headers'] ) ? implode( "\n", $data['headers'] ) : (string) $data['headers'];
		if ( false !== stripos( $headers, 'X-Gravity' ) || false !== stripos( $headers, 'gravityforms' ) ) {
			$is_gf = true;
		}
	}
	if ( ! $is_gf ) {
		return;
	}
	// Attribute to the most recent scoped submission in the last minute.
	$form_id = ibv_form_health_recent_form_id();
	if ( ! $form_id ) {
		return;
	}
	$domain = ibv_form_health_email_domain( $to );
	$msg    = sanitize_text_field( $error->get_error_message() );
	ibv_form_health_log( $form_id, 'notification_failed', 'wp_mail|' . $domain . '|' . $msg );
}
add_action( 'wp_mail_failed', 'ibv_form_health_on_wp_mail_failed', 10, 1 );

/**
 * Most recent scoped form_id with a submitted/spam event in the last 2 minutes.
 *
 * @return int
 */
function ibv_form_health_recent_form_id() {
	global $wpdb;
	$table = ibv_form_health_table();
	$since = gmdate( 'Y-m-d H:i:s', time() - 120 );
	$ids   = ibv_form_health_form_ids();
	if ( ! $ids ) {
		return 0;
	}
	$in = implode( ',', array_map( 'intval', $ids ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- IDs are intval'd.
	$form_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT form_id FROM {$table} WHERE created_at >= %s AND form_id IN ({$in}) AND event IN ('submitted','spam') ORDER BY id DESC LIMIT 1",
			$since
		)
	);
	return $form_id;
}

/* ── Assets ───────────────────────────────────────────────────────── */

/**
 * Register browser beacon script.
 */
function ibv_form_health_register_assets() {
	wp_register_script(
		'ibv-form-health',
		IBV_CORE_URL . 'includes/helpers/form-health.js',
		array(),
		IBV_CORE_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ibv_form_health_register_assets', 5 );

/**
 * Enqueue beacon when an in-scope form's scripts load.
 *
 * @param array $form    Form.
 * @param bool  $is_ajax Whether AJAX is enabled.
 */
function ibv_form_health_enqueue_for_form( $form, $is_ajax = false ) {
	unset( $is_ajax );
	$form_id = isset( $form['id'] ) ? (int) $form['id'] : 0;
	if ( ! ibv_form_health_is_scoped( $form_id ) ) {
		return;
	}
	wp_enqueue_script( 'ibv-form-health' );
	wp_localize_script(
		'ibv-form-health',
		'ibvFormHealth',
		array(
			'endpoint' => esc_url_raw( rest_url( 'ibv/v1/form-event' ) ),
			'formIds'  => ibv_form_health_form_ids(),
		)
	);
}
add_action( 'gform_enqueue_scripts', 'ibv_form_health_enqueue_for_form', 20, 2 );

/* ── REST ─────────────────────────────────────────────────────────── */

/**
 * Register REST routes.
 */
function ibv_form_health_register_rest() {
	register_rest_route(
		'ibv/v1',
		'/form-event',
		array(
			'methods'             => 'POST',
			'callback'            => 'ibv_form_health_rest_form_event',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'ibv/v1',
		'/form-health',
		array(
			'methods'             => 'GET',
			'callback'            => 'ibv_form_health_rest_health',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'ibv_form_health_register_rest' );

/**
 * Public browser beacon endpoint.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function ibv_form_health_rest_form_event( WP_REST_Request $request ) {
	$form_id = (int) $request->get_param( 'form_id' );
	$event   = sanitize_key( (string) $request->get_param( 'event' ) );
	$detail  = (string) $request->get_param( 'detail' );

	if ( ! ibv_form_health_is_scoped( $form_id ) ) {
		return new WP_Error( 'ibv_form_health_bad_form', 'Invalid form.', array( 'status' => 400 ) );
	}
	if ( ! in_array( $event, ibv_form_health_browser_event_types(), true ) ) {
		return new WP_Error( 'ibv_form_health_bad_event', 'Invalid event.', array( 'status' => 400 ) );
	}

	$hash = ibv_form_health_ip_hash();
	$key  = 'ibv_fh_rl_' . substr( $hash, 0, 32 );
	$count = (int) get_transient( $key );
	if ( $count >= IBV_FORM_HEALTH_RATE_LIMIT ) {
		return new WP_Error( 'ibv_form_health_rate', 'Rate limited.', array( 'status' => 429 ) );
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$referer = isset( $_SERVER['HTTP_REFERER'] ) ? (string) wp_unslash( $_SERVER['HTTP_REFERER'] ) : '';
	$path    = ibv_form_health_sanitize_path( $referer );

	ibv_form_health_log( $form_id, $event, $detail, $path );

	return new WP_REST_Response( array( 'success' => true ), 200 );
}

/**
 * Uptime monitor health endpoint (checks 1–3).
 *
 * @return WP_REST_Response
 */
function ibv_form_health_rest_health() {
	$failing = ibv_form_health_run_monitor_checks();
	if ( $failing ) {
		return new WP_REST_Response(
			array(
				'status' => 'fail',
				'checks' => array_values( $failing ),
			),
			503
		);
	}
	return new WP_REST_Response( array( 'status' => 'ok' ), 200 );
}

/**
 * Monitor checks 1–3 over 24h (names only).
 *
 * @return string[] Failing check keys.
 */
function ibv_form_health_run_monitor_checks() {
	$since   = gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS );
	$failing = array();

	if ( ibv_form_health_count_events( null, 'recaptcha_error', $since ) > 0 ) {
		$failing[] = 'recaptcha_error';
	}
	if ( ibv_form_health_count_events( null, 'notification_failed', $since ) > 0 ) {
		$failing[] = 'notification_failed';
	}
	foreach ( ibv_form_health_enquiry_form_ids() as $form_id ) {
		$lost = ibv_form_health_lost_stats( $form_id, $since );
		if ( $lost['lost'] >= IBV_FORM_HEALTH_LOST_MIN && $lost['ratio'] > IBV_FORM_HEALTH_LOST_RATIO ) {
			$failing[] = 'submissions_lost';
			break;
		}
	}
	return array_values( array_unique( $failing ) );
}

/**
 * Count events since a UTC datetime.
 *
 * @param int|null $form_id Form ID or null for all scoped.
 * @param string   $event   Event type.
 * @param string   $since   UTC datetime.
 * @return int
 */
function ibv_form_health_count_events( $form_id, $event, $since ) {
	global $wpdb;
	$table = ibv_form_health_table();
	$event = sanitize_key( $event );
	$ids   = ibv_form_health_form_ids();
	if ( ! $ids ) {
		return 0;
	}
	$in = implode( ',', array_map( 'intval', $ids ) );

	if ( null === $form_id ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE event = %s AND created_at >= %s AND form_id IN ({$in})",
				$event,
				$since
			)
		);
	}

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE form_id = %d AND event = %s AND created_at >= %s",
			(int) $form_id,
			$event,
			$since
		)
	);
}

/**
 * Lost submission stats for one form.
 *
 * lost = submit_clicked - (submitted + spam + validation_failed)
 *
 * @param int    $form_id Form ID.
 * @param string $since   UTC datetime.
 * @return array{clicks:int,submitted:int,spam:int,validation:int,lost:int,ratio:float}
 */
function ibv_form_health_lost_stats( $form_id, $since ) {
	$clicks = ibv_form_health_count_events( $form_id, 'submit_clicked', $since );
	$sub    = ibv_form_health_count_events( $form_id, 'submitted', $since );
	$spam   = ibv_form_health_count_events( $form_id, 'spam', $since );
	$val    = ibv_form_health_count_events( $form_id, 'validation_failed', $since );
	$lost   = max( 0, $clicks - ( $sub + $spam + $val ) );
	$ratio  = $clicks > 0 ? ( $lost / $clicks ) : 0.0;
	return array(
		'clicks'     => $clicks,
		'submitted'  => $sub,
		'spam'       => $spam,
		'validation' => $val,
		'lost'       => $lost,
		'ratio'      => $ratio,
	);
}

/* ── Cron ─────────────────────────────────────────────────────────── */

/**
 * Schedule daily and weekly events.
 */
function ibv_form_health_schedule_cron() {
	if ( ! wp_next_scheduled( 'ibv_form_health_daily' ) ) {
		// ~07:00 site time: convert to UTC timestamp for tomorrow 07:00 local.
		$tz      = wp_timezone();
		$local   = new DateTime( 'tomorrow 07:00:00', $tz );
		$ts      = $local->getTimestamp();
		wp_schedule_event( $ts, 'daily', 'ibv_form_health_daily' );
	}
	if ( ! wp_next_scheduled( 'ibv_form_health_weekly' ) ) {
		$tz    = wp_timezone();
		$local = new DateTime( 'next monday 07:15:00', $tz );
		wp_schedule_event( $local->getTimestamp(), 'weekly', 'ibv_form_health_weekly' );
	}
}
add_action( 'init', 'ibv_form_health_schedule_cron' );

/**
 * Daily prune + alert checks.
 */
function ibv_form_health_run_daily() {
	ibv_form_health_prune();
	$problems = ibv_form_health_collect_daily_problems();
	if ( $problems ) {
		ibv_form_health_send_alert( $problems );
	}
}
add_action( 'ibv_form_health_daily', 'ibv_form_health_run_daily' );

/**
 * Weekly summary (Mondays).
 */
function ibv_form_health_run_weekly() {
	ibv_form_health_send_weekly_summary();
}
add_action( 'ibv_form_health_weekly', 'ibv_form_health_run_weekly' );

/**
 * Delete rows older than retention.
 */
function ibv_form_health_prune() {
	global $wpdb;
	$table = ibv_form_health_table();
	$before = gmdate( 'Y-m-d H:i:s', time() - ( IBV_FORM_HEALTH_RETENTION_DAYS * DAY_IN_SECONDS ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s", $before ) );
}

/**
 * Build daily problem lines.
 *
 * @return string[]
 */
function ibv_form_health_collect_daily_problems() {
	$since24 = gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS );
	$since72 = gmdate( 'Y-m-d H:i:s', time() - ( IBV_FORM_HEALTH_QUIET_HOURS * HOUR_IN_SECONDS ) );
	$problems = array();

	$recaptcha = ibv_form_health_count_events( null, 'recaptcha_error', $since24 );
	if ( $recaptcha > 0 ) {
		$problems[] = sprintf( 'reCAPTCHA broken: %d recaptcha_error event(s) in 24h.', $recaptcha );
	}

	$mail = ibv_form_health_count_events( null, 'notification_failed', $since24 );
	if ( $mail > 0 ) {
		$problems[] = sprintf( 'Emails failing: %d notification_failed event(s) in 24h.', $mail );
	}

	$fatals = ibv_form_health_count_events( null, 'fatal', $since24 );
	if ( $fatals > 0 ) {
		$problems[] = sprintf( 'PHP fatal during form submit: %d fatal event(s) in 24h.', $fatals );
	}

	foreach ( ibv_form_health_enquiry_form_ids() as $form_id ) {
		$lost = ibv_form_health_lost_stats( $form_id, $since24 );
		if ( $lost['lost'] >= IBV_FORM_HEALTH_LOST_MIN && $lost['ratio'] > IBV_FORM_HEALTH_LOST_RATIO ) {
			$problems[] = sprintf(
				'Submissions lost on form %d: %d lost of %d clicks (%.0f%%).',
				$form_id,
				$lost['lost'],
				$lost['clicks'],
				$lost['ratio'] * 100
			);
		}

		if (
			$lost['clicks'] >= IBV_FORM_HEALTH_VALIDATION_MIN_CLICKS
			&& $lost['clicks'] > 0
			&& ( $lost['validation'] / $lost['clicks'] ) > IBV_FORM_HEALTH_VALIDATION_RATIO
		) {
			$top = ibv_form_health_top_validation_fields( $form_id, $since24 );
			$problems[] = sprintf(
				'Validation wall on form %d: %d/%d clicks failed validation. Top fields: %s.',
				$form_id,
				$lost['validation'],
				$lost['clicks'],
				$top ? $top : '(none listed)'
			);
		}
	}

	$js = 0;
	foreach ( ibv_form_health_enquiry_form_ids() as $form_id ) {
		$js += ibv_form_health_count_events( $form_id, 'js_error', $since24 );
		$js += ibv_form_health_count_events( $form_id, 'ajax_error', $since24 );
	}
	if ( $js >= IBV_FORM_HEALTH_JS_ERROR_MIN ) {
		$problems[] = sprintf( 'JS errors: %d js_error/ajax_error event(s) on enquiry forms in 24h.', $js );
	}

	$submitted_72 = 0;
	$rendered_72  = 0;
	foreach ( ibv_form_health_enquiry_form_ids() as $form_id ) {
		$submitted_72 += ibv_form_health_count_events( $form_id, 'submitted', $since72 );
		$rendered_72  += ibv_form_health_count_events( $form_id, 'rendered', $since72 );
	}
	if ( 0 === $submitted_72 && $rendered_72 > 0 ) {
		$problems[] = sprintf(
			'Quiet (for information): no enquiry submitted in %d hours, but %d rendered.',
			IBV_FORM_HEALTH_QUIET_HOURS,
			$rendered_72
		);
	}

	return $problems;
}

/**
 * Most common validation field labels from detail strings.
 *
 * @param int    $form_id Form ID.
 * @param string $since   UTC datetime.
 * @return string
 */
function ibv_form_health_top_validation_fields( $form_id, $since ) {
	global $wpdb;
	$table = ibv_form_health_table();
	$rows  = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT detail FROM {$table} WHERE form_id = %d AND event = 'validation_failed' AND created_at >= %s AND detail <> ''",
			(int) $form_id,
			$since
		)
	);
	$counts = array();
	foreach ( (array) $rows as $detail ) {
		foreach ( explode( ',', (string) $detail ) as $part ) {
			$part = trim( $part );
			if ( '' === $part ) {
				continue;
			}
			if ( ! isset( $counts[ $part ] ) ) {
				$counts[ $part ] = 0;
			}
			++$counts[ $part ];
		}
	}
	arsort( $counts );
	$top = array_slice( array_keys( $counts ), 0, 5 );
	return implode( ', ', $top );
}

/**
 * Email daily alert.
 *
 * @param string[] $problems Problem lines.
 */
function ibv_form_health_send_alert( $problems ) {
	$first   = isset( $problems[0] ) ? wp_strip_all_tags( $problems[0] ) : 'issue';
	$first   = strlen( $first ) > 80 ? substr( $first, 0, 77 ) . '...' : $first;
	$subject = '[Ibiza Villas 2000] Form health: ' . $first;
	$screen  = admin_url( 'tools.php?page=ibv-form-health' );
	$body    = "Form health problems detected:\n\n";
	foreach ( $problems as $line ) {
		$body .= '- ' . $line . "\n";
	}
	$body .= "\nAdmin report: " . $screen . "\n";
	$body .= 'Site: ' . home_url( '/' ) . "\n";
	wp_mail( IBV_FORM_HEALTH_ALERT_EMAIL, $subject, $body );
}

/**
 * Monday summary email.
 */
function ibv_form_health_send_weekly_summary() {
	$since = gmdate( 'Y-m-d H:i:s', time() - ( 7 * DAY_IN_SECONDS ) );
	$lines = array( '7-day form health summary:', '' );
	foreach ( ibv_form_health_form_ids() as $form_id ) {
		$lost = ibv_form_health_lost_stats( $form_id, $since );
		$rendered = ibv_form_health_count_events( $form_id, 'rendered', $since );
		$lines[]  = sprintf(
			'Form %d — rendered %d, clicks %d, validation %d, spam %d, submitted %d, lost %d, notify_fail %d, recaptcha_err %d, fatal %d',
			$form_id,
			$rendered,
			$lost['clicks'],
			$lost['validation'],
			$lost['spam'],
			$lost['submitted'],
			$lost['lost'],
			ibv_form_health_count_events( $form_id, 'notification_failed', $since ),
			ibv_form_health_count_events( $form_id, 'recaptcha_error', $since ),
			ibv_form_health_count_events( $form_id, 'fatal', $since )
		);
	}
	$lines[] = '';
	$lines[] = 'Admin: ' . admin_url( 'tools.php?page=ibv-form-health' );
	wp_mail(
		IBV_FORM_HEALTH_ALERT_EMAIL,
		'[Ibiza Villas 2000] Form health: weekly summary',
		implode( "\n", $lines )
	);
}
