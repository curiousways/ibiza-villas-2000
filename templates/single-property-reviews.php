<?php 

// Property Reviews

	// iVilla DB Credentials
	$ivilla_database = "iv2000_live";
	$ivilla_username = "iv2k";
	$ivilla_password = "1v2kLIVE";

	// Connect to iVilla DB
	$ivilla = new wpdb($ivilla_username,$ivilla_password,$ivilla_database,'localhost');
	$ivilla->show_errors();

	// Get current property ID
    $villa_id = get_field('property_id');



    // Setup Review Queries and Variables

	// ratingQ4 - Standard of the Villa
 	$villa_standard = $ivilla->get_results( "SELECT ROUND(AVG(ratingQ4) * 4, 0) / 2 FROM `marketResearch` WHERE villa ='" . $villa_id . "'" );
	$villa_standard = $villa_standard[0];
	if ($villa_standard) {
		foreach($villa_standard as $k => $v) { $villa_standard = round($v,1); }
	} else { $villa_standard = 0; }

	// ratingQ5 - Furniture
 	$villa_furniture = $ivilla->get_results( "SELECT ROUND(AVG(ratingQ5) * 4, 0) / 2 FROM `marketResearch` WHERE villa ='" . $villa_id . "'" );
	$villa_furniture = $villa_furniture[0];
	if ($villa_furniture) {
		foreach($villa_furniture as $k => $v) { $villa_furniture = round($v,1); }
	} else { $villa_furniture = 0; }

	// ratingQ6 - Cleanliness
 	$villa_cleanliness = $ivilla->get_results( "SELECT ROUND(AVG(ratingQ6) * 4, 0) / 2 FROM `marketResearch` WHERE villa ='" . $villa_id . "'" );
	$villa_cleanliness = $villa_cleanliness[0];
	if ($villa_cleanliness) {
		foreach($villa_cleanliness as $k => $v) { $villa_cleanliness = round($v,1); }
	} else { $villa_cleanliness = 0; }

	// ratingQ7 - Facilities
 	$villa_facilities = $ivilla->get_results( "SELECT ROUND(AVG(ratingQ7) * 4, 0) / 2 FROM `marketResearch` WHERE villa ='" . $villa_id . "'" );
	$villa_facilities = $villa_facilities[0];
	if ($villa_facilities) {
		foreach($villa_facilities as $k => $v) { $villa_facilities = round($v,1); }
	} else { $villa_facilities = 0; }

	// ratingQ8 - Value for Money
 	$villa_value = $ivilla->get_results( "SELECT ROUND(AVG(ratingQ8) * 4, 0) / 2 FROM `marketResearch` WHERE villa ='" . $villa_id . "'" );
	$villa_value = $villa_value[0];
	if ($villa_value) {
		foreach($villa_value as $k => $v) { $villa_value = round($v,1); }
	} else { $villa_value = 0; }

	// Villa Overall Rating
	$villa_overall_rating = $villa_standard+$villa_furniture+$villa_cleanliness+$villa_facilities+$villa_value;
	$villa_overall_rating = $villa_overall_rating*2/10;

	// Villa Overall Rating Text
	if ($villa_value<=7.5) { 
		$villa_overall_rating_text = "Good";
	} elseif ($villa_value<=8.4) {
		$villa_overall_rating_text = "Very Good";
	} elseif ($villa_value>=8.5) {
		$villa_overall_rating_text = "Excellent";
	}

	// Villa Based on how many Ratings
 	$villa_rating_count = $ivilla->get_results( "SELECT COUNT(*) FROM `marketResearch` WHERE villa ='" . $villa_id . "'");
 	$villa_rating_count = json_encode($villa_rating_count);
	$villa_rating_count = json_decode($villa_rating_count,true);
	$villa_rating_count = $villa_rating_count[0]['COUNT(*)'];



    // If we get a villa ID and no ratings are 0 proceed
    if ($villa_id && $villa_overall_rating != 0 && $villa_standard != 0 && $villa_furniture != 0 && $villa_cleanliness != 0 && $villa_facilities != 0 && $villa_value != 0) {
    	

    	// create villa rating container
    	echo '<div class="single-villa-rating">';

			// Check if rating is above 0 to confirm data and display if so

			if ($villa_overall_rating != 0) {
				echo '<div class="rating overall">';
				echo '<span>' . $villa_overall_rating . '/10</span>';
				echo '<h3>'. $villa_overall_rating_text .' Overall</h3>';
				echo '<p>Based on ' . $villa_rating_count . ' reviews</p>';
				echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>';
			}
			if ($villa_standard != 0) {
				echo '<div class="rating standard">';
				echo '<span>' . $villa_standard . '/10</span>';
				echo '<i class="fa fa-star"></i> <h3>Villa Standard</h3>';
				echo '<p>Based on ' . $villa_rating_count . ' reviews</p>';
				echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>';
			}
			if ($villa_furniture != 0) {
				echo '<div class="rating furniture">';
				echo '<span>' . $villa_furniture . '/10</span>';
				echo '<i class="fa fa-star"></i> <h3>Villa Furniture</h3>';
				echo '<p>Based on ' . $villa_rating_count . ' reviews</p>';
				echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>';
			}
			if ($villa_cleanliness != 0) {
				echo '<div class="rating cleanliness">';
				echo '<span>' . $villa_cleanliness . '/10</span>';
				echo '<i class="fa fa-star"></i> <h3>Villa Cleanliness</h3>';
				echo '<p>Based on ' . $villa_rating_count . ' reviews</p>';
				echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>';
			}
			if ($villa_facilities != 0) {
				echo '<div class="rating facilities">';
				echo '<span>' . $villa_facilities . '/10</span>';
				echo '<i class="fa fa-star"></i> <h3>Villa Facilities</h3>';
				echo '<p>Based on ' . $villa_rating_count . ' reviews</p>';
				echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>';
			}
			if ($villa_value != 0) {
				echo '<div class="rating value">';
				echo '<span>' . $villa_value . '/10</span>';
				echo '<i class="fa fa-star"></i> <h3>Villa Value</h3>';
				echo '<p>Based on ' . $villa_rating_count . ' reviews</p>';
				echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>';
			}

		echo '</div>';
	 
	} // end if villa id

	// <span class="icon-star-full"></span>


 ?>
