<?php
/**
 * Plugin Name: Ibiza Villas 2000 Core
 * Description: Project-specific functionality for Ibiza Villas 2000 — post types, taxonomies, ACF registrations, components, shared assets.
 * Version:     0.1.0
 * Author:      Curious Ways
 * Text Domain: ibv
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IBV_CORE_VERSION', '0.1.20' );
define( 'IBV_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'IBV_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once IBV_CORE_PATH . 'bootstrap.php';
