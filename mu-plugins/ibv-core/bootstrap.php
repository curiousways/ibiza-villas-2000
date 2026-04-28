<?php
/**
 * Ibiza Villas 2000 Core — bootstrap.
 *
 * Each module is a single concern. Add new modules here in load order.
 *
 * Sections:
 *   1. Helpers and shared assets (no dependencies, used by everything below).
 *   2. Data layer (taxonomies first, then post types). Empty in the boilerplate
 *      — add per-project files under `includes/taxonomies/` and `includes/post-types/`
 *      and require them here.
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

// 2. Data layer — add per-project taxonomy and post-type files here.
//    Order: taxonomies before post types so taxonomy keys are available
//    when post types declare them.
//
//    Example:
//    require_once IBV_CORE_PATH . 'includes/taxonomies/topic.php';
//    require_once IBV_CORE_PATH . 'includes/post-types/article.php';

// 3. Site chrome.
require_once IBV_CORE_PATH . 'includes/nav-menus.php';
require_once IBV_CORE_PATH . 'includes/image-sizes.php';
require_once IBV_CORE_PATH . 'includes/editor.php';

// 4. Components — each registers its own CSS handle and exposes a `ibv_core_*` helper.
require_once IBV_CORE_PATH . 'includes/components/button/button.php';
require_once IBV_CORE_PATH . 'includes/components/section-heading/section-heading.php';
require_once IBV_CORE_PATH . 'includes/components/image/image.php';

// 5. ACF — registrations hook to `acf/init`; require last.
require_once IBV_CORE_PATH . 'includes/acf/register-options.php';
