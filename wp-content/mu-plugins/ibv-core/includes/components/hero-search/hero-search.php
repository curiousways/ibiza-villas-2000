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
	ibv_core_date_range_picker_enqueue();

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
	<?php // GET submits keep the action URL's fragment, so the landing page anchors on the results grid. ?>
	<form class="ibv-hero-search" method="get" action="<?php echo esc_url( ibv_get_search_villas_url() . '#results' ); ?>" data-bob-date-range="hero">
		<div class="ibv-hero-search__inner">
			<div class="ibv-hero-search__field ibv-hero-search__field--when">
				<button type="button" class="ibv-hero-search__when-trigger" id="ibv-hero-when" data-bob-date-range-trigger>
					<span class="ibv-hero-search__label"><?php esc_html_e( 'When', 'ibv' ); ?></span>
					<span class="ibv-hero-search__when-value" data-bob-date-range-display data-placeholder="<?php esc_attr_e( 'Add dates', 'ibv' ); ?>"><?php esc_html_e( 'Add dates', 'ibv' ); ?></span>
				</button>
				<button type="button" class="ibv-hero-search__when-clear" data-bob-date-range-clear hidden aria-label="<?php esc_attr_e( 'Clear dates', 'ibv' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<input type="hidden" name="date_from" required value="<?php echo esc_attr( $qs['date_from'] ); ?>" data-bob-date-from>
				<input type="hidden" name="date_to" required value="<?php echo esc_attr( $qs['date_to'] ); ?>" data-bob-date-to>
			</div>
			<div class="ibv-hero-search__field">
				<label class="ibv-hero-search__label" for="ibv-hero-pax"><?php esc_html_e( 'Group size', 'ibv' ); ?></label>
				<span class="ibv-hero-search__select-wrap">
					<select class="ibv-hero-search__input ibv-hero-search__input--select" id="ibv-hero-pax" name="pax" required>
						<option value="" disabled hidden<?php selected( $qs['pax'], '' ); ?>><?php esc_html_e( 'Select group size', 'ibv' ); ?></option>
						<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>"<?php selected( $qs['pax'], (string) $i ); ?>><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
					<?php
					echo ibv_core_icon(
						'chevron-down',
						[
							'class' => 'ibv-hero-search__select-icon',
							'size'  => 16,
						]
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</span>
			</div>
			<button class="ibv-hero-search__submit" type="submit" aria-label="<?php esc_attr_e( 'Search villas', 'ibv' ); ?>">
				<?php
				echo ibv_core_icon(
					'search',
					[
						'class' => 'ibv-hero-search__submit-icon',
						'size'  => 18,
					]
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</button>
		</div>
	</form>
	<?php /* ─────────── END BOB SHELL ─────────── */ ?>
	<?php
}
