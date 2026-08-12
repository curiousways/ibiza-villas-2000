import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright config for the Bob API e2e suite.
 *
 * Target environment via BASE_URL:
 *   BASE_URL=https://staging.example.com npx playwright test
 * Defaults to the local Herd site.
 *
 * The five projects mirror the device matrix in
 * docs/testing/bob-e2e-test-suite.md section 0. Mobile Safari here is
 * emulated WebKit — a real-device iOS pass stays a manual step.
 */
const BASE_URL = process.env.BASE_URL || 'http://ibiza-villas-2000.test';

export default defineConfig( {
	testDir: './tests',
	fullyParallel: true,
	forbidOnly: !! process.env.CI,
	retries: process.env.CI ? 2 : 0,
	reporter: [ [ 'list' ], [ 'html', { open: 'never' } ] ],
	use: {
		baseURL: BASE_URL,
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		// The theme sets scroll-behavior: smooth, which makes Playwright's
		// scroll-into-view animate and elements report "not stable". The
		// site's own prefers-reduced-motion query switches it to auto.
		reducedMotion: 'reduce',
	},
	projects: [
		{ name: 'chromium', use: { ...devices[ 'Desktop Chrome' ] } },
		{ name: 'firefox', use: { ...devices[ 'Desktop Firefox' ] } },
		{ name: 'webkit', use: { ...devices[ 'Desktop Safari' ] } },
		{ name: 'mobile-safari', use: { ...devices[ 'iPhone 14' ] } },
		{ name: 'mobile-chrome', use: { ...devices[ 'Pixel 7' ] } },
	],
} );
