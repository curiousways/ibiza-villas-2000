<?php

$locationsleeps = '';

$property_sleeps = get_field('property_sleeps');
$sleeps = get_field('property_sleeps');
$property_bedrooms = get_field('property_bedrooms');
$property_bathrooms = get_field('property_bathrooms');

$sleeps = (!empty($sleeps) ? 'Sleeps ' . $sleeps : '');

$location_terms = get_the_terms($post->ID, 'property_location');
$location       = (is_array($location_terms) && isset($location_terms[0]) && is_object($location_terms[0])) ? $location_terms[0]->name : '';
$location2      = (is_array($location_terms) && isset($location_terms[1]) && is_object($location_terms[1])) ? $location_terms[1]->name : '';

$postTypeObj = get_post_type_object(get_post_type());
$type        = $postTypeObj->labels->singular_name;

if (!empty($location) && !empty($location2)) {
	$locationsleeps = $location . ' - ' . $location2 . ' ';
} elseif (!empty($location)) {
	$locationsleeps = $location . '  ';
} else {
	$locationsleeps = $location . $sleeps;
}

?>



<div style=" " class="small-12 medium-12 large-4 columns nomads-grid">
		
    <div style=" " class="small-12 medium-12 large-12 columns nomads-grid-item">

									<div style="padding-left: 0rem!important; padding-right: 0rem!important" class="small-12 medium-12 large-12 columns">
											<?php if ( has_post_thumbnail() ) { ?>

											<a href="<?php the_permalink(); ?>" title="Ibiza Villas 2000 - See this Ibiza Villa to Rent"	alt="Ibiza Villas 2000 - See this Ibiza Villa for Rent"><?php the_post_thumbnail('iv2000_420x280'); ?></a>

											<?php } else { ?>
												<a href="<?php echo esc_url( get_permalink() ); ?>" title="Ibiza Villas 2000 - See this Ibiza Villa" alt="Ibiza Villas 2000 - See this Ibiza Villa to Rent">
													<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />
												</a>
											<?php } ?>
									</div>



		<div style="padding-left: 0.9375rem; padding-right: 0.9375rem;" class="nomads-grid-title column medium-12 large-12">

												<div class="column medium-12 large-12">
															<?php
															  $villa_pretty_name = get_field('villa_pretty_name'); 

															  if ($villa_pretty_name) {
																echo '<h4 style="display:none;">' . esc_html( $villa_pretty_name ) . '</h4>';
																echo '<h3><a style="color:#1B5167;" href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
																echo '<p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;" class="fas fa-map-marker-alt"></i>' . esc_html( $locationsleeps ) . '</p>';
															  } else {
																echo '<h3><a style="color:#37B89A!important;" href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
																echo '<p style="color:#37B89A!important;" class="slider-location-sleeps">' . esc_html( $locationsleeps ) . '</p>';
															}
															?>

												</div>



					<div class="column small-12 medium-12 large-12 nomads-from-price">

							<?php

								$property_bedrooms = get_field('property_bedrooms');
								
								if ($property_sleeps > 12) {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-user-friends"></i> 12+</p>';
									echo '</div>';
								} else {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-user-friends"></i>  ' . esc_html( $property_sleeps ) . '</p>';
									echo '</div>';
								}
								
								$property_bathrooms = get_field('property_bathrooms');

								if ($property_bathrooms) {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-bath"></i> ' . esc_html( $property_bathrooms ) . '</p>';
									echo '</div>';
								}
								
								if ($property_bedrooms) {
									echo '<div style="text-align:center;" class="column small-4 medium-4 large-4">';
									echo '<p style="padding-left:0px!important;padding-right:0px!important;"><i style="color:#A5ACB5;" class="fas fa-bed"></i>  ' . esc_html( $property_bedrooms ) . '</p>';
									echo '</div>';
								}

							?>

					</div>

					<div class="column medium-12 large-12 nomads-from-price">
						
								<div class="slider-detail">

									<p class="property-grid-text">
									<?php
										if (has_excerpt()) {
											echo esc_html( excerpt(50) );
										} else {
											$trimmed = content(50);
											echo esc_html( wp_strip_all_tags( $trimmed ) );
										}
										?>
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
