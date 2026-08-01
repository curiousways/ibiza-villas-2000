# Cookie consent — vanilla-cookieconsent v3 site-wide (necessary + analytics)

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

**Brief type:** Feature — site-wide cookie consent for the IBZ002 rebuild,
using [CookieConsent by orestbida](https://cookieconsent.orestbida.com/)
v3.1.0 (the Curious Ways standard). Pre-launch requirement; pairs with the
Cookie Policy page being created on the Legal template.

## Background (verified against the codebase, 2026-08-01)

- The new stack (`ibv` theme + `ibv-core` mu-plugin) ships **no analytics
  and no consent tooling**. The only tracking snippet anywhere is a dead
  Universal Analytics tag (`UA-28761746-2`, `ga()` syntax) in the **legacy**
  theme's `header.php` — the legacy theme does not ship, so there is nothing
  to retrofit. Analytics arrives *with* this brief, correctly gated from day one.
- **`complianz-gdpr-premium` is present in `wp-content/plugins/`.** Two
  banners must never run at once. Its removal is Step 6 — but its *active*
  status on staging/production must be checked first, not assumed.
- Site-wide chrome belongs in `ibv-core`, not the theme: all shared assets
  are registered in `includes/shared-assets.php`, versioned with
  `IBV_CORE_VERSION` (currently `0.1.46` in `ibv-core.php`). Third-party
  vendor libraries live in `vendor/` subfolders (see
  `includes/components/date-range-picker/vendor/vanilla-calendar-pro/` and
  `includes/components/enquiry-panel/vendor/intl-tel-input/`). Consent is
  not a component — it goes under `includes/integrations/`, alongside the
  existing FacetWP integration.
- Categories: **`necessary` + `analytics` only.** No ad/remarketing pixels
  exist in the new stack (Gravity Forms feeds to Zoho/SendGrid/Mailchimp
  are server-side). Do not add a `marketing` category.
- WP Rocket is installed — the verification step must run against warm
  cache, and the cookieconsent files must be excluded from any "Delay JS"
  / minification if those features are enabled.

## Standards

- BEM `ibv-` prefix; plain CSS with `var(--ibv-*)` tokens from
  `assets/css/tokens.css`; plain PHP; no build step.
- Scripts registered in `shared-assets.php` with `IBV_CORE_VERSION`,
  `'in_footer' => true`, `'strategy' => 'defer'` where appropriate —
  follow the `ibv-intl-tel-input` registration as the model.
- New include files are required from `ibv-core.php` — match the existing
  require list ordering and comment style.
- No inline event handlers (`onclick=""`) — bind listeners in JS.

## Scope

**In:**
1. Self-host vanilla-cookieconsent v3.1.0 (UMD build) in `ibv-core`.
2. Site-wide enqueue + config: `necessary` + `analytics` categories,
   IV2000-branded copy, cookie tables, CW default GUI (`box inline`,
   `bottom right`).
3. GA4 snippet (`G-NW8WQP42F9`), consent-gated (`type="text/plain"` +
   `data-category`), with local/staging hostname blocking.
4. Banner styling via `--cc-*` variables mapped to `--ibv-*` tokens.
5. "Cookie preferences" reopen link in the site footer.
6. Complianz check-and-removal procedure (conditional — verify before acting).

**Out:**
- Cookie Policy page *content* (separate content task; page lands on the
  Legal template per the July checklist).
- Any `marketing` category, GTM, or Consent Mode conversation.
- The legacy theme — untouched, it never ships.
- Consent-record storage / IAB TCF — CookieConsent is not a CMP; fine for
  IV2000, flagged to the client if their side ever asks for consent logs.

## File list

| File | Action |
|---|---|
| `mu-plugins/ibv-core/assets/vendor/cookieconsent/cookieconsent.css` | **Create** (download, v3.1.0) |
| `mu-plugins/ibv-core/assets/vendor/cookieconsent/cookieconsent.umd.js` | **Create** (download, v3.1.0) |
| `mu-plugins/ibv-core/assets/js/cookieconsent-init.js` | **Create** |
| `mu-plugins/ibv-core/assets/js/ga4.js` | **Create** (gated GA4 bootstrap) |
| `mu-plugins/ibv-core/includes/integrations/cookie-consent.php` | **Create** (enqueue + gated GA4 output) |
| `mu-plugins/ibv-core/ibv-core.php` | **Edit** (require new include; bump `IBV_CORE_VERSION`) |
| `mu-plugins/ibv-core/assets/css/base.css` | **Edit** (banner token mapping — or the stylesheet the agent judges canonical for site-wide chrome; check where `.ibv-site-footer` styles live first) |
| `themes/ibv/footer.php` | **Edit** (Cookie preferences link) |

## Changes

### 1. Vendor files

Download the two v3.1.0 dist files into
`mu-plugins/ibv-core/assets/vendor/cookieconsent/`:

- `https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.css`
- `https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.umd.js`

UMD build only — it exposes a global `CookieConsent` object; no module
syntax, no build step. If shell access isn't available at implementation
time, stop and ask David to drop the files in.

### 2. `includes/integrations/cookie-consent.php`

```php
<?php
/**
 * Cookie consent — orestbida vanilla-cookieconsent v3 (UMD, self-hosted).
 *
 * Site-wide: banner + preferences modal, and the consent-gated GA4
 * bootstrap. Categories: necessary (read-only) + analytics.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'ibv_core_cookieconsent_enqueue' );
function ibv_core_cookieconsent_enqueue() {
	$dir = IBV_CORE_URL . 'assets/vendor/cookieconsent';

	wp_enqueue_style( 'ibv-cookieconsent', $dir . '/cookieconsent.css', [], '3.1.0' );

	wp_enqueue_script(
		'ibv-cookieconsent',
		$dir . '/cookieconsent.umd.js',
		[],
		'3.1.0',
		[ 'in_footer' => true, 'strategy' => 'defer' ]
	);

	wp_enqueue_script(
		'ibv-cookieconsent-init',
		IBV_CORE_URL . 'assets/js/cookieconsent-init.js',
		[ 'ibv-cookieconsent' ],
		IBV_CORE_VERSION,
		[ 'in_footer' => true, 'strategy' => 'defer' ]
	);
}

/**
 * GA4 — consent-gated. type="text/plain" + data-category stops execution
 * until CookieConsent flips the type on analytics consent.
 * Printed manually because wp_enqueue_script cannot emit a gated tag.
 */
add_action( 'wp_head', 'ibv_core_ga4_gated', 20 );
function ibv_core_ga4_gated() {
	$src = IBV_CORE_URL . 'assets/js/ga4.js?ver=' . IBV_CORE_VERSION;
	echo '<script async src="https://www.googletagmanager.com/gtag/js?id=G-NW8WQP42F9" type="text/plain" data-category="analytics" data-service="Google Analytics"></script>' . "\n";
	echo '<script src="' . esc_url( $src ) . '" type="text/plain" data-category="analytics" data-service="Google Analytics"></script>' . "\n";
}
```

`G-NW8WQP42F9` is the confirmed GA4 stream for the live site (David,
1 Aug 2026) — it appears here and in `ga4.js`; keep the two in sync. If a cleaner
pattern for printing the gated pair already suggests itself from the
codebase (e.g. a `script_loader_tag` filter on registered handles),
push back and propose it; the non-negotiables are `type="text/plain"`,
`data-category="analytics"`, and zero execution before consent.

### 3. `assets/js/ga4.js`

```js
/**
 * GA4 bootstrap — only ever executes after analytics consent
 * (cookieconsent flips the wrapping tag's type). Never fires on
 * local or staging hosts.
 */
(function () {
	var host = window.location.hostname;
	var blocked = [
		'ibiza-villas-2000.test',
		'localhost',
		'staging.ibizavillas2000.com'
	];
	if ( blocked.indexOf( host ) !== -1 || host.indexOf( '.test' ) !== -1 ) {
		console.log( 'Google Analytics blocked — development/staging environment' );
		return;
	}
	window.dataLayer = window.dataLayer || [];
	function gtag() { dataLayer.push( arguments ); }
	gtag( 'js', new Date() );
	gtag( 'config', 'G-NW8WQP42F9' );
})();
```

### 4. `assets/js/cookieconsent-init.js`

Full config — CW standard shape, IV2000 copy and links:

```js
/**
 * CookieConsent v3 config — IV2000.
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run({

	categories: {
		necessary: { enabled: true, readOnly: true },
		analytics: {}
	},

	guiOptions: {
		consentModal: { layout: 'box inline', position: 'bottom right' }
	},

	language: {
		default: 'en',
		translations: {
			en: {
				consentModal: {
					title: 'Cookies at Ibiza Villas 2000',
					description: 'This website uses essential cookies to ensure its proper operation and, with your consent, analytics cookies to understand how visitors use the site.',
					acceptAllBtn: 'Accept all',
					acceptNecessaryBtn: 'Reject all',
					showPreferencesBtn: 'Manage preferences'
				},
				preferencesModal: {
					title: 'Manage cookie preferences',
					acceptAllBtn: 'Accept all',
					acceptNecessaryBtn: 'Reject all',
					savePreferencesBtn: 'Accept current selection',
					closeIconLabel: 'Close modal',
					sections: [
						{
							title: 'Cookie usage',
							description: 'We use cookies to ensure the basic functionalities of the website and to enhance your online experience. You can opt in or out of each category whenever you want.'
						},
						{
							title: 'Strictly necessary cookies',
							description: 'These cookies are essential for the proper functioning of the website and cannot be disabled.',
							linkedCategory: 'necessary',
							cookieTable: {
								headers: { name: 'Cookie', domain: 'Domain', expiration: 'Expiration', description: 'Description' },
								body: [
									{ name: 'cc_cookie', domain: 'This website', expiration: '1 year', description: 'Stores your cookie consent preferences.' }
									// Populate further rows from the Step 5 audit.
								]
							}
						},
						{
							title: 'Performance and analytics',
							description: 'These cookies allow us to measure website traffic and understand how visitors interact with the site using Google Analytics. Data is anonymised.',
							linkedCategory: 'analytics',
							cookieTable: {
								headers: { name: 'Cookie', domain: 'Domain', expiration: 'Expiration', description: 'Description' },
								body: [
									{ name: '_ga', domain: 'google.com', expiration: '2 years', description: 'Used to distinguish users for Google Analytics.' },
									{ name: '_ga_*', domain: 'google.com', expiration: '2 years', description: 'Used to persist session state for Google Analytics.' }
								]
							}
						},
						{
							title: 'More information',
							description: 'See our <a href="/cookie-policy/">Cookie Policy</a> for full details, or <a href="/contact/">contact us</a> with any questions. Our <a href="/privacy-policy/">Privacy Policy</a> covers how we handle your data.'
						}
					]
				}
			}
		}
	}
});

// Footer "Cookie preferences" reopen link — no inline handlers.
document.addEventListener( 'click', function ( e ) {
	var link = e.target.closest( '[data-ibv-cc-preferences]' );
	if ( ! link ) {
		return;
	}
	e.preventDefault();
	CookieConsent.showPreferences();
} );
```

Link paths to verify at implementation time: `/cookie-policy/` and
`/privacy-policy/` are being created/adopted onto the Legal template per
the July admin checklist — confirm the slugs exist on the environment
being tested, and flag (don't silently ship) if they don't yet.

### 5. Footer link + banner styling

**`themes/ibv/footer.php`** — after the `footer_legal_html` output (David
keeps that WYSIWYG for legal links; the consent link is code, not
content, so it lives in the template), add within the same legal/meta
area, matching surrounding markup conventions:

```php
<a href="#" class="ibv-site-footer__cc-link" data-ibv-cc-preferences><?php esc_html_e( 'Cookie preferences', 'ibv' ); ?></a>
```

Style it to match the adjacent legal links (read the footer CSS first;
reuse existing classes if the legal-links block already styles plain
anchors).

**Banner tokens** — in the stylesheet that owns site-wide chrome:

```css
/* Cookie consent banner — map library vars to IV2000 tokens. */
#cc-main {
	--cc-font-family: inherit;
	--cc-btn-primary-bg: var(--ibv-color-forest-green);
	--cc-btn-primary-hover-bg: var(--ibv-color-forest-green-deep);
}
```

Read `tokens.css` and the button component CSS before finalising —
primary actions on this site are forest green; use the exact token
names in the file, and extend the mapping (border-radius, focus ring
via `--ibv-color-focus-ring`) only as far as needed for the banner to
sit comfortably in the design. Full variable list:
`https://github.com/orestbida/cookieconsent/blob/master/src/scss/abstracts/_light-color-scheme.scss`

### 6. Complianz — check, then remove (conditional)

**On each of staging and production, before this ships:**

Decision confirmed (David, 1 Aug 2026): **all cookie-consent plugins go,
unconditionally** — fewer plugins, custom solution. This includes
`complianz-gdpr-premium` and any other consent plugin found on an
environment.

1. Deactivate (if active) and **delete** `complianz-gdpr-premium` from
   local, staging, and production. Remove its folder from the repo's
   `wp-content/plugins/` too, so it can't ride along in a deploy.
2. While there, check the environment's plugin list for any *other*
   consent/cookie plugin and remove it the same way — one custom
   solution, zero consent plugins, is the end state.
3. Search for leftovers: `cmplz` / `complianz` references in theme
   files, any header/footer injection plugins, and WPCode-style
   snippets (none found in the new stack — this is a belt-and-braces
   sweep of the *environment*, not the repo).
4. Purge all cache layers (WP Rocket, any server/CDN cache) so cached
   pages don't carry the old banner.
5. Confirm no `cmplz_*` cookies are set for new private-window visitors.
   (Stale ones on returning visitors expire harmlessly.)

## Verification

Admin:
- [ ] `IBV_CORE_VERSION` bumped; new include required from `ibv-core.php` without notices.
- [ ] No Complianz banner/plugin active on the environment under test (Step 6).

Frontend (private window, logged out):
- [ ] Banner appears on first visit, bottom right, IV2000-styled, no console errors.
- [ ] **Reject all** → no GA execution: no `_ga*` cookies, no `googletagmanager.com` / `collect` network requests; only `cc_cookie` (plus any audited strictly-necessary cookies) present.
- [ ] **Accept all** → GA fires (Network tab shows `collect` requests) — *on the live domain only*; on `.test`/staging the hostname block logs and exits instead.
- [ ] Preferences modal opens from the banner **and** from the footer "Cookie preferences" link; selections persist across reloads; no banner on second visit.
- [ ] Cookie audit (DevTools → Application → Cookies): accept all, record every cookie/domain/expiry; reject all after clearing, confirm the clean state; **update both cookie tables in the config to match findings**. Check the villa detail page specifically (Google Maps embed) and any page with the Google reviews / Elfsight testimonials widgets — if third-party widgets set cookies, list them and flag to David rather than inventing a category.
- [ ] With WP Rocket cache warm: banner still appears for a fresh visitor (client-side JS, no cache exclusions needed — just confirm), and cookieconsent files are excluded from Delay JS/minification if those are on.
- [ ] Only one banner, ever.

## Notes

- **GA4:** `G-NW8WQP42F9` is the confirmed stream ID for the final live
  site (ibizavillas2000.com). It appears in exactly two files
  (`cookie-consent.php` and `ga4.js`) — keep them in sync. The legacy
  UA tag (`UA-28761746-2`) is dead and ships nowhere.
- **Hostnames:** live is `ibizavillas2000.com`; staging is
  `staging.ibizavillas2000.com` and is in the `ga4.js` blocklist along
  with `localhost` and any `.test` host — GA must never fire off the
  live domain.
- Self-hosted over CDN deliberately: no third-party request before
  consent, no jsDelivr privacy wrinkle. Version-pinned `3.1.0`
  everywhere for cache busting.
- The `gravity-forms-google-analytics-event-tracking` plugin only
  matters once GA4 is live; if it's kept, its output must be checked in
  the Step 5 audit (it may print its own gtag calls — if so, gate or
  retire it; likely retire, as GA4 form events can come later).
- Not a CMP: no stored consent records, no IAB TCF. Standard CW
  position; revisit only if IV2000's side ever demands consent logs.
- Deliberately deferred: GTM/Consent Mode, marketing category, GA4 form
  event tracking, and any cookie work for GTranslate (`googtrans`
  cookie) unless the audit shows it firing on the new stack — in which
  case flag it, don't improvise.
