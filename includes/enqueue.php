<?php
/**
 * Conditional frontend asset loading.
 *
 * CSS/JS are only enqueued when hrld_get_active_bars() actually has something
 * to show on the current request - checked here, before wp_head/wp_footer
 * print anything, so pages with no matching bar ship zero extra bytes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hrld_maybe_enqueue_frontend_assets() {
	$bars = hrld_get_active_bars();
	$bars = hrld_merge_shortcode_bars( $bars );

	if ( empty( $bars ) ) {
		return;
	}

	wp_enqueue_style( 'hld-frontend', HRLD_PLUGIN_URL . 'assets/css/frontend.css', array(), HRLD_VERSION );
	wp_enqueue_script( 'hld-frontend', HRLD_PLUGIN_URL . 'assets/js/frontend.js', array(), HRLD_VERSION, true );

	// Hides an already-dismissed bar the instant its markup lands in the DOM
	// (right after render.php's wp_footer output, before frontend.js's own
	// DOMContentLoaded-driven init() gets to it) so there's no flash of a
	// bar the visitor already dismissed. No per-bar dynamic value is needed -
	// each bar's own data-hld-bar-id attribute (already in its markup) is
	// enough - so this is a single static snippet shared by every bar rather
	// than one inline <script> per bar.
	wp_add_inline_script( 'hld-frontend', hrld_get_dismiss_guard_js(), 'before' );

	$has_promo = false;
	$data      = array();

	foreach ( $bars as $bar ) {
		$type          = get_post_meta( $bar->ID, '_hrld_type', true );
		$countdown_end = null;

		if ( 'promo' === $type ) {
			$has_promo = true;
			$end       = get_post_meta( $bar->ID, '_hrld_schedule_end', true );
			$end_ts    = $end ? hrld_site_time_to_timestamp( $end ) : false;
			if ( false !== $end_ts ) {
				$countdown_end = $end_ts * 1000; // JS Date expects milliseconds.
			}
		}

		$data[] = array(
			'id'           => $bar->ID,
			'type'         => $type,
			'position'     => get_post_meta( $bar->ID, '_hrld_position', true ),
			'sticky'       => (bool) get_post_meta( $bar->ID, '_hrld_sticky', true ),
			'dismissible'  => (bool) get_post_meta( $bar->ID, '_hrld_dismissible', true ),
			'dismissDays'  => (int) get_post_meta( $bar->ID, '_hrld_dismiss_days', true ),
			'countdownEnd' => $countdown_end,
		);
	}

	wp_localize_script(
		'hld-frontend',
		'heraldaData',
		array(
			'bars'    => $data,
			'strings' => array(
				'expired' => __( 'Offer expired', 'heralda' ),
			),
		)
	);

	if ( $has_promo ) {
		wp_enqueue_script( 'hld-countdown', HRLD_PLUGIN_URL . 'assets/js/countdown.js', array( 'hld-frontend' ), HRLD_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'hrld_maybe_enqueue_frontend_assets' );

/**
 * Static (no PHP-interpolated values) anti-flash guard, attached to the
 * 'hld-frontend' handle via wp_add_inline_script() instead of a raw
 * per-bar <script> tag in render.php. Mirrors the isDismissed() check
 * frontend.js's own init() already runs, just early enough to avoid a
 * flash of an already-dismissed bar.
 *
 * @return string
 */
function hrld_get_dismiss_guard_js() {
	return "(function(){try{document.querySelectorAll('.hld-bar[data-hld-bar-id]').forEach(function(el){var id=el.getAttribute('data-hld-bar-id');var raw=localStorage.getItem('hrld_dismissed_'+id);if(!raw){return;}var data=JSON.parse(raw);if(data&&data.expires&&Date.now()<data.expires){el.style.display='none';}});}catch(e){}})();";
}

/**
 * Add any bars referenced by [heralda_bar id="..."] in the current singular
 * post's content, so shortcode-embedded bars still get their frontend
 * assets/localized data even when they aren't part of the automatic
 * single-active-bar selection.
 *
 * @param WP_Post[] $bars Bars already selected by hrld_get_active_bars().
 * @return WP_Post[]
 */
function hrld_merge_shortcode_bars( $bars ) {
	if ( ! is_singular() ) {
		return $bars;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || empty( $post->post_content ) ) {
		return $bars;
	}

	$ids = hrld_get_shortcode_bar_ids_in_content( $post->post_content );
	if ( empty( $ids ) ) {
		return $bars;
	}

	$existing_ids = wp_list_pluck( $bars, 'ID' );

	foreach ( $ids as $id ) {
		if ( in_array( $id, $existing_ids, true ) ) {
			continue;
		}
		$candidate = get_post( $id );
		if ( $candidate && 'hrld_bar' === $candidate->post_type && 'publish' === $candidate->post_status ) {
			$bars[]         = $candidate;
			$existing_ids[] = $id;
		}
	}

	return $bars;
}
