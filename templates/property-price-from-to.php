
<?php 
// Price from / to simple fields

	$price_from_euros = get_field('property_price_from_euros'); 
	$price_to_euros = get_field('property_price_to_euros'); 
   
	if ($price_from_euros && $price_to_euros) {
		echo '<span class="slider-from">€' . $price_from_euros . ' - €' . $price_to_euros . ' / wk</span>';
	} elseif ($price_from_euros && $price_to_euros <= 0) {
		echo '<span class="slider-from">From €' . $price_from_euros . ' / wk</span>';
	} elseif( $price_to_euros ) {
		echo '<span class="slider-from">Upto €' . $price_to_euros . ' / wk</span>';
	}

?>