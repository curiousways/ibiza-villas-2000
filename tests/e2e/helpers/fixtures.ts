import { test as base, expect } from '@playwright/test';

/**
 * "Zero console errors is a pass criterion on every single test" —
 * docs/testing/bob-e2e-test-suite.md, section 0. This auto-fixture
 * collects console errors and uncaught page errors on every page and
 * fails the test at the end if any were seen.
 *
 * Exclusions must be deliberate: add a regex with a comment explaining
 * why the noise is acceptable (and on which environment).
 */
const IGNORED_CONSOLE_PATTERNS: RegExp[] = [
	// The villa map uses a Cloud-styled (vector) Map ID. Headless test
	// browsers have no WebGL, so Google Maps logs this error and falls
	// back to raster — cosmetic in tests, on every environment.
	/Attempted to load a Vector Map, but failed/,
	// Same headless-maps noise, WebKit flavour: Google Maps loads worker
	// scripts from blob: URLs, which headless WebKit refuses.
	/WebKitBlobResource error 1/,
];

/**
 * Local runs only: card price hydration calls the live Bob API
 * (ibizavillas2000.co.uk/cgi-bin/api) cross-origin from the .test site,
 * which the browser blocks with CORS errors. On staging the request is
 * expected to succeed, so these patterns are NOT ignored there — the
 * guard stays strict where the suite's sign-off happens.
 */
const LOCAL_ONLY_IGNORED_PATTERNS: RegExp[] = [
	/ibizavillas2000\.co\.uk\/cgi-bin\/api/,
	/Failed to load resource: net::ERR_FAILED/,
	// WebKit words the same CORS failure differently, without the URL.
	/is not allowed by Access-Control-Allow-Origin/,
	/due to access control checks/,
	// WebKit denies speculative prefetch on plain-HTTP origins; the local
	// site is http:// while staging is https://, so local-only.
	/Prefetch request denied: URL must be secure/,
];

export const test = base.extend< { _consoleGuard: void } >( {
	_consoleGuard: [
		async ( { page, baseURL }, use ) => {
			// The theme's scroll-behavior: smooth makes Playwright's
			// scroll-into-view animate, so elements report "not stable" and
			// clicks time out. The site's own reduced-motion query flips it
			// to auto. The config-level `reducedMotion` option is not
			// honoured by this Playwright version, hence the explicit call.
			await page.emulateMedia( { reducedMotion: 'reduce' } );

			const isLocal = /\.test(\/|$)/.test( baseURL || '' );
			const errors: string[] = [];
			const isIgnored = ( haystack: string ) =>
				IGNORED_CONSOLE_PATTERNS.some( ( re ) => re.test( haystack ) ) ||
				( isLocal &&
					LOCAL_ONLY_IGNORED_PATTERNS.some( ( re ) => re.test( haystack ) ) );

			page.on( 'console', ( msg ) => {
				if ( msg.type() !== 'error' ) {
					return;
				}
				const haystack = `${ msg.text() } ${ msg.location()?.url || '' }`;
				if ( ! isIgnored( haystack ) ) {
					errors.push( msg.text() );
				}
			} );
			// WebKit surfaces CORS failures as page errors too, so these
			// go through the same filter.
			page.on( 'pageerror', ( err ) => {
				if ( ! isIgnored( err.message ) ) {
					errors.push( `pageerror: ${ err.message }` );
				}
			} );

			await use();

			expect(
				errors,
				'zero console errors is a pass criterion (suite section 0)'
			).toEqual( [] );
		},
		{ auto: true },
	],
} );

export { expect };
