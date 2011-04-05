<?php
/*
Plugin Name: AutoBlogged Theme Support Example
Plugin URI: http://www.autoblogged.com
Description: Demonstrates how theme developers can build support for AutoBlogged into their themes.
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

if (!class_exists('ab_theme_support')) {
	class ab_theme_support {

		// Constructor
		function __construct(){
			add_filter('before_autoblogged_post', array(&$this,'ab_filter_action'));
		}

		// Callback function
		function ab_filter_action($postinfo) {
			
			// Get the current theme info
			$themedata = get_theme(get_current_theme());

			// Check to make sure it is the correct theme, for this example we are using WooTube from WooThemes
     	if ($themedata['Name'] == 'WooTube') {
     		
     		// Create a new custom field named 'embed' for our custom embedded video string
     		$postinfo['embed'] = '<object type="application/x-shockwave-flash" data="'.$postinfo['video_url'].'" width="300" height="210">';
     		$postinfo['embed'] .= '<param name="movie" value="'.$postinfo['video_url'].'" /><a href="'.$postinfo['link'].'">'.$postinfo['link'].'</a>';
     		$postinfo['embed'] .= '</object>';
     		
     		// Add the image URL
     		$postinfo['image'] = $postinfo['image_url'];

     		
     		// Empty unused custom fields
     		$postinfo['Video'] = '';
     		$postinfo['video'] = '';
     		$postinfo['video_url'] = '';
     		$postinfo['Image'] = '';
     		$postinfo['image_path'] = '';
     		$postinfo['thumbnail'] = '';
     		$postinfo['Thumbnail'] = '';
     		$postinfo['thumbnail_path'] = '';
     	}
     	
     	// Return our modified $postinfo
			return $postinfo;
		}
	}
}

// Init the class
if (class_exists('ab_theme_support')) {
	$ab_theme_support = new ab_theme_support();
}
?>