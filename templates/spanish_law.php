<?php 
  	
  	require_once( TEMPLATEPATH . '/geoplugin/geoplugin.class.php');

  	// Default state
  	global $spanish_law;
	$spanish_law = 'no';

	// Set $spanish_law variable based on previously set cookie
	if (isset($_COOKIE['spanish_law']) && isset($_COOKIE['spanish_law_acs']) && $_COOKIE['spanish_law_acs'] == md5($_SERVER['REMOTE_ADDR']) ) {
		
		$spanish_law = $_COOKIE['spanish_law'];

	} else {

		// French Example for DEV only
		// $_SERVER['REMOTE_ADDR'] = '108.61.123.85';

		// Spanish Example for DEV only
		// $_SERVER['REMOTE_ADDR'] = '88.20.38.236';


		$geoplugin = new geoPlugin();
		$geoplugin->locate();

		if ($geoplugin->countryCode == "ES") {
			$spanish_law = 'yes';
		}

	}


	if (isset($_GET['country']) && $_GET['country'] === 'ES') {
		$spanish_law = 'yes';
	}

	if (isset($_GET['country']) && $_GET['country'] === 'EN') {
		$spanish_law = 'no';
	}

    $secure = ( 'https' === parse_url( wp_login_url(), PHP_URL_SCHEME ) );
    setcookie( 'spanish_law', $spanish_law, time()+86400, COOKIEPATH, COOKIE_DOMAIN, $secure );
    setcookie( 'spanish_law_acs', md5($_SERVER['REMOTE_ADDR']), time()+86400, COOKIEPATH, COOKIE_DOMAIN, $secure );
