=== Don't Break The Code ===
Contributors: strangerstudios
Tags: disable, visual editor, html, tab
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: .1

Allows admins to disable the Visual editor tab on a per post basis.

== Description ==
The idea for this plugin came from a presentation at WordCamp Philly 
2011.

== Installation ==

1. Upload the `dont-break-the-code` directory to the `/wp-content/plugins/` 
directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Look for the visual editor checkbox in the Screen Options of the edit 
post page.

== Frequently Asked Questions ==

== Changelog ==
= .1 =

Pulled code from "Disable the Visual Editor WYSIWYG" (http://wordpress.org/extend/plugins/disable-visual-editor-wysiwyg/) and rewrote a few parts pased on my preferences for doing jQuery and which hook to use to determine we're on the edit post page.

Plans are to:

1. Move the disable checkbox into the screen options space.

2. Show the checkbox to admins -- current_user_can("manage_options") -- only.

3. Possibly add remove some more filters that WP does to the_content based on this plugin: http://wordpress.org/extend/plugins/preserved-html-editor-markup/

4. Want to also update the disable checkbox to switch to the HTML tab when clicked and disable the Visual Editor without require a save/update.

May include the Syntax Highlighting started here: http://wordpress.org/extend/plugins/preserved-html-editor-markup/ But also think this makes sense as a stand alone plugin. Running these two together nicely should work.

Tweet me other ideas.

* Initial Version.
