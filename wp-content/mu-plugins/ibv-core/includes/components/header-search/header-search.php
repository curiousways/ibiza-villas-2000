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
	ibv_core_date_range_picker_enqueue();

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
	<form class="ibv-header-search" method="get" action="<?php echo esc_url( ibv_get_search_villas_url() ); ?>" data-bob-date-range="header">
		<div class="ibv-header-search__field ibv-header-search__field--when">
			<button type="button" class="ibv-header-search__when-trigger" id="ibv-hs-when" data-bob-date-range-trigger>
				<span class="ibv-header-search__label"><?php esc_html_e( 'When', 'ibv' ); ?></span>
				<span class="ibv-header-search__when-value" data-bob-date-range-display data-placeholder="<?php esc_attr_e( 'Add dates', 'ibv' ); ?>"><?php esc_html_e( 'Add dates', 'ibv' ); ?></span>
			</button>
			<button type="button" class="ibv-header-search__when-clear" data-bob-date-range-clear hidden aria-label="<?php esc_attr_e( 'Clear dates', 'ibv' ); ?>">
				<span aria-hidden="true">&times;</span>
			</button>
			<input type="hidden" name="date_from" required value="<?php echo esc_attr( $qs['date_from'] ); ?>" data-bob-date-from>
			<input type="hidden" name="date_to" required value="<?php echo esc_attr( $qs['date_to'] ); ?>" data-bob-date-to>
		</div>
		<div class="ibv-header-search__field">
			<label class="ibv-header-search__label" for="ibv-hs-pax"><?php esc_html_e( 'Group size', 'ibv' ); ?></label>
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
