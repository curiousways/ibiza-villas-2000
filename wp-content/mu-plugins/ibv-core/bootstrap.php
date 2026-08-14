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
require_once IBV_CORE_PATH . 'includes/helpers/icon.php';
require_once IBV_CORE_PATH . 'includes/helpers/gravity-form.php';
require_once IBV_CORE_PATH . 'includes/shared-assets.php';
require_once IBV_CORE_PATH . 'includes/integrations/facetwp.php';
require_once IBV_CORE_PATH . 'includes/integrations/cookie-consent.php';

// 2. Data layer — pass 3b: CPT first, taxonomies next (slug preservation).
require_once IBV_CORE_PATH . 'includes/post-types/villa.php';
require_once IBV_CORE_PATH . 'includes/taxonomies/property-location.php';
require_once IBV_CORE_PATH . 'includes/taxonomies/villa-poi.php';

// 3. Site chrome.
require_once IBV_CORE_PATH . 'includes/nav-menus.php';
require_once IBV_CORE_PATH . 'includes/image-sizes.php';
require_once IBV_CORE_PATH . 'includes/editor.php';

// 3b. Admin UX (list columns, comments off).
require_once IBV_CORE_PATH . 'includes/admin/admin-columns-villa.php';
require_once IBV_CORE_PATH . 'includes/admin/disable-comments.php';

// WP-CLI commands (no-op when WP-CLI is not running).
require_once IBV_CORE_PATH . 'includes/cli/migrate-newsletter-to-options.php';

// Scaffolding seeders — define the ibv_seed_*() functions (code is the single
// source of truth for the fixed pages + GF form config). The orchestrator
// exposes `wp ibv seed`; nothing runs on include.
require_once IBV_CORE_PATH . 'includes/cli/seed-pages.php';
require_once IBV_CORE_PATH . 'includes/cli/seed-villa-enquiry-form.php';
require_once IBV_CORE_PATH . 'includes/cli/seed-accommodation-form.php';
require_once IBV_CORE_PATH . 'includes/cli/seed.php';

// TEMPORARY one-shot form sync for no-SSH deploys — runs the seeders once on the
// server, then is deleted on the next push. The file_exists() guard makes that
// removal clean (no edit here required). See the file header for the workflow.
if ( file_exists( IBV_CORE_PATH . 'includes/cli/seed-forms-once.php' ) ) {
	require_once IBV_CORE_PATH . 'includes/cli/seed-forms-once.php';
}

// 4. Components — each registers its own CSS handle and exposes a `ibv_core_*` helper.
require_once IBV_CORE_PATH . 'includes/components/button/button.php';
require_once IBV_CORE_PATH . 'includes/components/section-heading/section-heading.php';
require_once IBV_CORE_PATH . 'includes/components/image/image.php';
require_once IBV_CORE_PATH . 'includes/components/image-text-section/image-text-section.php';
require_once IBV_CORE_PATH . 'includes/components/villa-card/villa-card.php';
require_once IBV_CORE_PATH . 'includes/components/offer-panel/offer-panel.php';
require_once IBV_CORE_PATH . 'includes/components/quote-card/quote-card.php';
require_once IBV_CORE_PATH . 'includes/components/date-range-picker/date-range-picker.php';
require_once IBV_CORE_PATH . 'includes/components/header-search/header-search.php';
require_once IBV_CORE_PATH . 'includes/components/hero-search/hero-search.php';
require_once IBV_CORE_PATH . 'includes/components/facts-strip/facts-strip.php';
require_once IBV_CORE_PATH . 'includes/components/amenity-ticks/amenity-ticks.php';
require_once IBV_CORE_PATH . 'includes/components/distance-ticks/distance-ticks.php';
require_once IBV_CORE_PATH . 'includes/components/villa-map/villa-map.php';
require_once IBV_CORE_PATH . 'includes/components/gallery/gallery.php';
require_once IBV_CORE_PATH . 'includes/components/enquiry-panel/enquiry-panel.php';
require_once IBV_CORE_PATH . 'includes/components/accommodation-tile/accommodation-tile.php';
require_once IBV_CORE_PATH . 'includes/components/alternative-accommodation/alternative-accommodation.php';
require_once IBV_CORE_PATH . 'includes/components/article-card/article-card.php';
require_once IBV_CORE_PATH . 'includes/components/pull-quote/pull-quote.php';
require_once IBV_CORE_PATH . 'includes/components/villa-offers/villa-offers.php';
require_once IBV_CORE_PATH . 'includes/components/newsletter-form/newsletter-form.php';

require_once IBV_CORE_PATH . 'includes/sections/hero/hero.php';
require_once IBV_CORE_PATH . 'includes/sections/about-stats/about-stats.php';
require_once IBV_CORE_PATH . 'includes/sections/image-entries-section/image-entries-section.php';
require_once IBV_CORE_PATH . 'includes/sections/about-faq/about-faq.php';
require_once IBV_CORE_PATH . 'includes/sections/about-team/about-team.php';
require_once IBV_CORE_PATH . 'includes/sections/title-band/title-band.php';
require_once IBV_CORE_PATH . 'includes/sections/prose-section/prose-section.php';
require_once IBV_CORE_PATH . 'includes/sections/legal-tabs/legal-tabs.php';
require_once IBV_CORE_PATH . 'includes/sections/legal-content/legal-content.php';
require_once IBV_CORE_PATH . 'includes/sections/featured-villas/featured-villas.php';
require_once IBV_CORE_PATH . 'includes/sections/trust-strip/trust-strip.php';
require_once IBV_CORE_PATH . 'includes/sections/featured-offer/featured-offer.php';
require_once IBV_CORE_PATH . 'includes/sections/short-breaks/short-breaks.php';
require_once IBV_CORE_PATH . 'includes/sections/special-offers-grid/special-offers-grid.php';
require_once IBV_CORE_PATH . 'includes/sections/special-offers-empty-state/special-offers-empty-state.php';
require_once IBV_CORE_PATH . 'includes/sections/why-iv2000/why-iv2000.php';
require_once IBV_CORE_PATH . 'includes/sections/fancy-different/fancy-different.php';
require_once IBV_CORE_PATH . 'includes/sections/ips-panel/ips-panel.php';
require_once IBV_CORE_PATH . 'includes/sections/three-step/three-step.php';
require_once IBV_CORE_PATH . 'includes/sections/ibiza-guide-preview/ibiza-guide-preview.php';
require_once IBV_CORE_PATH . 'includes/sections/featured-article/featured-article.php';
require_once IBV_CORE_PATH . 'includes/sections/article-grid/article-grid.php';
require_once IBV_CORE_PATH . 'includes/sections/related-articles/related-articles.php';
require_once IBV_CORE_PATH . 'includes/sections/meet-team-teaser/meet-team-teaser.php';
require_once IBV_CORE_PATH . 'includes/sections/testimonials/testimonials.php';
require_once IBV_CORE_PATH . 'includes/sections/newsletter-cta/newsletter-cta.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-hero/villa-hero.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-header/villa-header.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-overview/villa-overview.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-location/villa-location.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-similar/villa-similar.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-listing-hero/villa-listing-hero.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-listing-grid/villa-listing-grid.php';
require_once IBV_CORE_PATH . 'includes/sections/listing-empty-state/listing-empty-state.php';
require_once IBV_CORE_PATH . 'includes/sections/concierge-cross-sell/concierge-cross-sell.php';
require_once IBV_CORE_PATH . 'includes/sections/concierge-services/concierge-services.php';
require_once IBV_CORE_PATH . 'includes/sections/contact-enquiry/contact-enquiry.php';
require_once IBV_CORE_PATH . 'includes/sections/concierge-services/concierge-service-dropdown.php';
require_once IBV_CORE_PATH . 'includes/sections/villa-testimonial-teaser/villa-testimonial-teaser.php';
require_once IBV_CORE_PATH . 'includes/sections/accommodation-overview/accommodation-overview.php';
require_once IBV_CORE_PATH . 'includes/sections/accommodation-enquiry/accommodation-enquiry.php';

// 5. ACF — hook to `acf/init`; load registration files last.
require_once IBV_CORE_PATH . 'includes/acf/picker-exclude-media.php';
require_once IBV_CORE_PATH . 'includes/acf/register-options.php';
require_once IBV_CORE_PATH . 'includes/acf/register-site-options-content.php';
require_once IBV_CORE_PATH . 'includes/acf/register-globals-content.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-home.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-special-offers.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-villa-listing.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-about.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-concierge.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-ips.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-legal.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-contact.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-ibiza-guide.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-booking-confirmation.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-accommodation.php';
require_once IBV_CORE_PATH . 'includes/acf/register-page-builder.php';
require_once IBV_CORE_PATH . 'includes/acf/register-post-fields.php';
require_once IBV_CORE_PATH . 'includes/acf/register-concierge.php';
require_once IBV_CORE_PATH . 'includes/acf/register-villa-fields.php';
