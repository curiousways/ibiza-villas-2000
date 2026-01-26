<?php 
// Price from / to simple fields

	$price_from_euros = get_field('property_price_from_euros'); 
    $price_from_pounds = get_field('property_price_from_pounds'); 

    if ($price_from_euros ) {
		$price_from_euros_day = floor($price_from_euros);
		$price_from_pounds_day = floor($price_from_pounds);
		echo '<span class="list-from">List Price </span><span class="list-from-number" style="font-size: 160%; color:#ffc029; font-weight: 800;">€' . $price_from_euros_day . ' / £' . $price_from_pounds_day . '</span> <span class="list-from"></span><br />';
	}else{
		echo '<span class="list-from-number">Price upon request</span>';
	}

?>