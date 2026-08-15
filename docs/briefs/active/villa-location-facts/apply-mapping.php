<?php
/**
 * One-shot: migrate the "Property Location" WYSIWYG (property_summary)
 * into Location tags (villa_distances), Property Facts (property_features)
 * and the Rental licence field (villa_rental_licence) for the 12 published
 * villas. Mapping drafted from the live content — see mapping.md alongside
 * this script for the full before/after including dropped lines.
 *
 * Run: wp eval-file docs/briefs/active/villa-location-facts/apply-mapping.php
 *
 * - Backs up property_summary HTML for EVERY villa (any status) to
 *   backups/{ID}-{slug}.html before touching anything, so parked/draft
 *   villas keep their source content too.
 * - Overwrites villa_distances / property_features / villa_rental_licence
 *   on the 12 mapped villas only (Tegui's existing "OK"/"Another" test
 *   facts are deliberately replaced).
 */

$backup_dir = __DIR__ . '/backups';
if ( ! is_dir( $backup_dir ) ) {
	mkdir( $backup_dir, 0755, true );
}

// ── Backups: every villa with property_summary content, any status ──
$all = get_posts(
	[
		'post_type'      => 'villas',
		'post_status'    => 'any',
		'posts_per_page' => -1,
	]
);
$backed = 0;
foreach ( $all as $p ) {
	$raw = (string) get_post_meta( $p->ID, 'property_summary', true );
	if ( '' !== trim( $raw ) ) {
		file_put_contents( $backup_dir . "/{$p->ID}-{$p->post_name}.html", $raw );
		$backed++;
	}
}
echo "backed up property_summary for {$backed} villas\n";

// ── Mapping: post ID => tags / facts / licence ──
$map = [
	3174 => [ // villa-alexa
		'tags'    => [ '10 mins walk to San Josep', '5 mins drive to San Antonio', '5–10 mins drive to beaches', '15 mins drive to Ibiza Town', '10 mins walk to shops and bars' ],
		'facts'   => [ 'Car recommended' ],
		'licence' => 'ETV2345E',
	],
	3186 => [ // villa-bella-vista
		'tags'    => [ '10 mins drive to Playa d’en Bossa', '12 mins drive to beaches', '15 mins drive to Ibiza Town', '8 mins drive to supermarket and bars', '20 mins drive to San Antonio' ],
		'facts'   => [ 'Car necessary' ],
		'licence' => 'ET1048E',
	],
	3322 => [ // villa-tunicu
		'tags'    => [ '5 mins drive to San Antonio', '5 mins drive to beaches', '20 mins drive to Ibiza Town', '5 mins drive to supermarket', '15 mins walk to cafés and restaurants' ],
		'facts'   => [ 'Car recommended' ],
		'licence' => 'ET0514E',
	],
	3524 => [ // villa-can-vicente
		'tags'    => [ '20 mins walk to San Antonio Bay', '20 mins walk to beach', '10 mins walk to restaurants and bars', '20 mins drive to Ibiza Town', '10 mins drive to Privilege and Amnesia' ],
		'facts'   => [ 'Car recommended' ],
		'licence' => 'ETV1697E',
	],
	2782 => [ // villa-daniel
		'tags'    => [ '5 mins drive to Playa d’en Bossa', '5 mins drive to beach', '7 mins drive to Ibiza Town', '7 mins walk to cafés and bars', '7 mins walk to supermarket' ],
		'facts'   => [ 'Car not necessary' ],
		'licence' => 'ET0405E',
	],
	4067 => [ // villa-km2
		'tags'    => [ '5 mins drive to Ibiza Town', '5 mins drive to Playa d’en Bossa', '10 mins drive to Cala Jondal and Salinas beaches', '5 mins walk to cafés and bars', '12 mins walk to supermarket' ],
		'facts'   => [ 'Car recommended' ],
		'licence' => 'ET0326E',
	],
	2818 => [ // villa-nieves
		'tags'    => [ '5 mins drive to San Rafael', '10 mins drive to San Antonio', '10 mins drive to Ibiza Town', '10 mins drive to beaches', '5 mins drive to supermarket' ],
		'facts'   => [ 'Car recommended' ],
		'licence' => 'ET0537E',
	],
	3155 => [ // villa-pep-luis-can-pep-mortera
		'tags'    => [ '5 mins drive to Playa d’en Bossa beach', '7 mins drive to Ibiza Town', '10 mins drive to Sa Caleta and Cala Jondal', '5 mins drive to supermarket', '15 mins drive to San Antonio' ],
		'facts'   => [ 'Car recommended' ],
		'licence' => 'VTV0225EIF',
	],
	6998 => [ // villa-savines
		'tags'    => [ '3 mins drive to Ibiza Town', '2 mins to Talamanca Beach', '5 mins drive to Playa d’en Bossa', '5 mins walk to supermarket and cafés', '2 mins to Pacha' ],
		'facts'   => [ 'Air conditioning throughout', 'WiFi', 'Security system inside and out' ],
		'licence' => 'ETV2564E',
	],
	9082 => [ // villa-tegui
		'tags'    => [ '5 mins drive to San Rafael', '15 mins drive to Ibiza Town', '15 mins drive to Playa d’en Bossa', '15 mins drive to beaches', '15 mins drive to San Antonio' ],
		'facts'   => [ 'Peaceful rural location', 'Car recommended', 'Good public transport links', 'WiFi', 'Air conditioning in the bedrooms' ],
		'licence' => 'ET0808E',
	],
	2894 => [ // villa-tom
		'tags'    => [ '5 mins drive to Playa d’en Bossa', '7 mins drive to Ibiza Town', '5–10 mins drive to beaches', '5 mins walk to bars and restaurants', '5 mins walk to supermarket' ],
		'facts'   => [ 'Car not necessary' ],
		'licence' => 'ET0406E',
	],
	4435 => [ // villa-torres
		'tags'    => [ '10 mins walk to Playa d’en Bossa', '7 mins walk to San Jordi', '15 mins walk to beaches', '5 mins drive to Ibiza Town', '5 mins walk to supermarket and bars' ],
		'facts'   => [ 'Car not necessary' ],
		'licence' => '2016007184/ETV2079E',
	],
];

foreach ( $map as $post_id => $data ) {
	$post = get_post( $post_id );
	if ( ! $post || 'villas' !== $post->post_type ) {
		echo "FAIL  {$post_id} — not a villa\n";
		continue;
	}

	// Repeater rows keyed by subfield key — most reliable update_field form.
	$tag_rows = array_map(
		static fn( $t ) => [ 'field_ibv_villa_distance_text' => $t ],
		$data['tags']
	);
	$fact_rows = array_map(
		static fn( $f ) => [ 'field_5582c5514c3b6' => $f ],
		$data['facts']
	);

	update_field( 'field_ibv_villa_distances', $tag_rows, $post_id );
	update_field( 'field_5582c42f4c3b4', $fact_rows, $post_id );
	update_field( 'field_ibv_villa_rental_licence', $data['licence'], $post_id );

	printf(
		"OK    %d %s — %d tags, %d facts, licence %s\n",
		$post_id,
		$post->post_name,
		count( $data['tags'] ),
		count( $data['facts'] ),
		$data['licence']
	);
}
