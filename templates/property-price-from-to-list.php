<?php 
// Price from / to simple fields

	$price_from_euros = get_field('property_price_from_euros'); 
    $price_from_pounds = get_field('property_price_from_pounds'); 

    if ($price_from_euros ) {
		$price_from_euros_day = floor($price_from_euros/7);
		$price_from_pounds_day = floor($price_from_pounds/7);
		
		


		echo '<div style="text-align: right!important;"class="column small-12 medium-12 large-12">';
	
		echo '<span style="float:left; font-size:12px;" class="list-from">From </span>';
		
		echo '<span class="list-from-number" style="text-align: right!important;font-size: 150%; color:#ffc029; font-weight: 800;">€' . $price_from_euros_day . ' / £' . $price_from_pounds_day . '</span> <span class="list-from">per day</span><br />';

		echo '<span class="list-from-number" style="padding-left: 0px; font-size: 150%; color:#ffc029; font-weight: 800;">€' . $price_from_euros . ' / £' . $price_from_pounds . '</span> <span style="font-size: 80%; " class="list-from">/week</span>';

		echo '</div>';

		


		
	
	}else{
		echo '<span class="list-from-number-nomads">Price upon request</span>';
	}

?>