# Genesis Widget Column Classes #
Adds Genesis (old bootstrap) column classes to widgets.

[![WordPress Plugin version](https://img.shields.io/wordpress/plugin/v/genesis-widget-column-classes.svg?style=flat)](https://wordpress.org/plugins/genesis-widget-column-classes/)
[![WordPress Plugin WP tested version](https://img.shields.io/wordpress/v/genesis-widget-column-classes.svg?style=flat)](https://wordpress.org/plugins/genesis-widget-column-classes/)
[![WordPress Plugin downloads](https://img.shields.io/wordpress/plugin/dt/genesis-widget-column-classes.svg?style=flat)](https://wordpress.org/plugins/genesis-widget-column-classes/)
[![WordPress Plugin rating](https://img.shields.io/wordpress/plugin/r/genesis-widget-column-classes.svg?style=flat)](https://wordpress.org/plugins/genesis-widget-column-classes/)
[![Travis](https://secure.travis-ci.org/JoryHogeveen/genesis-widget-column-classes.png?branch=master)](http://travis-ci.org/JoryHogeveen/genesis-widget-column-classes)
[![Code Climate](https://codeclimate.com/github/JoryHogeveen/genesis-widget-column-classes/badges/gpa.svg)](https://codeclimate.com/github/JoryHogeveen/genesis-widget-column-classes)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://github.com/JoryHogeveen/genesis-widget-column-classes/blob/master/license.txt)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.keraweb.nl/donate.php?for=genesis-widget-column-classes)

![Genesis Widget Column Classes](https://raw.githubusercontent.com/JoryHogeveen/genesis-widget-column-classes/master/.github/assets/banner-1544x500.jpg)  

## Description

As easy as it gets. Add column classes to widgets with a select box, check wether the widget is the first, and save!

I've built this plugin for the Genesis Framework, though it will work with any theme that uses the (old) Bootstrap column classes.

### Filters

#### `genesis_widget_column_classes`
Allows you to change the available column classes

**Parameters:** `array` Default column classes.  
**Return:** `array` Array of column classes.  

#### `genesis_widget_column_classes_capability`
Change the capability required to modify column classes.  
Since  1.2.2  

**Default:** `edit_theme_options`  
**Parameters:** `string` The default capability.  
**Return:** `string` The new capability.  

You can use these filters inside your theme functions.php file or in a plugin.

## Installation

Installation of this plugin works like any other plugin out there. Either:

1. Upload the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Or search for "Genesis Widget Column Classes" via your plugins menu.
