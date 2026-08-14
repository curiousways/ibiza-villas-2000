import type { Page } from '@playwright/test';
import { test, expect } from '../helpers/fixtures';
import { addDays, saturdayAtLeastDaysOut, ymd } from '../helpers/date-picker';
import { BOB_API_GLOB, bobJson, deferred } from '../helpers/bob-api';
import { LISTING_PATH } from '../helpers/test-data';

/**
 * Suite section 2 — villa listing grid (browse + search modes).
 * docs/testing/bob-e2e-test-suite.md
 *
 * WRITTEN FROM SOURCE, NOT YET EXECUTED — see the tranche 2 note in
 * tests/e2e/README.md.
 *
 * The Bob API endpoint is mocked with page.route() so availability and
 * pricing are deterministic. Contract (villa-listing-grid.js):
 *   request  {endpoint}?date_from&date_to&pax   — search AND probe mode
 *   response { success, count, query: { nights }, villas: [
 *              { villa: property_id, available: 1, eur_base_rental } ] }
 * parseResults() drops rows whose `available` isn't 1, reads the pid from
 * villa/property_id/propertyId/id, and normalises eur_base_rental to a
 * weekly rate via query.nights. applyResults() ignores rates <= 0 (the
 * out-of-season "€0" guard) and, in search mode, hides cards whose pid is
 * not in the response and sorts survivors price-ascending.
 */

// Saturday-to-Saturday week, comfortably in the future.
const FROM = ymd( saturdayAtLeastDaysOut( 45 ) );
const TO = ymd( addDays( saturdayAtLeastDaysOut( 45 ), 7 ) );
const SEARCH_URL = `${ LISTING_PATH }?date_from=${ FROM }&date_to=${ TO }&pax=4`;

const GRID = '[data-bob-listing-grid]';
const CARD = `${ GRID } article[data-bob-property-id]`;
const CHIP = '[data-bob-selected-dates]';

interface CardInfo {
	pid: string;
	priceText: string;
	hasOffer: boolean;
}

/**
 * Read the server-rendered cards (pid, static price text, offer flag).
 * Safe to call from inside a route handler: the routed request is the
 * API fetch (preloaded from <head>), not the document, so the card
 * markup streams in regardless of whether the mock has responded yet.
 * Cards without a property_id can never match an API row, so they are
 * excluded up front. So are cards whose property_id appears on MORE
 * THAN ONE card: villas 16699 and 4473 (a known probable-duplicate
 * listing) both carry pid "peppe", which breaks strict-mode locators
 * and makes visible-card counts ambiguous — a returned pid hydrates
 * and un-hides every card that carries it.
 */
async function scrapeCards( page: Page ): Promise< CardInfo[] > {
	await page.locator( CARD ).first().waitFor( { state: 'attached' } );
	const all = await page.locator( CARD ).evaluateAll( ( els ) =>
		els.map( ( el ) => ( {
			pid: el.getAttribute( 'data-bob-property-id' ) || '',
			priceText: ( el.querySelector( '[data-bob-from-price]' )?.textContent || '' ).trim(),
			hasOffer: el.hasAttribute( 'data-bob-has-offer' ),
		} ) )
	);
	const pidCounts = new Map< string, number >();
	for ( const c of all ) {
		pidCounts.set( c.pid, ( pidCounts.get( c.pid ) || 0 ) + 1 );
	}
	return all.filter( ( c ) => c.pid !== '' && pidCounts.get( c.pid ) === 1 );
}

function listingResponse( rows: Array< Record< string, unknown > > ) {
	return { success: 1, count: rows.length, query: { nights: 7 }, villas: rows };
}

function card( page: Page, pid: string ) {
	return page.locator( `${ GRID } article[data-bob-property-id="${ pid }"]` );
}

function visibleCards( page: Page ) {
	return page.locator( `${ CARD }:not([hidden])` );
}

/**
 * Expected toolbar chip label — mirrors ibv_villa_listing_format_range()
 * (villa-listing-grid.php), which itself mirrors en-GB Intl output
 * including the Sep → Sept patch, plus the " · %d guests" suffix.
 */
const CHIP_MONTHS = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' ];
function chipLabel( fromYMD: string, toYMD: string, pax: number ): string {
	const [ fy, fm, fd ] = fromYMD.split( '-' ).map( Number );
	const [ ty, tm, td ] = toYMD.split( '-' ).map( Number );
	const md = ( d: number, m: number ) => `${ d } ${ CHIP_MONTHS[ m - 1 ] }`;
	let range: string;
	if ( fy === ty && fm === tm ) {
		range = `${ md( fd, fm ) } – ${ td }`;
	} else if ( fy === ty ) {
		range = `${ md( fd, fm ) } – ${ md( td, tm ) }`;
	} else {
		range = `${ md( fd, fm ) } – ${ md( td, tm ) } ${ ty }`;
	}
	return `${ range } · ${ pax } guest${ pax === 1 ? '' : 's' }`;
}

/**
 * Search-mode mock: the dated search (date_from === FROM) matches only
 * the first two carded properties — cards[0] at €9,999/wk, cards[1] at
 * €1,111/wk, so the price sort is observable — while any other window
 * (the browse-mode probe after clearing) returns no rates, leaving the
 * static ACF prices alone. Assumes the catalog has at least two villas
 * with a property_id (true for the pushed local/staging DB).
 */
async function mockSearchTopTwo( page: Page ): Promise< () => CardInfo[] > {
	let cards: CardInfo[] = [];
	await page.route( BOB_API_GLOB, async ( route ) => {
		const reqFrom = new URL( route.request().url() ).searchParams.get( 'date_from' );
		if ( reqFrom !== FROM ) {
			await bobJson( route, listingResponse( [] ) );
			return;
		}
		if ( ! cards.length ) {
			cards = await scrapeCards( page );
		}
		await bobJson(
			route,
			listingResponse( [
				{ villa: cards[ 0 ].pid, available: 1, eur_base_rental: 9999 },
				{ villa: cards[ 1 ].pid, available: 1, eur_base_rental: 1111 },
			] )
		);
	} );
	return () => cards;
}

test.describe( 'villa listing grid — browse mode', () => {
	test( 'all cards render, prices masked until the probe responds, no "Select dates" prompt', async ( { page } ) => {
		// Hold the probe response open so the pre-hydration state is
		// observable, then release with an empty result set (probe mode
		// never filters — villa-listing-grid.js applyResults()).
		const gate = deferred();
		await page.route( BOB_API_GLOB, async ( route ) => {
			await gate.promise;
			await bobJson( route, listingResponse( [] ) );
		} );

		// domcontentloaded: the probe is preloaded from <head>
		// (ibv_villa_listing_preload_availability()), so waiting for the
		// full load event would deadlock on our held-open mock.
		await page.goto( LISTING_PATH, { waitUntil: 'domcontentloaded' } );

		const grid = page.locator( GRID );

		// Browse mode: no skeleton / search state, every card visible,
		// amounts masked by --price-pending so the static ACF price and
		// the probe rate never flash in sequence.
		await expect( grid ).toHaveClass( /ibv-listing-grid--price-pending/ );
		await expect( grid ).not.toHaveClass( /ibv-listing-grid--searching/ );
		await expect( page.locator( '[data-bob-skeleton]' ) ).toHaveCount( 0 );
		await expect( page.locator( CHIP ) ).toBeHidden();
		expect( await page.locator( CARD ).count() ).toBeGreaterThan( 0 );
		await expect( page.locator( `${ CARD }[hidden]` ) ).toHaveCount( 0 );

		gate.resolve();
		await expect( grid ).not.toHaveClass( /ibv-listing-grid--price-pending/ );
		await expect( page.locator( `${ CARD }[hidden]` ) ).toHaveCount( 0 );

		// 6bcbe72: the "Select dates for price" nudge must not appear on
		// cards — it lives on the villa detail page only.
		expect( await page.locator( 'body' ).innerText() ).not.toContain( 'Select dates for price' );
	} );

	test( 'card prices hydrate from the probe response; unpriced villas keep the honest empty state', async ( { page } ) => {
		let cards: CardInfo[] = [];
		await page.route( BOB_API_GLOB, async ( route ) => {
			cards = await scrapeCards( page );
			// First card gets a real rate; second gets the out-of-season
			// €0 row (must NOT hydrate — applyResults() rate > 0 guard);
			// the rest are absent from the response entirely.
			await bobJson(
				route,
				listingResponse( [
					{ villa: cards[ 0 ].pid, available: 1, eur_base_rental: 1234 },
					{ villa: cards[ 1 ].pid, available: 1, eur_base_rental: 0 },
				] )
			);
		} );

		await page.goto( LISTING_PATH, { waitUntil: 'domcontentloaded' } );
		await expect( page.locator( GRID ) ).not.toHaveClass( /ibv-listing-grid--price-pending/ );

		// Hydrated card: formatEuro() paints €1,234 (nights: 7 keeps the
		// weekly normalisation an identity), data-price feeds the sort,
		// and the From / "/ wk" prefix+suffix are un-hidden.
		const priced = card( page, cards[ 0 ].pid );
		await expect( priced.locator( '[data-bob-from-price]' ) ).toHaveText( '€1,234' );
		await expect( priced ).toHaveAttribute( 'data-price', '1234' );
		await expect( priced.locator( '.ibv-villa-card__price-prefix' ) ).toBeVisible();
		await expect( priced.locator( '.ibv-villa-card__price-suffix' ) ).toBeVisible();

		// Every other card keeps its server-rendered state: either the
		// static ACF fallback amount, or — when the villa has no ACF
		// from-price — an empty amount with prefix/suffix hidden
		// (villa-card.php). Never a broken placeholder, never our rate.
		for ( const info of cards.slice( 1 ) ) {
			const el = card( page, info.pid );
			await expect( el.locator( '[data-bob-from-price]' ) ).toHaveText( info.priceText );
			if ( info.priceText === '' ) {
				await expect( el.locator( '.ibv-villa-card__price-prefix' ) ).toBeHidden();
				await expect( el.locator( '.ibv-villa-card__price-suffix' ) ).toBeHidden();
			}
		}
		expect( await page.locator( 'body' ).innerText() ).not.toContain( 'Select dates for price' );
	} );
} );

test.describe( 'villa listing grid — search mode', () => {
	test( 'skeleton + server-rendered pill while pending, then filters to the availability response', async ( { page } ) => {
		const gate = deferred();
		let cards: CardInfo[] = [];
		await page.route( BOB_API_GLOB, async ( route ) => {
			cards = await scrapeCards( page );
			await gate.promise;
			await bobJson(
				route,
				listingResponse( [
					{ villa: cards[ 0 ].pid, available: 1, eur_base_rental: 9999 },
					{ villa: cards[ 1 ].pid, available: 1, eur_base_rental: 1111 },
				] )
			);
		} );

		await page.goto( SEARCH_URL, { waitUntil: 'domcontentloaded' } );

		const grid = page.locator( GRID );

		// Pre-response: f94aea5 skeleton state (server-rendered when the
		// three params are present), and the 2876cc6 pill — visible
		// BEFORE the API answers because PHP renders it from URL state.
		await expect( grid ).toHaveClass( /ibv-listing-grid--searching/ );
		await expect( grid ).toHaveAttribute( 'aria-busy', 'true' );
		await expect( page.locator( '[data-bob-skeleton]' ) ).toBeVisible();
		await expect( page.locator( CHIP ) ).toBeVisible();
		// 6897fe3: chip text matches ibv_villa_listing_format_range().
		await expect( page.locator( '[data-bob-selected-dates-label]' ) ).toHaveText(
			chipLabel( FROM, TO, 4 )
		);
		await expect( page.locator( '[data-bob-results-count]' ) ).toBeHidden();

		gate.resolve();

		// Post-response: only the two returned pids survive, sorted price
		// ascending (cards[1] at €1,111 first), skeleton dismissed, count
		// painted, aria-busy gone.
		await expect( grid ).not.toHaveClass( /ibv-listing-grid--searching/ );
		await expect( grid ).not.toHaveAttribute( 'aria-busy' );
		await expect( visibleCards( page ) ).toHaveCount( 2 );
		await expect( card( page, cards[ 0 ].pid ) ).toBeVisible();
		await expect( card( page, cards[ 1 ].pid ) ).toBeVisible();
		if ( cards.length > 2 ) {
			await expect( card( page, cards[ 2 ].pid ) ).toBeHidden();
		}
		await expect( visibleCards( page ).first() ).toHaveAttribute(
			'data-bob-property-id',
			cards[ 1 ].pid
		);
		// Dated weekly rate hydrated onto the card for the searched stay.
		await expect(
			card( page, cards[ 1 ].pid ).locator( '[data-bob-from-price]' )
		).toHaveText( '€1,111' );
		await expect( page.locator( '[data-bob-results-count]' ) ).toHaveText( 'Showing 2 villas' );
		await expect( page.locator( '[data-bob-skeleton]' ) ).toHaveCount( 0 );
		await expect( page.locator( '[data-bob-empty-state]' ) ).toBeHidden();
	} );

	test( 'clearing the search returns to browse mode — no stuck chip, pill, or hidden villas', async ( { page } ) => {
		await mockSearchTopTwo( page );

		await page.goto( SEARCH_URL, { waitUntil: 'domcontentloaded' } );
		await expect( visibleCards( page ) ).toHaveCount( 2 );

		// The chip's × is a plain link back to the listing root
		// (villa-listing-grid.php $listing_root) — a full reload with no
		// search params, where the mock serves the empty probe response.
		await page.locator( '.ibv-listing-grid-section__dates-clear' ).click();
		await page.waitForURL( ( url ) => ! url.searchParams.has( 'date_from' ) );

		await expect( page.locator( CHIP ) ).toBeHidden();
		await expect( page.locator( GRID ) ).not.toHaveClass( /ibv-listing-grid--searching/ );
		await expect( page.locator( '[data-bob-skeleton]' ) ).toHaveCount( 0 );
		await expect( page.locator( `${ CARD }[hidden]` ) ).toHaveCount( 0 );
		await expect( page.locator( '[data-bob-results-count]' ) ).toBeHidden();
		await expect( page.locator( '[data-bob-empty-state]' ) ).toBeHidden();
	} );

	test( 'the search URL is shareable — a fresh load renders the same state', async ( { page } ) => {
		const getCards = await mockSearchTopTwo( page );

		await page.goto( SEARCH_URL, { waitUntil: 'domcontentloaded' } );
		await expect( visibleCards( page ) ).toHaveCount( 2 );

		// Fresh document, same URL (each test already runs in a fresh
		// context, so this reload is the "paste into a private window"
		// step): same chip, pill, and filtered set.
		await page.reload( { waitUntil: 'domcontentloaded' } );
		await expect( page.locator( '[data-bob-selected-dates-label]' ) ).toHaveText(
			chipLabel( FROM, TO, 4 )
		);
		await expect( visibleCards( page ) ).toHaveCount( 2 );
		await expect( visibleCards( page ).first() ).toHaveAttribute(
			'data-bob-property-id',
			getCards()[ 1 ].pid
		);
		await expect( page.locator( '[data-bob-results-count]' ) ).toHaveText( 'Showing 2 villas' );
	} );

	test( 'offers filter narrows to offer villas and intersects an active search (fccea43)', async ( { page } ) => {
		const getCards = await mockSearchTopTwo( page );

		// Browse mode first: pure client-side filter on the
		// server-rendered data-bob-has-offer attribute.
		await page.goto( LISTING_PATH, { waitUntil: 'domcontentloaded' } );
		const cards = await scrapeCards( page );
		const offerPids = cards.filter( ( c ) => c.hasOffer ).map( ( c ) => c.pid );

		const checkbox = page.locator( '[data-bob-filter-offers]' );
		await checkbox.check();
		if ( offerPids.length === 0 ) {
			await expect( page.locator( GRID ) ).toBeHidden();
			await expect( page.locator( '[data-bob-empty-state]' ) ).toBeVisible();
		} else {
			await expect( visibleCards( page ) ).toHaveCount( offerPids.length );
			for ( const pid of offerPids ) {
				await expect( card( page, pid ) ).toBeVisible();
			}
			await expect( page.locator( '[data-bob-results-count]' ) ).toHaveText(
				`Showing ${ offerPids.length } villas`
			);
		}

		// Un-check recovers browse mode fully (applyFilters() sets state
		// both ways on every run).
		await checkbox.uncheck();
		await expect( page.locator( `${ CARD }[hidden]` ) ).toHaveCount( 0 );
		await expect( page.locator( '[data-bob-empty-state]' ) ).toBeHidden();

		// Combined with an active date search: the two filters intersect.
		await page.goto( SEARCH_URL, { waitUntil: 'domcontentloaded' } );
		await expect( visibleCards( page ) ).toHaveCount( 2 );

		const matched = [ getCards()[ 0 ].pid, getCards()[ 1 ].pid ];
		const intersection = matched.filter( ( pid ) => offerPids.includes( pid ) );
		await page.locator( '[data-bob-filter-offers]' ).check();
		if ( intersection.length === 0 ) {
			await expect( page.locator( GRID ) ).toBeHidden();
			await expect( page.locator( '[data-bob-empty-state]' ) ).toBeVisible();
		} else {
			await expect( visibleCards( page ) ).toHaveCount( intersection.length );
		}
		await page.locator( '[data-bob-filter-offers]' ).uncheck();
		await expect( visibleCards( page ) ).toHaveCount( 2 );
	} );
} );
