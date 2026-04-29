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

				<div class="ibv-villa-detail__main">
					<?php
					ibv_core_section_villa_overview( $villa_id );
					ibv_core_villa_map( $villa_id );
					ibv_core_distance_ticks( $villa_id );
					ibv_core_gallery( $villa_id );
					ibv_core_section_concierge_cross_sell( __( 'Make the most of your stay', 'ibv' ) );
					ibv_core_section_villa_testimonial_teaser();
					ibv_core_section_villa_similar( $villa_id );
					?>
				</div>

				<aside class="ibv-villa-detail__sidebar">
					<?php ibv_core_enquiry_panel( $villa_id ); ?>
				</aside>

			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
