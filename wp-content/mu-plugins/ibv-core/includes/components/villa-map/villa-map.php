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

	// AdvancedMarkerElement requires a Cloud-Console-registered Map ID.
	// Without it, the marker library refuses to render advanced markers and
	// the map shows a stylings/usage error. Fail closed with a clear message.
	$map_id = trim( (string) get_field( 'google_maps_map_id', 'option' ) );
	if ( '' === $map_id ) {
		echo '<p class="ibv-villa-map__fallback">' . esc_html__( 'Map will load once a Google Maps Map ID is configured in Site Options.', 'ibv' ) . '</p>';
		return;
	}

	$pin_url = IBV_CORE_URL . 'assets/icons/lucide/map-pin.svg';

	// Register a phantom handle (src=false) just to hang inline scripts on.
	// We don't add a <script src="...maps/api/js?..."> tag at all — Google's
	// inline bootstrap loader (added as the 'before' inline below) injects
	// it dynamically when importLibrary is first called. This is Google's
	// recommended modern loading pattern and is race-free by construction.
	wp_register_script( 'ibv-google-maps', false, [], null, true );
	wp_enqueue_script( 'ibv-google-maps' );

	// Note: with `mapId` set, the JS `styles` config is IGNORED by the Maps
	// API. Map styling now lives Cloud-side, against the same Map ID, under
	// Google Cloud Console → Map Styles. This is Google's required migration
	// path for AdvancedMarkerElement and is not optional.
	$pin_url_json = wp_json_encode( $pin_url );
	$api_key_json = wp_json_encode( $api_key );
	$map_id_json  = wp_json_encode( $map_id );

	// Google's official inline bootstrap loader (verbatim from Google's docs,
	// only the {key,v} config is interpolated). Registers
	// `google.maps.importLibrary` and lazily injects the actual maps/api/js
	// script tag on first use.
	$bootstrap = '(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({key:' . $api_key_json . ',v:"weekly"});';

	// Awaits importLibrary for maps + marker, then instantiates a Map with
	// the configured mapId and an AdvancedMarkerElement using a 40×40 SVG
	// pin as `content`. Wrapped in try/catch so a failed library load logs
	// but doesn't throw.
	$init = '(async function(){var PIN_URL=' . $pin_url_json . ';var MAP_ID=' . $map_id_json . ';try{var libs=await Promise.all([google.maps.importLibrary("maps"),google.maps.importLibrary("marker")]);var MapCtor=libs[0].Map;var AdvMarker=libs[1].AdvancedMarkerElement;document.querySelectorAll("[data-villa-map]").forEach(function(el){var lat=parseFloat(el.dataset.lat);var lng=parseFloat(el.dataset.lng);if(isNaN(lat)||isNaN(lng)){return;}var map=new MapCtor(el,{center:{lat:lat,lng:lng},zoom:12,mapId:MAP_ID,disableDefaultUI:false});var pin=document.createElement("img");pin.src=PIN_URL;pin.alt="";pin.width=40;pin.height=40;new AdvMarker({position:{lat:lat,lng:lng},map:map,content:pin});});}catch(e){if(window.console&&console.error){console.error("ibv-villa-map: Google Maps init failed",e);}}})();';

	wp_add_inline_script( 'ibv-google-maps', $bootstrap, 'before' );
	wp_add_inline_script( 'ibv-google-maps', $init, 'after' );

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
