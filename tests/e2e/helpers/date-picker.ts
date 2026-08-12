import type { Locator, Page } from '@playwright/test';
import { expect } from './fixtures';

/**
 * Helpers for driving the IBV date-range picker (Vanilla Calendar Pro
 * wrapped by date-range-picker.js). Selectors verified against the
 * vendored VCP v3 build: date cells are [data-vc-date="YYYY-MM-DD"]
 * with a [data-vc-date-btn] button inside; month arrows are
 * [data-vc-arrow="next"|"prev"].
 */

export function ymd( d: Date ): string {
	const pad = ( n: number ) => ( n < 10 ? '0' : '' ) + n;
	return `${ d.getFullYear() }-${ pad( d.getMonth() + 1 ) }-${ pad( d.getDate() ) }`;
}

export function addDays( d: Date, n: number ): Date {
	const out = new Date( d );
	out.setDate( out.getDate() + n );
	return out;
}

/**
 * A Saturday at least `minDaysOut` days from today — Saturday-to-Saturday
 * is the canonical villa week, and being weeks out keeps the range clear
 * of today's month-boundary edge cases.
 */
export function saturdayAtLeastDaysOut( minDaysOut: number ): Date {
	const d = addDays( new Date(), minDaysOut );
	while ( d.getDay() !== 6 ) {
		d.setDate( d.getDate() + 1 );
	}
	return d;
}

export function heroForm( page: Page ): Locator {
	return page.locator( 'form.ibv-hero-search' );
}

/**
 * The popover currently open in the top layer. Scoping by :popover-open
 * matters because every [data-bob-date-range] wrapper on the page gets
 * its own popover appended to <body>.
 */
export function openPopover( page: Page ): Locator {
	return page.locator( '.ibv-drp__popover:popover-open' );
}

export async function openHeroPicker( page: Page ): Promise< Locator > {
	const form = heroForm( page );
	// The popover is top-layer (position: fixed) and anchored below the
	// form, so it can extend past the bottom of the viewport — and fixed
	// content can't be scrolled into view by Playwright's actionability
	// pass. Park the form near the top of the viewport first so the whole
	// calendar fits on screen.
	await form.evaluate( ( el ) => {
		const top = el.getBoundingClientRect().top + window.scrollY - 90;
		window.scrollTo( 0, Math.max( 0, top ) );
	} );
	await form.locator( '[data-bob-date-range-trigger]' ).click();
	const popover = openPopover( page );
	await expect( popover ).toBeVisible();
	return popover;
}

/** Click "next month" until the given date's cell is rendered. */
export async function gotoMonthContaining(
	page: Page,
	dateYMD: string,
	maxClicks = 15
): Promise< void > {
	const popover = openPopover( page );
	const cell = popover.locator( `[data-vc-date="${ dateYMD }"]` );
	for ( let i = 0; i < maxClicks; i++ ) {
		if ( ( await cell.count() ) > 0 ) {
			return;
		}
		await popover.locator( '[data-vc-arrow="next"]' ).first().click();
	}
	throw new Error( `Could not navigate to month containing ${ dateYMD }` );
}

export async function clickDate( page: Page, dateYMD: string ): Promise< void > {
	await gotoMonthContaining( page, dateYMD );
	await openPopover( page )
		.locator( `[data-vc-date="${ dateYMD }"] [data-vc-date-btn]` )
		.first()
		.click();
}

/** Drive the full hero flow: open picker, pick a range, dismiss. */
export async function selectHeroRange(
	page: Page,
	fromYMD: string,
	toYMD: string
): Promise< void > {
	await openHeroPicker( page );
	await clickDate( page, fromYMD );
	await clickDate( page, toYMD );
	await page.keyboard.press( 'Escape' );
	await expect( openPopover( page ) ).toHaveCount( 0 );
}
