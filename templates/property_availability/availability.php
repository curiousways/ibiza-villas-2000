<?php

	// // Connect to the other DB
	//  $mydb = new wpdb('chamonix_TEST','TESTB1gf00t','chamonix_TEST','151.236.50.44');
	//  $mydb->show_errors();
	
	//  $start_date	= (!empty($_GET['startDate']) ? $_GET['startDate'] : "");
	//  $end_date	= (!empty($_GET['endDate']) ? $_GET['endDate'] : "");


	// // // construct date query
	//  if( !empty($start_date) && !empty($end_date) )  {
	//  	$start_date = date("Y-m-d",strtotime($start_date)); 
	//  	$end_date 	= date("Y-m-d",strtotime($end_date)); 


	//  	$date_query = 	"WHERE `startdate` >= '" . $start_date . "' AND `enddate` <= '". $end_date . "'";

	// // 	// Grab what we need
	//  	$rows = $mydb->get_results( 'SELECT * FROM `reservations` ' . $date_query, OBJECT );

	// // 	// Loop through it til we have what we need
	//  	$i = 0;
	//  	foreach ($rows as $row) {
	//  		if( $row->accom_id == $property_id) {
	//  			echo "<pre>";
	//  			print_r($row);
	//  			echo "</pre>";
	//  			$i++;
	//  		}
	//  	}
	//  }

	// // Take over the world...

?>