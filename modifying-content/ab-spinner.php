<?php

/*
Plugin Name: AutoBlogged - External spinner integration template
Version: 0.9
Plugin URI: http://autoblogged.com
Description: Runs articles through an external spinner API
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
*/



/*

 Instructions for customizing this plugin for your product:

1. In this file search for "abSpinnerIntegration" and replace it with a custom name for your plugin.
2. Add your custom code in the on_autoblogged_content function.

*/


if (!class_exists('abSpinnerIntegration')) {

	/**
	* Main plugin class
	*
	*/
	class abSpinnerIntegration {
		/**
		* @var instance - Static property to hold the singleton instance
		*/
		static $instance = false;
		
		
		/**
		* Filter triggered for new post content
		*
		* $content contains the post content that you can modify
		* 
		* @return
		* @access public
		*/
		public function on_autoblogged_content($content) {
			
			
			
			/* Include here your code to modify the $content.
			...
			...
			...
			
			*/
			
			



			// Content must not be empty. If there was an error, return the original value of content
			return $content;
		}
		


   // Nothing below needs to be modified
   
		/**
		* Class constructor adds the AutoBlogged callback function 
		*
		* @return void
		* @access public
		*/
		function __construct() {
			add_filter('autoblogged_content', array(&$this, 'on_autoblogged_content'), 1, 2);
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
abSpinnerIntegration::get_instance();

?>