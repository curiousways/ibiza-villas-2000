<?php
/**
 * Ibiza Villas 2000 Core — bootstrap.
 *
 * Each module is a single concern. Add new modules here in load order.
 *
 * Sections:
 *   1. Helpers and shared assets (no dependencies, used by everything below).
 *   2. Data layer — villas CPT, then related taxonomies; then villa ACF (PHP).
 *   3. Site chrome — nav menu locations, image sizes, editor preferences.
 *   4. Components — each registers its own CSS handle and exposes a helper.
 *   5. ACF — registrations hook to `acf/init`; require last.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Helpers and shared assets.
require_once IBV_CORE_PATH . 'includes/helpers.php';
require_once IBV_CORE_PATH . 'includes/shared-assets.php';

// 2. Data layer — pass 3b: CPT first, taxonomies next (slug preservation).
require_once IBV_CORE_PATH . 'includes/post-types/villa.php';
require_once IBV_CORE_PATH . 'includes/taxonomies/property-location.php';
require_once IBV_CORE_PATH . 'includes/taxonomies/villa-type.php';

// 3. Site chrome.
require_once IBV_CORE_PATH . 'includes/nav-menus.php';
require_once IBV_CORE_PATH . 'includes/image-sizes.php';
require_once IBV_CORE_PATH . 'includes/editor.php';

// 4. Components — each registers its own CSS handle and exposes a `ibv_core_*` helper.
require_once IBV_CORE_PATH . 'includes/components/button/button.php';
require_once IBV_CORE_PATH . 'includes/components/section-heading/section-heading.php';
require_once IBV_CORE_PATH . 'includes/components/image/image.php';

// 5. ACF — hook to `acf/init`; load registration files last.
require_once IBV_CORE_PATH . 'includes/acf/register-options.php';
require_once IBV_CORE_PATH . 'includes/acf/register-villa-fields.php';
