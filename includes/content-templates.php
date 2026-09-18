<?php
/**
 * Free content-starting-point templates for the bar body editor. Kept
 * separate from decorations.php since these govern post_content (via
 * meta-boxes.php's template-picker buttons), not post meta - the shape of
 * the data (label + HTML) and how it's consumed are entirely different from
 * every other decorations.php option. Each template deliberately exercises
 * one of the frontend.css content guardrails (see frontend.css's ".hld-bar__inner
 * img" comment) so a new user's first bar already respects them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A small gray placeholder square, sized to match frontend.css's 36px image
 * cap exactly - a bundled static asset, NOT an inline data: URI. TinyMCE's
 * classic editor round-trips <img src> through its own HTML parser/serializer
 * on every insert (see admin.js's hldInsertTemplate(), which calls
 * editor.setContent()), and mangles an inline `data:image/svg+xml` src into
 * invalid markup (a literal, unencoded `<svg>...</svg>` dumped inside the src
 * attribute, breaking out of its quotes) - the broken tag then gets saved
 * verbatim and shows up as raw HTML text on the live site. A same-origin
 * plugin asset URL isn't touched by that TinyMCE dataimg handling, still
 * has no network dependency, and never 404s since it ships with the plugin.
 *
 * @return string
 */
function hrld_get_placeholder_badge_url() {
	return HRLD_PLUGIN_URL . 'assets/images/badge-placeholder.svg';
}

/**
 * Free's 3 templates plus whatever Pro (or any 3rd party) contributes via
 * the documented hrld_content_templates filter (see extension-api.php) - the
 * same "Free defines the seam, Pro fills it" pattern used throughout this
 * plugin, applied to content templates instead of post meta options.
 *
 * @return array<string,array{label:string,html:string}>
 */
function hrld_get_content_templates() {
	return apply_filters(
		'hrld_content_templates',
		array(
			'text-only'       => array(
				'label' => __( 'Text only', 'heralda' ),
				'html'  => '<p>' . esc_html__( 'Your announcement text goes here.', 'heralda' ) . '</p>',
			),
			'text-badge'      => array(
				'label' => __( 'Text + badge icon', 'heralda' ),
				'html'  => '<p><img src="' . hrld_get_placeholder_badge_url() . '" alt="' . esc_attr__( 'Replace with your badge/logo image', 'heralda' ) . '" width="36" height="36" style="vertical-align:middle;margin-right:0.5em;" /> ' . esc_html__( 'Your announcement text goes here.', 'heralda' ) . '</p>',
			),
			'text-highlights' => array(
				'label' => __( 'Text + highlights list', 'heralda' ),
				'html'  => '<p>' . esc_html__( 'Why choose us:', 'heralda' ) . '</p><ul><li>' . esc_html__( 'Free shipping over $50', 'heralda' ) . '</li><li>' . esc_html__( '30-day returns', 'heralda' ) . '</li><li>' . esc_html__( '24/7 support', 'heralda' ) . '</li></ul>',
			),
		)
	);
}
