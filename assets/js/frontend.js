/**
 * Heralda frontend behavior: dismiss (localStorage TTL, or sessionStorage
 * for "0 days") + visibility gates + sticky spacing + stacking multiple
 * simultaneous bars. Free's own bar-selection query only
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
			if ( window.sessionStorage.getItem( dismissKey( id ) ) ) {
				return true;
			}
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

	/**
	 * 0 days means "until the browser is closed": sessionStorage clears
	 * itself then, whereas a localStorage entry expiring right now would
	 * bring the bar back on the very next page load.
	 */
	function dismiss( id, days ) {
		try {
			if ( days > 0 ) {
				window.localStorage.setItem( dismissKey( id ), JSON.stringify( { expires: Date.now() + days * 86400000 } ) );
			} else {
				window.sessionStorage.setItem( dismissKey( id ), '1' );
			}
		} catch ( e ) {
			// Storage unavailable (private mode etc.) - bar simply won't stay dismissed.
		}
	}

	var dataById = {};
	( ( window.heraldaData && window.heraldaData.bars ) || [] ).forEach( function ( bar ) {
		dataById[ bar.id ] = bar;
	} );

	function barId( el ) {
		return parseInt( el.getAttribute( 'data-hld-bar-id' ), 10 );
	}

	/**
	 * Visibility gates: extra client-side show/hide rules (Heralda Pro's
	 * frequency cap and UTM/referrer targeting) that can't be decided in PHP
	 * because a page cache would freeze the answer into the HTML. Each gate
	 * is `function ( id, info, el ) { return false to hide; }`, where `info`
	 * is this bar's entry in heraldaData.bars (extendable server-side via the
	 * hrld_frontend_bar_data filter). Register with
	 * `( window.hldBarGates = window.hldBarGates || [] ).push( fn )` from a
	 * script loaded before this one; a gate pushed later still applies, it
	 * just can't prevent the bar from flashing first.
	 */
	var gates = window.hldBarGates || [];

	function passesGates( el, list ) {
		var id = barId( el );
		var info = dataById[ id ] || {};
		for ( var i = 0; i < list.length; i++ ) {
			try {
				if ( false === list[ i ]( id, info, el ) ) {
					return false;
				}
			} catch ( e ) {
				// A broken gate never hides a bar.
			}
		}
		return true;
	}

	function isSuppressed( el ) {
		return isDismissed( barId( el ) ) || ! passesGates( el, gates );
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

	/**
	 * Runs immediately, not on DOMContentLoaded: this file is printed in the
	 * footer after every bar's markup (render.php hooks wp_footer at the
	 * default priority, footer scripts print later), so hiding suppressed
	 * bars here means a dismissed or gated bar is never painted at all.
	 */
	function hideSuppressedBars() {
		document.querySelectorAll( '.hld-bar[data-hld-bar-id]' ).forEach( function ( el ) {
			if ( isSuppressed( el ) ) {
				el.style.display = 'none';
			}
		} );
	}

	function init() {
		var bars = document.querySelectorAll( '.hld-bar[data-hld-bar-id]' );

		bars.forEach( function ( el ) {
			var id = barId( el );
			var info = dataById[ id ] || {};

			if ( 'none' === el.style.display || isSuppressed( el ) ) {
				el.style.display = 'none';
				return;
			}

			// Fired once per bar a visitor actually gets to see - after
			// dismiss and gates have had their say - so anything counting
			// impressions (Pro's frequency cap) counts real ones only.
			el.dispatchEvent( new window.CustomEvent( 'hld:barshown', { bubbles: true, detail: { id: id, info: info } } ) );

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

		// From here on, a newly pushed gate is applied on the spot.
		gates.push = function ( fn ) {
			Array.prototype.push.call( gates, fn );
			var changed = false;
			document.querySelectorAll( '.hld-bar[data-hld-bar-id]' ).forEach( function ( el ) {
				if ( 'none' !== el.style.display && ! passesGates( el, [ fn ] ) ) {
					el.style.display = 'none';
					changed = true;
				}
			} );
			if ( changed ) {
				stackBars();
			}
			return gates.length;
		};
	}

	// Exposed so countdown.js (and Pro's woo-live-cart.js) can trigger a
	// re-stack after they asynchronously fill in content that changes a
	// bar's height - the countdown span and the free-shipping message are
	// both still empty at the point stackBars() first runs here, so
	// anything that fills them in afterwards has to ask for a re-stack
	// itself or every following bar keeps the stale, too-small offset.
	window.hldStackBars = stackBars;
	window.hldBarGates = gates;

	hideSuppressedBars();

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
