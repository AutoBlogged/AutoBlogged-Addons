<?php

/*
Plugin Name: AutoBlogged - URL Whitelist
Version: 0.9
Plugin URI: http://autoblogged.com
Description: Only allows URLs that match a regular expression pattern
Author: AutoBlogged
Author URI: http://autoblogged.com
License: GPLv2


NOTICE: This addon is a proof-of-concept and does not contain the level
of error handling and logging that AutoBlogged has and therefore we do
not provide support for this code.

USE AT YOUR OWN RISK.


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
*
* @todo Add an option page under the main AutoBlogged page
* @todo Add support for media attachments
*/



// This example pattern will match any web site with "wp", "WordPress" or "Plugin" in the
// domain name. Change this pattern as needed.

define('WHITELIST_PATTERN', '/((?=[a-z0-9-]{1,10}\.)([^\.]*(?:wp|wordpress|plugin)[^\.]*)\.)+[a-z]{2,3}\b/i');




if (!class_exists('abwhitelist')) {

	/**
	* Main plugin class
	*
	*/
	class abwhitelist {
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
			add_filter('autoblogged_rss_feed_items', array(&$this, 'on_autoblogged_rss_feed_items'), 1, 2);
		}


		/**
		* Filter triggered for new post content
		*
		* @return
		* @access public
		*/
		public function on_autoblogged_rss_feed_items($items, $postinfo) {
			$i = 0;
			foreach($items as $item) {
				$url = $item->get_permalink();
				if (!preg_match(WHITELIST_PATTERN, $url, $matches)) {
					unset($items[$i]);
				}
				$i++;
			}
			return $items;
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
abwhitelist::get_instance();

?>