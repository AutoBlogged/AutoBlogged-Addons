<?php

/*
Plugin Name: AutoBlogged - Tagging Services
Version: 0.9
Plugin URI: http://autoblogged.com
Description: Allows using external tagging services
Author: AutoBlogged
Author URI: http://autoblogged.com
License: GPLv2


This plugin uses external tagging services to enhance the tagging
features of AutoBlogged

NOTICE: This addon is an experiment proof-of-concept and does not
contain the level of error handling and logging that AutoBlogged has
and therefore we do not provide support for this code.

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

/*********************************************************
* Set your API keys here
*
* NOTE: Each one of these could slow down AutoBlogged
*       significantly, we recommend only use one or two.
**********************************************************/

//define('APIKEY_REPUSTATE', 'YOUR KEY HERE');
define('APIKEY_ALCHEMYAPI', 'YOUR KEY HERE');
//define('APIKEY_DIFFBOT', 'YOUR KEY HERE');

// These don't require an API key but you can disable them here
define('ENABLE_TAGTHENET', true);
define('ENABLE_EVRI', false);



if (!class_exists('abexcerpt')) {

	/**
	* Main plugin class
	*
	*/
	class abexcerpt {
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
			add_filter('autoblogged_tags', array(&$this, 'on_autoblogged_tags'), 1, 2);
		}


		/**
		* Filter triggered for new post content
		*
		* @return
		* @access public
		*/
		public function on_autoblogged_tags($tags_list, $postinfo) {

			////
			/// Repustate (http://www.repustate.com/docs/)
			//
			if (defined('APIKEY_REPUSTATE')) {
				$ret = wp_remote_get('http://api.repustate.com/v1/'.APIKEY_REPUSTATE.'/entities.xml?url='.urlencode($postinfo['link']));

				if (!is_wp_error($ret)) {
					$returnXML = simplexml_load_string($ret['body']);
					$parsed_keywords = $returnXML->xpath('//result');
					if (count($parsed_keywords)) {
						foreach($parsed_keywords as $term) {
							$repustateKeywords[] = (string)$term[0];
						}
					}
				}
				//__d($repustateKeywords, 'repustate');
			}

			////
			/// Using AlchemyAPI URLGetRankedKeywords (http://www.alchemyapi.com/api/)
			//
			if (defined('APIKEY_ALCHEMYAPI')) {
				$ret = wp_remote_get('http://pipes.yahoo.com/pipes/pipe.run?_id=a190232fdfed244c480441dcb7f96ffa&_render=php&apikey='.APIKEY_ALCHEMYAPI.'&url='.urlencode($postinfo['link']));
				if (!is_wp_error($ret)) {
					$returnPHP = unserialize($ret['body']);
					foreach($returnPHP['value']['items'] as $term) {
						$alchemyAPIKeywords1[] = $term['title'];
					}
					//__d($alchemyAPIKeywords1, 'alchemy');
				}


				// AlchemyAPI again with URLGetRankedNamedEntities
				$ret = wp_remote_get('http://pipes.yahoo.com/pipes/pipe.run?_id=14becacefc2a64e187d2acb3e0f6c03f&_render=php&apikey='.APIKEY_ALCHEMYAPI.'&url='.urlencode($postinfo['link']));
				if (!is_wp_error($ret)) {
					$returnPHP = unserialize($ret['body']);
					foreach($returnPHP['value']['items'] as $term) {
						$alchemyAPIKeywords2[] = $term['title'];
					}
				}
				//__d($alchemyAPIKeywords2, 'alchemy');
			}

			////
			/// Diffbot (http://www.diffbot.com/docs/api/article)
			//
			if (defined('APIKEY_DIFFBOT')) {
				$ret = wp_remote_get('http://www.diffbot.com/api/article?html&tags=1&token='.APIKEY_DIFFBOT.'&url='.urlencode($postinfo['link']));
				if (!is_wp_error($ret)) {
					preg_match('/"tags"\s*:\s*\[([^\]]*)\]/i', $ret['body'], $matches);
					$diffbotKeywords = str_getcsv($matches[1]);
					$diffbotKeywords = $diffbotKeywords[0];
				}
				//__d($diffbotKeywords, 'Diffbot');
			}

			////
			/// tagthe.net  (http://tagthe.net/fordevelopers)
			//
			if (ENABLE_TAGTHENET) {

				$ret = wp_remote_get('http://tagthe.net/api/?url='.urlencode($postinfo['link']));
				if (!is_wp_error($ret)) {
					$returnXML = simplexml_load_string($ret['body']);
					$parsed_keywords = $returnXML->xpath('//dim[@type="topic"]/item');
					if (count($parsed_keywords)) {
						foreach($parsed_keywords as $term) {
							if (!is_array($term[0])) {
								$tagthenetKeywords[] = (string)$term[0];
							}
						}
					}
				}
				//__d($tagthenetKeywords, 'tagthenet');

			}

			////
			/// Evri  (http://www.evri.com/developer/rest/index.html)
			//
			if (ENABLE_EVRI) {
				$ret = wp_remote_get('http://api.evri.com/v1/media/entities?uri='.urlencode($postinfo['link']));
				if (!is_wp_error($ret)) {
					$returnXML = simplexml_load_string($ret['body']);
					$parsed_keywords = $returnXML->xpath('//canonicalName');
					if (count($parsed_keywords)) {
						foreach($parsed_keywords as $term) {
							$evriKeywords[] = (string)$term[0];
						}
					}
				}
				//__d($evriKeywords, 'evri');
			}


			$keywords = array_merge((array)$repustateKeywords, (array)$alchemyAPIKeywords1, (array)$alchemyAPIKeywords2, (array)$diffbotKeywords, (array)$tagthenetKeywords, (array)$evriKeywords);
			// Remove this next line to replace the existing tags list rather than merging
			//$keywords = array_merge((array)$tags_list, (array)$keywords);
			return $keywords;
		}  // end function

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

	} // end class
} // end if



if (!function_exists('str_getcsv')) {

	function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null) {
		$temp=fopen("php://memory", "rw");
		fwrite($temp, $input);
		fseek($temp, 0);
		$r = array();
		while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) {
			$r[] = $data;
		}
		fclose($temp);
		return $r;
	}
}

// Instantiate the class
abexcerpt::get_instance();
