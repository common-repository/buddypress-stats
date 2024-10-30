=== Plugin Name ===
Contributors: plrk
Tags: buddypress, stats, statistics
Requires at least: 2.8.1
Tested up to: 2.8.1
Stable tag: 1.0

This plugin adds an admin page with some bar graphs with basic statistics from your BuddyPress installation.

== Description ==

This plugin adds an admin page with some bar graphs with basic statistics from your BuddyPress installation.

More specifically, it fetches data from the following tables:

* wp\_users
* wp\_bp\_groups
* wp\_bp\_activity\_user\_activity
* wp\_bp\_xprofile\_fields
* wp\_bp\_xprofile\_data
* wp\_bp\_friends
* wp\_bp\_groups\_members

to generate the following bar graphs:

* (all "multiselectbox", "selectbox", "checkbox" and "radio" fields have one bar graph each) _(Xprofile)_
* Most friends _(Friends)_
* Most group members _(Groups)_
* Most actions _(Activity)_
* Most popular component\_action _(Activity)_
* Recorded activities per week _(Activity)_
* Recorded activities per month _(Activity)_

Suggestions for more graphs to be created are more than welcome!

== Installation ==

1. Make sure you have BuddyPress installed and running.
1. Upload the `buddypress-stats` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. The menu item can be found under the 'BuddyPress' top level menu to the left.

== Screenshots ==

1. The graph of the most popular `component_action`s, with the blue admin panel color scheme.
2. The graph of the most popular groups, with the grey/black admin panel color scheme.

== Changelog ==

= 1.0 =
* Initial release.

== Future releases ==
The activities table structure will change in a future release of BuddyPress, according to Andy Peatling. However, the code in this plugin will be easy to adapt. Expect that a new version of this plugin will be out a few days after that change.

I'm also thinking about using the Google Maps API to create fancier graphs, such as pie charts for radio and selectbox fields or Venn diagrams for some multiselectbox and checkbox fields. You would then have to enter your own API key, though. If you think this is a good idea, say so!