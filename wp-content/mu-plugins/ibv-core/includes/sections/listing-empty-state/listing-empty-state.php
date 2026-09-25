<?php
/**
 * Section: Listing empty state (Bob toggles visibility).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Yellow band when no results.
 */
function ibv_core_section_listing_empty_state() {
	wp_enqueue_style( 'ibv-section-listing-empty-state' );
	wp_enqueue_style( 'ibv-button' );

	$search_url = ibv_get_search_villas_url();
	$contact    = ibv_get_contact_page_url();
	?>
	<section class="ibv-listing-empty-state" data-bob-empty-state hidden>
		<div class="ibv-container ibv-listing-empty-state__inner">
			<h2 class="ibv-listing-empty-state__title ibv-font-display"><?php esc_html_e( 'Nothing matching your search?', 'ibv' ); ?></h2>
			<span class="ibv-rule ibv-rule--gold" aria-hidden="true"></span>
			<p class="ibv-listing-empty-state__subtitle"><?php esc_html_e( 'Try adjusting your dates or group size.', 'ibv' ); ?></p>
			<div class="ibv-listing-empty-state__actions">
				<?php
				ibv_core_button(
					[
						'url'     => $search_url,
						'label'   => __( 'View all villas', 'ibv' ),
						'variant' => 'primary-inverse',
						'arrow'   => false,
					]
				);
				ibv_core_button(
					[
						'url'     => $contact,
						'label'   => __( 'Contact us', 'ibv' ),
						'variant' => 'secondary',
						'arrow'   => false,
					]
				);
				?>
			</div>
		</div>
	</section>
	<?php
}
