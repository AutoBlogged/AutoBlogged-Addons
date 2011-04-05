<?php

/*
Plugin Name: AutoBlogged Permalink Changer
Version: 0.2
Plugin URI: http://autoblogged.com
Description: Changes the permalink on AutoBlogged posts to point to the original article.
Author: AutoBlogged
Author URI: http://autoblogged.com
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


if (!class_exists('abPerm')) {
  
	/**
   * Main plugin class
   *
   */
  class abPerm {
    
		/**
     * Class constructor
     *
     * @return void
     * @access public
     */
    function __construct() {
      add_filter('post_link', array(&$this, 'ab_permalink_intercept'), 1, 3);
    }
    
		/**
     * Called by the hooked post_link filter
     *
     * @param string $permalink   The permalink to the local blog's post
     * @param string $post				The Post object
     * @param boolean $leavename	Not used
     * @return string							Returns the new permalink of the original article
     */
    function ab_permalink_intercept($permalink, $post = null, $leavename = false) {
    	
    	if (!empty($post->ID)) {
    		$new_permalink = get_post_meta($post->ID, 'link', true);
    	  if (!empty($new_permalink)) {
    			return $new_permalink;
    		}
			}
			
    	// Always fall back to returning the blog's permalink
    	return $permalink;
    }
    
  }
}

// Instantiate the class
if (class_exists('abPerm')) {
  $abPerm = new abPerm();
}
?>