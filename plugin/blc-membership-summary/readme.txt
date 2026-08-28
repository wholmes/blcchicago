=== BLC Membership Summary ===
Contributors: blcchicago
Tags: membership, paid memberships pro, reports, email
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Wild Apricot-style membership summary by tier with scheduled email delivery for admins.

== Description ==

Recreates the Wild Apricot Members → Summary table for BLC Chicago:

* Counts per PMPro level: total, active, renewal overdue, lapsed, pending, new in 7/30 days
* Bundle counts when `blc_bundle_id` or bundle coordinator meta is present
* Admin screen under Memberships → Summary
* Subscribe unlimited admin emails for daily, weekly, or monthly HTML reports (7:00 AM site time)

Requires Paid Memberships Pro for live data.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or symlink from this repo's `plugin/blc-membership-summary/`.
2. Activate through the Plugins screen.
3. Go to Memberships → Summary (or BLC Members when PMPro is inactive).
4. Add subscriber emails and choose a schedule.

== Frequently Asked Questions ==

= How is renewal overdue defined? =

PMPro members with `active` status but a past `enddate` are counted as renewal overdue. Expired/cancelled rows count as lapsed.

= Does it replace Wild Apricot email blasts? =

It replaces the summary snapshot only. Member-facing email still uses your ESP or PMPro emails.

== Changelog ==

= 1.0.0 =
* Initial release.
