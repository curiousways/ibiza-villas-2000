<?php
/**
 * Component: Hero search (Bob API shell).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render hero overlay search form.
 */
function ibv_core_hero_search() {
	wp_enqueue_style( 'ibv-hero-search' );

	$qs = ibv_get_villa_listing_search_params();
	?>
	<?php /* ─────────────────────────────────────────────────────────────
	       BOB API INTEGRATION SHELL — homepage hero search
	       ─────────────────────────────────────────────────────────────
	       Form fields:
	         - date_from   (required, YYYY-MM-DD)
	         - date_to     (required, YYYY-MM-DD)
	         - pax         (required, integer)
	       Submit destination: villa listing page (wired via ibv_get_search_villas_url()).
	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>
	<form class="ibv-hero-search" method="get" action="<?php echo esc_url( ibv_get_search_villas_url() ); ?>">
		<div class="ibv-hero-search__inner">
			<div class="ibv-hero-search__field">
				<label class="ibv-hero-search__label" for="ibv-hero-from"><?php esc_html_e( 'Arrive', 'ibv' ); ?></label>
				<input class="ibv-hero-search__input" type="date" id="ibv-hero-from" name="date_from" required value="<?php echo esc_attr( $qs['date_from'] ); ?>">
			</div>
			<div class="ibv-hero-search__field">
				<label class="ibv-hero-search__label" for="ibv-hero-to"><?php esc_html_e( 'Depart', 'ibv' ); ?></label>
				<input class="ibv-hero-search__input" type="date" id="ibv-hero-to" name="date_to" required value="<?php echo esc_attr( $qs['date_to'] ); ?>">
			</div>
			<div class="ibv-hero-search__field">
				<label class="ibv-hero-search__label" for="ibv-hero-pax"><?php esc_html_e( 'Group Size', 'ibv' ); ?></label>
				<input class="ibv-hero-search__input" type="number" id="ibv-hero-pax" name="pax" min="1" max="30" required value="<?php echo esc_attr( $qs['pax'] ); ?>">
			</div>
			<button class="ibv-hero-search__submit" type="submit" aria-label="<?php esc_attr_e( 'Search villas', 'ibv' ); ?>">
				<svg class="ibv-hero-search__submit-icon" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
					<circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.5"/>
					<path d="M10.5 10.5L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
			</button>
		</div>
	</form>
	<?php /* ─────────── END BOB SHELL ─────────── */ ?>
	<?php
}
