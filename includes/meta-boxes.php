<?php
/**
 * Admin metabox: bar settings form, save handler.
 *
 * The bar-type list and the field set are both built through filters
 * (`hrld_bar_types`, `hrld_meta_box_fields`) rather than hardcoded, so Pro can
 * add its own bar types and settings rows without touching this file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core Free bar types. Pro adds more via the `hrld_bar_types` filter.
 *
 * @return array<string,string> slug => label
 */
function hrld_get_bar_types() {
	return apply_filters(
		'hrld_bar_types',
		array(
			'announcement' => __( 'Announcement', 'heralda' ),
			'promo'        => __( 'Promo (with countdown)', 'heralda' ),
		)
	);
}

/**
 * Extra field descriptors Pro can register.
 *
 * Each entry: array(
 *   'meta_key'    => '_hrld_pro_example',
 *   'label'       => 'Example',
 *   'type'        => 'text|textarea|checkbox|number|color|url|select',
 *   'options'     => array( value => label ) (for type=select),
 *   'description' => optional help text,
 * )
 *
 * @return array
 */
function hrld_get_extra_meta_box_fields() {
	return apply_filters( 'hrld_meta_box_fields', array() );
}

function hrld_register_meta_boxes() {
	add_meta_box(
		'hrld_bar_settings',
		__( 'Bar Settings', 'heralda' ),
		'hrld_render_meta_box',
		'hrld_bar',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'hrld_register_meta_boxes' );

function hrld_enqueue_admin_assets() {
	$screen = get_current_screen();
	if ( ! $screen || 'hrld_bar' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	// The live preview (see hrld_render_content_template_picker() and the
	// #hrld_preview_bar markup in hrld_render_meta_box()) reuses the real
	// frontend stylesheet rather than duplicating its rules, so it always
	// stays visually accurate as frontend.css changes.
	wp_enqueue_style( 'hld-frontend', HRLD_PLUGIN_URL . 'assets/css/frontend.css', array(), HRLD_VERSION );

	wp_enqueue_style( 'hld-admin', HRLD_PLUGIN_URL . 'assets/css/admin.css', array(), HRLD_VERSION );
	wp_enqueue_script( 'hld-admin', HRLD_PLUGIN_URL . 'assets/js/admin.js', array( 'wp-color-picker' ), HRLD_VERSION, true );

	wp_localize_script(
		'hld-admin',
		'heraldaAdminTemplates',
		array(
			'templates'      => hrld_get_content_templates(),
			'confirmMessage' => __( 'Replace current content with this template? This cannot be undone.', 'heralda' ),
		)
	);

	wp_localize_script(
		'hld-admin',
		'heraldaDesignPresets',
		array( 'presets' => hrld_get_design_presets() )
	);
}
add_action( 'admin_enqueue_scripts', 'hrld_enqueue_admin_assets' );

/**
 * A row of "start from a template" buttons above the native content editor.
 * edit_form_after_title fires right before that editor (the hrld_bar CPT uses
 * WP's own 'editor' support, not a custom meta box, for post_content) and
 * not on quick-edit/bulk-edit, so no extra screen guard is needed beyond the
 * post_type check below.
 *
 * @param WP_Post $post
 */
function hrld_render_content_template_picker( $post ) {
	if ( 'hrld_bar' !== $post->post_type ) {
		return;
	}
	?>
	<div class="hld-content-templates" id="hrld_content_templates">
		<p class="hld-content-templates__label"><?php esc_html_e( 'Start from a template:', 'heralda' ); ?></p>
		<div class="hld-content-templates__buttons">
			<?php foreach ( hrld_get_content_templates() as $slug => $template ) : ?>
				<button type="button" class="button hld-content-template-btn" data-hld-template="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $template['label'] ); ?></button>
			<?php endforeach; ?>
		</div>
		<p class="description"><?php esc_html_e( 'Inserts starting content into the editor below - replace the placeholder text/image, then use Bar Settings for colors, icon, and CTA.', 'heralda' ); ?></p>
	</div>
	<?php
}
add_action( 'edit_form_after_title', 'hrld_render_content_template_picker' );

/**
 * Render one generic Pro-declared field row.
 *
 * @param array $field Field descriptor, see hrld_get_extra_meta_box_fields().
 * @param int   $post_id Post ID.
 */
function hrld_render_extra_field( $field, $post_id ) {
	$meta_key = isset( $field['meta_key'] ) ? $field['meta_key'] : '';
	if ( ! $meta_key ) {
		return;
	}
	$type  = isset( $field['type'] ) ? $field['type'] : 'text';
	$label = isset( $field['label'] ) ? $field['label'] : $meta_key;
	$value = get_post_meta( $post_id, $meta_key, true );
	$id    = 'hrld_field_' . sanitize_key( $meta_key );

	echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';

	switch ( $type ) {
		case 'checkbox':
			echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" value="1"' . checked( $value, '1', false ) . ' />';
			break;

		case 'number':
			echo '<input type="number" id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" class="small-text" />';
			break;

		case 'color':
			echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" class="hld-color-field" />';
			break;

		case 'url':
			echo '<input type="url" id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
			break;

		case 'textarea':
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" class="large-text" rows="3">' . esc_textarea( $value ) . '</textarea>';
			break;

		case 'select':
			$options = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '">';
			foreach ( $options as $opt_value => $opt_label ) {
				echo '<option value="' . esc_attr( $opt_value ) . '"' . selected( $value, $opt_value, false ) . '>' . esc_html( $opt_label ) . '</option>';
			}
			echo '</select>';
			break;

		default:
			echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
			break;
	}

	if ( ! empty( $field['description'] ) ) {
		echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
	}

	echo '</td></tr>';
}

function hrld_render_meta_box( $post ) {
	wp_nonce_field( 'hrld_save_bar', 'hrld_bar_nonce' );

	$type           = get_post_meta( $post->ID, '_hrld_type', true );
	$type           = $type ? $type : 'announcement';
	$position       = get_post_meta( $post->ID, '_hrld_position', true );
	$position       = $position ? $position : 'top';
	$sticky         = get_post_meta( $post->ID, '_hrld_sticky', true );
	$bg_color       = get_post_meta( $post->ID, '_hrld_bg_color', true );
	$bg_color       = $bg_color ? $bg_color : '#1e1e1e';
	$text_color     = get_post_meta( $post->ID, '_hrld_text_color', true );
	$text_color     = $text_color ? $text_color : '#ffffff';
	$dismissible    = get_post_meta( $post->ID, '_hrld_dismissible', true );
	$dismiss_days   = get_post_meta( $post->ID, '_hrld_dismiss_days', true );
	$dismiss_days   = '' !== $dismiss_days ? $dismiss_days : 7;
	$schedule_start = get_post_meta( $post->ID, '_hrld_schedule_start', true );
	$schedule_end   = get_post_meta( $post->ID, '_hrld_schedule_end', true );
	$target_pages   = get_post_meta( $post->ID, '_hrld_target_pages', true );
	$target_all     = empty( $target_pages ) || 'all' === $target_pages;
	$target_ids     = $target_all ? array() : array_map( 'absint', (array) $target_pages );
	$cta_text       = get_post_meta( $post->ID, '_hrld_cta_text', true );
	$cta_url        = get_post_meta( $post->ID, '_hrld_cta_url', true );

	$color_preset = get_post_meta( $post->ID, '_hrld_color_preset', true );
	$icon         = get_post_meta( $post->ID, '_hrld_icon', true );
	$bar_style    = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_bar_style', true ), array_keys( hrld_get_bar_style_options() ), 'full' );
	$cta_style    = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_cta_style', true ), array_keys( hrld_get_cta_style_options() ), 'outline' );
	$animation    = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_animation', true ), array_keys( hrld_get_animation_options() ), 'none' );
	$align        = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_align', true ), array_keys( hrld_get_align_options() ), 'center' );
	$cta_position = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_cta_position', true ), array_keys( hrld_get_cta_position_options() ), 'inline' );
	$font         = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_font', true ), array_keys( hrld_get_font_options() ), '' );
	$text_style   = hrld_validate_enum( get_post_meta( $post->ID, '_hrld_text_style', true ), array_keys( hrld_get_text_style_options() ), 'normal' );

	$color_presets = hrld_get_color_presets();
	$bar_icons     = hrld_get_bar_icons();

	$bar_types = hrld_get_bar_types();
	$pages     = get_pages( array( 'sort_column' => 'post_title' ) );
	?>
	<div class="hld-bar-preview-wrap">
		<p><strong><?php esc_html_e( 'Live preview', 'heralda' ); ?></strong></p>
		<div class="hld-preview-frame">
			<div id="hrld_preview_bar" class="hld-bar hld-bar--preview">
				<div class="hld-bar__inner" id="hrld_preview_inner"></div>
			</div>
		</div>
		<p class="description"><?php esc_html_e( 'Updates as you change settings and content below. The countdown (Promo type) and entrance animation are not simulated here.', 'heralda' ); ?></p>
	</div>
	<table class="form-table hld-meta-box">
		<tr>
			<th scope="row"><label for="hrld_type"><?php esc_html_e( 'Type', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_type" name="_hrld_type">
					<?php foreach ( $bar_types as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $type, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_position"><?php esc_html_e( 'Position', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_position" name="_hrld_position">
					<option value="top" <?php selected( $position, 'top' ); ?>><?php esc_html_e( 'Top', 'heralda' ); ?></option>
					<option value="bottom" <?php selected( $position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'heralda' ); ?></option>
				</select>
				<label style="margin-left:1em;">
					<input type="checkbox" name="_hrld_sticky" value="1" <?php checked( $sticky, '1' ); ?> />
					<?php esc_html_e( 'Sticky', 'heralda' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Design preset', 'heralda' ); ?></th>
			<td>
				<div class="hld-design-presets">
					<?php foreach ( hrld_get_design_presets() as $slug => $preset ) : ?>
						<button type="button" class="button hld-design-preset-btn" data-hld-preset="<?php echo esc_attr( $slug ); ?>">
							<?php echo esc_html( $preset['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
				<p class="description"><?php esc_html_e( 'One-click starting point: fills in the color, style, font, and animation fields below. You can still change any of them afterward.', 'heralda' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Color preset', 'heralda' ); ?></th>
			<td>
				<div class="hld-swatches" id="hrld_color_swatches">
					<label class="hld-swatch <?php echo '' === $color_preset ? 'is-selected' : ''; ?>">
						<input type="radio" name="_hrld_color_preset" value="" <?php checked( '', $color_preset ); ?> />
						<span class="hld-swatch__preview hld-swatch__preview--custom"></span>
						<span class="hld-swatch__label"><?php esc_html_e( 'Custom', 'heralda' ); ?></span>
					</label>
					<?php foreach ( $color_presets as $slug => $preset ) : ?>
						<label class="hld-swatch <?php echo $color_preset === $slug ? 'is-selected' : ''; ?>">
							<input type="radio" name="_hrld_color_preset" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $slug, $color_preset ); ?> />
							<span class="hld-swatch__preview" style="background:<?php echo esc_attr( $preset['bg'] ); ?>;color:<?php echo esc_attr( $preset['text'] ); ?>;">A</span>
							<span class="hld-swatch__label"><?php echo esc_html( $preset['label'] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<p class="description"><?php esc_html_e( 'Pick a ready-made combo, or choose Custom to use the color pickers below.', 'heralda' ); ?></p>
			</td>
		</tr>
		<tr id="hrld_custom_colors_row" <?php echo '' !== $color_preset ? 'style="display:none;"' : ''; ?>>
			<th scope="row"><?php esc_html_e( 'Colors', 'heralda' ); ?></th>
			<td>
				<label>
					<?php esc_html_e( 'Background', 'heralda' ); ?>
					<input type="text" name="_hrld_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" class="hld-color-field" />
				</label>
				<label style="margin-left:1em;">
					<?php esc_html_e( 'Text', 'heralda' ); ?>
					<input type="text" name="_hrld_text_color" value="<?php echo esc_attr( $text_color ); ?>" class="hld-color-field" />
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Icon', 'heralda' ); ?></th>
			<td>
				<div class="hld-swatches hld-swatches--icons">
					<label class="hld-swatch <?php echo '' === $icon ? 'is-selected' : ''; ?>">
						<input type="radio" name="_hrld_icon" value="" <?php checked( '', $icon ); ?> />
						<span class="hld-swatch__preview hld-swatch__preview--none"><?php esc_html_e( 'None', 'heralda' ); ?></span>
					</label>
					<?php foreach ( $bar_icons as $slug => $icon_data ) : ?>
						<label class="hld-swatch <?php echo $icon === $slug ? 'is-selected' : ''; ?>">
							<input type="radio" name="_hrld_icon" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $slug, $icon ); ?> />
							<span class="hld-swatch__preview hld-swatch__preview--icon"><?php echo hrld_render_bar_icon_svg( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG from hrld_get_bar_icons(), not user input. ?></span>
							<span class="hld-swatch__label"><?php echo esc_html( $icon_data['label'] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_bar_style"><?php esc_html_e( 'Bar style', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_bar_style" name="_hrld_bar_style">
					<?php foreach ( hrld_get_bar_style_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $bar_style, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_cta_style"><?php esc_html_e( 'CTA button style', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_cta_style" name="_hrld_cta_style">
					<?php foreach ( hrld_get_cta_style_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cta_style, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_animation"><?php esc_html_e( 'Entrance animation', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_animation" name="_hrld_animation">
					<?php foreach ( hrld_get_animation_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $animation, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_align"><?php esc_html_e( 'Text alignment', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_align" name="_hrld_align">
					<?php foreach ( hrld_get_align_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $align, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_cta_position"><?php esc_html_e( 'CTA position', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_cta_position" name="_hrld_cta_position">
					<?php foreach ( hrld_get_cta_position_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cta_position, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Where the button sits relative to the message - independent of Text alignment above.', 'heralda' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_font"><?php esc_html_e( 'Font', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_font" name="_hrld_font">
					<?php foreach ( hrld_get_font_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $font, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_text_style"><?php esc_html_e( 'Text style', 'heralda' ); ?></label></th>
			<td>
				<select id="hrld_text_style" name="_hrld_text_style">
					<?php foreach ( hrld_get_text_style_options() as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $text_style, $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="hrld_dismiss_days"><?php esc_html_e( 'Dismissible', 'heralda' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="hrld_dismissible" name="_hrld_dismissible" value="1" <?php checked( $dismissible, '1' ); ?> />
					<?php esc_html_e( 'Visitors can dismiss this bar', 'heralda' ); ?>
				</label>
				<br />
				<label style="margin-top:.5em;display:inline-block;">
					<?php esc_html_e( 'Stay hidden for (days):', 'heralda' ); ?>
					<input type="number" id="hrld_dismiss_days" name="_hrld_dismiss_days" value="<?php echo esc_attr( $dismiss_days ); ?>" min="0" class="small-text" />
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Schedule', 'heralda' ); ?></th>
			<td>
				<label>
					<?php esc_html_e( 'Start', 'heralda' ); ?>
					<input type="datetime-local" name="_hrld_schedule_start" value="<?php echo esc_attr( $schedule_start ); ?>" />
				</label>
				<label style="margin-left:1em;">
					<?php esc_html_e( 'End', 'heralda' ); ?>
					<input type="datetime-local" name="_hrld_schedule_end" value="<?php echo esc_attr( $schedule_end ); ?>" />
				</label>
				<p class="description"><?php esc_html_e( 'Leave blank for no start/end limit. Promo countdown timers count down to the End date.', 'heralda' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Target Pages', 'heralda' ); ?></th>
			<td>
				<label>
					<input type="checkbox" id="hrld_target_all" name="hrld_target_all" value="1" <?php checked( $target_all ); ?> />
					<?php esc_html_e( 'All pages', 'heralda' ); ?>
				</label>
				<div id="hrld_target_pages_list" style="max-height:180px;overflow:auto;border:1px solid #ddd;padding:.5em;margin-top:.5em;<?php echo $target_all ? 'display:none;' : ''; ?>">
					<?php foreach ( $pages as $page ) : ?>
						<label style="display:block;">
							<input type="checkbox" name="_hrld_target_pages[]" value="<?php echo esc_attr( $page->ID ); ?>" <?php checked( in_array( $page->ID, $target_ids, true ) ); ?> />
							<?php echo esc_html( $page->post_title ); ?>
						</label>
					<?php endforeach; ?>
					<?php if ( empty( $pages ) ) : ?>
						<em><?php esc_html_e( 'No pages found.', 'heralda' ); ?></em>
					<?php endif; ?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'CTA Button', 'heralda' ); ?></th>
			<td>
				<label>
					<?php esc_html_e( 'Text', 'heralda' ); ?>
					<input type="text" name="_hrld_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" class="regular-text" />
				</label>
				<br />
				<label style="margin-top:.5em;display:inline-block;">
					<?php esc_html_e( 'URL', 'heralda' ); ?>
					<input type="url" name="_hrld_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" class="regular-text" />
				</label>
			</td>
		</tr>
		<?php foreach ( hrld_get_extra_meta_box_fields() as $field ) : ?>
			<?php hrld_render_extra_field( $field, $post->ID ); ?>
		<?php endforeach; ?>
	</table>
	<?php
}

function hrld_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['hrld_bar_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hrld_bar_nonce'] ) ), 'hrld_save_bar' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['post_type'] ) || 'hrld_bar' !== $_POST['post_type'] ) {
		return;
	}

	$valid_types = array_keys( hrld_get_bar_types() );
	$type        = isset( $_POST['_hrld_type'] ) ? sanitize_key( wp_unslash( $_POST['_hrld_type'] ) ) : 'announcement';
	if ( ! in_array( $type, $valid_types, true ) ) {
		$type = 'announcement';
	}
	update_post_meta( $post_id, '_hrld_type', $type );

	$position = isset( $_POST['_hrld_position'] ) && 'bottom' === $_POST['_hrld_position'] ? 'bottom' : 'top';
	update_post_meta( $post_id, '_hrld_position', $position );

	update_post_meta( $post_id, '_hrld_sticky', isset( $_POST['_hrld_sticky'] ) ? '1' : '' );

	$bg_color = isset( $_POST['_hrld_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['_hrld_bg_color'] ) ) : '';
	update_post_meta( $post_id, '_hrld_bg_color', $bg_color ? $bg_color : '#1e1e1e' );

	$text_color = isset( $_POST['_hrld_text_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['_hrld_text_color'] ) ) : '';
	update_post_meta( $post_id, '_hrld_text_color', $text_color ? $text_color : '#ffffff' );

	update_post_meta( $post_id, '_hrld_dismissible', isset( $_POST['_hrld_dismissible'] ) ? '1' : '' );
	update_post_meta( $post_id, '_hrld_dismiss_days', isset( $_POST['_hrld_dismiss_days'] ) ? absint( $_POST['_hrld_dismiss_days'] ) : 7 );

	$schedule_start = isset( $_POST['_hrld_schedule_start'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_schedule_start'] ) ) : '';
	update_post_meta( $post_id, '_hrld_schedule_start', $schedule_start );

	$schedule_end = isset( $_POST['_hrld_schedule_end'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_schedule_end'] ) ) : '';
	update_post_meta( $post_id, '_hrld_schedule_end', $schedule_end );

	if ( ! empty( $_POST['hrld_target_all'] ) ) {
		update_post_meta( $post_id, '_hrld_target_pages', 'all' );
	} else {
		$target_ids = isset( $_POST['_hrld_target_pages'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['_hrld_target_pages'] ) ) : array();
		update_post_meta( $post_id, '_hrld_target_pages', $target_ids );
	}

	$cta_text = isset( $_POST['_hrld_cta_text'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_cta_text'] ) ) : '';
	update_post_meta( $post_id, '_hrld_cta_text', $cta_text );

	$cta_url = isset( $_POST['_hrld_cta_url'] ) ? esc_url_raw( wp_unslash( $_POST['_hrld_cta_url'] ) ) : '';
	update_post_meta( $post_id, '_hrld_cta_url', $cta_url );

	$color_preset = isset( $_POST['_hrld_color_preset'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_color_preset'] ) ) : '';
	update_post_meta( $post_id, '_hrld_color_preset', hrld_validate_enum( $color_preset, array_keys( hrld_get_color_presets() ), '' ) );

	$icon = isset( $_POST['_hrld_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_icon'] ) ) : '';
	update_post_meta( $post_id, '_hrld_icon', hrld_validate_enum( $icon, array_keys( hrld_get_bar_icons() ), '' ) );

	$bar_style = isset( $_POST['_hrld_bar_style'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_bar_style'] ) ) : '';
	update_post_meta( $post_id, '_hrld_bar_style', hrld_validate_enum( $bar_style, array_keys( hrld_get_bar_style_options() ), 'full' ) );

	$cta_style = isset( $_POST['_hrld_cta_style'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_cta_style'] ) ) : '';
	update_post_meta( $post_id, '_hrld_cta_style', hrld_validate_enum( $cta_style, array_keys( hrld_get_cta_style_options() ), 'outline' ) );

	$animation = isset( $_POST['_hrld_animation'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_animation'] ) ) : '';
	update_post_meta( $post_id, '_hrld_animation', hrld_validate_enum( $animation, array_keys( hrld_get_animation_options() ), 'none' ) );

	$align = isset( $_POST['_hrld_align'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_align'] ) ) : '';
	update_post_meta( $post_id, '_hrld_align', hrld_validate_enum( $align, array_keys( hrld_get_align_options() ), 'center' ) );

	$cta_position = isset( $_POST['_hrld_cta_position'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_cta_position'] ) ) : '';
	update_post_meta( $post_id, '_hrld_cta_position', hrld_validate_enum( $cta_position, array_keys( hrld_get_cta_position_options() ), 'inline' ) );

	$font = isset( $_POST['_hrld_font'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_font'] ) ) : '';
	update_post_meta( $post_id, '_hrld_font', hrld_validate_enum( $font, array_keys( hrld_get_font_options() ), '' ) );

	$text_style = isset( $_POST['_hrld_text_style'] ) ? sanitize_text_field( wp_unslash( $_POST['_hrld_text_style'] ) ) : '';
	update_post_meta( $post_id, '_hrld_text_style', hrld_validate_enum( $text_style, array_keys( hrld_get_text_style_options() ), 'normal' ) );

	$meta = array();
	foreach ( hrld_get_extra_meta_box_fields() as $field ) {
		if ( empty( $field['meta_key'] ) ) {
			continue;
		}
		$meta_key   = $field['meta_key'];
		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- every branch below applies a type-appropriate sanitizer to $raw before it is used or saved.
		$raw = isset( $_POST[ $meta_key ] ) ? wp_unslash( $_POST[ $meta_key ] ) : null;

		switch ( $field_type ) {
			case 'checkbox':
				$value = isset( $_POST[ $meta_key ] ) ? '1' : '';
				break;
			case 'number':
				$value = null !== $raw ? absint( $raw ) : 0;
				break;
			case 'color':
				$value = null !== $raw ? sanitize_hex_color( $raw ) : '';
				break;
			case 'url':
				$value = null !== $raw ? esc_url_raw( $raw ) : '';
				break;
			case 'textarea':
				$value = null !== $raw ? sanitize_textarea_field( $raw ) : '';
				break;
			case 'select':
				$options = isset( $field['options'] ) && is_array( $field['options'] ) ? array_keys( $field['options'] ) : array();
				$value   = null !== $raw ? sanitize_text_field( $raw ) : '';
				if ( ! empty( $options ) && ! in_array( $value, $options, true ) ) {
					$value = '';
				}
				break;
			default:
				$value = null !== $raw ? sanitize_text_field( $raw ) : '';
				break;
		}

		update_post_meta( $post_id, $meta_key, $value );
		$meta[ $meta_key ] = $value;
	}

	do_action( 'hrld_after_bar_save', $post_id, $meta );
}
add_action( 'save_post_hrld_bar', 'hrld_save_meta_box' );
