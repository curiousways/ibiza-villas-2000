<?php global $spanish_law; ?>



<?php

	$locationsleeps = "";

    if ($spanish_law == 'yes') {
    	$property_sleeps  = get_field('property_sleeps_spanish');
    	$sleeps  = get_field('property_sleeps_spanish');
    	$property_bedrooms = get_field('property_bedrooms_spanish');
    	$property_bathrooms = get_field('property_bathrooms_spanish');
    	$property_description = get_field('property_description_spanish');
    } else {
    	$property_sleeps  = get_field('property_sleeps');
    	$sleeps  = get_field('property_sleeps');
    	$property_bedrooms = get_field('property_bedrooms');
    	$property_bathrooms = get_field('property_bathrooms');
    }



	

    $sleeps 		= (!empty($sleeps) ? 'Sleeps ' . $sleeps : '');
     // returns true

	$location 		= get_the_terms($post->ID,'property_location');
	$location 		= (!empty($location) ? $location[0]->name : ''); // returns true

	$location2 		= get_the_terms($post->ID,'property_location');
	$location2 		= (!empty($location2) ? $location2[1]->name : ''); // returns true

	$postTypeObj 	= get_post_type_object( get_post_type() );
	$type 			= $postTypeObj->labels->singular_name;

	// Location and sleeps, if either is empty don't print the /

	if(!empty($location) && !empty($location2) && !empty($sleeps)) {

		$locationsleeps = $location . ' - ' . $location2;


	} else if(!empty($location) && !empty($sleeps)) {

		$locationsleeps = $location;

	} else {

		$locationsleeps = $location;

		
		


	}

?>








<div class="slider-property slider-<?php echo strtolower($type); ?>">

		<div class="slider-top">

			<?php if ( has_post_thumbnail() ) { ?>

				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('property-featured-image'); ?></a>

			<?php } else { ?>
				<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
					<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" />
				</a>
			<?php } ?>

			<div class="slider-info">
				<?php get_template_part( 'templates/property-price-from-to' ); ?>
				<span class="slider-type"><?php echo $type; ?></span>
			</div>

		</div><!--  /slider top -->

		<div class="slider-detail">


	        <?php 
	          $villa_pretty_name = get_field('villa_pretty_name'); 
	          
			if ($property_sleeps > 12) {

				if ($villa_pretty_name) {
	           
	        	echo '<h4>' . $villa_pretty_name . '</h4>';

				echo '<p class="slider-location-sleeps">' . $locationsleeps .' - Sleeps 12+</p>';

				echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
	          
	          } else {
	        	echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
				echo '<p class="slider-location-sleeps">' . $locationsleeps . '</p>';
	          }


			}

			else{


				          

			if ($villa_pretty_name) {
	           
	        	echo '<h4>' . $villa_pretty_name . '</h4>';

				echo '<p class="slider-location-sleeps">' . $locationsleeps .' - Sleeps ' . $property_sleeps . '</p>';

				echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
	          
	          } else {
	        	echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
				echo '<p class="slider-location-sleeps">' . $locationsleeps . '</p>';
	          }
	         
}
 ?>



			<p class="property-grid-text">
			<?php

				if ($spanish_law == 'yes') {

					echo wp_trim_words($property_description, 50);

				} else {

					if (has_excerpt()) {
					    $excerpt = excerpt(50);
					    echo $excerpt;					
					} else {
						$trimmed = content(50);
					    echo wp_strip_all_tags($trimmed);
					}

				}



			?>
			</p>


			<a href="<?php the_permalink(); ?>" class="button">FIND OUT MORE</a>

		</div>

</div>


