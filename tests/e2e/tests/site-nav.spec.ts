import { test, expect } from '../helpers/fixtures';

/**
 * Header mobile menu — disclosure toggle (site-nav.js + site-chrome.css).
 *
 * Below the 64rem breakpoint the nav collapses behind a hamburger.
 * My Booking and Search Villas stay in the header bar down to 40rem;
 * below that they drop into the open menu. At desktop the toggle is
 * hidden and the menu is always visible via display: contents.
 */

const TOGGLE = '[data-ibv-site-nav-toggle]';
const MENU = '#ibv-site-menu';

test.describe( 'site header — mobile menu', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( '/' );
	} );

	test( 'toggle opens and closes the menu panel', async ( { page }, testInfo ) => {
		test.skip( ! testInfo.project.name.startsWith( 'mobile' ), 'mobile projects only' );

		const toggle = page.locator( TOGGLE );
		const menu = page.locator( MENU );

		// Collapsed by default: toggle visible, panel hidden.
		await expect( toggle ).toBeVisible();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( menu ).toBeHidden();

		// Open: panel shows nav links. On a phone-width project the
		// header CTAs have dropped into the open-menu row.
		await toggle.click();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( menu ).toBeVisible();
		await expect( menu.locator( '.ibv-nav-list a' ).first() ).toBeVisible();
		await expect(
			page
				.locator( '.ibv-site-header__actions' )
				.getByRole( 'link', { name: 'Search Villas' } )
		).toBeVisible();

		// Close again via the toggle.
		await toggle.click();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( menu ).toBeHidden();
	} );

	test( 'Escape closes the open menu and returns focus to the toggle', async ( { page }, testInfo ) => {
		test.skip( ! testInfo.project.name.startsWith( 'mobile' ), 'mobile projects only' );

		const toggle = page.locator( TOGGLE );
		const menu = page.locator( MENU );

		await toggle.click();
		await expect( menu ).toBeVisible();

		await page.keyboard.press( 'Escape' );
		await expect( menu ).toBeHidden();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( toggle ).toBeFocused();
	} );

	test( 'desktop: toggle hidden, nav and actions always visible', async ( { page }, testInfo ) => {
		test.skip( testInfo.project.name.startsWith( 'mobile' ), 'desktop projects only' );

		await expect( page.locator( TOGGLE ) ).toBeHidden();
		await expect(
			page.locator( '.ibv-site-header__nav .ibv-nav-list a' ).first()
		).toBeVisible();
		await expect(
			page
				.locator( '.ibv-site-header__actions' )
				.getByRole( 'link', { name: 'Search Villas' } )
		).toBeVisible();
	} );
} );
