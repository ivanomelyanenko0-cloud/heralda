/**
 * Heralda admin metabox UX: color pickers + "all pages" toggle.
 */
( function ( $ ) {
	'use strict';

	// Seam for Heralda Pro to extend the live preview (background gradient,
	// secondary CTA, etc.) without this file knowing Pro exists - the same
	// "Free defines the seam, Pro fills it" pattern as the PHP hooks in
	// extension-api.php, just for the admin-only preview instead of the
	// frontend render. Initialized here (not only in hldRebuildPreview())
	// so Pro's own script can safely push onto it regardless of script load
	// order.
	window.heraldaPreviewFilters = window.heraldaPreviewFilters || [];

	$( function () {
		$( '.hld-color-field' ).wpColorPicker();

		var $allToggle = $( '#hrld_target_all' );
		var $list      = $( '#hrld_target_pages_list' );

		function syncTargetList() {
			$list.toggle( ! $allToggle.is( ':checked' ) );
		}

		$allToggle.on( 'change', syncTargetList );
		syncTargetList();

		// Swatch pickers (color preset, icon): keep the .is-selected class in
		// sync with the checked radio, since the visual selection state lives
		// on the wrapping <label>, not on the (visually hidden) <input>.
		$( '.hld-swatches' ).each( function () {
			var $group = $( this );
			$group.on( 'change', 'input[type="radio"]', function () {
				$group.find( '.hld-swatch' ).removeClass( 'is-selected' );
				$( this ).closest( '.hld-swatch' ).addClass( 'is-selected' );
			} );
		} );

		var $colorSwatches = $( '#hrld_color_swatches' );
		var $customRow      = $( '#hrld_custom_colors_row' );

		function syncCustomColorsRow() {
			var preset = $colorSwatches.find( 'input[type="radio"]:checked' ).val();
			$customRow.toggle( ! preset );
		}

		$colorSwatches.on( 'change', 'input[type="radio"]', syncCustomColorsRow );
		syncCustomColorsRow();

		// Content templates: one-shot "insert starting content" buttons, not
		// persisted radio state like the swatches above, so they don't need
		// the .is-selected toggling pattern.
		var templates = ( window.heraldaAdminTemplates && window.heraldaAdminTemplates.templates ) || {};

		function hldEditorHasContent() {
			if ( typeof tinymce !== 'undefined' && tinymce.get( 'content' ) && ! tinymce.get( 'content' ).isHidden() ) {
				return tinymce.get( 'content' ).getContent( { format: 'text' } ).trim().length > 0;
			}
			return $.trim( $( '#content' ).val() ).length > 0;
		}

		function hldInsertTemplate( html ) {
			if ( typeof tinymce !== 'undefined' && tinymce.get( 'content' ) && ! tinymce.get( 'content' ).isHidden() ) {
				var editor = tinymce.get( 'content' );
				editor.setContent( html );
				editor.fire( 'change' );
			} else {
				$( '#content' ).val( html );
			}
		}

		$( '.hld-content-template-btn' ).on( 'click', function () {
			var tpl = templates[ $( this ).data( 'hld-template' ) ];
			if ( ! tpl ) {
				return;
			}
			if ( hldEditorHasContent() && ! window.confirm( window.heraldaAdminTemplates.confirmMessage ) ) {
				return;
			}
			hldInsertTemplate( tpl.html );
		} );

		// Design presets: one-click bundle of the individual decoration
		// fields (color/style/CTA/animation/font/text style) below - a
		// starting point, not a locked mode, so this just fills in the same
		// fields those controls already write to and lets their own change
		// handlers (swatch highlighting, live preview) take it from there.
		var designPresets = ( window.heraldaDesignPresets && window.heraldaDesignPresets.presets ) || {};

		$( '.hld-design-preset-btn' ).on( 'click', function () {
			var preset = designPresets[ $( this ).data( 'hld-preset' ) ];
			if ( ! preset ) {
				return;
			}

			$( '#hrld_color_swatches input[value="' + preset.color_preset + '"]' )
				.prop( 'checked', true )
				.trigger( 'change' );
			$( '#hrld_bar_style' ).val( preset.bar_style ).trigger( 'change' );
			$( '#hrld_cta_style' ).val( preset.cta_style ).trigger( 'change' );
			$( '#hrld_animation' ).val( preset.animation ).trigger( 'change' );
			$( '#hrld_align' ).val( preset.align ).trigger( 'change' );
			$( '#hrld_font' ).val( preset.font ).trigger( 'change' );
			$( '#hrld_text_style' ).val( preset.text_style ).trigger( 'change' );
		} );

		// Live preview: mirrors the current decoration + content selections
		// into #hrld_preview_bar, reading colors/icons straight from the
		// already-rendered swatch DOM rather than duplicating decorations.php's
		// option arrays a second time here.
		function hldPreviewColors() {
			var $checked = $( '#hrld_color_swatches input:checked' );
			if ( $checked.val() ) {
				var $preview = $checked.closest( '.hld-swatch' ).find( '.hld-swatch__preview' );
				return { bg: $preview.css( 'background-color' ), text: $preview.css( 'color' ) };
			}
			return {
				bg: $( 'input[name="_hrld_bg_color"]' ).val() || '#1e1e1e',
				text: $( 'input[name="_hrld_text_color"]' ).val() || '#ffffff'
			};
		}

		function hldPreviewIconSvg() {
			var $checked = $( '.hld-swatches--icons input:checked' );
			if ( ! $checked.val() ) {
				return '';
			}
			var $svg = $checked.closest( '.hld-swatch' ).find( '.hld-swatch__preview--icon svg' );
			return $svg.length ? $svg.clone()[ 0 ].outerHTML : '';
		}

		function hldPreviewContent() {
			if ( typeof tinymce !== 'undefined' && tinymce.get( 'content' ) && ! tinymce.get( 'content' ).isHidden() ) {
				return tinymce.get( 'content' ).getContent();
			}
			return $( '#content' ).val();
		}

		function hldRebuildPreview() {
			var colors = hldPreviewColors();
			var iconSvg = hldPreviewIconSvg();
			var barStyle = $( '#hrld_bar_style' ).val();
			var ctaStyle = $( '#hrld_cta_style' ).val();
			var animation = $( '#hrld_animation' ).val();
			var align = $( '#hrld_align' ).val();
			var font = $( '#hrld_font' ).val();
			var textStyle = $( '#hrld_text_style' ).val();
			var type = $( '#hrld_type' ).val();
			var dismissible = $( '#hrld_dismissible' ).is( ':checked' );
			var ctaText = $( 'input[name="_hrld_cta_text"]' ).val();
			var ctaUrl = $( 'input[name="_hrld_cta_url"]' ).val() || '#';

			var classes = [ 'hld-bar', 'hld-bar--preview', 'hld-bar--static' ];
			if ( 'floating' === barStyle ) {
				classes.push( 'hld-bar--floating' );
			}
			if ( 'none' !== animation ) {
				classes.push( 'hld-bar--anim-' + animation );
			}
			if ( 'center' !== align ) {
				classes.push( 'hld-bar--align-' + align );
			}
			if ( iconSvg ) {
				classes.push( 'hld-bar--has-icon' );
			}
			if ( font ) {
				classes.push( 'hld-bar--font-' + font );
			}
			if ( 'normal' !== textStyle ) {
				classes.push( 'hld-bar--textstyle-' + textStyle );
			}

			var body = '<div class="hld-bar__text">' + hldPreviewContent() + '</div>';
			if ( 'promo' === type ) {
				body += '<span class="hld-bar__countdown">00:00:00</span>';
			}
			var inner = ( iconSvg ? '<span class="hld-bar__icon">' + iconSvg + '</span>' : '' ) + body;
			if ( ctaText ) {
				inner += '<a class="hld-bar__cta hld-bar__cta--' + ctaStyle + '" href="' + ctaUrl + '">' + $( '<div>' ).text( ctaText ).html() + '</a>';
			}
			if ( dismissible ) {
				inner += '<button type="button" class="hld-bar__dismiss">&times;</button>';
			}

			// Mutable state passed through window.heraldaPreviewFilters before
			// anything is written to the DOM, so a registered filter (Pro's
			// gradient background / secondary CTA) can override or append to
			// what Free already computed.
			var state = { classes: classes, bg: colors.bg, text: colors.text, innerHtml: inner };
			$.each( window.heraldaPreviewFilters, function ( i, fn ) {
				fn( state );
			} );

			var $bar = $( '#hrld_preview_bar' );
			$bar.attr( 'class', state.classes.join( ' ' ) );
			$bar.css( { background: state.bg, color: state.text } );
			$( '#hrld_preview_inner' ).html( state.innerHtml );
		}

		// Exposed so Pro's own field listeners can trigger a refresh without
		// this file needing to know which Pro fields exist.
		window.hldRebuildPreview = hldRebuildPreview;

		// TinyMCE loads async, so its change/input listeners attach via the
		// 'tinymce-editor-setup' event WP fires for each editor instance,
		// not at document-ready time.
		$( '#hrld_color_swatches, .hld-swatches--icons' ).on( 'change', 'input[type="radio"]', hldRebuildPreview );
		$( '.hld-color-field' ).on( 'change', hldRebuildPreview );
		$( '#hrld_bar_style, #hrld_cta_style, #hrld_animation, #hrld_align, #hrld_font, #hrld_text_style, #hrld_type, #hrld_dismissible' ).on( 'change', hldRebuildPreview );
		$( 'input[name="_hrld_cta_text"], input[name="_hrld_cta_url"]' ).on( 'input', hldRebuildPreview );
		$( '#content' ).on( 'input', hldRebuildPreview );
		$( document ).on( 'tinymce-editor-setup', function ( event, editor ) {
			if ( 'content' !== editor.id ) {
				return;
			}
			editor.on( 'input change keyup SetContent', hldRebuildPreview );
		} );
		hldRebuildPreview();
	} );
} )( jQuery );
