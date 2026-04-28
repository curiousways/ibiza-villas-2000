<?php
/**
 * Ibiza Villas 2000 Core — bootstrap.
 *
 * Pass 3a: Site Options placeholder only. Pass 3b registers CPT / ACF here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/class-site-options.php';

Ibv_Core_Site_Options::init();
