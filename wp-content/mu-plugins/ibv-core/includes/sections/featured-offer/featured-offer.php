<?php
/**
 * Section: Featured offer (shared).
 *
 * Wraps the offer panel with an optional section header and CTA.
 * Shows one real offer from the villa_offers repeater: the Site
 * Options villa if it has an active offer, otherwise the soonest
 * active offer across all villas.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the (villa, offer) pair for the featured section.
 *
 * @return array{villa_id:int,offer:array}|null
 */
function ibv_featured_offer_resolve() {
	$vid = (int) get_field( 'featured_offer_villa', 'option' );
	if ( $vid ) {
		$villa = get_post( $vid );
		if ( ! $villa || 'villas' !== $villa->post_type || 'publish' !== $villa->post_status ) {
			return null;
		}
		$offers = ibv_villa_get_active_offers( $vid );
		if ( ! $offers ) {
			return null;
		}
		return [
			'villa_id' => $vid,
			'offer'    => $offers[0],
		];
	}

	$all = ibv_villa_get_all_active_offers();
	return $all[0] ?? null;
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

	$pair = ibv_featured_offer_resolve();
	if ( ! $pair ) {
		return;
	}

	$vid   = (int) $pair['villa_id'];
	$offer = $pair['offer'];

	$headline = trim( (string) ( $offer['offer_headline'] ?? '' ) );
	if ( '' === $headline ) {
		$headline = trim( (string) ( $offer['offer_name'] ?? '' ) );
	}

	$section_cta = [];
	if ( $args['show_section_cta'] ) {
		$section_cta = [
			'url'   => ibv_get_special_offers_url(),
			'label' => __( 'Search all Special Offers', 'ibv' ),
		];
	}

	wp_enqueue_style( 'ibv-section-featured-offer' );
	?>
	<section class="ibv-section-featured-offer ibv-section ibv-section--surface-tint-teal">
		<div class="ibv-container">
			<?php
			ibv_core_offer_panel(
				[
					'villa'             => $vid,
					'offer_headline'    => $headline,
					'offer_dates'       => ibv_core_villa_offers_format_range(
						(string) ( $offer['offer_date_from'] ?? '' ),
						(string) ( $offer['offer_date_to'] ?? '' )
					),
					'offer_description' => trim( (string) ( $offer['offer_description'] ?? '' ) ),
					'cta_label'         => __( 'Enquire about this offer', 'ibv' ),
					'cta_url'           => ibv_villa_offer_enquire_url(
						$vid,
						(string) ( $offer['offer_name'] ?? '' )
					),
					'cta_variant'       => 'primary',
					'section_title'     => $args['section_title'],
					'section_cta'       => $section_cta,
				]
			);
			?>
		</div>
	</section>
	<?php
}
