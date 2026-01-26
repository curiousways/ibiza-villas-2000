<?php

    $price_from_pounds = get_field('property_price_from_pounds'); 
    $price_to_pounds = get_field('property_price_to_pounds'); 
    $price_from_euros = get_field('property_price_from_euros'); 
    $price_to_euros = get_field('property_price_to_euros'); 

    $for_sale = get_field('property_for_sale');
        
    if ($price_from_euros || $price_from_pounds) {

        echo '<div class="property-price">';
         
		if($for_sale){
			echo "List Price" . '<br>';
			echo '€' . $price_from_euros . '<br>';
			echo '£' . $price_from_pounds . '<br>';
			
		}else{
         	echo '<h3>Weekly Prices</h3>';
         	echo '<p>(based on the season)</p>';
            if ($price_from_pounds && $price_to_pounds) {
              echo '<h3>£' . $price_from_pounds . ' - £' . $price_to_pounds . '<br>';
            } 

            if ($price_from_euros && $price_to_euros) {
              echo '€' . $price_from_euros . ' - €' . $price_to_euros . '</h3>';
            }
		}

        echo '</div>';

    }

?>
