import { test, expect } from '../helpers/fixtures';
import {
	addDays,
	clickDate,
	heroForm,
	openHeroPicker,
	openPopover,
	saturdayAtLeastDaysOut,
	selectHeroRange,
	ymd,
} from '../helpers/date-picker';
import { LISTING_PATH } from '../helpers/test-data';

/**
 * Suite section 1 — hero search (homepage).
 * docs/testing/bob-e2e-test-suite.md
 */

// Saturday-to-Saturday week, comfortably in the future.
const FROM = ymd( saturdayAtLeastDaysOut( 45 ) );
const TO = ymd( addDays( saturdayAtLeastDaysOut( 45 ), 7 ) );

test.describe( 'hero search — date-range picker', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( '/' );
	} );

	test( 'opens from trigger, closes on Esc and outside click, scroll not blocked', async ( { page } ) => {
		await openHeroPicker( page );

		// Regression eb755a2: page scroll must not be blocked while open.
		const scrolled = await page.evaluate( () => {
			window.scrollBy( 0, 400 );
			return window.scrollY;
		} );
		expect( scrolled, 'page scroll is blocked while the picker is open' ).toBeGreaterThan( 0 );
		await page.evaluate( () => window.scrollTo( 0, 0 ) );

		await page.keyboard.press( 'Escape' );
		await expect( openPopover( page ) ).toHaveCount( 0 );

		await openHeroPicker( page );
		// Light dismiss: click well away from the popover and trigger.
		await page.mouse.click( 5, 5 );
		await expect( openPopover( page ) ).toHaveCount( 0 );
	} );

	test( 'past dates cannot be selected', async ( { page } ) => {
		await openHeroPicker( page );

		const form = heroForm( page );
		const yesterday = ymd( addDays( new Date(), -1 ) );
		const cell = openPopover( page ).locator( `[data-vc-date="${ yesterday }"]` );

		if ( ( await cell.count() ) > 0 ) {
			// Rendered but must be inert — force the click past any
			// disabled/pointer-events guard and confirm nothing is written.
			await cell.locator( '[data-vc-date-btn]' ).first().click( { force: true } );
			await expect( form.locator( 'input[name="date_from"]' ) ).toHaveValue( '' );
			await expect( form.locator( 'input[name="date_to"]' ) ).toHaveValue( '' );
		} else {
			// Not rendered at all (displayDateMin hides it) — also a pass.
			expect( await cell.count() ).toBe( 0 );
		}
	} );

	test( 'selecting a range fills display and hidden inputs; reversed clicks re-anchor sanely', async ( { page } ) => {
		const form = heroForm( page );

		// Click the LATER date first, then the earlier one — the picker
		// must sort rather than produce a broken inverted range.
		await openHeroPicker( page );
		await clickDate( page, TO );
		await clickDate( page, FROM );

		await expect( form.locator( 'input[name="date_from"]' ) ).toHaveValue( FROM );
		await expect( form.locator( 'input[name="date_to"]' ) ).toHaveValue( TO );

		const display = form.locator( '[data-bob-date-range-display]' );
		await expect( display ).not.toHaveText( 'Add dates' );
		await expect( display ).not.toHaveClass( /is-empty/ );
	} );

	test( 'clear resets display, hidden inputs, and calendar state', async ( { page } ) => {
		const form = heroForm( page );
		await selectHeroRange( page, FROM, TO );

		const clearBtn = form.locator( '[data-bob-date-range-clear]' );
		await expect( clearBtn ).toBeVisible();
		await clearBtn.click();

		await expect( form.locator( 'input[name="date_from"]' ) ).toHaveValue( '' );
		await expect( form.locator( 'input[name="date_to"]' ) ).toHaveValue( '' );
		await expect( form.locator( '[data-bob-date-range-display]' ) ).toHaveText( 'Add dates' );
		await expect( clearBtn ).toBeHidden();

		// Reopen: calendar selection is gone too.
		await openHeroPicker( page );
		await expect( openPopover( page ).locator( '[data-vc-date-selected]' ) ).toHaveCount( 0 );
	} );

	test( 'long ranges selectable and month navigation works ~12 months out', async ( { page } ) => {
		const form = heroForm( page );

		// 3+ week range.
		const longFrom = ymd( saturdayAtLeastDaysOut( 45 ) );
		const longTo = ymd( addDays( saturdayAtLeastDaysOut( 45 ), 22 ) );
		await selectHeroRange( page, longFrom, longTo );
		await expect( form.locator( 'input[name="date_from"]' ) ).toHaveValue( longFrom );
		await expect( form.locator( 'input[name="date_to"]' ) ).toHaveValue( longTo );

		// Navigate forward ~12 months: the cell for the 15th of the month
		// 12 months out must become reachable.
		const yearOut = new Date();
		yearOut.setMonth( yearOut.getMonth() + 12, 15 );
		await openHeroPicker( page );
		const popover = openPopover( page );
		const target = popover.locator( `[data-vc-date="${ ymd( yearOut ) }"]` );
		for ( let i = 0; i < 14 && ( await target.count() ) === 0; i++ ) {
			await popover.locator( '[data-vc-arrow="next"]' ).first().click();
		}
		await expect( target.first() ).toBeVisible();
	} );

	test( 'picker fits the viewport on mobile-sized screens', async ( { page }, testInfo ) => {
		test.skip( ! testInfo.project.name.startsWith( 'mobile' ), 'mobile projects only' );

		await openHeroPicker( page );
		const box = await openPopover( page ).boundingBox();
		expect( box ).not.toBeNull();
		expect( box!.width, 'picker wider than the viewport' ).toBeLessThanOrEqual(
			page.viewportSize()!.width
		);
	} );
} );

test.describe( 'hero search — guests + submit', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( '/' );
	} );

	test( 'guests dropdown offers plain 1–12', async ( { page } ) => {
		const options = heroForm( page ).locator( 'select[name="pax"] option:not([disabled])' );
		await expect( options ).toHaveCount( 12 );
		for ( let i = 1; i <= 12; i++ ) {
			const opt = options.nth( i - 1 );
			// Plain numbers: no "guests" suffix weirdness.
			await expect( opt ).toHaveText( String( i ) );
			await expect( opt ).toHaveAttribute( 'value', String( i ) );
		}
	} );

	test( 'submit with dates + pax lands on listing with search context applied', async ( { page } ) => {
		await selectHeroRange( page, FROM, TO );
		await heroForm( page ).locator( 'select[name="pax"]' ).selectOption( '4' );
		await heroForm( page ).locator( 'button[type="submit"]' ).click();

		await page.waitForURL( `**${ LISTING_PATH }**` );
		const url = new URL( page.url() );
		expect( url.pathname ).toBe( LISTING_PATH );
		expect( url.searchParams.get( 'date_from' ) ).toBe( FROM );
		expect( url.searchParams.get( 'date_to' ) ).toBe( TO );
		expect( url.searchParams.get( 'pax' ) ).toBe( '4' );
		expect( url.hash ).toBe( '#results' );

		// Search context round-trips into the listing hero: the same form
		// arrives pre-filled from the URL params.
		const form = heroForm( page );
		await expect( form.locator( 'input[name="date_from"]' ) ).toHaveValue( FROM );
		await expect( form.locator( 'input[name="date_to"]' ) ).toHaveValue( TO );
		await expect( form.locator( '[data-bob-date-range-display]' ) ).not.toHaveText( 'Add dates' );
		await expect( form.locator( 'select[name="pax"]' ) ).toHaveValue( '4' );
	} );

	test( 'submit with no dates falls back to browse mode without errors', async ( { page } ) => {
		await heroForm( page ).locator( 'select[name="pax"]' ).selectOption( '2' );
		await heroForm( page ).locator( 'button[type="submit"]' ).click();

		await page.waitForURL( `**${ LISTING_PATH }**` );
		const url = new URL( page.url() );
		expect( url.searchParams.get( 'date_from' ) ?? '' ).toBe( '' );
		expect( url.searchParams.get( 'date_to' ) ?? '' ).toBe( '' );

		// Browse mode: the grid renders villas.
		await expect(
			page.locator( '#results' ).locator( 'article, .ibv-villa-card' ).first()
		).toBeVisible();
	} );

	test( 'on the listing page, submit anchor-scrolls to the results grid', async ( { page } ) => {
		// Regression a0b4990: submitting from the listing hero must land
		// anchored on #results, not reload to the top of the page.
		await page.goto( LISTING_PATH );
		await heroForm( page ).locator( 'select[name="pax"]' ).selectOption( '2' );
		await heroForm( page ).locator( 'button[type="submit"]' ).click();

		await page.waitForURL( '**#results' );
		await expect( page.locator( '#results' ) ).toBeInViewport();
	} );

	test( 'search is operable by keyboard', async ( { page }, testInfo ) => {
		test.skip( testInfo.project.name.startsWith( 'mobile' ), 'keyboard pass is desktop-only' );

		// Enter on the focused trigger opens the picker.
		await heroForm( page ).locator( '[data-bob-date-range-trigger]' ).focus();
		await page.keyboard.press( 'Enter' );
		await expect( openPopover( page ) ).toBeVisible();

		// Date buttons inside the calendar are reachable by Tab. The
		// popover element lives at the end of <body>, so the tab distance
		// from the trigger is itself a UX signal — recorded below.
		// WebKit mirrors real Safari: plain Tab skips buttons, Option+Tab
		// walks every focusable — use the same key a Safari user would.
		const tabKey = testInfo.project.name === 'webkit' ? 'Alt+Tab' : 'Tab';
		const isOnDateBtn = () =>
			page.evaluate( () => document.activeElement?.hasAttribute( 'data-vc-date-btn' ) );
		let tabs = 0;
		let reachedDate = false;
		for ( ; tabs < 150 && ! reachedDate; tabs++ ) {
			await page.keyboard.press( tabKey );
			reachedDate = ( await isOnDateBtn() ) === true;
		}
		testInfo.annotations.push( {
			type: 'keyboard-distance',
			description: `${ tabs } Tab presses from trigger to first calendar date`,
		} );
		expect( reachedDate, 'calendar dates unreachable by keyboard' ).toBe( true );

		await page.keyboard.press( 'Escape' );
		await expect( openPopover( page ) ).toHaveCount( 0 );

		// Pax + submit by keyboard (type-to-select on the native select).
		const pax = heroForm( page ).locator( 'select[name="pax"]' );
		await pax.focus();
		await page.keyboard.press( '4' );
		await expect( pax ).toHaveValue( '4' );
		await heroForm( page ).locator( 'button[type="submit"]' ).focus();
		await page.keyboard.press( 'Enter' );
		await page.waitForURL( `**${ LISTING_PATH }**` );
	} );
} );
