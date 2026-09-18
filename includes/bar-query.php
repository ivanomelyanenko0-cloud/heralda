<?php
/**
 * Active bar selection.
 *
 * hrld_get_active_bars() always returns an array. The Free plugin's own
 * business rule is "show only the single most-recently-published matching
 * bar", so in Free that array will always hold 0 or 1 items - but the shape
 * is plural from day one so Pro can extend it (e.g. a priority queue showing
 * several bars) purely through the `hrld_bars_to_render` filter, with zero
 * conditional branches inside this file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the list of bars that should render on the current request.
 *
 * Memoized per-request (static) because both the asset-enqueue check and
 * the frontend render need the same answer and must not run the underlying
 * query twice.
 *
 * @return WP_Post[]
 */
function hrld_get_active_bars() {
	static $resolved = false;
	static $bars     = array();

	if ( $resolved ) {
		return $bars;
	}
	$resolved = true;

	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $bars;
	}

	$current_id = is_singular() ? get_queried_object_id() : 0;
	$now        = time();

	$candidates = get_posts(
		array(
			'post_type'        => 'hrld_bar',
			'post_status'      => 'publish',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'posts_per_page'   => (int) apply_filters( 'hrld_active_bar_query_limit', 200 ),
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);

	$visible = array();

	foreach ( $candidates as $candidate ) {
		$is_visible = hrld_bar_matches_schedule( $candidate->ID, $now )
			&& hrld_bar_matches_target( $candidate->ID, $current_id );

		/**
		 * Final say on whether a candidate bar is visible on this request.
		 * Pro hooks in here for role/device/exclude/recurring-schedule rules.
		 */
		$is_visible = apply_filters( 'hrld_bar_is_visible', $is_visible, $candidate, $now );

		if ( $is_visible ) {
			$visible[] = $candidate;
		}
	}

	// Free's own rule: only the most recently published match (list is already DESC by date).
	$default_selection = array_slice( $visible, 0, 1 );

	/**
	 * Pro overrides this to return more bars in its own priority order.
	 */
	$bars = apply_filters( 'hrld_bars_to_render', $default_selection, $visible );

	return $bars;
}

/**
 * Whether a candidate bar's schedule window includes $now.
 *
 * @param int $post_id Bar post ID.
 * @param int $now     Current UTC timestamp (time()).
 * @return bool
 */
function hrld_bar_matches_schedule( $post_id, $now ) {
	$start = get_post_meta( $post_id, '_hrld_schedule_start', true );
	$end   = get_post_meta( $post_id, '_hrld_schedule_end', true );

	$start_ts = $start ? hrld_site_time_to_timestamp( $start ) : false;
	$end_ts   = $end ? hrld_site_time_to_timestamp( $end ) : false;

	if ( false !== $start_ts && $start_ts > $now ) {
		return false;
	}

	if ( false !== $end_ts && $end_ts < $now ) {
		return false;
	}

	return true;
}

/**
 * Parse an admin-entered date/time string as belonging to the site's
 * configured timezone (not PHP's/server's default) and return a real UTC
 * timestamp - so it compares correctly against time().
 *
 * @param string $datetime_string Date or datetime string, e.g. '2026-08-12' or '2026-08-12 14:00'.
 * @return int|false
 */
function hrld_site_time_to_timestamp( $datetime_string ) {
	try {
		$dt = new DateTime( $datetime_string, wp_timezone() );
		return $dt->getTimestamp();
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Whether a candidate bar targets the currently viewed page.
 *
 * `_hrld_target_pages` is a single serialized-array meta row (post IDs, or
 * the literal string 'all'). Filtering happens here in PHP rather than via
 * `meta_query` because WP core can't reliably match "array contains X"
 * against a serialized array in one meta row without fragile LIKE '%i:5;%'
 * patterns that also risk matching 15 when looking for 5. The CPT is
 * expected to hold at most dozens of entries on a real site, so a single
 * capped get_posts() plus this PHP-side filter is simpler and correct -
 * do not "optimize" this into a meta_query later.
 *
 * @param int $post_id    Bar post ID.
 * @param int $current_id Currently viewed singular post ID (0 if not singular).
 * @return bool
 */
function hrld_bar_matches_target( $post_id, $current_id ) {
	$targets = get_post_meta( $post_id, '_hrld_target_pages', true );

	if ( empty( $targets ) || 'all' === $targets ) {
		return true;
	}

	$targets = array_map( 'absint', (array) $targets );

	return in_array( $current_id, $targets, true );
}
