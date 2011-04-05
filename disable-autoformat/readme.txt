=== AutoBlogged Disable Auto Formatting ===
Tags: AutoBlogged, Formatting, wptexturize, convert_smilies, convert_chars, wpautop
Requires at least: 2.8
Tested up to: 3.1

Disables the wptexturize, convert_smilies, convert_chars, and wpautop auto formatting features in WordPress

== Description ==

AutoBlogged automatically creates new blog posts for each item in an RSS feed. When creating new posts, WordPress will run a series of filters to clean up the HTML content. These filters can sometimes have unwanted effects when importing posts from RSS feeds so this plugin allows you to disable these features. By default, this plugin disables wptexturize, convert_smilies, convert_chars, and wpautop for the_content and the_excerpt. If you would like any of these left intact, edit disable_autoformat.php and comment out or remove the lines you do not want. 

[Technical Support](http://support.autoblogged.com)

== Installation ==

Upload and activate the plugin through by using the Add New button on the WordPress Plugins admin page. To manually install the plugin, create a new directory called disable-autoformat in the /wp-content/plugins directory of your WordPress installation. Extract the zip file and upload the contents to the /wp-content/pluginsdisable-autoformatr directory and then activate the Plugin from Plugins page.

== Changelog == 

= v0.1 - 14 Feb 2011 =
* Initial public release