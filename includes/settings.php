<?php
/**
 * Single-option settings screen: "remove data on uninstall" opt-in.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hrld_register_settings() {
	register_setting(
		'hrld_settings',
		'hrld_remove_data_on_uninstall',
		array(
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
}
add_action( 'admin_init', 'hrld_register_settings' );

function hrld_register_settings_page() {
	add_submenu_page(
		'edit.php?post_type=hrld_bar',
		__( 'Heralda Settings', 'heralda' ),
		__( 'Settings', 'heralda' ),
		'manage_options',
		'hrld_settings',
		'hrld_render_settings_page'
	);
}
add_action( 'admin_menu', 'hrld_register_settings_page' );

function hrld_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Heralda Settings', 'heralda' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'hrld_settings' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Uninstall', 'heralda' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="hrld_remove_data_on_uninstall" value="1" <?php checked( get_option( 'hrld_remove_data_on_uninstall' ) ); ?> />
							<?php esc_html_e( 'Remove all Heralda bars and data when the plugin is deleted', 'heralda' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Unchecked by default - your bars are kept if you deactivate/delete the plugin by mistake.', 'heralda' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
