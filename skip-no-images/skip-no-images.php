<?php
/*
Plugin Name: AutoBlogged Skip No-Image Posts
Plugin URI: http://www.autoblogged.com
Description: Demonstrates how to use the AutoBlogged API to skip posts that do not contain images.
Author: AutoBlogged
Version: 1.0
Author URI: http://www.autoblogged.com
License: GPLv2

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
* @package   AutoBlogged v2
* @author    AutoBlogged <support@autoblogged.com>
* @copyright Copyright (c)2011 AutoBlogged
* @version   SVN: $Id:$
*/

if (!class_exists('ab_skip_post')) {
	class ab_skip_post {

		// Constructor
		function __construct(){
			add_filter('before_autoblogged_post', array(&$this,'ab_filter_action'));
		}

		// Callback function
		function ab_filter_action($postinfo) {
			
			// Check to see if the post contains an image
			if (empty($postinfo['image'])) {

				// Set skip_post to tell AutoBlogged to skip the post
				$postinfo['skip_post'] = 'Skipping post that does not contain an image.';
				
				// Note: An alternative would be to provide a default image
				// $postinfo['image'] = 'http://path to a default image';
				// $postinfo['thumbnail'] = 'http://path to a default thumbnail';
				
     	
     	// Return our modified $postinfo
			return $postinfo;
			}
		}
	}
}

// Init the class
if (class_exists('ab_skip_post')) {
	$ab_skip_post = new ab_skip_post();
}
?>