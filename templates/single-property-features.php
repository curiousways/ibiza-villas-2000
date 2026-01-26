<!-- Single Property Features List with Icons -->



    <?php  include( TEMPLATEPATH . '/templates/spanish_law.php'); 


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


    ?>




<?php




if( have_rows('property_features') ):

	echo '<div class="property-features"><h3>Property Facts</h3><ul>';


	if ($spanish_law == 'yes') {
		$property_bedrooms_spanish = get_field('property_bedrooms_spanish');
		$property_sleeps_spanish = get_field('property_sleeps_spanish');
		$property_bathrooms_spanish = get_field('property_bathrooms_spanish');
		
		if ($property_bathrooms_spanish) {
			echo '<li>Sleeps ' . $property_sleeps_spanish . '</li>';
		}
		if ($property_bathrooms_spanish) {
			echo '<li>' . $property_bedrooms_spanish . ' Bedrooms</li>';
		}
		if ($property_bathrooms_spanish) {
			echo '<li>' . $property_bathrooms_spanish . ' Bathrooms</li>';
		}

	} else {
		$property_bedrooms = get_field('property_bedrooms');
		if ($property_sleeps > 12) {
			echo '<li>Sleeps 12+</li>';
		}
		else {
			echo '<li>Sleeps ' . $property_sleeps . '</li>';
		}
		$property_bathrooms = get_field('property_bathrooms');


		
		if ($property_bathrooms) {
			echo '<li>' . $property_bedrooms . ' Bedrooms</li>';
		}
		if ($property_bathrooms) {
			echo '<li>' . $property_bathrooms . ' Bathrooms</li>';
		}	

	}

    while ( have_rows('property_features') ) : the_row();

		$icon = get_sub_field('property_feature_icon');
        $feature = get_sub_field('property_feature_text');

        echo '<li class="' . $icon . '">' . $feature . '</li>';

    endwhile;

echo '<ul></div>';


endif;

?>





