/**
 * GA4 bootstrap — only ever executes after analytics consent (CookieConsent
 * flips the wrapping tag's `type` from text/plain to text/javascript and
 * re-inserts it). Never fires on local or staging hosts.
 *
 * The measurement ID here must stay in sync with the gtag loader printed by
 * includes/integrations/cookie-consent.php.
 */
( function () {
	var host = window.location.hostname;
	var blocked = [
		'ibiza-villas-2000.test',
		'localhost',
		'127.0.0.1',
		'staging.ibizavillas2000.com'
	];

	if ( blocked.indexOf( host ) !== -1 || host.indexOf( '.test' ) !== -1 ) {
		console.log( 'Google Analytics blocked — development/staging environment' );
		return;
	}

	window.dataLayer = window.dataLayer || [];
	function gtag() {
		window.dataLayer.push( arguments );
	}
	gtag( 'js', new Date() );
	gtag( 'config', 'G-NW8WQP42F9' );
} )();
