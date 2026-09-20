<?php
/**
 * Frontend render.
 *
 * Always hooked to wp_footer (not wp_body_open) regardless of the bar's
 * configured top/bottom position - not every theme calls wp_body_open, while
 * wp_footer is effectively universal, and visual placement is done entirely
 * with CSS `position: fixed`, so DOM order has no visual effect.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hrld_render_active_bars() {
	$bars = hrld_get_active_bars();

	if ( empty( $bars ) ) {
		return;
	}

	foreach ( $bars as $bar ) {
		hrld_render_single_bar( $bar );
	}
}
add_action( 'wp_footer', 'hrld_render_active_bars' );

/**
 * Allowed HTML for the fully-assembled bar body, applied right before
 * output. Extends the standard "post" kses tag set with the extra
 * elements/attributes Heralda's own markup (the inline SVG icon, the
 * countdown span) and Heralda Pro's registered hrld_render_bar_body slots are
 * known to add, so this final pass doesn't strip them. Filterable so Pro (or
 * any other hrld_render_bar_body extension) can register its own tags/attrs
 * instead of Free having to hardcode every one.
 *
 * @return array
 */
function hrld_bar_body_allowed_html() {
	$allowed = wp_kses_allowed_html( 'post' );

	$allowed['svg']  = array(
		'class'           => true,
		'width'           => true,
		'height'          => true,
		'viewbox'         => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
		'xmlns'           => true,
		'aria-hidden'     => true,
	);
	$allowed['path']  = array(
		'd' => true,
	);

	foreach ( array( 'span', 'div', 'a' ) as $tag ) {
		$allowed[ $tag ]['data-hld-countdown']          = true;
		$allowed[ $tag ]['data-hld-pro-cart-remaining']  = true;
		$allowed[ $tag ]['data-hld-pro-threshold']       = true;
	}

	return apply_filters( 'hrld_render_bar_body_allowed_html', $allowed );
}

/**
 * @param WP_Post $bar
 * @param bool    $inline When true, render as a static inline block (for the
 *                        [heralda_bar] shortcode) instead of a fixed top/bottom bar.
 */
function hrld_render_single_bar( $bar, $inline = false ) {
	do_action( 'hrld_before_render_bar', $bar );

	$type        = get_post_meta( $bar->ID, '_hrld_type', true );
	$position    = get_post_meta( $bar->ID, '_hrld_position', true );
	$sticky      = (bool) get_post_meta( $bar->ID, '_hrld_sticky', true ) && ! $inline;
	$dismissible = (bool) get_post_meta( $bar->ID, '_hrld_dismissible', true );
	$bg_color    = get_post_meta( $bar->ID, '_hrld_bg_color', true );
	$text_color  = get_post_meta( $bar->ID, '_hrld_text_color', true );

	$color_preset = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_color_preset', true ), array_keys( hrld_get_color_presets() ), '' );
	if ( $color_preset ) {
		$presets    = hrld_get_color_presets();
		$bg_color   = $presets[ $color_preset ]['bg'];
		$text_color = $presets[ $color_preset ]['text'];
	}

	$icon         = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_icon', true ), array_keys( hrld_get_bar_icons() ), '' );
	$bar_style    = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_bar_style', true ), array_keys( hrld_get_bar_style_options() ), 'full' );
	$cta_style    = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_cta_style', true ), array_keys( hrld_get_cta_style_options() ), 'outline' );
	$animation    = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_animation', true ), array_keys( hrld_get_animation_options() ), 'none' );
	$align        = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_align', true ), array_keys( hrld_get_align_options() ), 'center' );
	$cta_position = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_cta_position', true ), array_keys( hrld_get_cta_position_options() ), 'inline' );
	$font         = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_font', true ), array_keys( hrld_get_font_options() ), '' );
	$text_style   = hrld_validate_enum( get_post_meta( $bar->ID, '_hrld_text_style', true ), array_keys( hrld_get_text_style_options() ), 'normal' );

	$bg_color   = $bg_color ? $bg_color : '#1e1e1e';
	$text_color = $text_color ? $text_color : '#ffffff';

	$style = sprintf( 'background:%1$s;color:%2$s;--hld-bg:%1$s;--hld-text:%2$s;', $bg_color, $text_color );
	$style = apply_filters( 'hrld_bar_container_style', $style, $bar );

	$classes = array( 'hld-bar' );
	if ( $inline ) {
		$classes[] = 'hld-bar--inline';
	} else {
		$classes[] = 'hld-bar--' . sanitize_html_class( $position ? $position : 'top' );
	}
	$classes[] = $sticky ? 'hld-bar--sticky' : 'hld-bar--static';
	if ( 'floating' === $bar_style ) {
		$classes[] = 'hld-bar--floating';
	} elseif ( 'pill' === $bar_style ) {
		$classes[] = 'hld-bar--pill';
	}
	if ( 'none' !== $animation ) {
		$classes[] = 'hld-bar--anim-' . $animation;
	}
	if ( 'center' !== $align ) {
		$classes[] = 'hld-bar--align-' . $align;
	}
	if ( 'inline' !== $cta_position ) {
		$classes[] = 'hld-bar--cta-' . sanitize_html_class( $cta_position );
	}
	if ( $icon ) {
		$classes[] = 'hld-bar--has-icon';
	}
	if ( $font ) {
		$classes[] = 'hld-bar--font-' . sanitize_html_class( $font );
	}
	if ( 'normal' !== $text_style ) {
		$classes[] = 'hld-bar--textstyle-' . sanitize_html_class( $text_style );
	}

	$body = wp_kses_post( $bar->post_content );
	$body = wpautop( $body );
	$body = do_shortcode( $body );

	$body = '<div class="hld-bar__text">' . $body . '</div>';

	if ( 'promo' === $type ) {
		// A sibling of .hld-bar__text, not nested inside it - .hld-bar__text
		// can contain a block-level <p>, and a block element always starts a
		// new line for anything after it even inside a flex item, so nesting
		// the countdown there would strand it under the message instead of
		// beside it on the shared .hld-bar__inner flex row.
		$body .= '<span class="hld-bar__countdown" data-hld-countdown></span>';
	}

	if ( $icon ) {
		$body = '<span class="hld-bar__icon">' . hrld_render_bar_icon_svg( $icon ) . '</span>' . $body;
	}

	$cta_text = get_post_meta( $bar->ID, '_hrld_cta_text', true );
	$cta_url  = get_post_meta( $bar->ID, '_hrld_cta_url', true );

	if ( $cta_text && $cta_url ) {
		$body .= sprintf(
			'<a class="hld-bar__cta hld-bar__cta--%s" href="%s">%s</a>',
			esc_attr( $cta_style ),
			esc_url( $cta_url ),
			esc_html( $cta_text )
		);
	}

	$body = apply_filters( 'hrld_render_bar_body', $body, $bar, $type );
	$body = wp_kses( $body, hrld_bar_body_allowed_html() );
	?>
	<div
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		data-hld-bar-id="<?php echo esc_attr( $bar->ID ); ?>"
		data-hld-position="<?php echo esc_attr( $position ? $position : 'top' ); ?>"
		style="<?php echo esc_attr( $style ); ?>"
	>
		<div class="hld-bar__inner">
			<?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses()'d above via hrld_bar_body_allowed_html(). ?>
		</div>
		<?php if ( $dismissible ) : ?>
			<button type="button" class="hld-bar__dismiss" aria-label="<?php esc_attr_e( 'Dismiss', 'heralda' ); ?>">&times;</button>
		<?php endif; ?>
	</div>
	<?php
}
