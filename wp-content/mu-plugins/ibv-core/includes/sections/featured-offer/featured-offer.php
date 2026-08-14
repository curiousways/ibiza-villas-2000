<?php
/**
 * Section: Featured offer (shared).
 *
 * Wraps the offer panel with an optional section header and CTA.
 * Data from Site Options; callers may override title and section CTA visibility.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Featured offer block (globals) with optional heading/CTA overrides.
 *
 * @param array $args {
 *     @type string $section_title    Heading above the panel.
 *     @type bool   $show_section_cta When true, show “Search all Special Offers” in header.
 * }
 */
function ibv_core_section_featured_offer( $args = [] ) {
	$defaults = [
		'section_title'    => __( "This Week's Special Offer", 'ibv' ),
		'show_section_cta' => true,
	];
	$args = wp_parse_args( $args, $defaults );

	wp_enqueue_style( 'ibv-section-featured-offer' );

	$vid = (int) get_field( 'featured_offer_villa', 'option' );
	if ( ! $vid ) {
		return;
	}

	$was   = get_field( 'featured_offer_was_price', 'option' );
	$now   = get_field( 'featured_offer_now_price', 'option' );
	$vfrom = get_field( 'featured_offer_valid_from', 'option' );
	$vto   = get_field( 'featured_offer_valid_to', 'option' );

	// Auto-expiry: once the Valid-to date has passed, the whole section
	// disappears rather than advertising a dead offer. No end date = always
	// on (manual control). Ymd strings compare lexically. With WP Rocket the
	// check is baked into the cached page, so in practice the section drops
	// on the first cache cycle after expiry.
	if ( $vto && (string) $vto < current_time( 'Ymd' ) ) {
		return;
	}
	$show_asterisk = (bool) get_field( 'featured_offer_show_now_asterisk', 'option' );
	$footnote      = get_field( 'featured_offer_footnote', 'option' );
	$footnote      = is_string( $footnote ) ? trim( $footnote ) : '';

	$section_cta = [];
	if ( $args['show_section_cta'] ) {
		$section_cta = [
			'url'   => ibv_get_special_offers_url(),
			'label' => __( 'Search all Special Offers', 'ibv' ),
		];
	}
	?>
	<section class="ibv-section-featured-offer ibv-section ibv-section--surface-tint-teal">
		<div class="ibv-container">
			<?php
			ibv_core_offer_panel(
				[
					'villa'                => $vid,
					'was_price'            => $was ? (float) $was : null,
					'now_price'            => $now ? (float) $now : null,
					'valid_from'           => $vfrom ? (string) $vfrom : null,
					'valid_to'             => $vto ? (string) $vto : null,
					'cta_label'            => __( 'View Villa', 'ibv' ),
					'section_title'        => $args['section_title'],
					'section_cta'          => $section_cta,
					'show_now_asterisk'    => $show_asterisk,
					'footnote'             => $footnote,
				]
			);
			?>
		</div>
	</section>
	<?php
}
