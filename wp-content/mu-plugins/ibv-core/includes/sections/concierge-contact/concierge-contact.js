/**
 * Concierge contact form — progressive JS enhancements over the
 * Gravity Forms embed.
 *
 * Submission, validation, sanitisation, spam, email notifications, and
 * the no-JS fallback are all Gravity Forms' responsibility. This file
 * only layers two things GF doesn't do out of the box:
 *
 *   1. **Coupled date range** on the Arrival / Departure date fields —
 *      upgrades each text input to `type="date"` (native HTML5 picker,
 *      consistent across desktop, touch, and screen readers), then
 *      couples them so Departure can't precede Arrival and neither can
 *      sit in the past.
 *   2. **intl-tel-input** on the phone field — country selector + dial
 *      code. On submit, writes the full international number into the
 *      input value so GF (and the office notification) receive a
 *      complete number, not just whatever the user typed.
 *
 * Editorial pairing (one-time GF admin step) noted in
 * register-page-concierge.php:
 *   - Date field "Date Format" must be `yyyy-mm-dd` so HTML5 inputs
 *     accept the pre-filled values GF emits.
 *   - Service dropdown should carry CSS class `ibv-gf-concierge-service`
 *     for the dynamic-population filter to find it.
 *
 * Field selection is scoped via the standard GF type classes — no
 * editor-assigned CSS class required on the date/phone fields.
 */
( function () {
	'use strict';

	function pad( n ) {
		return ( n < 10 ? '0' : '' ) + n;
	}

	function todayYMD() {
		var d = new Date();
		return d.getFullYear() + '-' + pad( d.getMonth() + 1 ) + '-' + pad( d.getDate() );
	}

	function initDateRange( form ) {
		var dateInputs = form.querySelectorAll( '.gfield--type-date input' );
		if ( dateInputs.length < 2 ) {
			return;
		}

		// Arrival = first date field in form order, Departure = second.
		// Editors who reorder fields will reorder behaviour to match.
		var arrival   = dateInputs[ 0 ];
		var departure = dateInputs[ 1 ];

		var today = todayYMD();
		[ arrival, departure ].forEach( function ( el ) {
			if ( el.getAttribute( 'type' ) !== 'date' ) {
				el.setAttribute( 'type', 'date' );
			}
			if ( ! el.hasAttribute( 'min' ) ) {
				el.setAttribute( 'min', today );
			}
		} );

		// Coupling — keep the range internally consistent.
		arrival.addEventListener( 'change', function () {
			if ( ! arrival.value ) {
				departure.setAttribute( 'min', today );
				return;
			}
			departure.setAttribute( 'min', arrival.value );
			if ( departure.value && departure.value < arrival.value ) {
				departure.value = arrival.value;
			}
		} );

		departure.addEventListener( 'change', function () {
			if ( departure.value ) {
				arrival.setAttribute( 'max', departure.value );
			} else {
				arrival.removeAttribute( 'max' );
			}
		} );
	}

	function initPhone( section, form ) {
		var phoneInput = form.querySelector( '.gfield--type-phone input[type="tel"]' );
		if ( ! phoneInput || ! window.intlTelInput ) {
			return;
		}

		// Already initialised — guard against re-init when GF re-renders
		// the form via AJAX after a validation error.
		if ( phoneInput.__ibvItiBound ) {
			return;
		}
		phoneInput.__ibvItiBound = true;

		var utilsUrl = section.getAttribute( 'data-iti-utils-url' ) || '';

		var iti = window.intlTelInput( phoneInput, {
			initialCountry:     'gb',
			preferredCountries: [ 'gb', 'es', 'fr', 'de', 'us' ],
			separateDialCode:   false,
			nationalMode:       true,
			autoPlaceholder:    'aggressive',
			utilsScript:        utilsUrl
		} );

		// Write the full international number into the input before the
		// form serialises. Capture phase runs before bubble listeners
		// (including GF's jQuery AJAX serialiser).
		form.addEventListener( 'submit', function () {
			try {
				var intl = iti.getNumber();
				if ( intl ) {
					phoneInput.value = intl;
				}
			} catch ( e ) {
				// utils.js not loaded yet — fall through with whatever the
				// user typed. An enquiry must never be lost to a missing
				// helper.
			}
		}, true );
	}

	function init() {
		var section = document.querySelector( '.ibv-section-concierge-contact' );
		if ( ! section ) {
			return;
		}

		var form = section.querySelector( '.ibv-gform form[id^="gform_"]' );
		if ( ! form ) {
			return;
		}

		initDateRange( form );
		initPhone( section, form );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// GF re-renders the form on AJAX submit (e.g. after a validation
	// error). Re-run our init each time to re-bind on the fresh DOM.
	if ( window.jQuery ) {
		window.jQuery( document ).on( 'gform_post_render', init );
	}
}() );
