<?php

/*
Plugin Name: AutoBlogged - Full content articles
Version: 0.9
Plugin URI: http://autoblogged.com
Description: Fetches the full articles from an RSS feed.
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


if (!class_exists('abfulltext')) {

	/**
	* Main plugin class
	*
	*/
	class abfulltext {
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
			add_filter('autoblogged_content', array(&$this, 'on_autoblogged_content'), 1, 2);
		}


		/**
		* Filter triggered for new post content
		*
		* @return
		* @access public
		*/
		public function on_autoblogged_content($content, &$postinfo) {
			require_once('inc/Safe.php');

			// Randomly switch between these two services to spread the load
			if (rand(0,1)) {
				//Using boilerpipe (http://boilerpipe-web.appspot.com/)
				$ret = wp_remote_get('http://boilerpipe-web.appspot.com/extract?extractor=LargestContentExtractor&output=htmlFragment&url='.$postinfo['link']);
				if (!is_wp_error($ret))	$article = $ret['body'];
			} else {

				// Using viewtext.org (http://viewtext.org/)
				$ret = wp_remote_get('http://viewtext.org/api/text?rl=false&format=xml&url='.$postinfo['link']);
				if (!is_wp_error($ret)) {
					$returnXML = simplexml_load_string($ret['body']);
					$article = $returnXML->Content;
				}
			}

			// Only allow certain HTML tags
			$safe = new HTML_Safe;
			$allowedTags = array_merge($safe->listTags, $safe->tableTags, array('p', 'br', 'i', 'em', 'strong', 'b', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'code', 'font', 'a', 'img', 'blockquote'));
			$safe->setAllowTags($allowedTags);
			$content = $safe->parse($article);
			$content = preg_replace('/[\r\n]+/', ' ', $content);
			$content = preg_replace('/<\/?div([^>]*)>/i', '', $content);
			$content = preg_replace('/(class|style)\s*=\s*\"[^\"]*\"/i', '', $content);

			// More cleanup
			$content = str_replace('[...]', '', $content);
			$content = preg_replace('/\s+/', ' ', $content);
			$config = array(
			'clean' => true,
			'output-xhtml' => true,
			'show-body-only' => true,
			'preserve-entities' => true,
			'char-encoding' => get_option('blog_charset')
			);

			$tidy = tidy_parse_string($content, $config);
			$tidy->cleanRepair();
			$content = mb_convert_encoding($tidy, get_option('blog_charset'));


			// Add a <!-- more --> tag after the first paragraph
			$content = preg_replace('/<\/p>/i', '', $content, 1);


			return $content;
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
abfulltext::get_instance();

?>