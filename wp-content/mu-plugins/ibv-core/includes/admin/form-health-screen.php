<?php
/**
 * Tools → Form health — admin report (manage_options only).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Tools submenu.
 */
function ibv_form_health_admin_menu() {
	add_management_page(
		__( 'Form health', 'ibv' ),
		__( 'Form health', 'ibv' ),
		'manage_options',
		'ibv-form-health',
		'ibv_form_health_render_screen'
	);
}
add_action( 'admin_menu', 'ibv_form_health_admin_menu' );

/**
 * Handle “Send test alert now” (nonce + manage_options).
 */
function ibv_form_health_handle_test_alert() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Forbidden.', 'ibv' ), 403 );
	}
	check_admin_referer( 'ibv_form_health_test_alert' );

	$problems = ibv_form_health_collect_daily_problems();
	if ( ! $problems ) {
		$problems = array(
			'Test alert: no threshold problems in the last 24h (forced send).',
		);
	}
	$sent = ibv_form_health_send_alert( $problems, '[TEST] ' );

	$redirect = add_query_arg(
		array(
			'page'              => 'ibv-form-health',
			'ibv_fh_test'       => $sent ? '1' : '0',
			'ibv_fh_test_count' => count( $problems ),
		),
		admin_url( 'tools.php' )
	);
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_ibv_form_health_test_alert', 'ibv_form_health_handle_test_alert' );

/**
 * Render the report screen.
 */
function ibv_form_health_render_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['ibv_fh_test'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flash after our redirect.
		$ok = '1' === (string) wp_unslash( $_GET['ibv_fh_test'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$count = isset( $_GET['ibv_fh_test_count'] ) ? absint( $_GET['ibv_fh_test_count'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			$ok ? 'success' : 'error',
			$ok
				? esc_html(
					sprintf(
						/* translators: 1: recipient email, 2: number of problem lines */
						__( 'Test alert sent via wp_mail (returned true) to %1$s. Body lines: %2$d. Subject prefixed with [TEST].', 'ibv' ),
						IBV_FORM_HEALTH_ALERT_EMAIL,
						$count
					)
				)
				: esc_html(
					sprintf(
						/* translators: %s: recipient email */
						__( 'wp_mail returned false when sending the test alert to %s. Check FluentSMTP / mail logs.', 'ibv' ),
						IBV_FORM_HEALTH_ALERT_EMAIL
					)
				)
		);
	}

	$cron_defined = defined( 'DISABLE_WP_CRON' );
	$cron_value   = $cron_defined ? ( DISABLE_WP_CRON ? 'true' : 'false' ) : '—';
	$next_daily   = wp_next_scheduled( 'ibv_form_health_daily' );
	$next_weekly  = wp_next_scheduled( 'ibv_form_health_weekly' );
	$last_daily   = (string) get_option( 'ibv_form_health_last_daily', '' );
	$last_weekly  = (string) get_option( 'ibv_form_health_last_weekly', '' );

	$since14 = gmdate( 'Y-m-d H:i:s', time() - ( 14 * DAY_IN_SECONDS ) );
	$forms   = ibv_form_health_form_ids();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Form health', 'ibv' ); ?></h1>
		<p>
			<?php
			echo esc_html__(
				'Attempt log for monitored Gravity Forms (no personal data). Health endpoint: GET /wp-json/ibv/v1/form-health',
				'ibv'
			);
			?>
		</p>

		<div class="card" style="max-width: 52rem; padding: 1rem 1.25rem; margin: 1rem 0 1.5rem;">
			<h2 style="margin-top: 0;"><?php echo esc_html__( 'Cron status', 'ibv' ); ?></h2>
			<table class="widefat striped" style="margin-bottom: 1rem;">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html__( 'DISABLE_WP_CRON', 'ibv' ); ?></th>
						<td>
							<?php
							if ( $cron_defined ) {
								echo esc_html(
									sprintf(
										/* translators: %s: true or false */
										__( 'Defined: %s', 'ibv' ),
										$cron_value
									)
								);
							} else {
								echo esc_html__( 'Not defined', 'ibv' );
							}
							?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Daily check — next run', 'ibv' ); ?></th>
						<td><?php echo esc_html( ibv_form_health_format_scheduled( $next_daily ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Daily check — last ran', 'ibv' ); ?></th>
						<td><?php echo esc_html( ibv_form_health_format_last_run( $last_daily ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Weekly summary — next run', 'ibv' ); ?></th>
						<td><?php echo esc_html( ibv_form_health_format_scheduled( $next_weekly ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Weekly summary — last ran', 'ibv' ); ?></th>
						<td><?php echo esc_html( ibv_form_health_format_last_run( $last_weekly ) ); ?></td>
					</tr>
				</tbody>
			</table>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="ibv_form_health_test_alert">
				<?php wp_nonce_field( 'ibv_form_health_test_alert' ); ?>
				<?php
				submit_button(
					__( 'Send test alert now', 'ibv' ),
					'secondary',
					'submit',
					false
				);
				?>
				<p class="description" style="margin: 0.5rem 0 0;">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: alert email */
							__( 'Runs the daily checks and emails %s with subject prefixed [TEST], even when nothing trips.', 'ibv' ),
							IBV_FORM_HEALTH_ALERT_EMAIL
						)
					);
					?>
				</p>
			</form>
		</div>

		<h2><?php echo esc_html__( 'Last 14 days', 'ibv' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'Form', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Rendered', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Clicks', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Validation failed', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Top fields', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Spam', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Submitted', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Lost', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Notify fail', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'reCAPTCHA err', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Fatal', 'ibv' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $forms as $form_id ) : ?>
					<?php
					$lost = ibv_form_health_lost_stats( $form_id, $since14 );
					$top  = ibv_form_health_top_validation_fields( $form_id, $since14 );
					?>
					<tr>
						<td><?php echo esc_html( (string) $form_id ); ?></td>
						<td><?php echo esc_html( (string) ibv_form_health_count_events( $form_id, 'rendered', $since14 ) ); ?></td>
						<td><?php echo esc_html( (string) $lost['clicks'] ); ?></td>
						<td><?php echo esc_html( (string) $lost['validation'] ); ?></td>
						<td><?php echo esc_html( $top ? $top : '—' ); ?></td>
						<td><?php echo esc_html( (string) $lost['spam'] ); ?></td>
						<td><?php echo esc_html( (string) $lost['submitted'] ); ?></td>
						<td><?php echo esc_html( (string) $lost['lost'] ); ?></td>
						<td><?php echo esc_html( (string) ibv_form_health_count_events( $form_id, 'notification_failed', $since14 ) ); ?></td>
						<td><?php echo esc_html( (string) ibv_form_health_count_events( $form_id, 'recaptcha_error', $since14 ) ); ?></td>
						<td><?php echo esc_html( (string) ibv_form_health_count_events( $form_id, 'fatal', $since14 ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2><?php echo esc_html__( 'Last 50 events', 'ibv' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'When (UTC)', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Form', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Event', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Detail', 'ibv' ); ?></th>
					<th><?php echo esc_html__( 'Path', 'ibv' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( ibv_form_health_recent_rows( 50 ) as $row ) : ?>
					<tr>
						<td><?php echo esc_html( (string) $row->created_at ); ?></td>
						<td><?php echo esc_html( (string) $row->form_id ); ?></td>
						<td><?php echo esc_html( (string) $row->event ); ?></td>
						<td><?php echo esc_html( (string) $row->detail ); ?></td>
						<td><?php echo esc_html( (string) $row->page_path ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Newest event rows.
 *
 * @param int $limit Max rows.
 * @return object[]
 */
function ibv_form_health_recent_rows( $limit = 50 ) {
	global $wpdb;
	$table = ibv_form_health_table();
	$limit = max( 1, min( 200, (int) $limit ) );
	$ids   = ibv_form_health_form_ids();
	if ( ! $ids ) {
		return array();
	}
	$in = implode( ',', array_map( 'intval', $ids ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	return $wpdb->get_results(
		"SELECT created_at, form_id, event, detail, page_path FROM {$table} WHERE form_id IN ({$in}) ORDER BY id DESC LIMIT {$limit}"
	);
}
