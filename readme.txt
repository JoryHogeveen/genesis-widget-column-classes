=== Genesis Widget Column Classes ===
Contributors: keraweb
Donate link: https://www.keraweb.nl/donate.php?for=genesis-widget-column-classes
Tags: genesis, bootstrap, column, grid, widget, sidebar, dynamik
Requires at least: 3.1
Tested up to: 5.0
Requires PHP: 5.2.4
Stable tag: 1.3

Adds Genesis column classes to widgets.

== Description ==

As easy as it gets. Add column classes to widgets with a select box, check whether the widget is the first, and save!

I've built this plugin for the Genesis Framework, though it will work with any theme that uses the (old) Bootstrap column classes.

= Filter: `genesis_widget_column_classes` =
Allows you to change the available column classes

**Parameters:** `array` Default column classes.  
**Return:** `array` Array of column classes.  

= Filter: `genesis_widget_column_classes_capability` =
Change the capability required to modify column classes.  
Since  1.2.2  

**Default:** `edit_theme_options`  
**Parameters:** `string` The default capability.  
**Return:** `string` The new capability.  

= Filter `genesis_widget_column_classes_select_multiple` =
Allow multiple classes to be selected.  
Since  1.3  
**Return:** boolean.  

You can use these filters inside your theme functions.php file or in a plugin.

== Installation ==

Installation of this plugin works like any other plugin out there. Either:

1. Upload and unpack the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Or search for "Genesis Widget Column Classes" via your plugins menu.

== Screenshots ==

1. Widget admin view
2. Frontend example ( `one-third + first` | `one-third` | `one-third` )

== Changelog ==

= 1.3 =

*	**Feature:** New filter: `genesis_widget_column_classes_select_multiple` to allow multiple class selections. [#8](https://github.com/JoryHogeveen/genesis-widget-column-classes/issues/8) 
*	**Compatibility:** [Dark Mode](https://nl.wordpress.org/plugins/dark-mode/) ([Github](https://github.com/danieltj27/Dark-Mode))

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/genesis-widget-column-classes/pull/10)

= 1.2.4.1 =

*	**Fix:** PHP notice. [#9](https://github.com/JoryHogeveen/genesis-widget-column-classes/issues/9)

= 1.2.4 =

*	**Enhancement:** UI improvement.
*	**Enhancement:** Add support links on plugins overview page.
*	**Maintenance:** Updated to CodeClimate v2.
*	**Updated/Added:** Screenshot.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/genesis-widget-column-classes/pull/7)

= 1.2.3 =

*	**Enhancement:** Better attribute replacement.
*	**Enhancement:** Add wrapper div if the widget parameters are incorrect.
*	**Enhancement:** Do not load textdomain if the user does not have access.
*	**Compatibility:** Tested with WordPress 4.9.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/genesis-widget-column-classes/pull/6)

= 1.2.2 =

*	**Feature:** new filter `genesis_widget_column_classes_capability`. Change the capability required to modify column classes.
*	**Enhancement:** Helper method to get the available column classes.
*	**Enhancement:** Fix CodeClimate coding standards issues.

Detailed info: [PR on GitHub](https://github.com/JoryHogeveen/genesis-widget-column-classes/pull/5)

= 1.2.1 =

*	**Enhancement:** Fixed code inspections from CodeClimate.
*	**Compatibility:** Tested with WordPress 4.8.

= 1.2 =

*	**Compatibility:** Compatibility with plugins that use the `widget_display_callback` hook.
*	**Enhancement:** Remove duplicate classes if found.
*	**Enhancement:** Update textdomain hook.

= 1.1.4 =

*	**Enhancement:** Usage of the WP_Widget object for generating input names and ID's.
*	**Feature:** Add filter `genesis_widget_column_classes` to add/modify available column classes.
*	**Compatibility:** Tested with WordPress 4.6.

= 1.1.3 =

*	**Enhancement:** Usage of a single instance of the class.
*	**Compatibility:** Add support for translate.wordpress.org.
*	**Enhancement:** Minor code standard fixes.

= 1.1.2 =

*	**Enhancement:** Allow "first" class when no width is selected.

= 1.1.1 =

*	**Fix:** constructor for PHP7.

= 1.1 =

*	**Enhancement:** Make plugin object oriented (OOP).
*	**Enhancement:** Make "no genesis theme" nag dismissible.
*	**Enhancement:** Code, format and security improvements.

= 1.0.1 =

*	**Enhancement:** Some small improvements.

= 1.0 =

*	Created from nothingness just to be one of the cool kids. Yay!

== Other Notes ==

You can find me here:

*	[Keraweb](http://www.keraweb.nl/ "Keraweb")
*	[GitHub](https://github.com/JoryHogeveen/genesis-widget-column-classes/)
*	[LinkedIn](https://nl.linkedin.com/in/joryhogeveen "LinkedIn profile")
