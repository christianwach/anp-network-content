=== Network Content Widgets and Shortcodes ===
Contributors: misfist, needle
Tags: multi-network, multi-site, content, widget, shortcode, sites, posts, events
Requires at least: 4.6
Tested up to: 4.8
Stable tag: 2.0.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Widgets and shortcodes to display content listings from across your WordPress multi-site or multi-network install.



== Description ==

A plugin that enables you to display posts, events and site listings from sites across a multi-site or multi-network instance. Both widgets and shortcodes are provided for each kind of listing. To display events, this plugin requires [Event Organiser](http://wordpress.org/plugins/event-organiser/) version 3.0 or greater.

<h3>Usage</h3>

Display using a shortcode.

<h4>Network Posts</h4>

```php
[embed_network_posts style="block" number_posts="20" posts_per_site="5" exclude_sites="3,4" include_categories="first-cat,second-cat" show_thumbnail="1" show_excerpt="1" excerpt_length="10" show_meta="1" show_site_name="1" /]
```

<h4>Network Events</h4>

```php
[embed_network_events style="block" event_scope="future" number_posts="20" posts_per_site="5" exclude_sites="2" include_categories="first-cat,second-cat" include_tags="tag-one,tag-two" show_thumbnail="1" show_excerpt="1" excerpt_length="10" show_meta="1" show_site_name="1" /]
```

<h4>Network Sites</h4>

```php
[embed_network_sites style="block" number_sites="20" exclude_sites="2" sort_by="registered" show_icon="library" default_image="/path/to/icon.jpg" attachment_id="10" show_meta="1" /]
```


== Installation ==

1. Extract the plugin archive
1. Upload plugin files to your `/wp-content/plugins/` directory
1. Network activate the plugin through the 'Network Admin > Plugins' menu in WordPress



== Changelog ==

<h4>2.0.2</h4>

* Fix display of Default Image preview when none previously exists
* Fix saving of Default Image data in Customizer

<h4>2.0.1</h4>

* First stable version of rewritten plugin

<h4>2.0.0</h4>

* Plugin rewrite

<h4>1.7.0 September 21, 2016</h4>

* Added template locate function
* Fixed template from local theme

<h4>1.6.12 June 28, 2016</h4>

* [bugfix #1466] Fixed path to JS file `upload-media.js`.

<h4>1.6.11 June 20, 2016</h4>

* Fixed event block display template, which was missing a closing `div` tag
* Change `post-excerpt` class to `entry-excerpt`

<h4>1.6.10 June 20, 2016</h4>

* Fixed rendering issue causing post-type list class not to display in ul tag

<h4>1.6.9 June 19, 2016</h4>

* Fixed template issue causing thumbnail not to appear when selected
* Added conditional to block view to hide excerpt if not selected
* Changed default view to list
* Improved comments
* Removed highlights template

<h4>1.6.8 June 7, 2016</h4>

* [Feature #1411]Added `event_scope` to Shortcake use `attrs`

<h4>1.6.7 June 7, 2016</h4>

* Modularized widgets into separate class files.
* [Bugfix #1134]Fixed event widget so it saves event scope properly.
* Removed "Highlights" style.

<h4>1.6.6 June 6, 2016</h4>

* Fixed mark-up issue causing event widget not to save Scope selections.

<h4>1.6.5 May 21, 2016</h4>

* Added `get_sites_select_array` function to return an array of site id => site name key value pairs for use in select

<h4>1.6.4 May 19, 2016</h4>

* Changed thumbnail size to 'medium'
* Removed $style from listing class
* Changed thumbnail markup classes to `.entry-image` and `.entry-image-link`
* Changed 'Last Recent Post' heading to 'Latest Post'
* Commented out Network Posts Highlight widget since it's no longer needed
* Modified event list template to be consistent with post lists

<h4>1.6.3</h4>

* [bugfix] Fixed PHP warning in `sort` function

<h4>1.6.2</h4>

* Added Shortcake UI shortcodes to replace quicktags

<h4>1.6.1</h4>

* Fixed PHP warnings in `inc/render.php` and `inc/shortcodes.php`
