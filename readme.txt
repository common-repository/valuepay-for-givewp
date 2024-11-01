=== ValuePay for GiveWP ===
Contributors:      valuepaymy
Tags:              valuepay, givewp, dontation, payment
Requires at least: 4.6
Tested up to:      6.0
Stable tag:        1.0.6
Requires PHP:      7.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Accept payment on GiveWP using ValuePay.

== Description ==

Allows user to made donation on GiveWP using ValuePay.

= Notes: =
- Recurring payment only creates one donation payment record in GiveWP with "Pre-Approved" status.

== Installation ==

1. Log in to your WordPress admin.
2. Search plugins "ValuePay for GiveWP" and click "Install Now".
3. Activate the plugin.
4. Navigate to "Plugins" in the sidebar, then find "ValuePay for GiveWP".
5. Click "Settings" link to access the plugin settings page.
6. Follow the instructions and update the plugin settings.

== Changelog ==

= 1.0.6 - 2022-04-17 =
- Modified: Improve instant payment notification response data sanitization

= 1.0.5 - 2022-04-16 =
- Fixed: Form always indicates the required fields error for identity information fields even if the fields has been filled out

= 1.0.4 - 2022-04-10 =
- Modified: Improve instant payment notification response data sanitization

= 1.0.3 - 2022-03-09 =
- Modified: Minor improvements

= 1.0.2 - 2022-02-22 =
- Fixed: Set identity fields as conditional required

= 1.0.1 - 2022-02-04 =
- Added: Frequency type settings for mandate
- Modified: Show bank and payment type input in donation form only if recurring payment option is enabled

= 1.0.0 - 2022-01-24 =
- Initial release of the plugin
