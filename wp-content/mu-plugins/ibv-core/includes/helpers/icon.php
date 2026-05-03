<?php
/**
 * Icon helper — render inline SVG from vendored Lucide / Simple Icons sets.
 *
 * Icons live under assets/icons/{set}/{name}.svg. Files are trusted project
 * assets; output is not passed through wp_kses (SVG is stripped by default).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return inline SVG markup for an icon.
 *
 * @param string $name Icon slug without .svg.
 * @param array  $args {
 *     @type string $set        'lucide' | 'brands'. Default 'lucide'.
 *     @type int    $size       Width/height in px. Default 24.
 *     @type string $class      Extra class(es) on `<svg>` (merged with any in file).
 *     @type string $aria_label If non-empty, sets role="img" and aria-label.
 *     @type string $title      Optional `<title>` inside the SVG.
 * }
 * @return string Markup, or '' if missing/invalid.
 */
function ibv_core_icon( $name, $args = [] ) {
	$defaults = [
		'set'        => 'lucide',
		'size'       => 24,
		'class'      => '',
		'aria_label' => '',
		'title'      => '',
	];
	$args = wp_parse_args( $args, $defaults );

	$name = sanitize_key( $name );
	$set  = in_array( $args['set'], [ 'lucide', 'brands' ], true ) ? $args['set'] : 'lucide';

	if ( ! $name ) {
		return '';
	}

	static $raw_cache = [];
	$cache_key = $set . '/' . $name;

	if ( ! isset( $raw_cache[ $cache_key ] ) ) {
		$path = IBV_CORE_PATH . 'assets/icons/' . $set . '/' . $name . '.svg';
		if ( ! is_readable( $path ) ) {
			$raw_cache[ $cache_key ] = '';
		} else {
			$contents                = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$raw_cache[ $cache_key ] = is_string( $contents ) ? $contents : '';
		}
	}

	$raw = $raw_cache[ $cache_key ];
	if ( '' === $raw ) {
		return '';
	}

	// Lucide-static ships a license HTML comment before `<svg>` — strip for XML parse.
	$raw = preg_replace( '/\A\s*<!--.*?-->\s*/s', '', $raw );
	$raw = trim( $raw );

	libxml_use_internal_errors( true );
	$doc = new DOMDocument();
	if ( ! $doc->loadXML( $raw ) ) {
		libxml_clear_errors();
		return '';
	}
	libxml_clear_errors();

	$svg = $doc->documentElement;
	if ( ! $svg || 'svg' !== strtolower( $svg->tagName ) ) {
		return '';
	}

	// Brand files use implicit black fill — drive from CSS.
	if ( 'brands' === $set ) {
		$svg->setAttribute( 'fill', 'currentColor' );
	}

	// Decorative inside a labeled parental control: no duplicate accessible name.
	$titles = $svg->getElementsByTagName( 'title' );
	for ( $i = $titles->length - 1; $i >= 0; $i-- ) {
		$tnode = $titles->item( $i );
		if ( $tnode && $tnode->parentNode ) {
			$tnode->parentNode->removeChild( $tnode );
		}
	}
	$svg->removeAttribute( 'role' );

	$size = (int) $args['size'];
	if ( $size > 0 ) {
		$svg->setAttribute( 'width', (string) $size );
		$svg->setAttribute( 'height', (string) $size );
	} else {
		$svg->removeAttribute( 'width' );
		$svg->removeAttribute( 'height' );
	}

	$merge_class = trim( (string) $args['class'] );
	$had         = trim( (string) $svg->getAttribute( 'class' ) );
	if ( $merge_class && $had ) {
		$svg->setAttribute( 'class', $had . ' ' . $merge_class );
	} elseif ( $merge_class ) {
		$svg->setAttribute( 'class', $merge_class );
	}

	if ( ! empty( $args['aria_label'] ) ) {
		$svg->setAttribute( 'role', 'img' );
		$svg->setAttribute( 'aria-label', $args['aria_label'] );
		$svg->removeAttribute( 'aria-hidden' );
		$svg->removeAttribute( 'focusable' );
	} else {
		$svg->setAttribute( 'aria-hidden', 'true' );
		$svg->setAttribute( 'focusable', 'false' );
		$svg->removeAttribute( 'role' );
		$svg->removeAttribute( 'aria-label' );
	}

	if ( ! empty( $args['title'] ) ) {
		$title_el = $doc->createElement( 'title' );
		$title_el->appendChild( $doc->createTextNode( $args['title'] ) );
		if ( $svg->firstChild ) {
			$svg->insertBefore( $title_el, $svg->firstChild );
		} else {
			$svg->appendChild( $title_el );
		}
	}

	return $doc->saveXML( $svg );
}

/**
 * Echo wrapper for templates.
 */
function ibv_core_the_icon( $name, $args = [] ) {
	echo ibv_core_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored SVG, attribute values escaped in DOM.
}
