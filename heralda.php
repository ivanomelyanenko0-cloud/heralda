<?php
/**
 * Plugin Name:       Heralda
 * Plugin URI:        https://cognitolab.net/products/heralda
 * Description:       A lightweight sticky announcement / promo bar for the top or bottom of your site.
 * Version:           1.0.1
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            CognitoLab
 * Author URI:        https://cognitolab.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       heralda
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HRLD_VERSION', '1.0.1' );
define( 'HRLD_PLUGIN_FILE', __FILE__ );
define( 'HRLD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HRLD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once HRLD_PLUGIN_DIR . 'includes/cpt.php';
require_once HRLD_PLUGIN_DIR . 'includes/extension-api.php';
require_once HRLD_PLUGIN_DIR . 'includes/decorations.php';
require_once HRLD_PLUGIN_DIR . 'includes/content-templates.php';
require_once HRLD_PLUGIN_DIR . 'includes/bar-query.php';
require_once HRLD_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once HRLD_PLUGIN_DIR . 'includes/settings.php';
require_once HRLD_PLUGIN_DIR . 'includes/enqueue.php';
require_once HRLD_PLUGIN_DIR . 'includes/render.php';
require_once HRLD_PLUGIN_DIR . 'includes/shortcode.php';

/**
 * No load_plugin_textdomain() call: discouraged since WP 4.6 for plugins
 * hosted on wordpress.org - core auto-loads translations for wp.org-hosted
 * plugins using the plugin slug, no manual loading needed.
 */

/**
 * Flush rewrite rules on activation/deactivation since hrld_bar is registered on init.
 */
function hrld_activate() {
	hrld_register_cpt();
	flush_rewrite_rules();
}
register_activation_hook( HRLD_PLUGIN_FILE, 'hrld_activate' );

function hrld_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( HRLD_PLUGIN_FILE, 'hrld_deactivate' );
