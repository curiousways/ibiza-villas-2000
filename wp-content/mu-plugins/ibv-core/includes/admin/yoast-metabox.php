<?php
/**
 * Keep the Yoast SEO metabox at the bottom of the edit screen.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'wpseo_metabox_prio',
	static function () {
		return 'low';
	}
);
