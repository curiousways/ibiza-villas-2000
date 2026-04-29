<?php
/**
 * Component: Header search (Bob API shell).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render compact header search form.
 */
function ibv_core_header_search() {
	wp_enqueue_style( 'ibv-header-search' );
	wp_enqueue_style( 'ibv-button' );

	$qs = ibv_get_villa_listing_search_params();
	?>
	<?php /* ─────────────────────────────────────────────────────────────
	       BOB API INTEGRATION SHELL — header search
	       ─────────────────────────────────────────────────────────────
	       Form fields:
	         - date_from   (required, YYYY-MM-DD)
	         - date_to     (required, YYYY-MM-DD)
	         - pax         (required, integer)
	       Submit destination: villa listing page (wired via ibv_get_search_villas_url()).
	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>
	<form class="ibv-header-search" method="get" action="<?php echo esc_url( ibv_get_search_villas_url() ); ?>">
		<div class="ibv-header-search__field">
			<label class="ibv-header-search__label" for="ibv-hs-from"><?php esc_html_e( 'Arrive', 'ibv' ); ?></label>
			<input class="ibv-header-search__input" type="date" id="ibv-hs-from" name="date_from" required value="<?php echo esc_attr( $qs['date_from'] ); ?>">
		</div>
		<div class="ibv-header-search__field">
			<label class="ibv-header-search__label" for="ibv-hs-to"><?php esc_html_e( 'Depart', 'ibv' ); ?></label>
			<input class="ibv-header-search__input" type="date" id="ibv-hs-to" name="date_to" required value="<?php echo esc_attr( $qs['date_to'] ); ?>">
		</div>
		<div class="ibv-header-search__field">
			<label class="ibv-header-search__label" for="ibv-hs-pax"><?php esc_html_e( 'Group Size', 'ibv' ); ?></label>
			<input class="ibv-header-search__input" type="number" id="ibv-hs-pax" name="pax" min="1" max="30" required value="<?php echo esc_attr( $qs['pax'] ); ?>">
		</div>
		<?php
		ibv_core_button(
			[
				'tag'   => 'button',
				'type'  => 'submit',
				'label' => __( 'Search Villas', 'ibv' ),
				'variant' => 'primary',
				'size'    => 'small',
				'class'   => 'ibv-header-search__submit',
			]
		);
		?>
	</form>
	<?php /* ─────────── END BOB SHELL ─────────── */ ?>
	<?php
}
