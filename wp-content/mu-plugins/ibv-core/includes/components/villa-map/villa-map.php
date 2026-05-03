<?php
/**
 * Component: Villa map (Google Maps, single marker).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_villa_map( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-map' );

	$raw   = get_field( 'property_map', $villa_id );
	$lat   = null;
	$lng   = null;
	if ( is_array( $raw ) ) {
		if ( isset( $raw['lat'] ) && is_numeric( $raw['lat'] ) ) {
			$lat = (float) $raw['lat'];
		}
		if ( isset( $raw['lng'] ) && is_numeric( $raw['lng'] ) ) {
			$lng = (float) $raw['lng'];
		}
	}

	if ( null === $lat || null === $lng ) {
		return;
	}

	$api_key = trim( (string) get_field( 'google_maps_api_key', 'option' ) );
	if ( '' === $api_key ) {
		echo '<p class="ibv-villa-map__fallback">' . esc_html__( 'Map will load once API key is configured.', 'ibv' ) . '</p>';
		return;
	}

	$pin_url = IBV_CORE_URL . 'assets/icons/lucide/map-pin.svg';

	$map_url = add_query_arg(
		array(
			'key'     => $api_key,
			'loading' => 'async',
		),
		'https://maps.googleapis.com/maps/api/js'
	);

	wp_enqueue_script( 'ibv-google-maps', esc_url_raw( $map_url ), [], null, true );

	$styles = wp_json_encode(
		array(
			array(
				'featureType' => 'all',
				'elementType' => 'geometry',
				'stylers'     => array( array( 'color' => '#f5f5f5' ) ),
			),
			array(
				'featureType' => 'water',
				'elementType' => 'geometry',
				'stylers'     => array( array( 'color' => '#d4d4d4' ) ),
			),
			array(
				'featureType' => 'road',
				'elementType' => 'geometry',
				'stylers'     => array( array( 'color' => '#ffffff' ) ),
			),
			array(
				'featureType' => 'poi',
				'elementType' => 'labels',
				'stylers'     => array( array( 'visibility' => 'off' ) ),
			),
			array(
				'featureType' => 'transit',
				'stylers'     => array( array( 'visibility' => 'off' ) ),
			),
		)
	);

	$pin_url_json = wp_json_encode( $pin_url );

	$inline = '(function(){var STYLES=' . $styles . ';var PIN_URL=' . $pin_url_json . ';function ibvInitVillaMaps(){if(typeof google==="undefined"||!google.maps){return;}var els=document.querySelectorAll("[data-villa-map]");els.forEach(function(el){var lat=parseFloat(el.dataset.lat);var lng=parseFloat(el.dataset.lng);if(isNaN(lat)||isNaN(lng)){return;}var map=new google.maps.Map(el,{center:{lat:lat,lng:lng},zoom:12,styles:STYLES,disableDefaultUI:false});new google.maps.Marker({position:{lat:lat,lng:lng},map:map,icon:{url:PIN_URL,scaledSize:new google.maps.Size(40,40)}});});}if(typeof google!=="undefined"&&google.maps){ibvInitVillaMaps();}else{window.addEventListener("load",ibvInitVillaMaps);}})();';

	wp_add_inline_script( 'ibv-google-maps', $inline, 'after' );

	$label = __( 'Villa location on map', 'ibv' );
	?>
	<div
		class="ibv-villa-map"
		data-villa-map
		data-lat="<?php echo esc_attr( (string) $lat ); ?>"
		data-lng="<?php echo esc_attr( (string) $lng ); ?>"
		aria-label="<?php echo esc_attr( $label ); ?>"
		role="presentation"
	></div>
	<?php
}
