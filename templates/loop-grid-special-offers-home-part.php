<?php 

global $spanish_law;

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

 ?>


<?php

	$locationsleeps = "";

	$sleeps 		= (!empty($sleeps) ? 'Sleeps ' . $sleeps : ''); // returns true

	$location 		= get_the_terms($post->ID,'property_location');
	$location 		= (!empty($location) ? $location[0]->name : ''); // returns true

	$location2 		= get_the_terms($post->ID,'property_location');
	$location2 		= (!empty($location2) ? $location2[1]->name : ''); // returns true

	$postTypeObj 	= get_post_type_object( get_post_type() );
	$type 			= $postTypeObj->labels->singular_name;

	// Location and sleeps, if either is empty don't print the /
	if(!empty($location) && !empty($location2) && !empty($sleeps)) {
		$locationsleeps = $location . ' - ' . $location2 . ' / ' . $sleeps;
	} else if(!empty($location) && !empty($sleeps)) {
		$locationsleeps = $location . ' / ' . $sleeps;
	} else {
		$locationsleeps = $location . $sleeps;
	}

?>



<div class="slider-property slider-<?php echo strtolower($type); ?>" style="margin: 0px 0px;width: 100%;object-fit: cover;">


<?php 


    $backgroundImg = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );


?>

		<div class="slider-top home" style="display: table; width: 100%; background: url('<?php echo($backgroundImg[0])?>'); background-repeat: no-repeat; background-size: cover; background-position-y: 50%;">

			<?php if ( has_post_thumbnail() ) { ?>

				<?php $images = get_field('property_images'); ?>




						<?php if ( has_post_thumbnail() ) { ?>

							

						<?php } else { ?>
							<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />
							</a>
						<?php } ?>


			<?php } else { ?>
				<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
					<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />

				</a>
			<?php } ?>
			
		<div class="medium-12 hero-title text-center columns">
		
 			<img class="hero-logo" src="/wp-content/themes/rudeibiza/images/rudeibiza-logo-white.svg" alt="Ibiza Villas 2000">
 			
			<?php 
	          $villa_pretty_name = get_field('villa_pretty_name'); 
	          $property_link = get_permalink();

	          if ($villa_pretty_name) {
	           
	        	echo '<span class="sliderh1 homeslider-villaname">' . $villa_pretty_name . '</span>';
	        	//echo '<span class="sliderh2 homeslider-villaname" style="color: #ecc237">' . $locationsleeps . '</span>';

	        	echo '<span class="sliderh2"><a href="' . $property_link . '" title="' . get_the_title() . '">' . get_the_title() . '</a></span>';
	        	echo '<a href="' . $property_link . '" class="button" tabindex="0">FIND OUT MORE</a>';
	            
	          
	          } else {
	        	echo '<h1><a href="' . $property_link . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
	          }

	        ?>
	        </div>
			

		</div>

</div>