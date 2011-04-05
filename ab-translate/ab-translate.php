<?php

/*
Plugin Name: AutoBlogged - Translate Addon
Version: 0.9
Plugin URI: http://autoblogged.com
Description: Translates AutoBlogged content to other languages.
Author: AutoBlogged
Author URI: http://autoblogged.com
License: GPLv2

This add-on module for AutoBlogged will automatically translate each
post into the language of your choice. See the Customization section
below to set your own parameters. 

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @package   AutoBlogged
* @author    AutoBlogged <support@autoblogged.com>
* @copyright Copyright (c)2011 AutoBlogged
* @version   SVN: $Id:$
*/


/////////////////////////////////////////////////////
// Customization - Set your parameters here

// Get a Google API key here: https://code.google.com/apis/console
define('AB_GOOGLE_API_KEY', 'YOUR-KEY-HERE');

// See supported languages here: http://code.google.com/apis/language/translate/v2/using_rest.html#language-params
define('AB_FROM_LANG', 'en');
define('AB_TO_LANG', 'fr');

/////////////////////////////////////////////////////


if (!class_exists('abtranslate')) {

	/**
	* Main plugin class
	*
	*/
	class abtranslate {
		/**
		* @var instance - Static property to hold the singleton instance
		*/
		static $instance = false;

		/**
		* Class constructor
		*
		* @return void
		* @access public
		*/
		function __construct() {
			add_filter('autoblogged_post', array(&$this, 'filter_autoblogged_post'), 1, 2);
		}

		/**
		* Filter triggered for new post content
		*
		* @return
		* @access public
		*/
		public function filter_autoblogged_post($postinfo) {
			// Send the request to Google
			$url = 'https://www.googleapis.com/language/translate/v2?key='.AB_GOOGLE_API_KEY.'&format=html&source='.AB_FROM_LANG.'&target='.AB_TO_LANG.'&callback=handleResponse&q='.urlencode($postinfo['content']);
			$ret = wp_remote_get($url);
			
			// Check to make sure we got something 
			if ($ret['response']['code'] == 200 && !empty($ret['body'])) {
				// Just using a simple regex to parse out what we want
				preg_match('/translatedText"\s*:\s*"(.*)"\s*}\s*]/', $ret['body'], $matches);
				$postinfo['content'] = $matches[1];
			}
			
			return $postinfo;
		}
		
		/**
		* Instantiates this class as a singleton
		*
		* @return instance
		* @access public
		*/
		public static function get_instance() {
			if ( !self::$instance ) self::$instance = new self;
			return self::$instance;
		}
	}
}

// Instantiate the class
abtranslate::get_instance();

?>