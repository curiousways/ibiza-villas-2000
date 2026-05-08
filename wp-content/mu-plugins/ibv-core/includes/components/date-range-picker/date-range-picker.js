/**
 * Date range picker — IBV wrapper around Vanilla Calendar Pro v3.x.
 *
 * Finds every form/element with [data-bob-date-range], pairs the
 * [data-bob-date-from] / [data-bob-date-to] inputs inside it, and binds one
 * Calendar instance configured for two-month side-by-side range selection.
 *
 * Visibility: native Popover API (`popover="auto"`) — top-layer rendering,
 * light dismiss (click outside) and Escape-to-close come for free.
 *
 * Position: CSS Anchor Positioning. Each picker instance gets a unique
 * `anchor-name` on its wrapping form and `position-anchor` on its popover,
 * declared via a per-instance <style> element injected into <head>. The
 * actual `top` / `left` / `translate` rules live in date-range-picker.css.
 * Anchor positioning re-evaluates on every layout/scroll update, so the
 * popover follows the form on scroll.
 *
 * Contracts preserved:
 *   - Inputs keep name="date_from" / name="date_to" and YYYY-MM-DD format.
 *   - A `change` event is dispatched on each input after writing, so the
 *     enquiry-panel pricing-fetch listener (which subscribes to `change`)
 *     re-fires automatically.
 *   - The form submits exactly as before — picker never calls submit().
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

	/**
	 * Format a YYYY-MM-DD pair as a compact human-readable range.
	 * Example: "2026-05-22" / "2026-05-29" -> "May 22 – 29"
	 *          "2026-05-22" / "2026-06-04" -> "May 22 – Jun 4"
	 *          "2026-12-30" / "2027-01-04" -> "Dec 30 – Jan 4, 2027"
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

	/**
	 * Inject a per-instance <style> element pairing the anchor source and
	 * the popover. Done via a real stylesheet (not inline style) so the CSS
	 * parser treats `--ibv-drp-anchor-N` as a <dashed-ident> (anchor-name's
	 * value type), not a custom-property reference — which is what bites
	 * when you go via element.style.
	 */
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

	function init( wrapper ) {
		if ( wrapper.__ibvDrpBound ) {
			return;
		}
		wrapper.__ibvDrpBound = true;

		var fromInput = wrapper.querySelector( '[data-bob-date-from]' );
		var toInput   = wrapper.querySelector( '[data-bob-date-to]' );
		if ( ! fromInput || ! toInput ) {
			return;
		}

		// Optional consolidated UI: a single "When" trigger button + a display
		// element + a clear button. When present, the picker drives those
		// instead of the bare inputs (which can then be hidden form fields).
		var trigger = wrapper.querySelector( '[data-bob-date-range-trigger]' );
		var display = wrapper.querySelector( '[data-bob-date-range-display]' );
		var clearBtn = wrapper.querySelector( '[data-bob-date-range-clear]' );
		var displayPlaceholder = display ? ( display.dataset.placeholder || display.textContent || 'Add dates' ) : '';

		var instanceId = ++instanceCount;

		var popover = document.createElement( 'div' );
		popover.className = 'ibv-drp__popover';
		popover.setAttribute( 'popover', 'auto' );
		popover.setAttribute( 'data-ibv-drp-anchor-target', String( instanceId ) );

		// Inner host — VCP rewrites attributes/classes on its target
		// element, so we mount it into a child div. The popover keeps
		// its own class/styles.
		var calendarHost = document.createElement( 'div' );
		calendarHost.className = 'ibv-drp__calendar';
		popover.appendChild( calendarHost );

		// Append to <body> — top-layer rendering ignores DOM position for
		// z-index, and keeping it out of the form avoids form-field
		// submission semantics.
		document.body.appendChild( popover );

		// Anchor source: an inner element marked with [data-bob-date-range-anchor]
		// if present (e.g. the When field on enquiry-panel where the form is
		// vertical and tall), otherwise the wrapper itself (e.g. hero/header
		// search where the form is a horizontal pill).
		var anchorEl = wrapper.querySelector( '[data-bob-date-range-anchor]' ) || wrapper;
		anchorEl.setAttribute( 'data-ibv-drp-anchor-source', String( instanceId ) );
		injectAnchorRules( instanceId );

		var calendar = null;

		function buildCalendar() {
			if ( calendar ) {
				return;
			}
			var Calendar = window.VanillaCalendarPro && window.VanillaCalendarPro.Calendar;
			if ( ! Calendar ) {
				console.warn( '[ibv-date-range-picker] VanillaCalendarPro not loaded' );
				return;
			}

			var seedFrom = isValidYMD( fromInput.value ) ? fromInput.value : '';
			var seedTo   = isValidYMD( toInput.value )   ? toInput.value   : '';
			var initialSelected = ( seedFrom && seedTo ) ? [ seedFrom, seedTo ] : [];
			var today = todayYMD();

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
				selectedDates: initialSelected,
				firstWeekday: 1,
				onClickDate: function ( self ) {
					var dates = self.context.selectedDates || [];
					if ( dates.length === 2 ) {
						var sorted = dates.slice().sort();
						setInputValue( fromInput, sorted[ 0 ] );
						setInputValue( toInput, sorted[ 1 ] );
						// Stay open after the second click — user dismisses
						// via outside-click or Escape.
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

		// If a trigger is provided, that's the only thing the user clicks to
		// open the picker. Otherwise, fall back to focus/click on the inputs
		// (the legacy pattern still used by enquiry-panel).
		if ( trigger ) {
			trigger.addEventListener( 'click', function ( ev ) {
				ev.preventDefault();
				open();
			} );
		} else {
			[ fromInput, toInput ].forEach( function ( input ) {
				input.addEventListener( 'focus', open );
				input.addEventListener( 'click', open );
			} );
		}

		// The picker writes to the hidden inputs and dispatches `change` on
		// them — listen for that to keep the visible display in sync.
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
					calendar.set(
						{ selectedDates: [] },
						{ year: false, month: false, time: false }
					);
				}
				syncDisplay();
			} );
		}

		// Initial paint — covers pre-filled values from URL params.
		syncDisplay();
	}

	function boot() {
		var wrappers = document.querySelectorAll( '[data-bob-date-range]' );
		for ( var i = 0; i < wrappers.length; i++ ) {
			init( wrappers[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
