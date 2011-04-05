=== AutoBlogged Translation Addon ===
Requires at least: 2.8
Tested up to: 3.1

Automatically translates posts you create with AutoBlogged. 

== Description ==

This plugin will automatically translate into any language each post that AutoBlogged creates. To use this plugin, you must first acquire a Google Translate API key at https://code.google.com/apis/console. Once you have your key, open ab-translate.php and find this line and enter your key:

define('AB_GOOGLE_API_KEY', 'YOUR-KEY-HERE');

You can also set the languages you want to translate from and to by changing these values:

define('AB_FROM_LANG', 'en');
define('AB_TO_LANG', 'fr');

To see a full list of languages that Google Translate supports, you can visit this URL:
http://code.google.com/apis/language/translate/v2/using_rest.html#language-params


[Technical Support](http://support.autoblogged.com)

== Installation ==

Upload and activate the plugin through by using the Add New button on the WordPress Plugins admin page. To manually install the plugin, create a new directory called xtra-dupecheck in the /wp-content/plugins directory of your WordPress installation. Extract the zip file and upload the contents to the /wp-content/plugins/xtra-dupecheck directory and then activate it from the Plugins admin page.

== Changelog == 

= v0.9 - 30 Mar 2011 =
* Initial public release