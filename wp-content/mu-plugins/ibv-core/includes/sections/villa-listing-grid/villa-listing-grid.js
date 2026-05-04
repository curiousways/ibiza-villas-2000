/**
 * Bob API search-mode hydration for the villa listing grid.
 *
 * Reads date_from / date_to / pax from the URL via the localized
 * `ibvListingSearch` object, calls Steve's PMS endpoint in search mode,
 * filters server-rendered cards down to the returned property_id set,
 * sorts price ascending, and toggles the empty-state block on zero results.
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

	if ( ! params.date_from || ! params.date_to || ! params.pax ) {
		return;
	}

	var TIMEOUT_MS = 6000;

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
				var pid = row.property_id || row.propertyId || row.id || row.villa;
				var rate = row.weekly_rate;
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

	function applyResults( results ) {
		var grid = $( '[data-bob-listing-grid]' );
		if ( ! grid ) {
			return;
		}

		var rateByPropertyId = {};
		results.forEach( function ( r ) {
			rateByPropertyId[ r.propertyId ] = r.weeklyRate;
		} );

		var cards = $$( 'article[data-bob-property-id]', grid );
		var visible = [];

		cards.forEach( function ( card ) {
			var pid = card.getAttribute( 'data-bob-property-id' );
			if ( ! pid || ! Object.prototype.hasOwnProperty.call( rateByPropertyId, pid ) ) {
				card.hidden = true;
				return;
			}
			card.hidden = false;
			var rate = rateByPropertyId[ pid ];
			if ( rate !== null && !isNaN( rate ) ) {
				card.setAttribute( 'data-price', String( rate ) );
				var priceEl = card.querySelector( '[data-bob-from-price]' );
				if ( priceEl ) {
					priceEl.textContent = formatEuro( rate );
				}
			}
			visible.push( card );
		} );

		visible.sort( function ( a, b ) {
			var pa = parseFloat( a.getAttribute( 'data-price' ) ) || Infinity;
			var pb = parseFloat( b.getAttribute( 'data-price' ) ) || Infinity;
			return pa - pb;
		} );
		visible.forEach( function ( card ) {
			grid.appendChild( card );
		} );

		var toolbar = $( '[data-bob-listing-toolbar]' );
		var countEl = $( '[data-bob-results-count]' );
		var emptyEl = $( '[data-bob-empty-state]' );

		if ( visible.length === 0 ) {
			grid.hidden = true;
			if ( emptyEl ) {
				emptyEl.hidden = false;
			}
			if ( toolbar ) {
				toolbar.hidden = true;
			}
			return;
		}

		if ( toolbar ) {
			toolbar.hidden = false;
		}
		if ( countEl ) {
			countEl.hidden = false;
			var template = ( config.i18n && config.i18n.showing ) || 'Showing %d villas';
			countEl.textContent = template.replace( '%d', String( visible.length ) );
		}
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
				// Per spec: leave server fallback in place on failure.
				if ( window.console && console.warn ) {
					console.warn( '[ibv listing search] availability fetch failed:', err );
				}
			} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', fetchAvailability );
	} else {
		fetchAvailability();
	}
}() );
