<?php
/**
 * Free decorative options for bars: color presets, bar container style, CTA
 * button style, entrance animation, text alignment, and a small fixed icon
 * set. Every new meta value defaults to '' / the option below marked as
 * default, which renders byte-identical to how bars looked before this file
 * existed - see includes/render.php for where each default is applied.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string,array{label:string,bg:string,text:string}>
 */
function hrld_get_color_presets() {
	return array(
		'dark'       => array(
			'label' => __( 'Dark', 'heralda' ),
			'bg'    => '#1e1e1e',
			'text'  => '#ffffff',
		),
		'light'      => array(
			'label' => __( 'Light', 'heralda' ),
			'bg'    => '#f5f5f5',
			'text'  => '#1e1e1e',
		),
		'royal-blue' => array(
			'label' => __( 'Royal Blue', 'heralda' ),
			'bg'    => '#1d4ed8',
			'text'  => '#ffffff',
		),
		'emerald'    => array(
			'label' => __( 'Emerald', 'heralda' ),
			'bg'    => '#059669',
			'text'  => '#ffffff',
		),
		'crimson'    => array(
			'label' => __( 'Crimson', 'heralda' ),
			'bg'    => '#dc2626',
			'text'  => '#ffffff',
		),
		'violet'     => array(
			'label' => __( 'Violet', 'heralda' ),
			'bg'    => '#7c3aed',
			'text'  => '#ffffff',
		),
		'amber'      => array(
			'label' => __( 'Amber', 'heralda' ),
			'bg'    => '#f59e0b',
			'text'  => '#1e1e1e',
		),
		'slate'      => array(
			'label' => __( 'Slate', 'heralda' ),
			'bg'    => '#334155',
			'text'  => '#f1f5f9',
		),
		'terracotta' => array(
			'label' => __( 'Terracotta', 'heralda' ),
			'bg'    => '#c67139',
			'text'  => '#fff5ea',
		),
		'sage'       => array(
			'label' => __( 'Sage', 'heralda' ),
			'bg'    => '#7a8a5e',
			'text'  => '#fbf6ec',
		),
	);
}

/**
 * Six fixed icons. SVG `path` data is static/trusted (authored here, not
 * user input) - only the lookup slug needs validating before use.
 *
 * @return array<string,array{label:string,path:string}>
 */
function hrld_get_bar_icons() {
	return array(
		'megaphone' => array(
			'label' => __( 'Megaphone', 'heralda' ),
			'path'  => 'M3 10v4a1 1 0 0 0 1 1h2l4 4V5L6 9H4a1 1 0 0 0-1 1zM15 8a4 4 0 0 1 0 8M18.5 5.5a9 9 0 0 1 0 13',
		),
		'bell'      => array(
			'label' => __( 'Bell', 'heralda' ),
			'path'  => 'M18 16v-5a6 6 0 1 0-12 0v5l-2 2h16l-2-2zM10 20a2 2 0 0 0 4 0',
		),
		'gift'      => array(
			'label' => __( 'Gift', 'heralda' ),
			'path'  => 'M4 8h16v4H4zM4 12h16v9H4zM12 8v13M8 8c-1.5 0-2.5-1-2.5-2S6.5 4 8 4c1.5 0 3 1.5 4 4M16 8c1.5 0 2.5-1 2.5-2S17.5 4 16 4c-1.5 0-3 1.5-4 4',
		),
		'star'      => array(
			'label' => __( 'Star', 'heralda' ),
			'path'  => 'M12 2.5l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2.5z',
		),
		'fire'      => array(
			'label' => __( 'Fire', 'heralda' ),
			'path'  => 'M12 2c1.5 3-1.5 4.5-1.5 7.5a3.5 3.5 0 1 0 7 0c0-1-.5-1.8-.5-1.8.8 3.6-1 5.3-2.5 5.3a3 3 0 0 1-3-3c0-2.5 2.5-3.5.5-8z',
		),
		'tag'       => array(
			'label' => __( 'Tag', 'heralda' ),
			'path'  => 'M20.59 13.41L11 3.83A2 2 0 0 0 9.59 3H4a1 1 0 0 0-1 1v5.59a2 2 0 0 0 .59 1.41l9.58 9.58a2 2 0 0 0 2.83 0l4.59-4.59a2 2 0 0 0 0-2.58zM7.5 7.5h.01',
		),
		'info'      => array(
			'label' => __( 'Info', 'heralda' ),
			'path'  => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM12 11v6M12 7h.01',
		),
	);
}

function hrld_get_bar_style_options() {
	return array(
		'full'     => __( 'Full-width (edge to edge)', 'heralda' ),
		'floating' => __( 'Floating (inset, rounded, shadow)', 'heralda' ),
		'pill'     => __( 'Pill (inset, fully rounded, shadow)', 'heralda' ),
	);
}

function hrld_get_cta_style_options() {
	return array(
		'outline' => __( 'Outline', 'heralda' ),
		'solid'   => __( 'Solid', 'heralda' ),
		'pill'    => __( 'Pill', 'heralda' ),
	);
}

function hrld_get_animation_options() {
	return array(
		'none'  => __( 'None', 'heralda' ),
		'slide' => __( 'Slide in', 'heralda' ),
		'fade'  => __( 'Fade in', 'heralda' ),
	);
}

function hrld_get_align_options() {
	return array(
		'center' => __( 'Center', 'heralda' ),
		'left'   => __( 'Left', 'heralda' ),
		'right'  => __( 'Right', 'heralda' ),
	);
}

/**
 * Where the CTA button sits relative to the message text. Independent of
 * "Text alignment" above: alignment positions the icon+text+CTA group as a
 * whole within the bar, this positions the CTA within that group.
 *
 * @return array<string,string>
 */
function hrld_get_cta_position_options() {
	return array(
		'inline' => __( 'Inline, next to the text', 'heralda' ),
		'edge'   => __( 'Right edge of the bar', 'heralda' ),
		'below'  => __( 'Below the text', 'heralda' ),
	);
}

/**
 * Font-family choices. Every stack is made of fonts already present on the
 * OS (no @font-face, no remote font service - see readme.txt's "External
 * services" section, which states this plugin loads nothing off-site).
 * '' (Default) applies no font-family at all, so the bar keeps inheriting
 * the theme's own font exactly like every bar did before this option
 * existed.
 *
 * @return array<string,string>
 */
function hrld_get_font_options() {
	return array(
		''          => __( 'Default (matches your theme)', 'heralda' ),
		'sans'      => __( 'Sans-serif', 'heralda' ),
		'serif'     => __( 'Serif', 'heralda' ),
		'mono'      => __( 'Monospace', 'heralda' ),
		'condensed' => __( 'Bold condensed (display)', 'heralda' ),
	);
}

/**
 * @return array<string,string> CSS font-family value per hrld_get_font_options() slug.
 */
function hrld_get_font_stacks() {
	return array(
		'sans'      => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
		'serif'     => "Georgia, Cambria, 'Times New Roman', Times, serif",
		'mono'      => "'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace",
		'condensed' => "'Arial Narrow', 'Helvetica Neue Condensed', Arial, sans-serif",
	);
}

/**
 * Text style presets for the bar's message (weight/transform/spacing
 * combos), independent of the CTA button style set above.
 *
 * @return array<string,string>
 */
function hrld_get_text_style_options() {
	return array(
		'normal'    => __( 'Normal', 'heralda' ),
		'bold'      => __( 'Bold', 'heralda' ),
		'uppercase' => __( 'Uppercase, spaced out', 'heralda' ),
		'italic'    => __( 'Italic', 'heralda' ),
	);
}

/**
 * Design presets: one-click bundles of the individual decoration fields
 * above (color, bar style, CTA style, animation, font, text style),
 * for people who want a finished look without tuning six controls one at
 * a time. Purely a convenience that fills in the same fields those
 * controls already write to - there's no separate "preset" meta key and
 * no persistent "which preset is this" state, so applying one is a
 * one-time starting point (like the content templates in
 * content-templates.php), not a locked mode; every field stays fully
 * editable afterward. This is the single source of truth: admin.js reads
 * these values straight off the wp_localize_script() data (see
 * meta-boxes.php), it doesn't keep its own copy to stay in sync with.
 *
 * @return array<string,array{label:string,color_preset:string,bar_style:string,cta_style:string,animation:string,align:string,font:string,text_style:string}>
 */
function hrld_get_design_presets() {
	return array(
		'minimal'   => array(
			'label'        => __( 'Minimal', 'heralda' ),
			'color_preset' => 'dark',
			'bar_style'    => 'full',
			'cta_style'    => 'outline',
			'animation'    => 'none',
			'align'        => 'left',
			'font'         => 'sans',
			'text_style'   => 'normal',
		),
		'bold'      => array(
			'label'        => __( 'Bold', 'heralda' ),
			'color_preset' => 'crimson',
			'bar_style'    => 'full',
			'cta_style'    => 'solid',
			'animation'    => 'slide',
			'align'        => 'left',
			'font'         => 'condensed',
			'text_style'   => 'uppercase',
		),
		'corporate' => array(
			'label'        => __( 'Corporate', 'heralda' ),
			'color_preset' => 'slate',
			'bar_style'    => 'floating',
			'cta_style'    => 'outline',
			'animation'    => 'fade',
			'align'        => 'left',
			'font'         => 'sans',
			'text_style'   => 'normal',
		),
		'playful'   => array(
			'label'        => __( 'Playful', 'heralda' ),
			'color_preset' => 'terracotta',
			'bar_style'    => 'pill',
			'cta_style'    => 'pill',
			'animation'    => 'fade',
			'align'        => 'left',
			'font'         => 'sans',
			'text_style'   => 'bold',
		),
		'promo'     => array(
			'label'        => __( 'Promo', 'heralda' ),
			'color_preset' => 'sage',
			'bar_style'    => 'pill',
			'cta_style'    => 'pill',
			'animation'    => 'fade',
			'align'        => 'left',
			'font'         => 'sans',
			'text_style'   => 'normal',
		),
	);
}

/**
 * @param mixed  $value    Value to validate.
 * @param array  $allowed  Whitelist of accepted values.
 * @param string $fallback Fallback when $value isn't in the whitelist.
 * @return string
 */
function hrld_validate_enum( $value, array $allowed, $fallback ) {
	return in_array( $value, $allowed, true ) ? $value : $fallback;
}

/**
 * Render one bar icon as inline SVG, or '' for an unknown/empty slug.
 *
 * @param string $slug
 * @return string
 */
function hrld_render_bar_icon_svg( $slug ) {
	$icons = hrld_get_bar_icons();
	if ( ! isset( $icons[ $slug ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="hld-bar__icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="%s" /></svg>',
		esc_attr( $icons[ $slug ]['path'] )
	);
}
