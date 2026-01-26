<?php global $spanish_law; ?>


<?php

	/* Loop through posts with same location but exclude current post */

	$post_types = array('villas', 'apartments');


    if ($spanish_law == 'yes') {
		$args = array(
			'post_type' => $post_types,
			'orderby' => 'title',
			'showposts'=> '100',
			'meta_key'		=> 'property_featured',
			'meta_value'	=> 1,
	        'post__not_in'  => array('5610','2928'),
		);
    } else {
		$args = array(
			'post_type' => $post_types,
			'orderby' => 'title',
			'showposts'=> '100',
			'meta_key'		=> 'property_featured',
			'meta_value'	=> 1,
		);
    } 


	$featured_property = new WP_Query($args);

?>

<?php if ( $featured_property->have_posts() ) : ?>

	<h3 class="text-center"><?php _e('BROWSE OUR FEATURED ACCOMMODATION'); ?></h3>


	<div class="slider featured-properties">

		<?php while ( $featured_property->have_posts() ): $featured_property->the_post(); global $post; ?>

      		<?php 
      			// get the inside of the grid loop to make life easier 
      			get_template_part( 'templates/loop-grid-part' ); 
      		?>

		<?php endwhile; ?>

	</div>

<?php endif; ?>

<?php wp_reset_postdata(); ?>


