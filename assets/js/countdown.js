/**
 * Heralda promo countdown timer. Self-contained, vanilla JS - no dependency
 * on the sibling blocks-plugin project (a separate product, code is
 * intentionally not shared between the two).
 */
( function () {
	'use strict';

	function pad( n ) {
		return n < 10 ? '0' + n : '' + n;
	}

	function format( ms ) {
		var totalSeconds = Math.max( 0, Math.floor( ms / 1000 ) );
		var days = Math.floor( totalSeconds / 86400 );
		var hours = Math.floor( ( totalSeconds % 86400 ) / 3600 );
		var minutes = Math.floor( ( totalSeconds % 3600 ) / 60 );
		var seconds = totalSeconds % 60;

		if ( days > 0 ) {
			return days + 'd ' + pad( hours ) + ':' + pad( minutes ) + ':' + pad( seconds );
		}
		return pad( hours ) + ':' + pad( minutes ) + ':' + pad( seconds );
	}

	function tick( el, endMs, expiredText ) {
		var remaining = endMs - Date.now();
		if ( remaining <= 0 ) {
			el.textContent = expiredText;
			return false;
		}
		el.textContent = format( remaining );
		return true;
	}

	function init() {
		var data = ( window.heraldaData && window.heraldaData.bars ) || [];
		var strings = ( window.heraldaData && window.heraldaData.strings ) || {};
		var expiredText = strings.expired || 'Expired';

		var changedHeight = false;

		data.forEach( function ( bar ) {
			if ( 'promo' !== bar.type || ! bar.countdownEnd ) {
				return;
			}
			var barEl = document.querySelector( '.hld-bar[data-hld-bar-id="' + bar.id + '"]' );
			if ( ! barEl ) {
				return;
			}
			var el = barEl.querySelector( '[data-hld-countdown]' );
			if ( ! el ) {
				return;
			}

			if ( ! tick( el, bar.countdownEnd, expiredText ) ) {
				return;
			}
			changedHeight = true;

			var interval = window.setInterval( function () {
				if ( ! tick( el, bar.countdownEnd, expiredText ) ) {
					window.clearInterval( interval );
				}
			}, 1000 );
		} );

		// The countdown span is still empty when frontend.js's own
		// DOMContentLoaded listener runs stackBars() (its script tag loads
		// first, so its listener registers and fires first) - filling it
		// in here can change a promo bar's height, so any bar stacked
		// after it needs its offset recalculated now that the real height
		// is known.
		if ( changedHeight && window.hldStackBars ) {
			window.hldStackBars();
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
