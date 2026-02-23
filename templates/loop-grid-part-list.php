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



	$sleeps 		= (!empty($sleeps) ? 'Sleeps ' . $sleeps : ''); // returns true

	$location_terms = get_the_terms($post->ID, 'property_location');
	$location       = (is_array($location_terms) && isset($location_terms[0]) && is_object($location_terms[0])) ? $location_terms[0]->name : '';
	$location2      = (is_array($location_terms) && isset($location_terms[1]) && is_object($location_terms[1])) ? $location_terms[1]->name : '';

	$postTypeObj 	= get_post_type_object( get_post_type() );
	$type 			= $postTypeObj->labels->singular_name;

	// Location and sleeps, if either is empty don't print the /
	if(!empty($location) && !empty($location2)) {

		$locationsleeps = $location . ' - ' . $location2 . ' ';

	} else if(!empty($location) ) {

		$locationsleeps = $location . '  ' ;

	} else {

		$locationsleeps = $location . $sleeps;

	}

?>



<div style=" " class="small-12 medium-12 large-4 columns nomads-grid">
		
    <div style=" " class="small-12 medium-12 large-12 columns nomads-grid-item">

									<div style="padding-left: 0rem!important; padding-right: 0rem!important" class="small-12 medium-12 large-12 "columns>
											<?php if ( has_post_thumbnail() ) { ?>

											<a href="<?php the_permalink(); ?>" title="Ibiza Villas 2000 - See this Ibiza Villa to Rent" 	alt="Ibiza Villas 2000 - See this Ibiza Villa for Rent"><?php the_post_thumbnail('property-featured-image'); ?></a>

											<?php } else { ?>
												<a href="<?php echo get_permalink(); ?>" title="Ibiza Villas 2000 - See this Ibiza Villa" alt="Ibiza Villas 2000 - See this Ibiza Villa to Rent">
													<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" />
												</a>
											<?php } ?>
									</div>



		<div style="padding-left: 0.9375rem; padding-right: 0.9375rem;" class="nomads-grid-title column medium-12 large-12">

												<div class="column medium-12 large-12">
															<?php 
															  $villa_pretty_name = get_field('villa_pretty_name'); 

															  if ($villa_pretty_name) {
															  	echo '<h4 style="display:none;">' . $villa_pretty_name . '</h4>';
																echo '<h3><a style="color:#1B5167;" href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
																
																echo '<p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;" class="fas fa-map-marker-alt"></i>' . $locationsleeps . '</p>';
															  } else {
																echo '<h3><a style="color:#37B89A!important;" href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
																echo '<p style="color:#37B89A!important;" class="slider-location-sleeps">' . $locationsleeps . '</p>';
															}?>

												</div>



					<div class="column small-12 medium-12 large-12 nomads-from-price">

							<?php


							if ($spanish_law == 'yes') {
								$property_bedrooms_spanish = get_field('property_bedrooms_spanish');
								$property_sleeps_spanish = get_field('property_sleeps_spanish');
								$property_bathrooms_spanish = get_field('property_bathrooms_spanish');
								
								if ($property_sleeps_spanish) {
									

									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style:"padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-user-friends"></i> ' . $property_sleeps_spanish . '</p>';
									echo '</div>';

								}
								if ($property_bathrooms_spanish) {

									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-bath"></i>  ' . $property_bathrooms_spanish . '</p>';
									echo '</div>';
								}
								
								if ($property_bedrooms_spanish) {

									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-bed"></i>  ' . $property_bedrooms_spanish . '</p>';
									echo '</div>';
								}
								

							} else {
								

								$property_bedrooms = get_field('property_bedrooms');
								
								if ($property_sleeps > 12) 
									
									{
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-user-friends"></i> 12+</p>';
									echo '</div>';
								}			
									/* { !
									echo "<p>Sleeps 12</b></p>" ;
						            echo "<p>Please note:</b> If you are a large group of 12 or more people please email us at<br>" ;
						            echo '<a href="mailto:bookings@ibizavillas2000.com">bookings@ibizavillas2000.com</a></p>';
								}*/
								

								else {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-user-friends"></i>  ' . $property_sleeps . '</p>';
									echo '</div>';
								}
								
								$property_bathrooms = get_field('property_bathrooms');

								if ($property_bathrooms) {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-bath"></i> ' . $property_bathrooms . '</p>';
									echo '</div>';
								}	
								
								if ($property_bedrooms) {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-bed"></i>  ' . $property_bedrooms . '</p>';
									echo '</div>';
								}
								
							}


						                    ?>



					</div>

					<div class="column medium-12 large-12 nomads-from-price">
						
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

					<div class="column small-12 medium-12 large-12 nomads-from-price">
						<div class="lider-info">
							<?php get_template_part( 'templates/property-price-from-to-list' ); ?>
							
						</div>
					</div>


					<div class="nomads-grid-button column small-12 medium-12 large-12" style="padding-top: 20px;">


						<a style="padding:10px;width: 100%!important;" href="<?php the_permalink(); ?>" class="button butds" data-hover="CHECK THIS VILLA">ENQUIRE</a>

					</div>
				
		</div>

	</div>
		
</div>
<?php ?>
