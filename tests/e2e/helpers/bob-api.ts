import type { Route } from '@playwright/test';

/**
 * Mocking helpers for Steve's PMS endpoint (ibv_get_bob_endpoint_url()):
 *   https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
 *
 * Two request shapes hit the same endpoint:
 *   - listing search / probe (villa-listing-grid.js buildUrl()):
 *       ?date_from=YYYY-MM-DD&date_to=YYYY-MM-DD&pax=N
 *   - enquiry-panel detail pricing (enquiry-panel.js fetchPricing()):
 *       ?villa={property_id}&date_from=&date_to=&pax=
 *
 * Response shape both scripts parse (per the AGENTS.md contract quoted in
 * enquiry-panel.js):
 *   { success, count, query: { nights }, villas: [ {
 *       villa, available, eur_base_rental, eur_total_price,
 *       eur_adw_amount, eur_extra_cleaning, ... } ] }
 * A row only counts when `available` is 1 (or absent); both scripts
 * normalise eur_base_rental to a per-week figure via query.nights.
 */
export const BOB_API_GLOB = '**/cgi-bin/api/web_availability.pl*';

/**
 * Fulfil a routed Bob API request with a JSON body. The endpoint is
 * cross-origin from the site under test (listing preload uses
 * crossorigin="anonymous", enquiry fetch uses credentials: 'omit'), so
 * the mocked response must carry a permissive CORS header or Firefox /
 * WebKit reject the fulfilled response at the CORS check.
 */
export function bobJson( route: Route, body: unknown, status = 200 ): Promise< void > {
	return route.fulfill( {
		status,
		contentType: 'application/json',
		headers: { 'access-control-allow-origin': '*' },
		body: JSON.stringify( body ),
	} );
}

/**
 * A manually-resolved gate, used to hold a mocked response open while the
 * test asserts the pre-response state (skeletons, pending masks).
 */
export function deferred(): { promise: Promise< void >; resolve: () => void } {
	let resolve!: () => void;
	const promise = new Promise< void >( ( res ) => {
		resolve = res;
	} );
	return { promise, resolve };
}
