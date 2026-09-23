import { test, expect } from '@playwright/test';

test( 'live forms 32–36 render and widgets bind on GF 3.0', async ( { page } ) => {
	const jsErrors: string[] = [];
	page.on( 'pageerror', ( err ) => jsErrors.push( err.message ) );

	await page.goto( '/villas/villa-savines/' );
	await expect( page.locator( '#gform_34' ) ).toBeVisible();
	await expect( page.locator( '[data-bob-date-range-trigger]' ) ).toBeVisible();
	await expect( page.locator( '#gform_34 .ibv-drp-hidden .gform-datepicker' ) ).toHaveCount( 0 );
	await expect( page.locator( '#gform_34 .ibv-drp-hidden .gform-datepicker-toggle' ) ).toHaveCount( 0 );
	await expect( page.locator( '#gform_34 .iti' ) ).toHaveCount( 1 );
	await expect( page.locator( '#gform_submit_button_34' ) ).toHaveAttribute(
		'onclick',
		/gform\.submission\.handleButtonClick/
	);

	await page.goto( '/hotel/' );
	await expect( page.locator( '#gform_35' ) ).toBeVisible();
	await expect( page.locator( '#gform_35 [data-bob-date-range-trigger]' ) ).toBeVisible();
	await expect( page.locator( '#gform_35 .ibv-drp-hidden .gform-datepicker' ) ).toHaveCount( 0 );
	await expect( page.locator( '#gform_35 .iti' ) ).toHaveCount( 1 );

	await page.goto( '/contact/' );
	await expect( page.locator( '#gform_36' ) ).toBeVisible();
	await expect( page.locator( '#gform_36 .iti' ) ).toHaveCount( 1 );
	await expect( page.locator( '#gform_submit_button_36' ) ).toHaveAttribute(
		'onclick',
		/gform\.submission\.handleButtonClick/
	);

	await page.goto( '/concierge/' );
	await expect( page.locator( '#gform_33' ) ).toBeVisible();
	await expect( page.locator( '#gform_33 .iti' ) ).toHaveCount( 1 );
	const toggle = page.locator( '#gform_33 .gform-datepicker-toggle' ).first();
	await expect( toggle ).toBeVisible();
	await toggle.click();
	await expect( page.locator( '.gform-datepicker-calendar' ).first() ).toBeVisible();
	expect( await page.locator( '#gform_33 select option' ).count() ).toBeGreaterThan( 1 );

	await page.goto( '/' );
	await expect( page.locator( '#gform_32' ).first() ).toBeVisible();
	await expect( page.locator( '#gform_submit_button_32' ).first() ).toHaveAttribute(
		'onclick',
		/gform\.submission\.handleButtonClick/
	);

	expect( jsErrors, jsErrors.join( '\n' ) ).toEqual( [] );
} );
