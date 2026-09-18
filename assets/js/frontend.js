/**
 * Heralda frontend behavior: dismiss (localStorage TTL) + sticky spacing +
 * stacking multiple simultaneous bars. Free's own bar-selection query only
 * ever returns one bar (see bar-query.php), but Heralda Pro's priority queue
 * (see heralda-pro/includes/priority-queue.php) can hand back several, all
 * rendered here via the same wp_footer hook - so this file can't assume a
 * single `.hld-bar--top` / `.hld-bar--bottom` exists on the page.
 * Vanilla JS, no jQuery dependency.
 */
( function () {
	'use strict';

	function dismissKey( id ) {
		return 'hrld_dismissed_' + id;
	}

	function isDismissed( id ) {
		try {
			var raw = window.localStorage.getItem( dismissKey( id ) );
			if ( ! raw ) {
				return false;
			}
			var data = JSON.parse( raw );
			return !! ( data && data.expires && Date.now() < data.expires );
		} catch ( e ) {
			return false;
		}
	}

	function dismiss( id, days ) {
		try {
			var expires = Date.now() + ( days > 0 ? days : 0 ) * 86400000;
			window.localStorage.setItem( dismissKey( id ), JSON.stringify( { expires: expires } ) );
		} catch ( e ) {
			// localStorage unavailable (private mode etc.) - bar simply won't stay dismissed.
		}
	}

	/**
	 * Stacks every visible bar sharing an edge (top or bottom) instead of
	 * letting each one independently claim `top:0`/`bottom:0` and overlap -
	 * DOM order already matches priority order (render.php's foreach over the
	 * already-sorted queue), so a plain running total is enough. Each bar's
	 * own CSS-defined inset (plain 0, or the "floating" style's 1em) is read
	 * via getComputedStyle *before* any inline override is applied, so
	 * stacking adapts to whatever base inset a bar's style already has
	 * instead of this file having to know those values itself. Only sticky
	 * bars reserve body padding (matching the previous single-bar behavior);
	 * non-sticky ("static") bars still stack visually but don't push
	 * page content down while they're temporarily showing.
	 */
	function stackBars() {
		[ 'top', 'bottom' ].forEach( function ( position ) {
			var bars = Array.prototype.filter.call(
				document.querySelectorAll( '.hld-bar--' + position ),
				function ( el ) {
					return el.style.display !== 'none';
				}
			);

			var runningOffset = 0;
			var stickyOffset  = 0;

			bars.forEach( function ( el ) {
				el.style[ position ] = '';
				var base = parseFloat( window.getComputedStyle( el )[ position ] ) || 0;
				el.style[ position ] = ( base + runningOffset ) + 'px';
				// The next bar's offset has to clear THIS bar's own inset
				// too, not just its height - otherwise a "floating" bar's
				// base inset (CSS top:1em) is applied to its own position
				// but never added to the running total, so the next bar
				// starts that many pixels too early and overlaps this
				// bar's bottom edge by exactly its inset amount.
				runningOffset += base + el.getBoundingClientRect().height;
				if ( el.classList.contains( 'hld-bar--sticky' ) ) {
					stickyOffset = runningOffset;
				}
			} );

			document.documentElement.style.setProperty(
				'--hld-bar-offset-' + position,
				stickyOffset + 'px'
			);
		} );
	}

	function debounce( fn, wait ) {
		var t;
		return function () {
			var args = arguments;
			clearTimeout( t );
			t = setTimeout( function () {
				fn.apply( null, args );
			}, wait );
		};
	}

	function init() {
		var bars = document.querySelectorAll( '.hld-bar' );
		var data = ( window.heraldaData && window.heraldaData.bars ) || [];
		var dataById = {};
		data.forEach( function ( bar ) {
			dataById[ bar.id ] = bar;
		} );

		bars.forEach( function ( el ) {
			var id = parseInt( el.getAttribute( 'data-hld-bar-id' ), 10 );
			var info = dataById[ id ] || {};

			if ( isDismissed( id ) ) {
				el.style.display = 'none';
				return;
			}

			var dismissBtn = el.querySelector( '.hld-bar__dismiss' );
			if ( dismissBtn ) {
				dismissBtn.addEventListener( 'click', function () {
					dismiss( id, info.dismissDays || 0 );
					el.classList.add( 'hld-bar--hiding' );
					window.setTimeout( function () {
						el.style.display = 'none';
						stackBars();
					}, 200 );
				} );
			}
		} );

		stackBars();

		window.addEventListener( 'resize', debounce( stackBars, 100 ) );
	}

	// Exposed so countdown.js (and Pro's woo-live-cart.js) can trigger a
	// re-stack after they asynchronously fill in content that changes a
	// bar's height - the countdown span and the free-shipping message are
	// both still empty at the point stackBars() first runs here, so
	// anything that fills them in afterwards has to ask for a re-stack
	// itself or every following bar keeps the stale, too-small offset.
	window.hldStackBars = stackBars;

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
