<?php
/**
 * Free extension API consumed by Heralda Pro.
 *
 * hrld_register_pro_slot() is a validated wrapper over add_filter()/add_action()
 * against a documented registry of extension points below. It never carries
 * any conditional "is premium" logic - it fires unconditionally for whoever
 * hooks in, keeping the Free plugin fully self-contained.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Documented extension points a Pro (or any third-party) plugin can hook into.
 *
 * @return array<string,string> hook name => 'filter'|'action'
 */
function hrld_get_extension_points() {
	return apply_filters(
		'hrld_extension_points_registry',
		array(
			// (array $types) => array   extra `_hrld_type` slug => label pairs.
			'hrld_bar_types'           => 'filter',
			// (array $fields) => array   extra metabox form field definitions.
			'hrld_meta_box_fields'     => 'filter',
			// (bool $is_visible, WP_Post $bar, int $now) => bool.
			'hrld_bar_is_visible'      => 'filter',
			// (array $selected, array $visible) => array.
			'hrld_bars_to_render'      => 'filter',
			// (string $style, WP_Post $bar) => string.
			'hrld_bar_container_style' => 'filter',
			// (string $html, WP_Post $bar, string $type) => string.
			'hrld_render_bar_body'     => 'filter',
			// (array $templates) => array   extra slug => array('label','html') content-template picker entries.
			'hrld_content_templates'   => 'filter',
			// Fires with the saved post ID and sanitized meta array.
			'hrld_after_bar_save'      => 'action',
			// Fires with the WP_Post about to render.
			'hrld_before_render_bar'   => 'action',
		)
	);
}

/**
 * Register a callback against a documented Heralda extension point.
 *
 * @param string   $hook     One of the keys returned by hrld_get_extension_points().
 * @param callable $callback Callback to attach.
 * @return bool True on success, false if the hook is undocumented or the callback isn't callable.
 */
function hrld_register_pro_slot( $hook, $callback ) {
	$points = hrld_get_extension_points();

	if ( ! isset( $points[ $hook ] ) || ! is_callable( $callback ) ) {
		return false;
	}

	if ( 'action' === $points[ $hook ] ) {
		return add_action( $hook, $callback, 10, 10 );
	}

	return add_filter( $hook, $callback, 10, 10 );
}
