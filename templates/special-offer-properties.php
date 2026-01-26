<?php global $spanish_law; ?>


<?php
	/* Loop through posts with same location but exclude current post */
	$post_types = array('villas');




    if ($spanish_law == 'yes') {
		$args = array(
			'post_type' => $post_types,
			'orderby' => 'title',
			'showposts'=> '99',
			'meta_key'		=> 'property_special_offer_display',
			'meta_value'	=> 1,
	        'post__not_in'  => array('5610','2928'),
		);
    } else {
		$args = array(
			'post_type' => $post_types,
			'orderby' => 'title',
			'showposts'=> '99',
			'meta_key'		=> 'property_special_offer_display',
			'meta_value'	=> 1,
		);
    } 


	$special_offer_properties = new WP_Query($args);

?>

<?php if ( $special_offer_properties->have_posts() ) : ?>

	<div class="collapse special-offer-properties">
	
		<div class="row">
			<h3 class="text-center"><?php _e('BROWSE OUR SPECIAL OFFERS'); ?></h3>
			


			<div class="slider">

				<?php while ( $special_offer_properties->have_posts() ): $special_offer_properties->the_post(); global $post; ?>

		      		<?php 
		      			// get the inside of the grid loop to make life easier 
		      			get_template_part( 'templates/loop-grid-special-offers-part' ); 
		      		?>

				<?php endwhile; ?>

			</div>
		</div>
	</div>
<?php endif; ?>

<?php wp_reset_postdata(); ?>


