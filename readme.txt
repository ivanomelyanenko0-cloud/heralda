=== Heralda ===
Contributors: lukystile
Tags: announcement bar, notification bar, sticky bar, promo bar, countdown
Requires at least: 5.9
Tested up to: 7.1
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight sticky announcement / promo bar for the top or bottom of your site, with dismiss, scheduling, page targeting, and a built-in countdown.

== Description ==

**Heralda** adds a clean, fast, sticky bar to the top or bottom of your site for announcements, promotions, and time-limited offers.

**[Try the live demo](https://founder.cognitolab.net/heralda-demo/index.html)** — a real WordPress site with a sample bar already published, no install needed.

* **Announcement bars** for site-wide notices, maintenance windows, or news.
* **Promo bars** with a built-in countdown timer for limited-time offers.
* **Dismissible** — visitors can close the bar, and it stays hidden for as long as you choose.
* **Scheduling** — set a start and end date so a bar appears and disappears automatically.
* **Page targeting** — show a bar site-wide or only on the pages you pick.
* **One bar at a time** — if several bars are eligible to show, Heralda shows the most recently published one, so your site never looks cluttered.
* **Fast** — the bar's CSS/JS is only loaded on pages where a bar will actually show, and the frontend script has no jQuery dependency.

Need more than one bar at a time, extra bar types (social share, WooCommerce free-shipping progress), advanced targeting (by user role or device), a repeating schedule, analytics, or A/B testing? Those are available in **[Heralda Pro](https://cognitolab.net/products/heralda)** — a separate, optional add-on. Everything listed above is fully functional in the free version with no restrictions or nag screens.

== External services ==

This plugin does not connect to any external service. No data leaves your site. All bar content, settings, and dismiss state are stored either in your own WordPress database (bar content and settings) or in the visitor's own browser `localStorage` (dismiss state) - nothing is sent to Heralda, CognitoLab, or any third party.

== Installation ==

1. Upload the `heralda` folder to `/wp-content/plugins/`, or install it directly from the WordPress plugin directory.
2. Activate the plugin through the **Plugins** screen.
3. Go to **Heralda Bars → Add New** to create your first bar.
4. Set its type, position, colors, schedule, and target pages, then publish it.

== Frequently Asked Questions ==

= Can I show more than one bar at the same time? =

The free version always shows a single bar - if more than one published bar matches the current page and schedule, the most recently published one is shown. Showing multiple bars at once with a priority queue is a Heralda Pro feature.

= Does this plugin slow down my site? =

No. The bar's CSS and JavaScript are only enqueued on pages where an active bar will actually render, and the frontend script does not depend on jQuery.

= Where is the dismissed state stored? =

In the visitor's browser, via `localStorage`. Nothing is stored on the server or sent anywhere.

= Can I place a bar manually inside a page instead of at the top/bottom of the site? =

Yes, use the `[heralda_bar id="123"]` shortcode with the ID of the bar you want to embed.

== Screenshots ==

1. Minimal design preset - a quiet, full-width announcement bar.
2. Bold design preset - a high-contrast, uppercase promo bar.
3. Corporate design preset - a floating notice with the Info icon.
4. Playful design preset - a friendly, fully-rounded Pill bar.
5. Promo design preset - a Pill bar with a live countdown.

== Changelog ==

= 1.0.2 =
* New "Pill" bar style (fully rounded, floating) alongside Full-width and Floating.
* 2 new color presets: Terracotta and Sage (10 total).
* New "Info" icon (7 built-in icons total).
* New "Promo" design preset (Sage colors, Pill shape, pill CTA) - 5 one-click design presets now available.
* Refreshed the Minimal, Bold, Corporate, and Playful design presets: Minimal now uses the Dark color preset and Sans-serif font; Bold, Corporate, and Playful now align left instead of center; Playful now uses the Terracotta color preset and Pill bar style.
* New "Right" text alignment, alongside Center and Left.
* New "CTA position" field: Inline next to the text (default), Right edge of the bar, or Below the text.
* The message and CTA button now always sit on the same row when they fit, regardless of whether an icon is set - previously that only happened with an icon selected, so most bars without one showed the button stranded on its own line underneath.
* Fixed the Promo countdown rendering nested inside the message text, which forced it (and the CTA button next to it) onto its own line even when there was room to share the first one.
* The dismiss ("x") button now always sits in the top-right corner instead of vertically centered, so it stays put on bars with wrapped or multi-line content instead of drifting toward the middle.
* Fixed the "Solid" CTA button rendering unreadable (same color as its own background) in the bar editor's live preview for every color preset - a preview-only bug, the frontend was never affected.

= 1.0.1 =
* Renumbered ahead of the first public release - nothing above 1.0.0 was ever published, so this replaces what was internally 1.1.0 through 1.4.1 with a single real-world version.
* 8 ready-made color presets for bars, alongside the custom color pickers.
* "Floating" bar style (inset margins, rounded corners, shadow) as an alternative to full-width.
* CTA button styles: Outline, Solid, and Pill.
* Optional icon before the bar text (6 built-in icons), entrance animation (slide-in or fade-in), and left/center text alignment.
* Font choice (Default/theme, Sans-serif, Serif, Monospace, Bold condensed) and text style presets (Normal, Bold, Uppercase, Italic) for the bar's message.
* "Design preset" one-click buttons (Minimal, Bold, Corporate, Playful) that fill in a matching combination of the above in one click - every field stays editable afterward.
* "Start from a template" buttons and a live preview on the bar editor screen.
* Correct spacing between a bar's content elements (heading, paragraph, CTA, and Pro-added content like Social Share links), and correct stacking when Heralda Pro shows multiple bars at once - including bars whose height changes after load, like a Promo countdown filling in its text.
* Pasted images in a bar's content are capped to a small badge/logo size; pasted headings, blockquotes, and lists get bar-appropriate sizing instead of full browser-default styling.

= 1.0.0 =
* Initial release.
