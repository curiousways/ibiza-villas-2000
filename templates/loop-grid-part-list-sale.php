<?php global $spanish_law; ?>
 <style type="text/css">

.props li { padding: 15px 20px;
    margin-bottom: 0px;
    display: inline-block;
   
    text-align: center;
    vertical-align: top;}
}

.slider-location-sleeps-two {
	font-size:20px;
}
.property-list .slider-detail {
    padding-top: 20px;
    padding-left: 0px!important;
    padding-right: 0px!important;
}


@media only screen and (min-width: 40.0625em){

h3 {
    font-size: 1.45rem;
}
}

h3 {
    font-size: 1.5rem;
    padding-top: 20px;
}

</style>

<?php

	$locationsleeps = "";

    if ($spanish_law == 'yes') {
    	$property_sleeps  = get_field('property_sleeps_spanish');
    	$sleeps  = get_field('property_bedrooms_spanish');
    	$property_bedrooms = get_field('property_bedrooms_spanish');
    	$property_bathrooms = get_field('property_bathrooms_spanish');
    	$property_description = get_field('property_description_spanish');
    } else {
    	$property_sleeps  = get_field('property_sleeps');
    	$sleeps  = get_field('property_bedrooms');
    	$property_bedrooms = get_field('property_bedrooms');
    	$property_bathrooms = get_field('property_bathrooms');
    }



	

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

		$locationsleeps = $location ;

	} else {

		$locationsleeps = $location ;

	}

?>




<div class="row" style="      margin-bottom: 40px;
    border-radius: 5px;">
			

			<div class="small-12 medium-12 large-4 columns">
				<?php if ( has_post_thumbnail() ) { ?>

				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('property-featured-image'); ?></a>

				<?php } else { ?>
					<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
						<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />
					</a>
				<?php } ?>
			</div>



			<div class="small-12 medium-12 large-4 columns">

				<?php 
				  $villa_pretty_name = get_field('villa_pretty_name'); 
				  $property_bedrooms = get_field('property_bedrooms_spanish');

				  if ($villa_pretty_name) {

					echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
					//echo '<h4>' . $villa_pretty_name . '</h4>'; 
					echo '<p class="slider-location-sleeps">' . $locationsleeps . '</p>';
					


				  } else {
					echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
					echo '<p class="slider-location-sleeps">' . $locationsleeps . '</p>';

				  }

				?>
				
				<div class="column small-12 medium-12 large-12" style="padding-left:0px!important;">
					
					


						<div class="column medium-12 large-12">
				<div class="slider-detail">
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
					}}?>
			</p>

		</div>
	</div>

				</div>
					</div>


					
						
				<div class="column small-12 medium-12 large-4" style="    padding-right: 40px;padding-top: 20px;">
					
				


					<div class="small-12 medium-12 large-12" style="padding-bottom: 10px;border-bottom: 1px solid #E8E8E8;padding-top: 20px;">
							<ul class="props" style="padding: 0;
		    				display: block;text-align: center;
		  					  flex-wrap: wrap;
		   						 margin-bottom: 0;">
		                                        <li style="">
		                        <div style="padding-top:5px;"><span class="icon-wa50-sup"></span><?php the_field( 'property_dimension' ); ?> m<sup>2</sup></div> 
		                    </li>
		                    

		                    <li style="">
		                        <div style="padding-top:5px;"><span class="icon-wa50-bed"><i style="padding-right: 5px;" class="fas fa-bed"></i></span><?php the_field( 'property_bedrooms_spanish' ); ?></div> 
		                       
		                    
		                    </li>
		                                                            

		                    <li style="border-right: none;">
		                        <div style="padding-top:5px;"><span class="icon-wa50-bath"><i style="padding-right: 5px;" class="fas fa-bath"></i></span><?php the_field( 'property_bathrooms_spanish' ); ?></div>
		                        
		                    </li>
		                                                                            </ul>
						</div>

	<div style="        padding-top: 20px;padding-bottom: 30px;" class="slider-info">
						<?php get_template_part( 'templates/property-price-sale' ); ?>
						
					</div>

					<a style="padding:10px 20px; width: 100%;" href="<?php the_permalink(); ?>" class="button" >SEE THIS PROPERTY</a>
				</div>
					
		  
</div>
<?php /*

<div class="slider-property slider-<?php echo strtolower($type); ?>">

		<div class="slider-top">

			<?php if ( has_post_thumbnail() ) { ?>

				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('property-featured-image'); ?></a>

			<?php } else { ?>
				<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>">
					<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />
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
	          
	          if ($villa_pretty_name) {
	           
	        	echo '<h4>' . $villa_pretty_name . '</h4>';
				echo '<p class="slider-location-sleeps">' . $locationsleeps . '</p>';
	        	echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
	            
	          
	          } else {
	        	echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
				echo '<p class="slider-location-sleeps">' . $locationsleeps . '</p>';
	        	
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

*/?>
