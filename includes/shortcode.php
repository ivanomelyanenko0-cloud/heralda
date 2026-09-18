<?php
/**
 * Optional [heralda_bar id="123"] shortcode - renders a specific bar inline
 * in page content, independent of the automatic single-active-bar selection
 * in bar-query.php (explicit placement by the editor overrides automation).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hrld_shortcode_heralda_bar( $atts ) {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'heralda_bar' );
	$id   = absint( $atts['id'] );

	if ( ! $id ) {
		return '';
	}

	$bar = get_post( $id );

	if ( ! $bar || 'hrld_bar' !== $bar->post_type || 'publish' !== $bar->post_status ) {
		return '';
	}

	ob_start();
	hrld_render_single_bar( $bar, true );
	return ob_get_clean();
}
add_shortcode( 'heralda_bar', 'hrld_shortcode_heralda_bar' );

/**
 * Find hrld_bar IDs referenced by [heralda_bar id="..."] in a content string,
 * so the enqueue check can load assets for shortcode-embedded bars even when
 * they aren't part of the automatic active-bar selection.
 *
 * @param string $content Post content.
 * @return int[]
 */
function hrld_get_shortcode_bar_ids_in_content( $content ) {
	if ( ! $content || false === strpos( $content, '[heralda_bar' ) ) {
		return array();
	}

	$ids = array();

	if ( preg_match_all( '/\[heralda_bar([^\]]*)\]/', $content, $matches ) ) {
		foreach ( $matches[1] as $atts_str ) {
			$atts = shortcode_parse_atts( $atts_str );
			if ( ! empty( $atts['id'] ) ) {
				$ids[] = absint( $atts['id'] );
			}
		}
	}

	return array_unique( $ids );
}
