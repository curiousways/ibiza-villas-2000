/**
 * Villa enquiry panel — live pricing / availability gate grafted onto the
 * embedded Gravity Form (#33).
 *
 * GF owns submit / validation / notification / redirect. This script grafts on
 * the villa-specific behaviour the same way accommodation-enquiry.js grafts the
 * picker + phone — and re-binds on `gform_post_render` so everything survives
 * GF's AJAX re-render (validation errors):
 *
 *   - the single-field "When" date-range picker (self-contained VanillaCalendarPro
 *     binding, reusing the ibv-date-range-picker brand CSS) writing the YYYY-MM-DD
 *     range into the .ibv-drp-from / .ibv-drp-to date inputs;
 *   - live pricing from Steve's PMS (detail mode), painted into the [data-bob-*]
 *     targets in the GF HTML price field;
 *   - the submit gate (GF submit button disabled until dates + pax are filled)
 *     + the contact-field reveal (is-contact-pending); unavailable dates still
 *     show the form so the guest can enquire;
 *   - intl-tel-input phone with strict validation + E.164 normalisation before GF
 *     serialises.
 *
 * The shared date-range-picker.js (header/hero search) boots once on
 * DOMContentLoaded with no re-init API and must stay untouched, so the picker
 * binding is duplicated here (the accommodation pattern).
 *
 * Pricing response shape (per AGENTS.md):
 *   { success, count, query, villas: [ { eur_total_price, eur_base_rental,
 *     eur_adw_amount, eur_extra_cleaning, available, ... } ] }
 */
( function () {
	'use strict';

	var instanceCount = 0;

	/* ── Date helpers ───────────────────────────────────────────────── */

	function pad( n ) {
		return ( n < 10 ? '0' : '' ) + n;
	}

	function formatYMD( d ) {
		return d.getFullYear() + '-' + pad( d.getMonth() + 1 ) + '-' + pad( d.getDate() );
	}

	function todayYMD() {
		return formatYMD( new Date() );
	}

	function isValidYMD( s ) {
		return /^\d{4}-\d{2}-\d{2}$/.test( s || '' );
	}

	function setInputValue( input, value ) {
		if ( ! input ) {
			return;
		}
		input.value = value;
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
	}

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

	function injectAnchorRules( instanceId ) {
		var anchorName = '--ibv-drp-anchor-' + instanceId;
		var styleEl = document.createElement( 'style' );
		styleEl.setAttribute( 'data-ibv-drp-anchor-styles', String( instanceId ) );
		styleEl.textContent =
			'[data-ibv-drp-anchor-source="' + instanceId + '"] {' +
				'anchor-name: ' + anchorName + ';' +
			'}' +
			'[data-ibv-drp-anchor-target="' + instanceId + '"] {' +
				'position-anchor: ' + anchorName + ';' +
			'}';
		document.head.appendChild( styleEl );
	}

	/* ── Date-range picker (one per form) ───────────────────────────── */

	function bindPicker( panel, form ) {
		if ( form.__ibvVillaDrpBound ) {
			return;
		}

		var fromWrap = form.querySelector( '.ibv-drp-from' );
		var toWrap   = form.querySelector( '.ibv-drp-to' );
		var fromInput = fromWrap ? fromWrap.querySelector( 'input' ) : null;
		var toInput   = toWrap ? toWrap.querySelector( 'input' ) : null;
		var trigger   = form.querySelector( '[data-bob-date-range-trigger]' );
		var display   = form.querySelector( '[data-bob-date-range-display]' );
		var clearBtn  = form.querySelector( '[data-bob-date-range-clear]' );
		if ( ! fromInput || ! toInput || ! trigger ) {
			return;
		}
		form.__ibvVillaDrpBound = true;

		// Tear down any popover from a previous render of this panel.
		if ( panel.__ibvPopover && panel.__ibvPopover.parentNode ) {
			panel.__ibvPopover.parentNode.removeChild( panel.__ibvPopover );
		}

		var displayPlaceholder = display ? ( display.dataset.placeholder || display.textContent || 'Add dates' ) : '';
		var instanceId = ++instanceCount;

		var popover = document.createElement( 'div' );
		popover.className = 'ibv-drp__popover';
		popover.setAttribute( 'popover', 'auto' );
		popover.setAttribute( 'data-ibv-drp-anchor-target', String( instanceId ) );

		var calendarHost = document.createElement( 'div' );
		calendarHost.className = 'ibv-drp__calendar';
		popover.appendChild( calendarHost );
		document.body.appendChild( popover );
		panel.__ibvPopover = popover;

		var anchorEl = form.querySelector( '[data-bob-date-range-anchor]' ) || trigger;
		anchorEl.setAttribute( 'data-ibv-drp-anchor-source', String( instanceId ) );
		injectAnchorRules( instanceId );

		var calendar = null;

		function buildCalendar() {
			if ( calendar ) {
				return;
			}
			var Calendar = window.VanillaCalendarPro && window.VanillaCalendarPro.Calendar;
			if ( ! Calendar ) {
				return;
			}
			var seedFrom = isValidYMD( fromInput.value ) ? fromInput.value : '';
			var seedTo   = isValidYMD( toInput.value )   ? toInput.value   : '';
			var today    = todayYMD();

			calendar = new Calendar( calendarHost, {
				type: 'multiple',
				displayMonthsCount: 2,
				monthsToSwitch: 1,
				selectionDatesMode: 'multiple-ranged',
				enableEdgeDatesOnly: true,
				disableDatesPast: true,
				selectedTheme: 'light',
				themeAttrDetect: false,
				locale: 'en-GB',
				dateMin: today,
				displayDateMin: today,
				selectedDates: ( seedFrom && seedTo ) ? [ seedFrom, seedTo ] : [],
				firstWeekday: 1,
				onClickDate: function ( self ) {
					var dates = self.context.selectedDates || [];
					if ( dates.length === 2 ) {
						var sorted = dates.slice().sort();
						setInputValue( fromInput, sorted[ 0 ] );
						setInputValue( toInput, sorted[ 1 ] );
					} else if ( dates.length === 0 ) {
						setInputValue( fromInput, '' );
						setInputValue( toInput, '' );
					}
				},
			} );
			calendar.init();
		}

		function open() {
			if ( popover.matches( ':popover-open' ) ) {
				return;
			}
			buildCalendar();
			popover.showPopover();
		}

		function syncDisplay() {
			if ( ! display ) {
				return;
			}
			var f = fromInput.value;
			var t = toInput.value;
			var formatted = ( isValidYMD( f ) && isValidYMD( t ) ) ? formatRangeForDisplay( f, t ) : '';
			if ( formatted ) {
				display.textContent = formatted;
				display.classList.remove( 'is-empty' );
			} else {
				display.textContent = displayPlaceholder;
				display.classList.add( 'is-empty' );
			}
			if ( clearBtn ) {
				clearBtn.hidden = ! formatted;
			}
		}

		trigger.addEventListener( 'click', function ( ev ) {
			ev.preventDefault();
			open();
		} );

		[ fromInput, toInput ].forEach( function ( input ) {
			input.addEventListener( 'change', syncDisplay );
		} );

		if ( clearBtn ) {
			clearBtn.addEventListener( 'click', function ( ev ) {
				ev.preventDefault();
				ev.stopPropagation();
				setInputValue( fromInput, '' );
				setInputValue( toInput, '' );
				if ( calendar ) {
					calendar.set( { selectedDates: [] }, { year: false, month: false, time: false } );
				}
				syncDisplay();
			} );
		}

		syncDisplay();
	}

	/* ── Pricing / gate / phone graft ───────────────────────────────── */

	function bindEnquiry( panel, form ) {
		if ( form.__ibvVillaBound ) {
			return;
		}
		form.__ibvVillaBound = true;

		var endpoint   = panel.getAttribute( 'data-bob-endpoint' ) || '';
		var propertyId = panel.getAttribute( 'data-bob-property-id' ) || '';

		var fromEl   = ( form.querySelector( '.ibv-drp-from' ) || form ).querySelector( 'input' );
		var toEl     = ( form.querySelector( '.ibv-drp-to' ) || form ).querySelector( 'input' );
		var paxWrap  = form.querySelector( '.ibv-pax' );
		var paxEl    = paxWrap ? ( paxWrap.querySelector( 'select' ) || paxWrap.querySelector( 'input' ) ) : null;
		var submitEl = form.querySelector( 'button[type="submit"], input[type="submit"]' );

		// Re-resolve the .ibv-drp inputs precisely (the fallback above is only a guard).
		var fromWrap = form.querySelector( '.ibv-drp-from' );
		var toWrap   = form.querySelector( '.ibv-drp-to' );
		fromEl = fromWrap ? fromWrap.querySelector( 'input' ) : fromEl;
		toEl   = toWrap ? toWrap.querySelector( 'input' ) : toEl;

		var phoneEl      = form.querySelector( '.gfield--type-phone input[type="tel"]' );
		var phoneFieldEl = phoneEl ? phoneEl.closest( '.gfield' ) : null;
		var utilsUrl     = panel.getAttribute( 'data-iti-utils-url' ) || '';
		var msgInvalidPhone = panel.getAttribute( 'data-bob-msg-invalid-phone' ) || '';

		// Phone error element — created in the phone field (re-created each render).
		var phoneErrorEl = null;
		if ( phoneFieldEl ) {
			phoneErrorEl = phoneFieldEl.querySelector( '[data-bob-phone-error]' );
			if ( ! phoneErrorEl ) {
				phoneErrorEl = document.createElement( 'p' );
				phoneErrorEl.className = 'ibv-enquiry-panel__phone-error';
				phoneErrorEl.setAttribute( 'data-bob-phone-error', '' );
				phoneErrorEl.setAttribute( 'role', 'alert' );
				phoneErrorEl.setAttribute( 'hidden', '' );
				phoneFieldEl.appendChild( phoneErrorEl );
			}
		}

		var iti = null;
		if ( phoneEl && typeof window.intlTelInput === 'function' ) {
			iti = window.intlTelInput( phoneEl, {
				initialCountry:   'gb',
				separateDialCode: true,
				strictMode:       true,
				placeholderNumberPolicy: 'AGGRESSIVE',
				loadUtils:        utilsUrl ? function () { return import( utilsUrl ); } : null,
			} );

			// Skip the flag button in the keyboard tab order (still mouse-clickable).
			var itiWrap    = phoneEl.closest( '.iti' );
			var countryBtn = itiWrap ? itiWrap.querySelector( '.iti__selected-country' ) : null;
			if ( countryBtn ) {
				countryBtn.setAttribute( 'tabindex', '-1' );
			}
		}

		function showPhoneError() {
			if ( phoneErrorEl && msgInvalidPhone ) {
				phoneErrorEl.textContent = msgInvalidPhone;
				phoneErrorEl.removeAttribute( 'hidden' );
			}
			if ( phoneFieldEl ) {
				phoneFieldEl.classList.add( 'has-error' );
			}
		}

		function clearPhoneError() {
			if ( phoneErrorEl ) {
				phoneErrorEl.textContent = '';
				phoneErrorEl.setAttribute( 'hidden', '' );
			}
			if ( phoneFieldEl ) {
				phoneFieldEl.classList.remove( 'has-error' );
			}
		}

		if ( phoneEl ) {
			phoneEl.addEventListener( 'input', clearPhoneError );
			phoneEl.addEventListener( 'countrychange', clearPhoneError );
		}

		var errorEl        = form.querySelector( '[data-bob-error]' );
		var msgUnavailable = panel.getAttribute( 'data-bob-msg-unavailable' ) || '';
		var msgPriceError  = panel.getAttribute( 'data-bob-msg-price-error' ) || '';

		var totalEl    = form.querySelector( '[data-bob-total-eur]' );
		var rentalEl   = form.querySelector( '[data-bob-base-rental]' );
		var adwEl      = form.querySelector( '[data-bob-adw]' );
		var cleaningEl = form.querySelector( '[data-bob-cleaning]' );

		var EUR = new Intl.NumberFormat( 'en-GB', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 } );

		// Villa-overview indicative price ("From €X / wk") — swapped to the
		// average weekly rate for the selected dates while a priced search is
		// active; restored when dates clear / unavailable / fetch fails.
		var ovPrice  = document.querySelector( '.ibv-villa-overview__price' );
		var ovAmount = ovPrice ? ovPrice.querySelector( '[data-bob-from-price]' ) : null;
		var ovFrom   = ovPrice ? ovPrice.querySelector( '.ibv-villa-overview__price-from' ) : null;
		var ovUnit   = ovPrice ? ovPrice.querySelector( '.ibv-villa-overview__price-unit' ) : null;
		var ovSeason = ovPrice ? ovPrice.querySelector( '.ibv-villa-overview__price-note--season' ) : null;
		var ovDated  = ovPrice ? ovPrice.querySelector( '.ibv-villa-overview__price-note--dated' ) : null;
		var ovStatic = {
			text:       ovAmount ? ovAmount.textContent : '',
			onRequest:  ovAmount ? ovAmount.classList.contains( 'ibv-villa-overview__price-amount--on-request' ) : false,
			unitHidden: ovUnit ? ovUnit.hidden : false,
			forAttr:    ovAmount ? ovAmount.getAttribute( 'for' ) : null,
		};

		function showDatedOverviewPrice( weekly ) {
			if ( ! ovAmount || ! ( weekly > 0 ) ) {
				return;
			}
			ovAmount.textContent = EUR.format( Math.round( weekly ) );
			ovAmount.classList.remove( 'ibv-villa-overview__price-amount--on-request' );
			ovAmount.removeAttribute( 'for' );
			if ( ovFrom ) {
				ovFrom.hidden = true;
			}
			if ( ovUnit ) {
				ovUnit.hidden = false;
			}
			if ( ovSeason ) {
				ovSeason.hidden = true;
			}
			if ( ovDated ) {
				ovDated.hidden = false;
			}
		}

		function resetOverviewPrice() {
			if ( ! ovAmount ) {
				return;
			}
			ovAmount.textContent = ovStatic.text;
			if ( ovStatic.onRequest ) {
				ovAmount.classList.add( 'ibv-villa-overview__price-amount--on-request' );
			}
			if ( ovStatic.forAttr ) {
				ovAmount.setAttribute( 'for', ovStatic.forAttr );
			}
			if ( ovFrom ) {
				ovFrom.hidden = false;
			}
			if ( ovUnit ) {
				ovUnit.hidden = ovStatic.unitHidden;
			}
			if ( ovSeason ) {
				ovSeason.hidden = false;
			}
			if ( ovDated ) {
				ovDated.hidden = true;
			}
		}

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

		function hidePriceBlock() {
			panel.classList.add( 'is-pricing-pending' );
		}

		// Contact fields are hidden by CSS (.is-contact-pending .ibv-contact-field)
		// until dates and guests are filled. Unavailable dates still reveal them
		// — the guest can enquire for alternatives. The submit gate only waits
		// for dates + pax, so no field-disabling is needed (keeps GF required
		// validation clean).
		function revealContactFields() {
			panel.classList.remove( 'is-contact-pending' );
		}

		function hideContactFields() {
			panel.classList.add( 'is-contact-pending' );
			clearPhoneError();
		}

		function showNotice( msg ) {
			if ( errorEl && msg ) {
				errorEl.textContent = msg;
				errorEl.removeAttribute( 'hidden' );
			}
		}

		function clearNotice() {
			if ( errorEl ) {
				errorEl.textContent = '';
				errorEl.setAttribute( 'hidden', '' );
			}
		}

		function resetPrices() {
			setText( totalEl, '—' );
			setText( rentalEl, '—' );
			setText( adwEl, '—' );
			setText( cleaningEl, '—' );
			resetOverviewPrice();
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
			if ( data && Array.isArray( data.villas ) ) {
				node = data.villas.length ? data.villas[ 0 ] : null;
			} else if ( data && data.villa ) {
				node = data.villa;
			} else {
				node = data;
			}

			if ( ! node || ( node.available !== undefined && Number( node.available ) !== 1 ) ) {
				return false;
			}

			var total = pickNumber( node, [ 'eur_total_price' ] );
			var rent  = pickNumber( node, [ 'eur_base_rental' ] );
			var adw   = pickNumber( node, [ 'eur_adw_amount' ] );
			var clean = pickNumber( node, [ 'eur_extra_cleaning' ] );

			// Out of season the PMS returns available:1 with no rate card loaded —
			// eur_base_rental 0 and a total that is only ADW + cleaning. That is not a
			// price. Painting it also restores the static ACF "From" price above it via
			// resetOverviewPrice(), so the guest sees a high-season figure over a
			// fee-only total. Treat as unpriced and let the caller show the notice.
			if ( total === null || rent === null || rent <= 0 ) {
				return false;
			}

			setText( totalEl,    EUR.format( total ) );
			setText( rentalEl,   rent  !== null ? EUR.format( rent )  : '—' );
			setText( adwEl,      adw   !== null ? EUR.format( adw )   : '—' );
			setText( cleaningEl, clean !== null ? EUR.format( clean ) : '—' );

			var nights = data && data.query ? pickNumber( data.query, [ 'nights' ] ) : null;
			if ( rent !== null && rent > 0 ) {
				showDatedOverviewPrice( nights > 0 ? ( rent * 7 ) / nights : rent );
			} else {
				resetOverviewPrice();
			}
			return true;
		}

		var inflight = null;
		function fetchPricing() {
			if ( ! endpoint || ! propertyId ) {
				return;
			}
			var s = readState();
			if ( ! gateReady( s ) ) {
				resetPrices();
				clearNotice();
				hidePriceBlock();
				hideContactFields();
				updateGate();
				return;
			}
			clearNotice();

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
					if ( paint( data ) ) {
						clearNotice();
						revealPriceBlock();
						revealContactFields();
					} else {
						resetPrices();
						hidePriceBlock();
						revealContactFields();
						showNotice( msgUnavailable );
					}
					updateGate();
				} )
				.catch( function ( err ) {
					if ( err && err.name === 'AbortError' ) {
						return;
					}
					console.warn( '[ibv-enquiry-panel] pricing fetch failed', err );
					resetPrices();
					hidePriceBlock();
					revealContactFields();
					showNotice( msgPriceError );
					updateGate();
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

		// Strict phone validation + E.164 write before GF serialises. Capture
		// phase so we can block GF's AJAX submit on an invalid number; fail-open
		// if utils.js hasn't loaded so an enquiry is never lost to a missing script.
		form.addEventListener( 'submit', function ( ev ) {
			if ( ! iti || ! phoneEl || panel.classList.contains( 'is-contact-pending' ) ) {
				return;
			}
			var valid = true;
			try {
				valid = iti.isValidNumber() === true;
			} catch ( e ) {
				valid = true;
			}
			if ( ! valid ) {
				ev.preventDefault();
				ev.stopImmediatePropagation();
				showPhoneError();
				try {
					phoneEl.focus();
				} catch ( e2 ) {}
				return;
			}
			clearPhoneError();
			try {
				phoneEl.value = iti.getNumber();
			} catch ( e3 ) {}
		}, true );

		updateGate();
		if ( gateReady( readState() ) ) {
			fetchPricing();
		}
	}

	/* ── Boot + GF re-render ─────────────────────────────────────────── */

	function boot() {
		var panels = document.querySelectorAll( '[data-bob-enquiry-panel]' );
		for ( var i = 0; i < panels.length; i++ ) {
			var panel = panels[ i ];
			var form  = panel.querySelector( '.ibv-gform form[id^="gform_"]' );
			if ( ! form ) {
				continue;
			}
			bindPicker( panel, form );
			bindEnquiry( panel, form );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}

	// GF re-renders the form on AJAX (validation errors). Re-bind on the fresh
	// DOM. This GF build fires `gform_post_render` via the jQuery event, NOT the
	// gform JS-API action, so bind the jQuery event (the one that actually fires)
	// and ALSO the JS API for builds where only it fires. boot() is idempotent
	// (per-form __ibvVilla* guards), so a double-fire is harmless.
	if ( window.jQuery ) {
		window.jQuery( document ).on( 'gform_post_render', boot );
	}
	if ( window.gform && typeof window.gform.addAction === 'function' ) {
		window.gform.addAction( 'gform_post_render', boot );
	}
}() );
