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

		$bar_data = array(
			'id'           => $bar->ID,
			'type'         => $type,
			'position'     => get_post_meta( $bar->ID, '_hrld_position', true ),
			'sticky'       => (bool) get_post_meta( $bar->ID, '_hrld_sticky', true ),
			'dismissible'  => (bool) get_post_meta( $bar->ID, '_hrld_dismissible', true ),
			'dismissDays'  => (int) get_post_meta( $bar->ID, '_hrld_dismiss_days', true ),
			'countdownEnd' => $countdown_end,
		);

		/**
		 * Per-bar data handed to the frontend as heraldaData.bars[] - the
		 * `info` argument every window.hldBarGates gate receives. Extra keys
		 * only; Free's own keys above are read by frontend.js/countdown.js.
		 */
		$data[] = apply_filters( 'hrld_frontend_bar_data', $bar_data, $bar );
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
