/**
 * Accommodation enquiry — single-field date-range picker + phone, grafted onto
 * the embedded Gravity Form.
 *
 * The shared date-range-picker.js (used by the villa RTB panel + header/hero
 * search) only boots on DOMContentLoaded and exposes no re-init API, and must
 * stay untouched. GF re-renders the form on every AJAX validation error
 * (firing `gform_post_render`), so we run a self-contained VanillaCalendarPro
 * binding here that re-attaches on each render. Reuses the vendored VCP library
 * + the `ibv-date-range-picker` brand CSS (.ibv-drp__popover / .ibv-drp__calendar)
 * for visual parity; only the binding is local.
 *
 * Graft contract (seeded form fields): an HTML "When" field carrying
 * [data-bob-date-range-trigger/display/clear] + [data-bob-date-range-anchor],
 * and two date fields with CSS classes .ibv-drp-from / .ibv-drp-to (hidden via
 * CSS) whose inputs receive the YYYY-MM-DD range.
 */
( function () {
	'use strict';

	var instanceCount = 0;

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

	function bindPicker( section, form ) {
		if ( form.__ibvAccomDrpBound ) {
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
		form.__ibvAccomDrpBound = true;

		// Tear down any popover from a previous render of this section.
		if ( section.__ibvPopover && section.__ibvPopover.parentNode ) {
			section.__ibvPopover.parentNode.removeChild( section.__ibvPopover );
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
		section.__ibvPopover = popover;

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

	/* ── Phone (intl-tel-input) ─────────────────────────────────────── */

	function bindPhone( section, form ) {
		var phoneInput = form.querySelector( '.gfield--type-phone input[type="tel"]' );
		if ( ! phoneInput || ! window.intlTelInput || phoneInput.__ibvItiBound ) {
			return;
		}
		phoneInput.__ibvItiBound = true;

		var utilsUrl = section.getAttribute( 'data-iti-utils-url' ) || '';

		var iti = window.intlTelInput( phoneInput, {
			initialCountry:   'gb',
			countryOrder:     [ 'gb', 'es', 'fr', 'de', 'us' ],
			separateDialCode: true,
			placeholderNumberPolicy: 'AGGRESSIVE',
			loadUtils:        utilsUrl ? function () { return import( utilsUrl ); } : null,
		} );

		// Skip the country/flag button in the keyboard tab order — Tab goes
		// straight from the email field to the number input. Still mouse-clickable.
		var itiWrap    = phoneInput.closest( '.iti' );
		var countryBtn = itiWrap ? itiWrap.querySelector( '.iti__selected-country' ) : null;
		if ( countryBtn ) {
			countryBtn.setAttribute( 'tabindex', '-1' );
		}

		// Write the full international number before GF serialises (capture phase).
		form.addEventListener( 'submit', function () {
			try {
				var intl = iti.getNumber();
				if ( intl ) {
					phoneInput.value = intl;
				}
			} catch ( e ) {
				// utils not loaded — submit whatever was typed; never block an enquiry.
			}
		}, true );
	}

	/* ── Message: one row until the guest focuses it ────────────────── */

	function bindMessage( form ) {
		var textarea = form.querySelector( '.gfield--type-textarea textarea' );
		if ( ! textarea || textarea.__ibvExpandBound ) {
			return;
		}
		textarea.__ibvExpandBound = true;
		textarea.setAttribute( 'rows', '1' );

		function expand() {
			textarea.setAttribute( 'rows', '5' );
			textarea.classList.add( 'is-expanded' );
		}

		function collapseIfEmpty() {
			if ( textarea.value.replace( /\s+/g, '' ) ) {
				return;
			}
			textarea.setAttribute( 'rows', '1' );
			textarea.classList.remove( 'is-expanded' );
		}

		textarea.addEventListener( 'focus', expand );
		textarea.addEventListener( 'click', expand );
		textarea.addEventListener( 'blur', collapseIfEmpty );

		if ( textarea.value ) {
			expand();
		}
	}

	function boot() {
		var sections = document.querySelectorAll( '.ibv-accommodation-enquiry' );
		sections.forEach( function ( section ) {
			var form = section.querySelector( '.ibv-gform form[id^="gform_"]' );
			if ( ! form ) {
				return;
			}
			bindPicker( section, form );
			bindPhone( section, form );
			bindMessage( form );
		} );
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
	// (per-form __ibvAccom* / __ibvItiBound guards), so a double-fire is harmless.
	if ( window.jQuery ) {
		window.jQuery( document ).on( 'gform_post_render', boot );
	}
	if ( window.gform && typeof window.gform.addAction === 'function' ) {
		window.gform.addAction( 'gform_post_render', boot );
	}
}() );
