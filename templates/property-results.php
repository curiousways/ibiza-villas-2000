    <?php
/**
 * Template Name: Property Results
**/

get_header(); ?>

<?php global $spanish_law; ?>


<?php

// Checks if there is query strings to get and if not sets a default value

	if(empty($_GET['location'])) {
		$property_location = array('san-antonio','ibiza-town','playa-den-bossa', 'san-rafel', 'san-josep', 'north-island');
	} else if(($_GET['location']) == 'all' ) {
		$property_location = array('san-antonio','ibiza-town','playa-den-bossa', 'san-rafel', 'san-josep', 'north-island');
	} else {
		$property_location = htmlspecialchars($_GET['location'],ENT_QUOTES);
	}

	if(empty($_GET['min'])) {
		$sleeps_minimum = '1';
	} else {
		$sleeps_minimum = htmlspecialchars($_GET['min'],ENT_QUOTES);
	}

	if(empty($_GET['max'])) {
		$sleeps_maximum = '99';
	} else {
		$sleeps_maximum = htmlspecialchars($_GET['max'],ENT_QUOTES);
	}


?>

<div class="row collapse full-width">
	<?php 
		get_template_part( 'templates/search-home-new' );
		
	?>
	</div>


<div class="row full-width you-searched-for">

	<div class="small-12 columns text-center">


	  	<h1 style="letter-spacing: 0px;">
			<?php
				echo 'You searched for villas';

				if ($sleeps_maximum == 99) {
					echo ' sleeping ' . $sleeps_minimum . '+ in ';
				} else {
					echo ' sleeping ' . $sleeps_minimum . '-' . $sleeps_maximum . ' in ';
				}

				if($property_location == array('san-antonio','ibiza-town','playa-den-bossa', 'san-rafel', 'san-josep', 'north-island')) {
				 	echo 'all locations';
				 } else {
				 	echo ucfirst($property_location);
				 }
     		?>
		</h1>



	    <div class="toggle-buttons results-map-toggle">
		    <a style="margin-right: 10px;" class="button hide-results-map active" href="#">View Map</a><a style="margin-left: 10px;" class="button show-results-map" href="#">Hide Map</a>
	    </div>


		<h3>We have listed multiple locations on various villas where appropriate<br>to help you find your ideal property based on their geography.</h3>

	</div>

</div>


		<?php get_template_part( 'loop' ); ?>


		<?php



	    if ($spanish_law == 'yes') {
			$args = array(
			    'post_type' => $property_type,
			    'posts_per_page' => -1,
				'property_location' => $property_location,
				'post__not_in'  => array('5610','2928'),
				'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
				'meta_value'	=> 0
				
			);
	    } else {
			$args = array(
			    'post_type' => $property_type,
			    'posts_per_page' => -1,
				'property_location' => $property_location,
				'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
				'meta_value'	=> 0
				
			);
	    } 			

		$property_query = new WP_Query( $args );

		if ( $property_query->have_posts() ) : ?>
			<div style="display:none" class="row map-container"><div class="acf-map">
			<?php while ( $property_query->have_posts() ): $property_query->the_post(); global $post; ?>
			<?php

				$location = get_field('property_map');
			


	            if ($spanish_law == 'yes') {
	            	$property_sleeps  = get_field('property_sleeps_spanish');
	            	$property_bedrooms = get_field('property_bedrooms_spanish');
	            	$property_bathrooms = get_field('property_bathrooms_spanish');
	            	$property_description = get_field('property_description_spanish');
	            } else {
	            	$property_sleeps  = get_field('property_sleeps');
	            	$property_bedrooms = get_field('property_bedrooms');
	            	$property_bathrooms = get_field('property_bathrooms');
	            	
	            }




				if( !empty($location) && ($property_sleeps >= $sleeps_minimum && $property_sleeps <= $sleeps_maximum) ): ?>

				<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
					<div class="marker-info">
						<div class="info">
							<div class="thumb">
								<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
							</div>
							
							<h3><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?>

								<?php 
									
									echo "- Sleeps " . $property_sleeps;

								?>

							</a></h3>
							
							<?php $location = wp_get_post_terms( $post->ID, 'property_location'); ?>
							

							<h4><?php echo $location[0]->name;?></h4>
							
							<?php
								if ( ! has_excerpt() ) {
									$trimmed = content(25);
								    echo wp_strip_all_tags($trimmed);
								} else {
								    $excerpt = excerpt(25);
								    echo $excerpt;
								}
							?>

							<br><a href="<?php the_permalink(); ?>" class="button">Find out more</a>
						</div><!-- /.info -->
					</div>
				</div>



				<?php endif; ?>

			<?php endwhile; ?>
			</div></div>



			<?php else : ?>

			<div class="row no-results">
			<div class="small-12 text-center columns">
				<div class="alert-box error"><h2><?php _e("Sorry, your search criteria didn't return any results"); ?></h2></div>
				<h4>Why not take a look at <a href="/property-results/">all our properties?</a></h4>
			</div>
			</div>

			<?php get_template_part( 'templates/explore' ); ?>


		<?php endif; ?>






		<?php wp_reset_query(); ?>

	</div>

</div>


<div class="site-content">

  	<div class="row">

		<?php


	    if ($spanish_law == 'yes') {
			$args = array(
			    'post_type' => $property_type,
			    'posts_per_page' => -1,
				'property_location' => $property_location,
				'post__not_in'  => array('5610','2928'),
				'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
				'meta_value'	=> 0
				
			);
	    } else {
			$args = array(
			    'post_type' => $property_type,
			    'posts_per_page' => -1,
				'property_location' => $property_location,
				'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
				'meta_value'	=> 0
				
			);
	    } 			


		$property_query = new WP_Query( $args );

		if ( $property_query->have_posts() ) : ?>
			<ul class="property-grid">

			<?php while ( $property_query->have_posts() ): $property_query->the_post(); global $post; ?>

				<?php

					$location 			= get_field('property_map');



		            if ($spanish_law == 'yes') {
		            	$property_sleeps  = get_field('property_sleeps_spanish');
		            	$property_bedrooms = get_field('property_bedrooms_spanish');
		            	$property_bathrooms = get_field('property_bathrooms_spanish');
		            	$property_description = get_field('property_description_spanish');
		            } else {
		            	$property_sleeps  = get_field('property_sleeps');
		            	$property_bedrooms = get_field('property_bedrooms');
		            	$property_bathrooms = get_field('property_bathrooms');
		            	
		            }





					if($property_sleeps >= $sleeps_minimum && $property_sleeps <= $sleeps_maximum):

				?>

					<li>

						<?php

							// get the inside of the grid loop to make life easier
							get_template_part( 'templates/loop-grid-part' );
						?>

					</li>

				<?php endif; ?>

			<?php endwhile; ?>

			</ul>

		<?php endif; ?>

		<?php wp_reset_query(); ?>

  	</div><!--/.row -->

</div>


<?php get_footer(); ?>