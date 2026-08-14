<?php
/**
 * Component: Villa map (Google Maps, approximate area).
 *
 * Deliberately does NOT pinpoint the villa (matching the old/live site):
 * coordinates are rounded server-side so the exact address never reaches
 * the page source, and the map draws a translucent gold circle over the
 * general area instead of a marker.
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

	// Privacy: round to 3 decimal places (~110 m grid) before the values
	// touch the DOM — guests see the neighbourhood, not the gate. The
	// circle below is bigger than the rounding error, so the villa is
	// always inside it.
	$lat = round( $lat, 3 );
	$lng = round( $lng, 3 );

	$api_key = trim( (string) get_field( 'google_maps_api_key', 'option' ) );
	if ( '' === $api_key ) {
		echo '<p class="ibv-villa-map__fallback">' . esc_html__( 'Map will load once API key is configured.', 'ibv' ) . '</p>';
		return;
	}

	// Cloud-side map styling requires a Cloud-Console-registered Map ID.
	// Fail closed with a clear message rather than render a half-broken map.
	$map_id = trim( (string) get_field( 'google_maps_map_id', 'option' ) );
	if ( '' === $map_id ) {
		echo '<p class="ibv-villa-map__fallback">' . esc_html__( 'Map will load once a Google Maps Map ID is configured in Site Options.', 'ibv' ) . '</p>';
		return;
	}

	// Register a phantom handle (src=false) just to hang inline scripts on.
	// We don't add a <script src="...maps/api/js?..."> tag at all — Google's
	// inline bootstrap loader (added as the 'before' inline below) injects
	// it dynamically when importLibrary is first called. This is Google's
	// recommended modern loading pattern and is race-free by construction.
	wp_register_script( 'ibv-google-maps', false, [], null, true );
	wp_enqueue_script( 'ibv-google-maps' );

	// Note: with `mapId` set, the JS `styles` config is IGNORED by the Maps
	// API. Map styling now lives Cloud-side, against the same Map ID, under
	// Google Cloud Console → Map Styles.
	$api_key_json = wp_json_encode( $api_key );
	$map_id_json  = wp_json_encode( $map_id );

	// Google's official inline bootstrap loader (verbatim from Google's docs,
	// only the {key,v} config is interpolated). Registers
	// `google.maps.importLibrary` and lazily injects the actual maps/api/js
	// script tag on first use.
	$bootstrap = '(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({key:' . $api_key_json . ',v:"weekly"});';

	// Awaits importLibrary for maps, then instantiates a Map with the
	// configured mapId and a translucent gold Circle (~500 m radius) over
	// the approximate area — no marker, per the privacy note in the file
	// header. Gold is --ibv-color-gold-500 (#ffbd00); inline scripts can't
	// read CSS custom properties, hence the literal. Wrapped in try/catch
	// so a failed library load logs but doesn't throw.
	$init = '(async function(){var MAP_ID=' . $map_id_json . ';try{var lib=await google.maps.importLibrary("maps");var MapCtor=lib.Map;var CircleCtor=lib.Circle;document.querySelectorAll("[data-villa-map]").forEach(function(el){var lat=parseFloat(el.dataset.lat);var lng=parseFloat(el.dataset.lng);if(isNaN(lat)||isNaN(lng)){return;}var map=new MapCtor(el,{center:{lat:lat,lng:lng},zoom:13,mapId:MAP_ID,disableDefaultUI:false});new CircleCtor({map:map,center:{lat:lat,lng:lng},radius:500,fillColor:"#ffbd00",fillOpacity:0.35,strokeColor:"#ffbd00",strokeOpacity:0.6,strokeWeight:1,clickable:false});});}catch(e){if(window.console&&console.error){console.error("ibv-villa-map: Google Maps init failed",e);}}})();';

	wp_add_inline_script( 'ibv-google-maps', $bootstrap, 'before' );
	wp_add_inline_script( 'ibv-google-maps', $init, 'after' );

	$label = __( 'Approximate villa location on map', 'ibv' );
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
