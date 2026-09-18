<?php
/**
 * Uninstall handler.
 *
 * Only removes hrld_bar posts/postmeta and plugin options when the admin has
 * explicitly opted in via the "Remove data on uninstall" checkbox - data is
 * kept by default.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! get_option( 'hrld_remove_data_on_uninstall' ) ) {
	return;
}

/**
 * Wrapped in a function (instead of running at this file's top level) so
 * its working variables aren't flagged as plugin-defined globals - this
 * file only ever runs once, standalone, during uninstall.
 */
function hrld_uninstall_remove_data() {
	$bar_ids = get_posts(
		array(
			'post_type'     => 'hrld_bar',
			'post_status'   => 'any',
			'numberposts'   => -1,
			'fields'        => 'ids',
			'no_found_rows' => true,
		)
	);

	foreach ( $bar_ids as $bar_id ) {
		wp_delete_post( $bar_id, true ); // Force delete, also removes postmeta.
	}

	delete_option( 'hrld_remove_data_on_uninstall' );
}
hrld_uninstall_remove_data();
