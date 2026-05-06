/**
 * Enquiry panel — Bob API detail-mode pricing fetch.
 *
 * Reads property_id + URL state from data-* attributes on the wrapper,
 * gates the Request to Book button on dates + pax being filled, and
 * fetches live pricing from Steve's PMS endpoint to update the price
 * block. On API failure it resets prices to "—" and logs a console
 * warning — the user-facing error pattern is owned by TODO #7.
 *
 * Response shape (per AGENTS.md):
 *   { success, count, query, villas: [ { eur_total_price, eur_base_rental,
 *     eur_adw_amount, eur_extra_cleaning, gbp_total_price, available, ... } ] }
 */
( function () {
	'use strict';

	function init( panel ) {
		var endpoint   = panel.getAttribute( 'data-bob-endpoint' ) || '';
		var propertyId = panel.getAttribute( 'data-bob-property-id' ) || '';
		var confirmUrl = panel.getAttribute( 'data-bob-confirm-url' ) || '';
		var villaId    = panel.getAttribute( 'data-villa-id' ) || '';

		var form = panel.querySelector( '.ibv-enquiry-panel__form' );
		if ( ! form ) {
			return;
		}

		var fromEl   = form.querySelector( '[name="date_from"]' );
		var toEl     = form.querySelector( '[name="date_to"]' );
		var paxEl    = form.querySelector( '[name="pax"]' );
		var submitEl = panel.querySelector( '[data-bob-submit]' );

		var totalEl    = panel.querySelector( '[data-bob-total-eur]' );
		var totalGbpEl = panel.querySelector( '[data-bob-total-gbp]' );
		var rentalEl   = panel.querySelector( '[data-bob-base-rental]' );
		var adwEl      = panel.querySelector( '[data-bob-adw]' );
		var cleaningEl = panel.querySelector( '[data-bob-cleaning]' );

		function money( currency ) {
			return new Intl.NumberFormat( 'en-GB', { style: 'currency', currency: currency, maximumFractionDigits: 0 } );
		}
		var EUR = money( 'EUR' );
		var GBP = money( 'GBP' );

		function isValidDate( s ) {
			return /^\d{4}-\d{2}-\d{2}$/.test( s || '' );
		}

		function readState() {
			return {
				date_from: fromEl ? fromEl.value : '',
				date_to:   toEl ? toEl.value : '',
				pax:       paxEl ? parseInt( paxEl.value, 10 ) || 0 : 0,
			};
		}

		function gateReady( s ) {
			return isValidDate( s.date_from ) && isValidDate( s.date_to ) && s.pax > 0;
		}

		function updateGate() {
			if ( ! submitEl ) {
				return;
			}
			if ( gateReady( readState() ) ) {
				submitEl.removeAttribute( 'disabled' );
			} else {
				submitEl.setAttribute( 'disabled', 'disabled' );
			}
		}

		function setText( el, txt ) {
			if ( el ) {
				el.textContent = txt;
			}
		}

		function revealPriceBlock() {
			panel.classList.remove( 'is-pricing-pending' );
		}

		function resetPrices() {
			setText( totalEl, '—' );
			setText( totalGbpEl, '—' );
			setText( rentalEl, '—' );
			setText( adwEl, '—' );
			setText( cleaningEl, '—' );
		}

		function pickNumber( obj, keys ) {
			for ( var i = 0; i < keys.length; i++ ) {
				var v = obj && obj[ keys[ i ] ];
				if ( typeof v === 'number' ) {
					return v;
				}
				if ( typeof v === 'string' && v !== '' && ! isNaN( parseFloat( v ) ) ) {
					return parseFloat( v );
				}
			}
			return null;
		}

		function paint( data ) {
			var node = null;
			if ( data && Array.isArray( data.villas ) && data.villas.length ) {
				node = data.villas[ 0 ];
			} else if ( data && data.villa ) {
				node = data.villa;
			} else {
				node = data;
			}

			if ( ! node || ( node.available !== undefined && Number( node.available ) !== 1 ) ) {
				resetPrices();
				return;
			}

			var total    = pickNumber( node, [ 'eur_total_price' ] );
			var totalGbp = pickNumber( node, [ 'gbp_total_price' ] );
			var rent     = pickNumber( node, [ 'eur_base_rental' ] );
			var adw      = pickNumber( node, [ 'eur_adw_amount' ] );
			var clean    = pickNumber( node, [ 'eur_extra_cleaning' ] );

			setText( totalEl,    total    !== null ? EUR.format( total )    : '—' );
			setText( totalGbpEl, totalGbp !== null ? GBP.format( totalGbp ) : '—' );
			setText( rentalEl,   rent     !== null ? EUR.format( rent )     : '—' );
			setText( adwEl,      adw      !== null ? EUR.format( adw )      : '—' );
			setText( cleaningEl, clean    !== null ? EUR.format( clean )    : '—' );
		}

		var inflight = null;
		function fetchPricing() {
			if ( ! endpoint || ! propertyId ) {
				return;
			}
			var s = readState();
			if ( ! gateReady( s ) ) {
				resetPrices();
				return;
			}

			var url = endpoint
				+ '?villa='     + encodeURIComponent( propertyId )
				+ '&date_from=' + encodeURIComponent( s.date_from )
				+ '&date_to='   + encodeURIComponent( s.date_to )
				+ '&pax='       + encodeURIComponent( String( s.pax ) );

			if ( inflight && typeof inflight.abort === 'function' ) {
				try {
					inflight.abort();
				} catch ( e ) {}
			}
			var ctrl = ( typeof AbortController !== 'undefined' ) ? new AbortController() : null;
			inflight = ctrl;

			fetch( url, { method: 'GET', credentials: 'omit', signal: ctrl ? ctrl.signal : undefined } )
				.then( function ( r ) {
					if ( ! r.ok ) {
						throw new Error( 'HTTP ' + r.status );
					}
					return r.json();
				} )
				.then( function ( data ) {
					paint( data );
					revealPriceBlock();
				} )
				.catch( function ( err ) {
					if ( err && err.name === 'AbortError' ) {
						return;
					}
					console.warn( '[ibv-enquiry-panel] pricing fetch failed', err );
					resetPrices();
					revealPriceBlock();
				} );
		}

		var debounceTimer = null;
		function schedule() {
			updateGate();
			if ( debounceTimer ) {
				clearTimeout( debounceTimer );
			}
			debounceTimer = setTimeout( fetchPricing, 300 );
		}

		[ fromEl, toEl, paxEl ].forEach( function ( el ) {
			if ( ! el ) {
				return;
			}
			el.addEventListener( 'change', schedule );
			el.addEventListener( 'input', schedule );
		} );

		form.addEventListener( 'submit', function ( ev ) {
			ev.preventDefault();
			var s = readState();
			if ( ! gateReady( s ) ) {
				updateGate();
				return;
			}
			// TODO: POST to enquiry endpoint when Steve confirms URL — redirect-only for now.
			var params = new URLSearchParams();
			if ( villaId ) {
				params.set( 'villa', villaId );
			}
			if ( s.date_from ) {
				params.set( 'arrival', s.date_from );
			}
			if ( s.date_to ) {
				params.set( 'departure', s.date_to );
			}
			if ( s.pax ) {
				params.set( 'guests', String( s.pax ) );
			}
			var sep = confirmUrl.indexOf( '?' ) === -1 ? '?' : '&';
			window.location.assign( confirmUrl + sep + params.toString() );
		} );

		updateGate();
		if ( gateReady( readState() ) ) {
			fetchPricing();
		}
	}

	function boot() {
		var panels = document.querySelectorAll( '[data-bob-enquiry-panel]' );
		for ( var i = 0; i < panels.length; i++ ) {
			init( panels[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
