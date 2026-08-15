<?php
/**
 * One-shot: split each published villa's first paragraph out of
 * post_content into the villa_summary ACF field (plain text).
 *
 * Run: wp eval-file docs/briefs/active/villa-summary-split/migrate-summaries.php
 *
 * - Backs up the full pre-migration post_content per villa to
 *   backups/{ID}-{slug}.html before touching anything.
 * - Idempotent: skips villas that already have villa_summary set.
 * - The paragraph is stripped of inline tags (<strong>, links) for the
 *   plain-text field; the original formatting survives in the backup.
 */

$backup_dir = __DIR__ . '/backups';
if ( ! is_dir( $backup_dir ) ) {
	mkdir( $backup_dir, 0755, true );
}

$villas = get_posts(
	[
		'post_type'      => 'villas',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	]
);

echo count( $villas ) . " published villas\n";

foreach ( $villas as $villa ) {
	$label = "{$villa->ID} {$villa->post_name}";

	$existing = trim( (string) get_field( 'villa_summary', $villa->ID ) );
	if ( '' !== $existing ) {
		echo "SKIP  {$label} — villa_summary already set\n";
		continue;
	}

	$content = $villa->post_content;
	if ( ! preg_match( '/\A\s*<p[^>]*>(.*?)<\/p>\s*/s', $content, $m ) ) {
		echo "SKIP  {$label} — content does not start with a <p>\n";
		continue;
	}

	$summary = trim( wp_strip_all_tags( $m[1] ) );
	$rest    = ltrim( substr( $content, strlen( $m[0] ) ) );

	if ( '' === $summary || '' === $rest ) {
		echo "SKIP  {$label} — empty summary or nothing left after the split\n";
		continue;
	}

	file_put_contents( $backup_dir . "/{$villa->ID}-{$villa->post_name}.html", $content );

	update_field( 'field_ibv_villa_summary', $summary, $villa->ID );
	$updated = wp_update_post(
		[
			'ID'           => $villa->ID,
			'post_content' => $rest,
		],
		true
	);

	if ( is_wp_error( $updated ) ) {
		echo "FAIL  {$label} — " . $updated->get_error_message() . "\n";
		continue;
	}

	printf(
		"OK    %s — summary %d words, remaining content %d chars\n",
		$label,
		str_word_count( $summary ),
		strlen( $rest )
	);
}
