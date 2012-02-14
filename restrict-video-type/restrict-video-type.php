<?php
/*
Plugin Name: AutoBlogged Restrict Video Type Addon
Plugin URI: http://www.autoblogged.com
Description: Skips all posts except those with the specified video type
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

if (!class_exists('ab_restrict_type')) {
	class ab_restrict_type {

		// Constructor
		function __construct(){
			add_filter('before_autoblogged_post', array(&$this,'ab_filter_action'));
		}

		// Callback function
		function ab_filter_action($postinfo) {

			// Loop through each attached video
			foreach ($postinfo['video_urls'] as $video) {
				$ext = strtolower(substr($video, -3, 3));

				// Only allow mp4 and m4v
				if ($ext == 'mp4' || $ext == 'm4v') {
					$video_urls[] = $video;
				}
			}

			if (count($video_urls[])) {
				$postinfo['video_urls'] = $video_urls;
			}

			return $postinfo;
		}
	}
}

// Init the class
if (class_exists('ab_restrict_type')) {
	$ab_skip_post = new ab_restrict_type();
}
