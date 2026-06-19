<?php
/**
 * Template Name: Accommodation
 *
 * Shared template for the Hotel + Airstream editorial pages. Looks like a villa
 * detail page (full-width hero → two-column main + right rail) but is NOT a
 * villa: no availability/pricing/RTB, not in the villas CPT. Composition:
 *   1. Hero        — reuses ibv_core_section_hero() (reads hero_* page ACF)
 *   2. Overview    — heading + body + free-text pills + "from" price
 *   3. Gallery     — reuses ibv_core_gallery() (reads property_images page ACF)
 *   4. Enquiry     — embedded Gravity Form + single-field date picker + chrome
 *
 * Reuses the villa-detail shell grid classes (.ibv-villa-detail*) for the
 * two-column layout; that CSS is enqueued for this template in shared-assets.php.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_hero( [ 'compact' => true ] );
	?>

	<article class="ibv-villa-detail">
		<div class="ibv-villa-detail__container ibv-container">
			<div class="ibv-villa-detail__grid">

				<div class="ibv-villa-detail__main">
					<?php
					ibv_core_section_accommodation_overview();
					ibv_core_gallery( get_the_ID() );
					?>
				</div>

				<aside class="ibv-villa-detail__sidebar">
					<?php ibv_core_section_accommodation_enquiry(); ?>
				</aside>

			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
