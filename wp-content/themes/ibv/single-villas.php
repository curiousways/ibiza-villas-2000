<?php
/**
 * Single villas CPT template.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$villa_id = get_the_ID();
	?>

	<article class="ibv-villa-detail">

		<?php ibv_core_section_villa_hero( $villa_id ); ?>

		<div class="ibv-villa-detail__container ibv-container">
			<div class="ibv-villa-detail__grid">

				<?php // --villa modifier: roomier section gap than the accommodation pages, which share this shell. ?>
				<div class="ibv-villa-detail__main ibv-villa-detail__main--villa">
					<?php
					/*
					 * TODO villa-detail-page-rebuild:
					 * The villa detail page is due a full rebuild against
					 * wireframe v0.4 (hero / sticky bar / overview / pricing
					 * / location / enquiry form). When that work runs,
					 * reposition this accordion to live at the top of the
					 * overview block as designed. For now, it renders inline
					 * at the top of the existing template so the data +
					 * component are live for editorial use.
					 */
					ibv_core_section_villa_header( $villa_id );
					ibv_core_villa_offers( [ 'post_id' => $villa_id ] );
					ibv_core_section_villa_overview( $villa_id );
					ibv_core_section_villa_location( $villa_id );
					ibv_core_gallery( $villa_id );
					?>
				</div>

				<aside class="ibv-villa-detail__sidebar">
					<?php ibv_core_enquiry_panel( $villa_id ); ?>
				</aside>

			</div>
		</div>

		<?php
		// Everything below the gallery sits outside the two-column grid so it
		// renders full-width at the page container, not constrained to the
		// narrower main column. Concierge is the lead-in to that band; the
		// related/testimonial sections follow.
		ibv_core_section_concierge_cross_sell( __( 'Make the most of your stay', 'ibv' ) );
		ibv_core_section_villa_testimonial_teaser();
		ibv_core_section_villa_similar( $villa_id );
		?>
	</article>

	<?php
endwhile;

get_footer();
