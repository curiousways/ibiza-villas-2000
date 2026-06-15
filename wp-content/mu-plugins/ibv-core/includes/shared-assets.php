<?php
/**
 * Ibiza Villas 2000 Core — shared asset registration.
 *
 * Registers (does not enqueue) the base CSS bundle and every component's CSS.
 * The theme enqueues `ibv-base` globally; components enqueue themselves
 * conditionally via their helper functions.
 *
 * Pattern for adding a new component:
 *   1. Co-locate the .css next to the component .php.
 *   2. Register the handle here, with `ibv-base` as a dependency.
 *   3. Call `wp_enqueue_style( 'ibv-{name}' )` from the component helper.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'ibv_register_styles', 5 );
function ibv_register_styles() {
	wp_register_style(
		'ibv-fonts',
		IBV_CORE_URL . 'assets/css/fonts.css',
		[],
		IBV_CORE_VERSION
	);

	// File-bundled handles. `base.css` registers as `ibv-elements` so that
	// `ibv-base` is free to act as the aggregate handle below.
	$foundation_files = [
		'ibv-tokens'     => 'assets/css/tokens.css',
		'ibv-reset'      => 'assets/css/reset.css',
		'ibv-elements'   => 'assets/css/base.css',
		'ibv-typography' => 'assets/css/typography.css',
		'ibv-layout'     => 'assets/css/layout.css',
		'ibv-sections'   => 'assets/css/sections.css',
	];

	foreach ( $foundation_files as $handle => $rel ) {
		wp_register_style(
			$handle,
			IBV_CORE_URL . $rel,
			[],
			IBV_CORE_VERSION
		);
	}

	// Aggregate handle — has no file of its own, just pulls in all the
	// foundation handles (and the font handle) via dependencies. The theme
	// enqueues this once globally; components depend on it.
	wp_register_style(
		'ibv-base',
		false,
		[ 'ibv-fonts', 'ibv-tokens', 'ibv-reset', 'ibv-elements', 'ibv-typography', 'ibv-layout', 'ibv-sections' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-forms',
		IBV_CORE_URL . 'assets/css/forms.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-gravity-forms',
		IBV_CORE_URL . 'assets/css/gravity-forms.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	// Components — each registered with `ibv-base` as a dependency.
	wp_register_style(
		'ibv-button',
		IBV_CORE_URL . 'includes/components/button/button.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-section-heading',
		IBV_CORE_URL . 'includes/components/section-heading/section-heading.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-site-chrome',
		IBV_CORE_URL . 'includes/layout/site-chrome.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-vanilla-calendar-pro',
		IBV_CORE_URL . 'includes/components/date-range-picker/vendor/vanilla-calendar-pro/styles/index.css',
		[],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-intl-tel-input',
		IBV_CORE_URL . 'includes/components/enquiry-panel/vendor/intl-tel-input/css/intlTelInput.min.css',
		[],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-date-range-picker',
		IBV_CORE_URL . 'includes/components/date-range-picker/date-range-picker.css',
		[ 'ibv-base', 'ibv-vanilla-calendar-pro' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-header-search',
		IBV_CORE_URL . 'includes/components/header-search/header-search.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-hero-search',
		IBV_CORE_URL . 'includes/components/hero-search/hero-search.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-image-text-section',
		IBV_CORE_URL . 'includes/components/image-text-section/image-text-section.css',
		[ 'ibv-base', 'ibv-button' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-accommodation-tile',
		IBV_CORE_URL . 'includes/components/accommodation-tile/accommodation-tile.css',
		[ 'ibv-base', 'ibv-button' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-article-card',
		IBV_CORE_URL . 'includes/components/article-card/article-card.css',
		[ 'ibv-base', 'ibv-button' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-pull-quote',
		IBV_CORE_URL . 'includes/components/pull-quote/pull-quote.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-villa-offers',
		IBV_CORE_URL . 'includes/components/villa-offers/villa-offers.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-article-shell',
		IBV_CORE_URL . 'includes/sections/article-shell/article-shell.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-facetwp',
		IBV_CORE_URL . 'includes/integrations/facetwp.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-newsletter-form',
		IBV_CORE_URL . 'includes/components/newsletter-form/newsletter-form.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-villa-card',
		IBV_CORE_URL . 'includes/components/villa-card/villa-card.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-offer-panel',
		IBV_CORE_URL . 'includes/components/offer-panel/offer-panel.css',
		[ 'ibv-base', 'ibv-villa-card', 'ibv-button' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-quote-card',
		IBV_CORE_URL . 'includes/components/quote-card/quote-card.css',
		[ 'ibv-base' ],
		IBV_CORE_VERSION
	);

	$section_handles = [
		'ibv-section-hero'                 => 'includes/sections/hero/hero.css',
		'ibv-section-featured-villas'       => 'includes/sections/featured-villas/featured-villas.css',
		'ibv-section-trust-strip'          => 'includes/sections/trust-strip/trust-strip.css',
		'ibv-section-featured-offer'      => 'includes/sections/featured-offer/featured-offer.css',
		'ibv-section-why-iv2000'           => 'includes/sections/why-iv2000/why-iv2000.css',
		'ibv-section-three-step'          => 'includes/sections/three-step/three-step.css',
		'ibv-section-about-stats'         => 'includes/sections/about-stats/about-stats.css',
		'ibv-section-about-story'         => 'includes/sections/about-story/about-story.css',
		'ibv-section-about-faq'           => 'includes/sections/about-faq/about-faq.css',
		'ibv-section-about-team'          => 'includes/sections/about-team/about-team.css',
		'ibv-section-ibiza-guide-preview' => 'includes/sections/ibiza-guide-preview/ibiza-guide-preview.css',
		'ibv-section-featured-article'    => 'includes/sections/featured-article/featured-article.css',
		'ibv-section-article-grid'        => 'includes/sections/article-grid/article-grid.css',
		'ibv-section-related-articles'    => 'includes/sections/related-articles/related-articles.css',
		'ibv-section-testimonials'                  => 'includes/sections/testimonials/testimonials.css',
		'ibv-section-special-offers-grid'           => 'includes/sections/special-offers-grid/special-offers-grid.css',
		'ibv-section-special-offers-empty-state'    => 'includes/sections/special-offers-empty-state/special-offers-empty-state.css',
		'ibv-section-concierge-services'            => 'includes/sections/concierge-services/concierge-services.css',
		'ibv-section-concierge-contact'             => 'includes/sections/concierge-contact/concierge-contact.css',
	];

	foreach ( $section_handles as $handle => $rel ) {
		wp_register_style(
			$handle,
			IBV_CORE_URL . $rel,
			[ 'ibv-base' ],
			IBV_CORE_VERSION
		);
	}

	wp_register_style(
		'ibv-section-fancy-different',
		IBV_CORE_URL . 'includes/sections/fancy-different/fancy-different.css',
		[ 'ibv-base', 'ibv-accommodation-tile' ],
		IBV_CORE_VERSION
	);

	$detail_handles = [
		'ibv-facts-strip'                   => 'includes/components/facts-strip/facts-strip.css',
		'ibv-amenity-ticks'                 => 'includes/components/amenity-ticks/amenity-ticks.css',
		'ibv-distance-ticks'                => 'includes/components/distance-ticks/distance-ticks.css',
		'ibv-villa-map'                    => 'includes/components/villa-map/villa-map.css',
		'ibv-gallery'                      => 'includes/components/gallery/gallery.css',
		'ibv-villa-detail'                 => 'includes/sections/villa-detail-shell/villa-detail-shell.css',
		'ibv-section-villa-hero'           => 'includes/sections/villa-hero/villa-hero.css',
		'ibv-section-villa-header'         => 'includes/sections/villa-header/villa-header.css',
		'ibv-section-villa-overview'       => 'includes/sections/villa-overview/villa-overview.css',
		'ibv-section-villa-location'       => 'includes/sections/villa-location/villa-location.css',
		'ibv-section-villa-similar'        => 'includes/sections/villa-similar/villa-similar.css',
		'ibv-section-villa-listing-grid'   => 'includes/sections/villa-listing-grid/villa-listing-grid.css',
		'ibv-section-listing-empty-state'  => 'includes/sections/listing-empty-state/listing-empty-state.css',
		'ibv-section-villa-testimonial-teaser' => 'includes/sections/villa-testimonial-teaser/villa-testimonial-teaser.css',
		'ibv-section-villa-listing-hero'    => 'includes/sections/villa-listing-hero/villa-listing-hero.css',
		'ibv-booking-confirmation'         => 'includes/sections/booking-confirmation-page/booking-confirmation-page.css',
	];

	foreach ( $detail_handles as $handle => $rel ) {
		wp_register_style(
			$handle,
			IBV_CORE_URL . $rel,
			[ 'ibv-base' ],
			IBV_CORE_VERSION
		);
	}

	wp_register_style(
		'ibv-enquiry-panel',
		IBV_CORE_URL . 'includes/components/enquiry-panel/enquiry-panel.css',
		[ 'ibv-base', 'ibv-intl-tel-input' ],
		IBV_CORE_VERSION
	);

	wp_register_style(
		'ibv-alternative-accommodation',
		IBV_CORE_URL . 'includes/components/alternative-accommodation/alternative-accommodation.css',
		[ 'ibv-base', 'ibv-accommodation-tile' ],
		IBV_CORE_VERSION
	);

	// Inline-only script targets (wp_add_inline_script) for Pass 3c-detail components.
	wp_register_script( 'ibv-villa-overview', '', [], IBV_CORE_VERSION, true );
	wp_register_script( 'ibv-gallery-script', '', [], IBV_CORE_VERSION, true );

	wp_register_script(
		'ibv-vanilla-calendar-pro',
		IBV_CORE_URL . 'includes/components/date-range-picker/vendor/vanilla-calendar-pro/index.js',
		[],
		IBV_CORE_VERSION,
		[ 'in_footer' => true ]
	);

	wp_register_script(
		'ibv-date-range-picker',
		IBV_CORE_URL . 'includes/components/date-range-picker/date-range-picker.js',
		[ 'ibv-vanilla-calendar-pro' ],
		IBV_CORE_VERSION,
		[ 'in_footer' => true ]
	);

	wp_register_script(
		'ibv-intl-tel-input',
		IBV_CORE_URL . 'includes/components/enquiry-panel/vendor/intl-tel-input/js/intlTelInput.min.js',
		[],
		IBV_CORE_VERSION,
		[
			'in_footer' => true,
			'strategy'  => 'defer',
		]
	);

	wp_register_script(
		'ibv-enquiry-panel',
		IBV_CORE_URL . 'includes/components/enquiry-panel/enquiry-panel.js',
		[ 'ibv-intl-tel-input' ],
		IBV_CORE_VERSION,
		[
			'in_footer' => true,
			'strategy'  => 'defer',
		]
	);

	wp_register_script(
		'ibv-villa-listing-search',
		IBV_CORE_URL . 'includes/sections/villa-listing-grid/villa-listing-grid.js',
		[],
		IBV_CORE_VERSION,
		[ 'in_footer' => true ]
	);
}

add_action( 'wp_enqueue_scripts', 'ibv_enqueue_template_styles', 20 );
function ibv_enqueue_template_styles() {
	if ( is_singular( 'villas' ) && wp_style_is( 'ibv-villa-detail', 'registered' ) ) {
		wp_enqueue_style( 'ibv-villa-detail' );
	}

	if ( is_page_template( 'page-booking-confirmation.php' ) && wp_style_is( 'ibv-booking-confirmation', 'registered' ) ) {
		wp_enqueue_style( 'ibv-booking-confirmation' );
	}
}
