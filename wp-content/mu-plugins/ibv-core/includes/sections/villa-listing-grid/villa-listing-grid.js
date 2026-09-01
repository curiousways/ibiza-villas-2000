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
	//
	// Prefer the server-computed window (ibvListingSearch.probe): it is the
	// same one ibv_villa_listing_preload_availability() preloads from
	// <head>, so using it keeps the fetch URL byte-identical and lets the
	// browser reuse the in-flight preload instead of fetching twice.
	var isProbe = ! params.date_from || ! params.date_to || ! params.pax;
	if ( isProbe ) {
		var probe = config.probe;
		if ( ! probe || ! probe.date_from || ! probe.date_to || ! probe.pax ) {
			var range = defaultProbeRange();
			probe = {
				date_from: range.from,
				date_to: range.to,
				pax: 2,
			};
		}
		params = {
			date_from: probe.date_from,
			date_to: probe.date_to,
			pax: probe.pax,
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
	 * Normalise the API response into [{ propertyId, stayTotal, nights }, …].
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

		// eur_base_rental is the rental total for the exact dates searched.
		// Steve confirmed on 28 August that no weekly equivalent can be
		// derived from it — short-break pricing is already weighted inside
		// the figure. Keep the stay total and label the stay; do not
		// convert.
		var nights = ( data && data.query ) ? Number( data.query.nights ) : NaN;

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
				var stayTotal = ( rate !== undefined && rate !== null ) ? Number( rate ) : null;

				// No rate card for these dates: available, but eur_base_rental 0. Not a
				// sellable result — drop it, rather than leaving the card visible with its
				// static price or a stale figure from an earlier search.
				if ( stayTotal === null || isNaN( stayTotal ) || stayTotal <= 0 ) {
					return null;
				}

				return {
					propertyId: String( pid ),
					stayTotal: stayTotal,
					nights: nights > 0 ? nights : null,
				};
			} )
			.filter( Boolean );
	}

	function formatNightsLabel( nights ) {
		var i18n = config.i18n || {};
		var template = nights === 1 ? i18n.nights_one : i18n.nights_many;
		if ( ! template ) {
			return '';
		}
		return template.replace( '%d', String( nights ) );
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

	/**
	 * Soft-dismiss the skeleton row: it shares the listing stack's single
	 * grid area with the card grid (see .ibv-listing-stack), so it overlaps
	 * the entering cards instead of flowing below them and adds no height —
	 * the stack takes the result set's size in the same paint, no jump.
	 * The --dismiss class keeps it rendered once --searching leaves the
	 * grid; it cross-fades away while the cards enter above it, and the
	 * node is removed once the fade lands. Under prefers-reduced-motion
	 * the transition is off, so the inline opacity applies in one paint
	 * and the timeout sweeps the node.
	 */
	function dismissSkeletonRow( row ) {
		row.classList.add( 'ibv-villa-skeleton-row--dismiss' );
		// Flush styles so the fade transitions from the rendered state.
		void row.offsetHeight;
		row.style.opacity = '0';

		var removed = false;
		function remove() {
			if ( ! removed && row.parentNode ) {
				removed = true;
				row.parentNode.removeChild( row );
			}
		}
		row.addEventListener( 'transitionend', function ( e ) {
			if ( e.target === row && e.propertyName === 'opacity' ) {
				remove();
			}
		} );
		// transitionend never fires inside a display:none grid (zero
		// results) or with transitions disabled — sweep the node regardless.
		setTimeout( remove, 1200 );
	}

	/**
	 * End the server-rendered "searching" state (skeleton placeholders shown,
	 * real cards CSS-hidden — see villa-listing-grid.php). Called on API
	 * response after filtering, and on fetch failure so the fallback grid is
	 * revealed per spec. The skeleton row cross-fades out in place over the
	 * entering cards rather than vanishing in the same paint. No-op when no
	 * dated search is active — PHP only adds the state then.
	 */
	function clearSearchingState() {
		var grid = $( '[data-bob-listing-grid]' );
		if ( ! grid || ! grid.classList.contains( 'ibv-listing-grid--searching' ) ) {
			return;
		}
		grid.classList.remove( 'ibv-listing-grid--searching' );
		grid.removeAttribute( 'aria-busy' );
		// The skeleton row is the grid's stack sibling, not a child.
		$$( '[data-bob-skeleton]' ).forEach( function ( row ) {
			// Zero results: the grid is hidden and the empty state takes
			// over — a lingering cross-fade would hover above it, so the
			// row goes instantly.
			if ( grid.hidden ) {
				if ( row.parentNode ) {
					row.parentNode.removeChild( row );
				}
				return;
			}
			dismissSkeletonRow( row );
		} );

		// Staggered enter: revealed cards fade/blur in, 60ms apart (capped
		// at 600ms so long result sets don't drag).
		// Selecting :not([hidden]) AFTER applyFilters() means delays follow
		// the final price-sorted order, not the server's menu_order.
		$$( 'article[data-bob-property-id]:not([hidden])', grid ).forEach( function ( card, i ) {
			card.style.animationDelay = Math.min( i * 60, 600 ) + 'ms';
			card.classList.add( 'ibv-villa-card--enter' );
			card.addEventListener( 'animationend', function handler( e ) {
				if ( e.target !== card ) {
					return;
				}
				card.classList.remove( 'ibv-villa-card--enter' );
				card.style.animationDelay = '';
				card.removeEventListener( 'animationend', handler );
			} );
		} );
	}

	/**
	 * End probe mode's --price-pending state (card price amounts masked
	 * with a skeleton block — see villa-listing-grid.php), so the static
	 * ACF price and the probe rate never flash in sequence. Called after
	 * probe hydration (revealing live rates; villas the probe didn't
	 * return show their static fallback) and on fetch failure (static
	 * fallback for all). Separate from clearSearchingState() — that one
	 * guards on --searching and staggers a card-enter animation that
	 * probe mode's already-visible cards must not replay.
	 */
	function clearPricePendingState() {
		var grid = $( '[data-bob-listing-grid]' );
		if ( ! grid ) {
			return;
		}
		grid.classList.remove( 'ibv-listing-grid--price-pending' );
	}

	// Offers checkbox state. Pure client-side filter — independent of
	// the availability fetch, so it works even when the API is down.
	var offersOnly = false;

	// propertyId → stay total from the last successful search. null =
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

		var totalByPropertyId = {};
		var nightsByPropertyId = {};
		results.forEach( function ( r ) {
			var key = String( r.propertyId ).toLowerCase();
			totalByPropertyId[ key ] = r.stayTotal;
			nightsByPropertyId[ key ] = r.nights;
		} );

		// Hydrate prices on matching cards. Visibility is exclusively
		// applyFilters()'s job.
		$$( 'article[data-bob-property-id]', grid ).forEach( function ( card ) {
			var pid = ( card.getAttribute( 'data-bob-property-id' ) || '' ).toLowerCase();
			if ( ! pid || ! Object.prototype.hasOwnProperty.call( totalByPropertyId, pid ) ) {
				return;
			}
			// rate > 0 guard (belt-and-braces: parseResults already drops <= 0):
			// out-of-season the API returns available villas with eur_base_rental 0
			// (no rate card loaded) — hydrating those would show "From €0 / wk"
			// and sort them first.
			var rate = totalByPropertyId[ pid ];
			if ( rate !== null && ! isNaN( rate ) && rate > 0 ) {
				card.setAttribute( 'data-price', String( rate ) );
				var priceEl = card.querySelector( '[data-bob-from-price]' );
				if ( priceEl ) {
					priceEl.textContent = formatEuro( rate );
					var pricePrefix = card.querySelector( '.ibv-villa-card__price-prefix' );
					var priceSuffix = card.querySelector( '.ibv-villa-card__price-suffix' );
					// Probe is an undated browse: keep the static "From … / wk"
					// treatment. A dated search is an exact stay total, not a
					// "from" weekly figure.
					if ( isProbe ) {
						if ( pricePrefix ) {
							pricePrefix.hidden = false;
						}
						if ( priceSuffix ) {
							priceSuffix.hidden = false;
						}
					} else {
						if ( pricePrefix ) {
							pricePrefix.hidden = true;
						}
						if ( priceSuffix ) {
							var nights = nightsByPropertyId[ pid ];
							if ( nights > 0 ) {
								priceSuffix.textContent = formatNightsLabel( nights );
								priceSuffix.hidden = false;
							} else {
								priceSuffix.hidden = true;
							}
						}
					}
				}
			}
		} );

		// Probe mode: prices only, leave grid order and visibility alone.
		// Unmasking after hydration paints each price exactly once.
		if ( isProbe ) {
			clearPricePendingState();
			return;
		}

		// The selected-dates pill ([data-bob-selected-dates]) is fully
		// server-rendered — its label is pure URL state, and revealing it
		// here after the fetch used to grow the sticky toolbar mid-view.

		availablePids = totalByPropertyId;
		applyFilters();
		// Reveal only after filtering/sorting — the unfiltered catalog is
		// never painted.
		clearSearchingState();
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

		// Keep this request vanilla (no custom headers, default credentials):
		// in search mode the same URL is preloaded from <head> via
		// ibv_villa_listing_preload_availability(), and any mismatch in mode,
		// credentials, or headers makes the browser fetch twice instead of
		// reusing the in-flight preload.
		fetch( url, {
			method: 'GET',
			signal: controller.signal,
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
				// Per spec: show the full fallback grid on failure/timeout —
				// including the static ACF prices probe mode had masked.
				clearSearchingState();
				clearPricePendingState();
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
