=== Don't Break The Code ===
Contributors: strangerstudios
Tags: disable, visual editor, html, tab
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: .2.1

Allows admins to disable the Visual editor tab on a per post basis.

== Description ==
The idea for this plugin came from a presentation at WordCamp Philly 
2011.

Code from these other plugins has served as reference and inspiration, and was sometimes borrowed from:

http://wordpress.org/extend/plugins/disable-visual-editor-wysiwyg/
http://wordpress.org/extend/plugins/raw-html/

Plans:

* Hide the disable option from non-admins.
* Possibly remove certain filters from the the_content filter, similar to the Raw HTML or Preserved HTML Editor Markup plugins.

== Installation ==

1. Upload the `dont-break-the-code` directory to the `/wp-content/plugins/` 
directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Look for the visual editor checkbox in the Screen Options of the edit 
post page.

== Frequently Asked Questions ==

== Changelog ==

= .2.1 =
* The checkbox wasn't showing up on the Edit Page page. Now it is. May need to figure a way to make this work with custom post types.

= .2 =
* Moved the checkbox into the screen options space.
* Updated the checkbox to switch disable/enable and switch tabs without requiring a save or update.

= .1 =

* Initial Version. Pulled code from "Disable the Visual Editor WYSIWYG" (http://wordpress.org/extend/plugins/disable-visual-editor-wysiwyg/) and rewrote a few parts pased on my preferences for doing jQuery and which hook to use to determine we're on the edit post page.

