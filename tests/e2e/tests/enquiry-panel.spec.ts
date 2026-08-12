import type { Locator, Page } from '@playwright/test';
import { test, expect } from '../helpers/fixtures';
import { addDays, saturdayAtLeastDaysOut, ymd } from '../helpers/date-picker';
import { BOB_API_GLOB, bobJson, deferred } from '../helpers/bob-api';
import { VILLA_PATH } from '../helpers/test-data';

/**
 * Suite section 3 (live pricing, RTB gating, failure modes) — villa
 * enquiry panel. docs/testing/bob-e2e-test-suite.md
 *
 * WRITTEN FROM SOURCE, NOT YET EXECUTED — see the tranche 2 note in
 * tests/e2e/README.md.
 *
 * Contract read from enquiry-panel.js fetchPricing()/paint():
 *   request  {data-bob-endpoint}?villa={data-bob-property-id}
 *            &date_from=YYYY-MM-DD&date_to=YYYY-MM-DD&pax=N
 *   response { success, count, query: { nights }, villas: [ {
 *              available, eur_total_price, eur_base_rental,
 *              eur_adw_amount, eur_extra_cleaning } ] }
 * paint() takes villas[0] (a bare object or data.villa also parse),
 * requires available === 1 (or absent) AND a numeric eur_total_price,
 * and formats every figure with Intl en-GB EUR, 0 fraction digits.
 * Availability failure → the data-bob-msg-unavailable notice + submit
 * gated off; fetch failure → the data-bob-msg-price-error notice, prices
 * reset to '—', price block hidden, but the gate deliberately FAIL-OPENS
 * (availability unknown — the enquiry is still valid, per the source
 * comment). Re-fetches abort the in-flight request via AbortController,
 * and the AbortError is swallowed in .catch().
 *
 * Arriving with ?date_from&date_to&pax prefills the embedded GF form
 * (gform_field_value_ibv_arrival / _departure / _pax, enquiry-panel.php)
 * and bindEnquiry() auto-fetches on load — no picker driving needed.
 */

// Two distinct Saturday-to-Saturday weeks, comfortably in the future.
const RANGE_A = {
	from: ymd( saturdayAtLeastDaysOut( 45 ) ),
	to: ymd( addDays( saturdayAtLeastDaysOut( 45 ), 7 ) ),
};
const RANGE_B = {
	from: ymd( saturdayAtLeastDaysOut( 60 ) ),
	to: ymd( addDays( saturdayAtLeastDaysOut( 60 ), 7 ) ),
};

// Same formatter as the EUR const in enquiry-panel.js.
const EUR = new Intl.NumberFormat( 'en-GB', {
	style: 'currency',
	currency: 'EUR',
	maximumFractionDigits: 0,
} );

interface PriceParts {
	rental: number;
	adw: number;
	cleaning: number;
}

const PARTS_A: PriceParts = { rental: 4200, adw: 180, cleaning: 350 };
const PARTS_B: PriceParts = { rental: 5600, adw: 240, cleaning: 350 };

function totalOf( parts: PriceParts ): number {
	return parts.rental + parts.adw + parts.cleaning;
}

function pricingResponse( parts: PriceParts, nights = 7 ) {
	return {
		success: 1,
		count: 1,
		query: { nights },
		villas: [
			{
				available: 1,
				eur_total_price: totalOf( parts ),
				eur_base_rental: parts.rental,
				eur_adw_amount: parts.adw,
				eur_extra_cleaning: parts.cleaning,
			},
		],
	};
}

const UNAVAILABLE_RESPONSE = {
	success: 1,
	count: 0,
	query: { nights: 7 },
	villas: [ { available: 0 } ],
};

function villaUrl( range?: { from: string; to: string }, pax = 4 ): string {
	if ( ! range ) {
		return VILLA_PATH;
	}
	return `${ VILLA_PATH }?date_from=${ range.from }&date_to=${ range.to }&pax=${ pax }`;
}

function panel( page: Page ): Locator {
	return page.locator( '[data-bob-enquiry-panel]' );
}

function gform( page: Page ): Locator {
	// Same lookup as boot() in enquiry-panel.js.
	return panel( page ).locator( '.ibv-gform form[id^="gform_"]' );
}

function priceField( page: Page ): Locator {
	// The whole GF HTML field is display:none while .is-pricing-pending
	// (enquiry-panel.css).
	return panel( page ).locator( '.ibv-enquiry-panel__price-field' );
}

function notice( page: Page ): Locator {
	return gform( page ).locator( '[data-bob-error]' );
}

function submitBtn( page: Page ): Locator {
	// gform_submit_button rewrites GF's <input> to <button>; keep both
	// like the JS does in case the rewrite ever regresses.
	return gform( page ).locator( 'button[type="submit"], input[type="submit"]' );
}

/**
 * Write a new range into the hidden GF date fields exactly the way the
 * grafted picker does (setInputValue() in enquiry-panel.js): set .value,
 * then bubble change + input. bindEnquiry() listens for those events and
 * debounce-schedules fetchPricing() 300ms later. Driving the real
 * popover UI is already covered by the tranche 1 picker tests.
 */
async function setPanelDates( page: Page, fromYMD: string, toYMD: string ): Promise< void > {
	await page.evaluate(
		( [ f, t ] ) => {
			const form = document.querySelector( '[data-bob-enquiry-panel] .ibv-gform form[id^="gform_"]' )!;
			const write = ( wrap: string, value: string ) => {
				const input = form.querySelector< HTMLInputElement >( `${ wrap } input` )!;
				input.value = value;
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
			};
			write( '.ibv-drp-from', f );
			write( '.ibv-drp-to', t );
		},
		[ fromYMD, toYMD ]
	);
}

async function parsedEuro( locator: Locator ): Promise< number > {
	const text = ( await locator.innerText() ).trim();
	return Number( text.replace( /[^\d]/g, '' ) );
}

test.describe( 'enquiry panel — live pricing', () => {
	test( 'price block stays hidden until the API responds, then the breakdown sums (ada756c)', async ( { page } ) => {
		const gate = deferred();
		let requestUrl = '';
		await page.route( BOB_API_GLOB, async ( route ) => {
			requestUrl = route.request().url();
			await gate.promise;
			await bobJson( route, pricingResponse( PARTS_A ) );
		} );

		await page.goto( villaUrl( RANGE_A ) );

		// Pre-response: pending mask on, price field display:none, no
		// notice, contact fields still gated.
		await expect( panel( page ) ).toHaveClass( /is-pricing-pending/ );
		await expect( priceField( page ) ).toBeHidden();
		await expect( notice( page ) ).toBeHidden();
		await expect( panel( page ) ).toHaveClass( /is-contact-pending/ );

		// The prefill auto-fetch carries the panel's own property id and
		// the URL search context.
		await expect.poll( () => requestUrl ).not.toBe( '' );
		const reqParams = new URL( requestUrl ).searchParams;
		expect( reqParams.get( 'villa' ) ).toBe(
			await panel( page ).getAttribute( 'data-bob-property-id' )
		);
		expect( reqParams.get( 'date_from' ) ).toBe( RANGE_A.from );
		expect( reqParams.get( 'date_to' ) ).toBe( RANGE_A.to );
		expect( reqParams.get( 'pax' ) ).toBe( '4' );

		gate.resolve();

		// Painted breakdown: every figure formatted by the same Intl
		// call, and base rental + ADW + cleaning really sum to the total.
		await expect( priceField( page ) ).toBeVisible();
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText(
			EUR.format( totalOf( PARTS_A ) )
		);
		await expect( gform( page ).locator( '[data-bob-base-rental]' ) ).toHaveText(
			EUR.format( PARTS_A.rental )
		);
		await expect( gform( page ).locator( '[data-bob-cleaning]' ) ).toHaveText(
			EUR.format( PARTS_A.cleaning )
		);
		const total = await parsedEuro( gform( page ).locator( '[data-bob-total-eur]' ) );
		const rental = await parsedEuro( gform( page ).locator( '[data-bob-base-rental]' ) );
		const adw = await parsedEuro( gform( page ).locator( '[data-bob-adw]' ) );
		const cleaning = await parsedEuro( gform( page ).locator( '[data-bob-cleaning]' ) );
		expect( total, 'total ≠ base rental + ADW + cleaning' ).toBe( rental + adw + cleaning );

		// Happy path unlocks the rest of the flow: contact fields
		// revealed, RTB submit enabled.
		await expect( panel( page ) ).not.toHaveClass( /is-contact-pending/ );
		await expect( submitBtn( page ) ).toBeEnabled();
	} );

	test( 'changing dates re-fetches — totals track the latest range, no stale totals', async ( { page } ) => {
		await page.route( BOB_API_GLOB, async ( route ) => {
			const from = new URL( route.request().url() ).searchParams.get( 'date_from' );
			await bobJson(
				route,
				pricingResponse( from === RANGE_B.from ? PARTS_B : PARTS_A )
			);
		} );

		await page.goto( villaUrl( RANGE_A ) );
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText(
			EUR.format( totalOf( PARTS_A ) )
		);

		const refetch = page.waitForRequest( ( req ) =>
			req.url().includes( `date_from=${ RANGE_B.from }` )
		);
		await setPanelDates( page, RANGE_B.from, RANGE_B.to );
		await refetch;

		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText(
			EUR.format( totalOf( PARTS_B ) )
		);
		await expect( gform( page ).locator( '[data-bob-base-rental]' ) ).toHaveText(
			EUR.format( PARTS_B.rental )
		);
	} );

	test( 'race: a slow older response never overwrites the newer range', async ( { page } ) => {
		// First range delays 1200ms, second answers immediately.
		// enquiry-panel.js aborts the in-flight AbortController before
		// each new fetch, so the browser cancels request A the moment
		// range B is fetched — the stale response is discarded at the
		// network layer and its route.fulfill() throws (hence try/catch).
		let staleFulfilled = false;
		await page.route( BOB_API_GLOB, async ( route ) => {
			const from = new URL( route.request().url() ).searchParams.get( 'date_from' );
			if ( from === RANGE_A.from ) {
				await new Promise( ( res ) => setTimeout( res, 1200 ) );
				try {
					await bobJson( route, pricingResponse( PARTS_A ) );
					staleFulfilled = true;
				} catch ( e ) {
					// Request already aborted by the panel — expected.
				}
				return;
			}
			await bobJson( route, pricingResponse( PARTS_B ) );
		} );

		// Arrive with no search so no fetch fires on load, then flip:
		// pax → range A → (A's request in flight) → range B.
		await page.goto( villaUrl() );
		await gform( page ).locator( '.ibv-pax select' ).selectOption( '4' );

		const requestA = page.waitForRequest( ( req ) =>
			req.url().includes( `date_from=${ RANGE_A.from }` )
		);
		await setPanelDates( page, RANGE_A.from, RANGE_A.to );
		await requestA;
		await setPanelDates( page, RANGE_B.from, RANGE_B.to );

		// Final total matches the final range…
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText(
			EUR.format( totalOf( PARTS_B ) )
		);
		// …and stays that way after A's delayed response would have
		// landed (1200ms + margin).
		await page.waitForTimeout( 1600 );
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText(
			EUR.format( totalOf( PARTS_B ) )
		);
		expect( staleFulfilled, 'stale response was not aborted by the panel' ).toBe( false );
	} );
} );

test.describe( 'enquiry panel — availability gating (RTB)', () => {
	test( 'unavailable dates show the notice and gate request-to-book off (eb271a6)', async ( { page } ) => {
		await page.route( BOB_API_GLOB, ( route ) => bobJson( route, UNAVAILABLE_RESPONSE ) );

		await page.goto( villaUrl( RANGE_A ) );

		// The exact copy comes from the panel's own attribute — the same
		// string showNotice() reads.
		const msgUnavailable = await panel( page ).getAttribute( 'data-bob-msg-unavailable' );
		await expect( notice( page ) ).toBeVisible();
		await expect( notice( page ) ).toHaveText( msgUnavailable! );

		await expect( submitBtn( page ) ).toBeDisabled();
		await expect( priceField( page ) ).toBeHidden();
		// Contact capture stays gated too (0cdd0ff).
		await expect( panel( page ) ).toHaveClass( /is-contact-pending/ );
	} );

	test( 'switching from unavailable to available dates re-enables cleanly', async ( { page } ) => {
		await page.route( BOB_API_GLOB, async ( route ) => {
			const from = new URL( route.request().url() ).searchParams.get( 'date_from' );
			await bobJson(
				route,
				from === RANGE_A.from ? UNAVAILABLE_RESPONSE : pricingResponse( PARTS_B )
			);
		} );

		await page.goto( villaUrl( RANGE_A ) );
		await expect( notice( page ) ).toBeVisible();
		await expect( submitBtn( page ) ).toBeDisabled();

		await setPanelDates( page, RANGE_B.from, RANGE_B.to );

		// No lingering notice or disabled button.
		await expect( notice( page ) ).toBeHidden();
		await expect( priceField( page ) ).toBeVisible();
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText(
			EUR.format( totalOf( PARTS_B ) )
		);
		await expect( submitBtn( page ) ).toBeEnabled();
		await expect( panel( page ) ).not.toHaveClass( /is-contact-pending/ );
	} );
} );

test.describe( 'enquiry panel — failure modes', () => {
	test( 'blocked endpoint degrades to the error state — never a silent bookable state with no price', async ( { page } ) => {
		// route.abort() defaults to 'failed' → net::ERR_FAILED, which the
		// console guard's local-only ignore list covers.
		await page.route( BOB_API_GLOB, ( route ) => route.abort() );

		await page.goto( villaUrl( RANGE_A ) );

		const msgPriceError = await panel( page ).getAttribute( 'data-bob-msg-price-error' );
		await expect( notice( page ) ).toBeVisible();
		await expect( notice( page ) ).toHaveText( msgPriceError! );

		// No price is ever shown: block hidden, figures reset to the
		// em-dash placeholders.
		await expect( priceField( page ) ).toBeHidden();
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText( '—' );

		// Deliberate deviation from the manual suite's wording: on fetch
		// failure enquiry-panel.js FAIL-OPENS the gate (availability is
		// unknown, "you can still send your enquiry" per the notice), so
		// the submit is enabled and contact fields revealed. What must
		// never happen is a *silent* bookable state — the error notice
		// asserted above is the safeguard.
		await expect( submitBtn( page ) ).toBeEnabled();
		await expect( panel( page ) ).not.toHaveClass( /is-contact-pending/ );
	} );

	test( 'offline mid-flow: panel degrades to the notice with no unhandled promise errors', async ( { page, context } ) => {
		// Phase 1: dates chosen online, price painted.
		await page.route( BOB_API_GLOB, ( route ) => bobJson( route, pricingResponse( PARTS_A ) ) );
		await page.goto( villaUrl( RANGE_A ) );
		await expect( priceField( page ) ).toBeVisible();

		// Phase 2: go offline. Routed requests can bypass network-level
		// offline emulation, so the mock is also swapped for a hard
		// abort — belt and braces, same failure either way. The
		// browser's resource-failure console line for this endpoint is
		// in the guard's LOCAL_ONLY_IGNORED_PATTERNS; the guard still
		// fails the test on any pageerror (unhandled rejection).
		await context.setOffline( true );
		await page.unroute( BOB_API_GLOB );
		await page.route( BOB_API_GLOB, ( route ) => route.abort( 'internetdisconnected' ) );

		await setPanelDates( page, RANGE_B.from, RANGE_B.to );

		const msgPriceError = await panel( page ).getAttribute( 'data-bob-msg-price-error' );
		await expect( notice( page ) ).toBeVisible();
		await expect( notice( page ) ).toHaveText( msgPriceError! );
		// The stale RANGE_A total is never left on screen.
		await expect( priceField( page ) ).toBeHidden();
		await expect( gform( page ).locator( '[data-bob-total-eur]' ) ).toHaveText( '—' );
	} );
} );
