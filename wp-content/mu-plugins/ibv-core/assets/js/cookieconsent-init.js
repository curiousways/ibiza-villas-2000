/**
 * CookieConsent v3 configuration — Ibiza Villas 2000.
 *
 * Two categories only: `necessary` (read-only) and `analytics`. The library's
 * script-tag manager flips any `type="text/plain" data-category="analytics"`
 * tag on consent — that is what gates GA4 (see includes/integrations/
 * cookie-consent.php).
 *
 * Reference:
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run( {

	categories: {
		necessary: {
			enabled: true,
			readOnly: true
		},
		analytics: {}
	},

	guiOptions: {
		consentModal: {
			layout: 'box inline',
			position: 'bottom right'
		}
	},

	language: {
		default: 'en',
		translations: {
			en: {
				consentModal: {
					title: 'Cookies at Ibiza Villas 2000',
					description: 'This website uses essential cookies to ensure its proper operation and, with your consent, analytics cookies to understand how visitors use the site.',
					acceptAllBtn: 'Accept all',
					acceptNecessaryBtn: 'Reject all',
					showPreferencesBtn: 'Manage preferences'
				},
				preferencesModal: {
					title: 'Manage cookie preferences',
					acceptAllBtn: 'Accept all',
					acceptNecessaryBtn: 'Reject all',
					savePreferencesBtn: 'Accept current selection',
					closeIconLabel: 'Close modal',
					sections: [
						{
							title: 'Cookie usage',
							description: 'We use cookies to ensure the basic functionalities of the website and to enhance your online experience. You can opt in or out of each category whenever you want.'
						},
						{
							title: 'Strictly necessary cookies',
							description: 'These cookies are essential for the proper functioning of the website and cannot be disabled.',
							linkedCategory: 'necessary',
							cookieTable: {
								headers: {
									name: 'Cookie',
									domain: 'Domain',
									expiration: 'Expiration',
									description: 'Description'
								},
								body: [
									{
										name: 'cc_cookie',
										domain: 'This website',
										expiration: '6 months',
										description: 'Stores your cookie consent preferences.'
									}
								]
							}
						},
						{
							title: 'Performance and analytics',
							description: 'These cookies allow us to measure website traffic and understand how visitors interact with the site using Google Analytics. Data is anonymised.',
							linkedCategory: 'analytics',
							cookieTable: {
								headers: {
									name: 'Cookie',
									domain: 'Domain',
									expiration: 'Expiration',
									description: 'Description'
								},
								body: [
									{
										name: '_ga',
										domain: 'google.com',
										expiration: '2 years',
										description: 'Used to distinguish users for Google Analytics.'
									},
									{
										name: '_ga_*',
										domain: 'google.com',
										expiration: '2 years',
										description: 'Used to persist session state for Google Analytics.'
									}
								]
							}
						},
						{
							title: 'More information',
							description: 'See our <a href="/cookie-policy/">Cookie Policy</a> for full details, or <a href="/contact/">contact us</a> with any questions. Our <a href="/privacy-policy/">Privacy Policy</a> covers how we handle your data.'
						}
					]
				}
			}
		}
	}
} );

/**
 * Footer "Cookie preferences" reopen link — delegated listener, no inline
 * handlers. Any element carrying `data-ibv-cc-preferences` reopens the modal.
 */
document.addEventListener( 'click', function ( e ) {
	var trigger = e.target.closest ? e.target.closest( '[data-ibv-cc-preferences]' ) : null;
	if ( ! trigger ) {
		return;
	}
	e.preventDefault();
	CookieConsent.showPreferences();
} );
