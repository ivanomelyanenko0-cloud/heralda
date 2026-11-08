=== Heralda – Announcement Bar & Notification Bar ===
Contributors: lukystile
Tags: announcement bar, notification bar, top bar, promo bar, countdown
Requires at least: 5.9
Tested up to: 7.1
Stable tag: 1.1.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sticky announcement bar and notification bar for WordPress: top or bottom bar with countdown timer, scheduling, page targeting and a close button.

== Description ==

A message that matters gets lost in a sidebar widget or ignored as just another popup. **Heralda puts it where visitors actually look** - a sticky bar at the top or bottom of your site, gone the moment it's no longer useful.

**Heralda** is a lightweight announcement bar and notification bar plugin for WordPress. Use it as a top bar for site-wide news, a promo bar for a sale with a countdown timer, or a header banner for a maintenance notice. Schedule when it shows, pick the pages it appears on, and let visitors dismiss it with one click. It works with any theme, needs no page builder, and loads nothing on pages where no bar is shown.

**[Try the live demo](https://founder.cognitolab.net/heralda-demo/index.html)** — a real WordPress site with a sample bar already published, no install needed.

= What you can put in the bar =

* **Announcements** — site-wide notices, maintenance windows, or news.
* **Promotions** — a sale or discount code in a promo bar, with a built-in countdown timer for time-limited offers.
* **One bar at a time** — if several bars are eligible to show, Heralda shows the most recently published one, so your site never looks cluttered with competing banners.

= Notification bar designs in one click =

* **5 design presets** (Minimal, Bold, Corporate, Playful, Promo) that set a matching style in one click. Everything stays editable afterwards.
* **16 colour presets**, every one readable (WCAG AA contrast), or pick your own with the custom colour pickers. A built-in contrast checker warns when your own colours are hard to read and finds the nearest readable text colour in one click.
* **3 bar styles**: Full-width, Floating (inset with shadow), or Pill (fully rounded), with an optional border: thin, thick, dashed, or an accent line on the inner edge.
* **CTA button styles** (Outline, Solid, Pill) and position (inline next to the text, right edge of the bar, or below the text).
* **7 built-in icons** with an optional pulse, wiggle or bounce that plays a few times and stops, entrance animation (slide-in or fade-in), and left/centre/right text alignment.
* **Typography**: use your theme's font or pick sans-serif, serif, monospace or bold condensed, plus normal, bold, uppercase or italic text styles.

= Built to get out of the way =

* **Dismissible.** Visitors close it once, and it stays hidden for as many days as you choose, or until they close their browser.
* **Scheduling.** Set a start and end date so a bar appears and disappears automatically — no need to remember to take it down.
* **Page targeting.** Show a bar site-wide or only on the pages you pick.
* **Fast.** CSS/JS load only on pages where a bar will actually show, and the frontend script has no jQuery dependency.
* **No external services and no tracking.** Nothing leaves your site.

= Who it's for =

* **Site owners** running a sale, a launch, or a maintenance notice who want it seen without resorting to a popup.
* **Agencies** who need a quick, on-brand promo bar for a client site without installing a heavier marketing plugin.
* **Content teams** running scheduled campaigns who don't want to remember to manually take a bar down afterward.

= Want more? =

Need more than one bar at a time, extra bar types (social share, WooCommerce free-shipping progress), advanced targeting (by user role or device), a repeating schedule, analytics, or A/B testing? Those are available in **[Heralda Pro](https://cognitolab.net/products/heralda)** — a separate, optional add-on. Everything listed above is fully functional in the free version with no restrictions or nag screens.

== External services ==

This plugin does not connect to any external service. No data leaves your site. All bar content, settings, and dismiss state are stored either in your own WordPress database (bar content and settings) or in the visitor's own browser `localStorage` (dismiss state) - nothing is sent to Heralda, CognitoLab, or any third party.

== Installation ==

1. Upload the `heralda` folder to `/wp-content/plugins/`, or install it directly from the WordPress plugin directory.
2. Activate the plugin through the **Plugins** screen.
3. Go to **Heralda Bars → Add New** to create your first bar.
4. Set its type, position, colors, schedule, and target pages, then publish it.

== Frequently Asked Questions ==

= How do I add an announcement bar to WordPress? =

Install and activate Heralda, go to **Heralda Bars → Add New**, write your message, pick a design preset and publish. The bar shows at the top of your site straight away. You can move it to the bottom, schedule it or limit it to certain pages in the same screen.

= Can I add a countdown timer to the bar? =

Yes. Choose the Promo bar type and set an end date: the bar shows a live countdown, which is useful for sales, launches and early-bird deadlines.

= Can I show the bar only on certain pages? =

Yes. Each bar can show site-wide or only on the pages you pick, and a start and end date make it appear and disappear on its own.

= Can I show more than one bar at the same time? =

The free version always shows a single bar - if more than one published bar matches the current page and schedule, the most recently published one is shown. Showing multiple bars at once with a priority queue is a Heralda Pro feature.

= Does this plugin slow down my site? =

No. The bar's CSS and JavaScript are only enqueued on pages where an active bar will actually render, and the frontend script does not depend on jQuery.

= Where is the dismissed state stored? =

In the visitor's browser, via `localStorage`. Nothing is stored on the server or sent anywhere.

= Can I place a bar manually inside a page instead of at the top/bottom of the site? =

Yes, use the `[heralda_bar id="123"]` shortcode with the ID of the bar you want to embed.

= Is Heralda accessible? =

Accessibility is built into the free plugin, not sold as an extra:

* Screen readers announce the bar as a labelled "Announcement" region, so it can be found from the landmarks list wherever it sits on screen.
* The close button works with the keyboard (Tab, then Enter or Space), has a visible focus outline in the bar's own text color, and is at least 24 by 24 pixels.
* The countdown timer is not read out every second.
* Visitors who turn on reduced motion in their system settings get no slide, fade, or icon animations.
* All 16 color presets meet WCAG AA contrast (4.5:1) for normal text.
* A contrast checker under the live preview shows the ratio of your text and background colours, warns below 4.5:1, and offers the nearest readable text colour. It never blocks saving.

The checker covers your own colors too. Colors inside the text you write (for example a highlighted word) aren't checked, so keep an eye on those.

== Screenshots ==

1. Minimal design preset - a quiet, full-width announcement bar.
2. Bold design preset - a high-contrast, uppercase promo bar.
3. Corporate design preset - a floating notice with the Info icon.
4. Playful design preset - a friendly, fully-rounded Pill bar.
5. Promo design preset - a Pill bar with a live countdown.

== Changelog ==

= 1.1.2 =
* New: contrast checker. Under the live preview, Heralda shows how readable the bar's text is against its background and warns when it falls below WCAG AA (4.5:1).
* New: "Use the nearest readable text color" button keeps your color's hue and adjusts only its lightness, as little as needed to pass. Saving is never blocked.
* For developers: the live preview's filter state now carries `contrastBackgrounds` and `contrastUnknown`, so an extension that paints its own background (such as a gradient) can have it checked too.

= 1.1.1 =
* Accessibility: a top or bottom bar is now a labelled "Announcement" region for screen readers, and changes to its content are announced politely.
* Accessibility: the countdown timer is no longer read out by screen readers every second.
* Accessibility: the close button shows a clear focus outline when reached with the keyboard, is at least 24 by 24 pixels, and is labelled "Dismiss announcement".
* Accessibility: visitors who turn on reduced motion no longer see the bar slide or fade in.
* For developers: new `hrld_bar_region_label` filter for the region's accessible name.

= 1.1.0 =
* 6 new color presets: Midnight, Teal, Rose, Indigo, Sand, and Forest (16 total).
* Emerald, Terracotta, and Sage are slightly darker so white text on them meets WCAG AA contrast (4.5:1). Bars using these presets will look a touch deeper after updating.
* New "Border" option: Thin, Thick, Dashed, or an accent line on the edge facing the page. Always drawn in the bar's text color.
* New "Icon animation" option: Pulse, Wiggle, or Bounce. Plays three times when the bar appears, then stops, and is skipped for visitors who turn on reduced motion.
* Setting "Stay hidden for" to 0 days now keeps a dismissed bar hidden until the visitor closes their browser. Before, it came back on the very next page.
* For developers: new `hrld_frontend_bar_data` filter, `window.hldBarGates` client-side visibility gates, and a `hld:barshown` event.

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
