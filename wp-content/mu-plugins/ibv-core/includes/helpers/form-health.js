/**
 * Form health — browser beacons for in-scope Gravity Forms.
 *
 * Posts rendered / submit_clicked / js_error / ajax_error to ibv/v1/form-event.
 * No personal data. No cookies.
 */
( function () {
	'use strict';

	if ( typeof window.ibvFormHealth === 'undefined' ) {
		return;
	}

	var config = window.ibvFormHealth;
	var endpoint = config.endpoint || '';
	var allowed = {};
	( config.formIds || [] ).forEach( function ( id ) {
		allowed[ String( id ) ] = true;
	} );

	var renderedOnce = {};
	var scopedPresent = false;

	function formIdFromEl( form ) {
		if ( ! form || ! form.id ) {
			return 0;
		}
		var m = String( form.id ).match( /^gform_(\d+)$/ );
		return m ? parseInt( m[ 1 ], 10 ) : 0;
	}

	function isScoped( formId ) {
		return !! allowed[ String( formId ) ];
	}

	function truncate( str, max ) {
		str = String( str || '' );
		if ( str.length <= max ) {
			return str;
		}
		return str.slice( 0, max - 3 ) + '...';
	}

	function send( formId, event, detail ) {
		if ( ! endpoint || ! isScoped( formId ) ) {
			return;
		}
		var payload = {
			form_id: String( formId ),
			event: event,
			detail: truncate( detail || '', 255 ),
		};
		try {
			if ( navigator.sendBeacon ) {
				var params = new URLSearchParams();
				params.set( 'form_id', payload.form_id );
				params.set( 'event', payload.event );
				params.set( 'detail', payload.detail );
				if ( navigator.sendBeacon( endpoint, params ) ) {
					return;
				}
			}
		} catch ( e ) {}
		try {
			fetch( endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( {
					form_id: formId,
					event: event,
					detail: payload.detail,
				} ),
				keepalive: true,
				credentials: 'omit',
			} ).catch( function () {} );
		} catch ( e2 ) {}
	}

	function onPostRender( formId ) {
		formId = parseInt( formId, 10 ) || 0;
		if ( ! isScoped( formId ) ) {
			return;
		}
		scopedPresent = true;
		if ( ! renderedOnce[ formId ] ) {
			renderedOnce[ formId ] = true;
			send( formId, 'rendered', '' );
		}
		bindSubmit( formId );
	}

	function bindSubmit( formId ) {
		var form = document.getElementById( 'gform_' + formId );
		if ( ! form || form.__ibvFormHealthBound ) {
			return;
		}
		form.__ibvFormHealthBound = true;
		form.addEventListener(
			'submit',
			function () {
				send( formId, 'submit_clicked', '' );
			},
			true
		);
	}

	function bootExisting() {
		Object.keys( allowed ).forEach( function ( id ) {
			var form = document.getElementById( 'gform_' + id );
			if ( form ) {
				onPostRender( parseInt( id, 10 ) );
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', bootExisting );
	} else {
		bootExisting();
	}

	if ( window.jQuery ) {
		window.jQuery( document ).on( 'gform_post_render', function ( _ev, formId ) {
			onPostRender( formId );
		} );
	}
	if ( window.gform && typeof window.gform.addAction === 'function' ) {
		window.gform.addAction( 'gform_post_render', function ( formId ) {
			onPostRender( formId );
		} );
	}

	// GF 3 new AJAX: docs.gravityforms.com/gform-ajax-pre_display_error/
	function hookAjaxError() {
		if ( ! window.gform || ! gform.utils || typeof gform.utils.addAsyncFilter !== 'function' ) {
			return false;
		}
		gform.utils.addAsyncFilter( 'gform/ajax/pre_display_error', function ( data ) {
			try {
				var formId = data && data.formId ? parseInt( data.formId, 10 ) : 0;
				var err = data && data.error ? String( data.error ) : 'ajax_error';
				var status = '';
				if ( data && data.submissionResult && data.submissionResult.data && data.submissionResult.data.response ) {
					status = String( data.submissionResult.data.response.status || '' );
				}
				send( formId, 'ajax_error', truncate( ( status ? status + ' ' : '' ) + err, 255 ) );
			} catch ( e ) {}
			return Promise.resolve( data );
		} );
		return true;
	}

	if ( ! hookAjaxError() ) {
		document.addEventListener( 'DOMContentLoaded', hookAjaxError );
		setTimeout( hookAjaxError, 2000 );
	}

	window.addEventListener( 'error', function ( ev ) {
		if ( ! scopedPresent ) {
			return;
		}
		var msg = ev && ev.message ? ev.message : 'error';
		var file = ev && ev.filename ? ev.filename : '';
		var formId = parseInt( Object.keys( renderedOnce )[ 0 ], 10 ) || parseInt( Object.keys( allowed )[ 0 ], 10 ) || 0;
		send( formId, 'js_error', truncate( msg + ( file ? ' @' + file.split( '/' ).pop() : '' ), 255 ) );
	} );

	window.addEventListener( 'unhandledrejection', function ( ev ) {
		if ( ! scopedPresent ) {
			return;
		}
		var reason = ev && ev.reason ? ev.reason : '';
		var msg = typeof reason === 'string' ? reason : ( reason && reason.message ? reason.message : 'unhandledrejection' );
		var formId = parseInt( Object.keys( renderedOnce )[ 0 ], 10 ) || parseInt( Object.keys( allowed )[ 0 ], 10 ) || 0;
		send( formId, 'js_error', truncate( msg, 255 ) );
	} );
}() );
