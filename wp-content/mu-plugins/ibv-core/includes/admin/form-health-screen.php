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
 * Render the report screen.
 */
function ibv_form_health_render_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

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
