/**
 * Bob API search-mode hydration + filters for the villa listing grid.
 *
 * Reads date_from / date_to / pax from the URL via the localized
 * `ibvListingSearch` object, calls Steve's PMS endpoint in search mode,
 * filters server-rendered cards down to the returned property_id set,
 * sorts price ascending, and toggles the empty-state block on zero results.
 *
 * The "Offers" checkbox is a pure client-side filter on the
 * server-rendered `data-bob-has-offer` card attribute (driven by the
 * villa_offers ACF repeater). It works with or without an active search
 * — the two filters intersect — and stays functional when the API
 * fetch fails.
 *
 * On API failure or timeout the server fallback grid is left intact —
 * the page must not break.
 */
( function () {
	'use strict';

	if ( typeof window.ibvListingSearch === 'undefined' ) {
		return;
	}

	var config = window.ibvListingSearch;
	var params = config.params || {};

	// When no user search is active, probe the API with a default future
	// week so cards can show a real "from" weekly rate instead of the
	// €420 ACF fallback. In probe mode we hydrate prices only — never
	// filter, hide, sort, or toggle the empty state.
	var isProbe = ! params.date_from || ! params.date_to || ! params.pax;
	if ( isProbe ) {
		var probe = defaultProbeRange();
		params = {
			date_from: probe.from,
			date_to: probe.to,
			pax: 2,
		};
	}

	var TIMEOUT_MS = 6000;

	function pad( n ) {
		return n < 10 ? '0' + n : String( n );
	}

	function formatYmd( d ) {
		return d.getUTCFullYear() + '-' + pad( d.getUTCMonth() + 1 ) + '-' + pad( d.getUTCDate() );
	}

	function defaultProbeRange() {
		var from = new Date();
		from.setUTCDate( from.getUTCDate() + 30 );
		var to = new Date( from.getTime() );
		to.setUTCDate( to.getUTCDate() + 7 );
		return { from: formatYmd( from ), to: formatYmd( to ) };
	}

	/**
	 * Format a YYYY-MM-DD pair as a compact human-readable range.
	 * Mirrors formatRangeForDisplay() in date-range-picker.js (IIFE-scoped
	 * there, so duplicated here to keep this script dependency-free).
	 * Example: "2026-05-22" / "2026-05-29" -> "May 22 – 29"
	 */
	function formatRangeForDisplay( fromYMD, toYMD ) {
		var from = new Date( fromYMD + 'T00:00:00' );
		var to   = new Date( toYMD + 'T00:00:00' );
		if ( isNaN( from.getTime() ) || isNaN( to.getTime() ) ) {
			return '';
		}
		var sameYear  = from.getFullYear() === to.getFullYear();
		var sameMonth = sameYear && from.getMonth() === to.getMonth();
		var monthDay  = new Intl.DateTimeFormat( 'en-GB', { month: 'short', day: 'numeric' } );
		var dayOnly   = new Intl.DateTimeFormat( 'en-GB', { day: 'numeric' } );
		var withYear  = new Intl.DateTimeFormat( 'en-GB', { month: 'short', day: 'numeric', year: 'numeric' } );
		if ( sameMonth ) {
			return monthDay.format( from ) + ' – ' + dayOnly.format( to );
		}
		if ( sameYear ) {
			return monthDay.format( from ) + ' – ' + monthDay.format( to );
		}
		return monthDay.format( from ) + ' – ' + withYear.format( to );
	}

	function $( selector, root ) {
		return ( root || document ).querySelector( selector );
	}

	function $$( selector, root ) {
		return Array.prototype.slice.call( ( root || document ).querySelectorAll( selector ) );
	}

	function buildUrl( base, query ) {
		var url = new URL( base );
		Object.keys( query ).forEach( function ( key ) {
			if ( query[ key ] !== '' && query[ key ] !== null && query[ key ] !== undefined ) {
				url.searchParams.set( key, query[ key ] );
			}
		} );
		return url.toString();
	}

	/**
	 * Normalise the API response into [{ propertyId, weeklyRate }, …].
	 * Field names are based on the v1 contract; keep this funnel small so
	 * we have one place to tweak when Steve confirms the schema.
	 */
	function parseResults( data ) {
		var list = [];
		if ( Array.isArray( data ) ) {
			list = data;
		} else if ( data && Array.isArray( data.villas ) ) {
			list = data.villas;
		} else if ( data && Array.isArray( data.results ) ) {
			list = data.results;
		}

		return list
			.map( function ( row ) {
				if ( ! row || typeof row !== 'object' ) {
					return null;
				}
				if ( row.available !== undefined && Number( row.available ) !== 1 ) {
					return null;
				}
				var pid = row.villa || row.property_id || row.propertyId || row.id;
				var rate = row.eur_base_rental;
				if ( rate === undefined ) {
					rate = row.weekly_rate;
				}
				if ( rate === undefined ) {
					rate = row.weeklyRate;
				}
				if ( rate === undefined ) {
					rate = row.rate;
				}
				if ( ! pid ) {
					return null;
				}
				return {
					propertyId: String( pid ),
					weeklyRate: rate !== undefined && rate !== null ? Number( rate ) : null,
				};
			} )
			.filter( Boolean );
	}

	function formatEuro( amount ) {
		if ( amount === null || isNaN( amount ) ) {
			return '';
		}
		try {
			return '€' + new Intl.NumberFormat( 'en-GB', { maximumFractionDigits: 0 } ).format( amount );
		} catch ( e ) {
			return '€' + Math.round( amount );
		}
	}

	// Offers checkbox state. Pure client-side filter — independent of
	// the availability fetch, so it works even when the API is down.
	var offersOnly = false;

	// propertyId → weekly rate from the last successful search. null =
	// no availability filter applied (probe mode, missing params, or
	// failed fetch): every card passes the availability check.
	var availablePids = null;

	/**
	 * Single owner of card visibility. Intersects the availability set
	 * (when a search is active) with the offers filter, then reconciles
	 * grid / empty-state / count. Re-runnable: every branch sets state
	 * both ways so check → uncheck always recovers.
	 */
	function applyFilters() {
		var grid = $( '[data-bob-listing-grid]' );
		if ( ! grid ) {
			return;
		}

		var searchActive = availablePids !== null;
		var visible = [];

		$$( 'article[data-bob-property-id]', grid ).forEach( function ( card ) {
			var pid = ( card.getAttribute( 'data-bob-property-id' ) || '' ).toLowerCase();
			var show = ( ! searchActive || Object.prototype.hasOwnProperty.call( availablePids, pid ) )
				&& ( ! offersOnly || card.hasAttribute( 'data-bob-has-offer' ) );
			card.hidden = ! show;
			if ( show ) {
				visible.push( card );
			}
		} );

		// Search results are price-sorted; offers-only without a search
		// keeps the server's menu_order, so no re-append in that case.
		if ( searchActive ) {
			visible.sort( function ( a, b ) {
				var pa = parseFloat( a.getAttribute( 'data-price' ) ) || Infinity;
				var pb = parseFloat( b.getAttribute( 'data-price' ) ) || Infinity;
				return pa - pb;
			} );
			visible.forEach( function ( card ) {
				grid.appendChild( card );
			} );
		}

		var emptyEl = $( '[data-bob-empty-state]' );
		grid.hidden = visible.length === 0;
		if ( emptyEl ) {
			emptyEl.hidden = visible.length !== 0;
		}

		var countEl = $( '[data-bob-results-count]' );
		if ( countEl ) {
			var showCount = ( searchActive || offersOnly ) && visible.length > 0;
			countEl.hidden = ! showCount;
			if ( showCount ) {
				var template = ( config.i18n && config.i18n.showing ) || 'Showing %d villas';
				countEl.textContent = template.replace( '%d', String( visible.length ) );
			}
		}
	}

	function applyResults( results ) {
		var grid = $( '[data-bob-listing-grid]' );
		if ( ! grid ) {
			return;
		}

		var rateByPropertyId = {};
		results.forEach( function ( r ) {
			rateByPropertyId[ String( r.propertyId ).toLowerCase() ] = r.weeklyRate;
		} );

		// Hydrate prices on matching cards. Visibility is exclusively
		// applyFilters()'s job.
		$$( 'article[data-bob-property-id]', grid ).forEach( function ( card ) {
			var pid = ( card.getAttribute( 'data-bob-property-id' ) || '' ).toLowerCase();
			if ( ! pid || ! Object.prototype.hasOwnProperty.call( rateByPropertyId, pid ) ) {
				return;
			}
			// rate > 0 guard: out-of-season the API returns available villas
			// with eur_base_rental 0 (no rate card loaded) — hydrating those
			// would show "From €0 / wk" and sort them first.
			var rate = rateByPropertyId[ pid ];
			if ( rate !== null && ! isNaN( rate ) && rate > 0 ) {
				card.setAttribute( 'data-price', String( rate ) );
				var priceEl = card.querySelector( '[data-bob-from-price]' );
				if ( priceEl ) {
					priceEl.textContent = formatEuro( rate );
					priceEl.classList.remove( 'ibv-villa-card__price-amount--on-request' );
					var pricePrefix = card.querySelector( '.ibv-villa-card__price-prefix' );
					var priceSuffix = card.querySelector( '.ibv-villa-card__price-suffix' );
					if ( pricePrefix ) {
						pricePrefix.hidden = false;
					}
					if ( priceSuffix ) {
						priceSuffix.hidden = false;
					}
				}
			}
		} );

		// Probe mode: prices only, leave grid order and visibility alone.
		if ( isProbe ) {
			return;
		}

		// Only reachable when !isProbe (probe mode returns early above), so
		// params still holds the user's real URL params, never probe dates.
		var datesEl = $( '[data-bob-selected-dates]' );
		var datesLabel = $( '[data-bob-selected-dates-label]' );
		if ( datesEl && datesLabel ) {
			var range = formatRangeForDisplay( params.date_from, params.date_to );
			var pax = parseInt( params.pax, 10 );
			var paxTemplate = ( pax === 1 )
				? ( ( config.i18n && config.i18n.guest ) || '%d guest' )
				: ( ( config.i18n && config.i18n.guests ) || '%d guests' );
			datesLabel.textContent = range + ' · ' + paxTemplate.replace( '%d', String( pax ) );
			datesEl.hidden = false;
		}

		availablePids = rateByPropertyId;
		applyFilters();
	}

	function fetchAvailability() {
		var controller = new AbortController();
		var timeoutId = setTimeout( function () {
			controller.abort();
		}, TIMEOUT_MS );

		var url = buildUrl( config.endpoint, {
			date_from: params.date_from,
			date_to: params.date_to,
			pax: params.pax,
		} );

		// console.log( '[ibv listing search] fetching', url );

		fetch( url, {
			method: 'GET',
			headers: { Accept: 'application/json' },
			signal: controller.signal,
			credentials: 'omit',
		} )
			.then( function ( res ) {
				clearTimeout( timeoutId );
				if ( ! res.ok ) {
					throw new Error( 'HTTP ' + res.status );
				}
				return res.json();
			} )
			.then( function ( data ) {
				applyResults( parseResults( data ) );
			} )
			.catch( function ( err ) {
				clearTimeout( timeoutId );
				// Per spec: leave server fallback in place on failure. We still
				// surface the error to the console so silent CORS / endpoint
				// failures are diagnosable in dev — production fallback is the
				// same regardless.
				if ( window.console && console.warn ) {
					console.warn( '[ibv listing search] availability fetch failed:', err );
				}
			} );
	}

	function init() {
		// Bind at init (not in the fetch callback) so the offers filter
		// works in probe mode, after a failed fetch, and while a fetch
		// is in flight — the response's applyFilters() call re-applies
		// the intersection when it lands.
		var checkbox = $( '[data-bob-filter-offers]' );
		if ( checkbox ) {
			checkbox.addEventListener( 'change', function () {
				offersOnly = checkbox.checked;
				applyFilters();
			} );
		}
		fetchAvailability();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
