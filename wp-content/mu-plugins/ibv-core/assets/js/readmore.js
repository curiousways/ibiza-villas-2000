/**
 * Read-more toggle — shared by villa-overview, accommodation-overview, and any
 * future clamped block.
 *
 * A trigger button carries:
 *   data-ibv-readmore="<open-class>"   the class toggled on the controlled element
 *   data-ibv-readmore-less="<label>"   the expanded-state label (collapsed label is
 *                                      the button's initial text)
 *   aria-controls="<id>"               the element that collapses/expands
 *
 * Clicking toggles the open-class on the controlled element, flips
 * aria-expanded, and swaps the label. One global listener set, idempotent (a
 * per-button guard prevents double-binding), so two clamped sections on one page
 * never double-fire.
 */
( function () {
	function bind( btn ) {
		if ( btn.dataset.ibvReadmoreBound ) {
			return;
		}
		var openClass = btn.getAttribute( 'data-ibv-readmore' );
		var id = btn.getAttribute( 'aria-controls' );
		var target = id ? document.getElementById( id ) : null;
		if ( ! openClass || ! target ) {
			return;
		}
		btn.dataset.ibvReadmoreBound = '1';

		var moreLabel = btn.textContent;
		var lessLabel = btn.getAttribute( 'data-ibv-readmore-less' ) || moreLabel;

		btn.addEventListener( 'click', function () {
			var open = target.classList.toggle( openClass );
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			btn.textContent = open ? lessLabel : moreLabel;
		} );
	}

	function init() {
		var buttons = document.querySelectorAll( '[data-ibv-readmore]' );
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
