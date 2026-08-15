import { test, expect } from '../helpers/fixtures';
import {
	CONFIRMATION_PATH,
	NON_VILLA_POST_ID,
	VILLA_ID,
	VILLA_NAME,
	VILLA_SLUG,
} from '../helpers/test-data';

/**
 * Suite section 4 (URL-driven parts) — booking confirmation page.
 * docs/testing/bob-e2e-test-suite.md
 *
 * URL contract (page-booking-confirmation.php):
 *   ?villa={id}&arrival=YYYY-MM-DD&departure=YYYY-MM-DD&guests={n}&offer={string}&ref={entry_id}
 * All params optional and untrusted; the details panel renders only rows
 * whose params survive validation. `ref` is displayed as IV-{n}.
 */

const DETAILS = '.ibv-booking-confirmation__details';
const DETAILS_LIST = '.ibv-booking-confirmation__details-list';

function detailsRow( page: import( '@playwright/test' ).Page, label: string ) {
	return page.locator( `${ DETAILS_LIST } li`, {
		has: page.locator( 'strong', { hasText: label } ),
	} );
}

function confirmationUrl( params: Record< string, string > ): string {
	const qs = new URLSearchParams( params ).toString();
	return qs ? `${ CONFIRMATION_PATH }?${ qs }` : CONFIRMATION_PATH;
}

test.describe( 'booking confirmation — URL-driven details panel', () => {
	test( 'valid params render villa link, dates, guests, and offer', async ( { page } ) => {
		await page.goto(
			confirmationUrl( {
				villa: String( VILLA_ID ),
				arrival: '2026-09-05',
				departure: '2026-09-12',
				guests: '4',
				offer: 'Early bird 10%',
			} )
		);

		await expect( page.locator( 'h1' ) ).toBeVisible();

		const villaRow = detailsRow( page, 'Villa' );
		await expect( villaRow ).toContainText( VILLA_NAME );
		await expect( villaRow.locator( 'a' ) ).toHaveAttribute(
			'href',
			new RegExp( VILLA_SLUG )
		);

		// Same-month range formats as e.g. "5–12 Sep 2026".
		await expect( detailsRow( page, 'Dates' ) ).toContainText( 'Sep 2026' );
		await expect( detailsRow( page, 'Guests' ) ).toContainText( '4' );
		await expect( detailsRow( page, 'Offer' ) ).toContainText( 'Early bird 10%' );
	} );

	test( 'direct access with no params degrades gracefully', async ( { page } ) => {
		const response = await page.goto( CONFIRMATION_PATH );
		expect( response!.status() ).toBe( 200 );

		// Confirmation panel still renders; details panel is absent
		// entirely — no half-empty list.
		await expect( page.locator( 'h1' ) ).toBeVisible();
		await expect( page.locator( DETAILS ) ).toHaveCount( 0 );

		const body = await page.locator( 'body' ).innerText();
		expect( body ).not.toMatch( /Fatal error|Uncaught|Warning: |Notice: / );
	} );

	test( 'nonsense villa id drops the villa row, keeps valid rows', async ( { page } ) => {
		await page.goto(
			confirmationUrl( {
				villa: '99999999',
				arrival: '2026-09-05',
				departure: '2026-09-12',
				guests: '4',
			} )
		);

		await expect( detailsRow( page, 'Villa' ) ).toHaveCount( 0 );
		await expect( detailsRow( page, 'Dates' ) ).toContainText( 'Sep 2026' );
		await expect( detailsRow( page, 'Guests' ) ).toContainText( '4' );
	} );

	test( 'a non-villa post id is rejected', async ( { page } ) => {
		await page.goto(
			confirmationUrl( {
				villa: String( NON_VILLA_POST_ID ),
				guests: '2',
			} )
		);

		await expect( detailsRow( page, 'Villa' ) ).toHaveCount( 0 );
		await expect( detailsRow( page, 'Guests' ) ).toContainText( '2' );
	} );

	test( 'garbage dates and extreme pax do not break the page', async ( { page } ) => {
		const response = await page.goto(
			confirmationUrl( {
				villa: 'not-a-number',
				arrival: 'garbage',
				departure: '<script>alert(1)</script>',
				guests: '999',
			} )
		);
		expect( response!.status() ).toBe( 200 );
		await expect( page.locator( 'h1' ) ).toBeVisible();

		// Non-Y-m-d dates fail validation: no Dates row.
		await expect( detailsRow( page, 'Dates' ) ).toHaveCount( 0 );
		await expect( detailsRow( page, 'Villa' ) ).toHaveCount( 0 );
		// guests=999 is absint-valid; it renders rather than breaking.
		await expect( detailsRow( page, 'Guests' ) ).toContainText( '999' );
	} );

	test( 'script tags in params never execute or echo raw', async ( { page } ) => {
		// Regressions 3ce1b00 / 5afbab5: escaped output must hold.
		await page.goto(
			confirmationUrl( {
				villa: '<script>window.__pwned=1</script>',
				arrival: '"><img src=x onerror=window.__pwned=1>',
				departure: '2026-09-12',
				guests: '4',
				offer: '<script>window.__pwned=1</script><img src=x onerror=window.__pwned=1>',
			} )
		);

		expect(
			await page.evaluate( () => ( window as unknown as { __pwned?: number } ).__pwned )
		).toBeUndefined();

		// Nothing injected inside the details area (if it rendered at all).
		await expect( page.locator( `${ DETAILS } img, ${ DETAILS } script` ) ).toHaveCount( 0 );

		// Raw payloads never echo anywhere in the document.
		const html = await page.content();
		expect( html ).not.toContain( '<script>window.__pwned' );
		expect( html ).not.toContain( 'onerror=window.__pwned' );
	} );

	test( 'over-long offer strings are truncated, not echoed whole', async ( { page } ) => {
		const longOffer = 'A'.repeat( 200 );
		await page.goto( confirmationUrl( { offer: longOffer, guests: '2' } ) );

		const offerRow = detailsRow( page, 'Offer' );
		await expect( offerRow ).toBeVisible();
		const text = ( await offerRow.innerText() ).replace( 'Offer', '' ).trim();
		expect( text.length ).toBeLessThanOrEqual( 80 );
		expect( text ).toMatch( /^A+$/ );
	} );

	test( 'valid ref renders as a prefixed IV- reference', async ( { page } ) => {
		await page.goto(
			confirmationUrl( {
				guests: '2',
				ref: '1042',
			} )
		);

		await expect( detailsRow( page, 'Reference' ) ).toContainText( 'IV-1042' );
	} );

	test( 'zero or garbage ref drops the reference row', async ( { page } ) => {
		await page.goto( confirmationUrl( { guests: '2', ref: 'not-a-number' } ) );
		await expect( detailsRow( page, 'Reference' ) ).toHaveCount( 0 );
		await expect( detailsRow( page, 'Guests' ) ).toContainText( '2' );
	} );
} );

test.describe( 'booking confirmation — page chrome', () => {
	test( 'is noindexed via Yoast robots meta', async ( { page } ) => {
		await page.goto( CONFIRMATION_PATH );
		const robots = page.locator( 'meta[name="robots"]' );
		await expect( robots ).toHaveAttribute( 'content', /noindex/ );
		await expect( robots ).toHaveAttribute( 'content', /nofollow/ );
	} );

	test( 'contact strip exposes WhatsApp and tel links', async ( { page } ) => {
		await page.goto( CONFIRMATION_PATH );
		const strip = page.locator( '.ibv-booking-confirmation__contact-line' );
		await expect( strip ).toBeVisible();
		await expect( strip.locator( 'a[href^="https://wa.me/"]' ) ).toBeVisible();
		await expect( strip.locator( 'a[href^="tel:"]' ).first() ).toHaveAttribute(
			'href',
			/tel:\+\d+/
		);
	} );
} );

test.describe( 'booking confirmation — variants', () => {
	test( 'type=villa shows villa heading and three steps', async ( { page } ) => {
		await page.goto( confirmationUrl( { type: 'villa' } ) );
		await expect( page.locator( 'h1' ) ).toHaveText( /We've got your request/ );
		await expect( page.locator( '.ibv-step-card' ) ).toHaveCount( 3 );
		await expect( page.locator( '.ibv-step-card__title' ).first() ).toHaveText(
			'We check the villa'
		);
	} );

	test( 'type=accommodation shows two steps and no deposit card', async ( { page } ) => {
		await page.goto( confirmationUrl( { type: 'accommodation' } ) );
		await expect( page.locator( 'h1' ) ).toHaveText( /We've got your enquiry/ );
		await expect( page.locator( '.ibv-step-card' ) ).toHaveCount( 2 );
		await expect( page.locator( 'body' ) ).not.toContainText( '30% deposit' );
	} );

	test( 'missing or junk type falls back to general with no steps', async ( { page } ) => {
		await page.goto( confirmationUrl( { type: 'nonsense' } ) );
		await expect( page.locator( 'h1' ) ).toHaveText( /Thanks — that's with us/ );
		await expect( page.locator( '.ibv-section-three-step' ) ).toHaveCount( 0 );

		await page.goto( confirmationUrl( { type: '<script>alert(1)</script>' } ) );
		await expect( page.locator( 'h1' ) ).toHaveText( /Thanks — that's with us/ );
		expect( await page.locator( 'h1' ).innerHTML() ).not.toContain( '<script>' );

		await page.goto( CONFIRMATION_PATH );
		await expect( page.locator( 'h1' ) ).toHaveText( /Thanks — that's with us/ );
		await expect( page.locator( '.ibv-section-three-step' ) ).toHaveCount( 0 );
	} );
} );
