/**
 * Mobile site-nav disclosure toggle.
 *
 * The header hamburger ([data-ibv-site-nav-toggle]) expands/collapses the
 * menu panel it points at via aria-controls. Plain disclosure semantics:
 * aria-expanded on the button, .is-open on the panel, Escape closes and
 * returns focus to the button. No focus trap — it's a disclosure, not a
 * modal; the panel only intercepts layout below the desktop breakpoint
 * (site-chrome.css).
 */
( function () {
	function bind( btn ) {
		if ( btn.dataset.ibvSiteNavBound ) {
			return;
		}
		var id = btn.getAttribute( 'aria-controls' );
		var menu = id ? document.getElementById( id ) : null;
		if ( ! menu ) {
			return;
		}
		btn.dataset.ibvSiteNavBound = '1';

		function setOpen( open ) {
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			menu.classList.toggle( 'is-open', open );
		}

		btn.addEventListener( 'click', function () {
			setOpen( 'true' !== btn.getAttribute( 'aria-expanded' ) );
		} );

		document.addEventListener( 'keydown', function ( ev ) {
			if ( 'Escape' === ev.key && 'true' === btn.getAttribute( 'aria-expanded' ) ) {
				setOpen( false );
				btn.focus();
			}
		} );
	}

	function init() {
		var buttons = document.querySelectorAll( '[data-ibv-site-nav-toggle]' );
		for ( var i = 0; i < buttons.length; i++ ) {
			bind( buttons[ i ] );
		}
	}

	if ( 'loading' !== document.readyState ) {
		init();
	} else {
		document.addEventListener( 'DOMContentLoaded', init );
	}
}() );
