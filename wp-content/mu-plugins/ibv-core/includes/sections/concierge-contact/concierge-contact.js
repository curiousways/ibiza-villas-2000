/**
 * Concierge contact form — phone-field enhancement.
 *
 * Submission, validation, sanitisation, spam, email notifications, and
 * the no-JS fallback are all Gravity Forms' responsibility. This file
 * only layers intl-tel-input on the phone field, giving international
 * visitors a country selector + dial code, and writes the full E.164
 * number into the input on submit so GF (and the office notification)
 * receive a complete, unambiguous number.
 *
 * Date-field enhancements were trialled and rolled back — fighting GF's
 * per-field-type markup added more maintenance burden than the coupling
 * was worth. Date fields stay configured purely through GF; if a
 * "departure ≥ arrival" rule is needed, the editor can enforce it via
 * GF's own field validation / conditional logic.
 *
 * The phone field needs no editorial config beyond using a Phone-type
 * field — scoped via the standard `.gfield--type-phone` class.
 */
( function () {
	'use strict';

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
