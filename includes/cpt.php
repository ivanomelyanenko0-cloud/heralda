<?php
/**
 * Registers the hrld_bar custom post type.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hrld_register_cpt() {
	$labels = array(
		'name'               => __( 'Heralda Bars', 'heralda' ),
		'singular_name'      => __( 'Heralda Bar', 'heralda' ),
		'add_new'            => __( 'Add New', 'heralda' ),
		'add_new_item'       => __( 'Add New Bar', 'heralda' ),
		'edit_item'          => __( 'Edit Bar', 'heralda' ),
		'new_item'           => __( 'New Bar', 'heralda' ),
		'view_item'          => __( 'View Bar', 'heralda' ),
		'search_items'       => __( 'Search Bars', 'heralda' ),
		'not_found'          => __( 'No bars found.', 'heralda' ),
		'not_found_in_trash' => __( 'No bars found in Trash.', 'heralda' ),
		'menu_name'          => __( 'Heralda Bars', 'heralda' ),
	);

	register_post_type(
		'hrld_bar',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-megaphone',
			'supports'            => array( 'title', 'editor' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'query_var'           => false,
		)
	);
}
add_action( 'init', 'hrld_register_cpt' );
