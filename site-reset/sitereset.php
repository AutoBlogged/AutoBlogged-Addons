<?php

/*
Plugin Name: AutoBlogged Site Reset
Version: 1.0
Plugin URI: http://autoblogged.com
Description: Completely resets a WordPress blog by deleting all posts, pages, tags, and comments.
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


if (!class_exists('abSiteReset')) {

	/**
	* Main plugin class
	*
	*/
	class abSiteReset {

		/**
		* abSiteReset class constructor
		*
		* @return void
		* @access public
		*/
		function __construct() {
			add_action('admin_menu', array(&$this, 'add_submenu'));
		}

		/**
		 * Add a new submenu under the Tools menu
		 *
		 * @return void
		 */
		function add_submenu() {
			add_submenu_page('tools.php', 'Reset Site', 'Reset Site', 10, __FILE__, array(&$this, 'commit_sitereset'));
		}
	  
		/**
		* Empty all the tables
		*
		* @return void
		*/
		function commit_sitereset() {
			global $wpdb;
			echo '<div class="wrap">';
			if ($_POST['submit'] == 'doreset') {
				echo "<p>Removing data...</p>";
				$this->empty_tables($wpdb->posts);
				$this->empty_tables($wpdb->postmeta);
				$this->empty_tables($wpdb->comments);
				$this->empty_tables($wpdb->commentmeta);
				$this->empty_tables($wpdb->term_relationships);
				$this->empty_tables($wpdb->terms);
				$this->empty_tables($wpdb->term_taxonomy);

				echo '<p>&nbsp;</p><p>Site reset complete.</p>';
			} else {
				$num_posts = wp_count_posts('post');
				$num = number_format_i18n($num_posts->publish);
				$text = _n('Post', 'Posts', intval($num_posts->publish));
				$posts = "$num $text";
				
				$num_pages = wp_count_posts('page');
				$num = number_format_i18n($num_pages->publish);
				$text = _n('Page', 'Pages',$num_pages->publish);
				$pages = "$num $text";
				
				$num_categories = wp_count_terms('category');
				$num = number_format_i18n($num_cats);
				$text = _n('Category', 'Categories', $num_cats);
				$categories = "$num $text";

				$num_tags = wp_count_terms('post_tag');
				$num = number_format_i18n($num_tags);
				$text = _n('Tag', 'Tags', $num_tags);
				$tags = "$num $text";
				
				$num_comm = wp_count_comments();
				$num = number_format_i18n($num_comm->total_comments);
				$text = _n('Comment', 'Comments', $num_comm->total_comments);
				$comments = "$num $text";
		
				echo '<h2>AutoBlogged Site Reset</h2><p>&nbsp;</p>';
				echo '<div id="sn-warning" class="updated fade"><strong style="color:red;"><img style="vertical-align: text-bottom;" src="'.dirname(get_option('siteurl').'/'.PLUGINDIR.'/'.plugin_basename(__FILE__)).'/warn.png"/>&nbsp;Warning:</strong>';
				echo "<p>Are you sure you want to compeletely remove $posts, $pages, $comments, $categories, and $tags?</p>";
				echo '<form name="sitereset" action="" method="post">
				<p> Please confirm that you want to continue by clicking <button type="submit" name="submit" value="doreset">Yes</button</p>
				</form></div></div>';
				// Preload the check and error images
				echo '<div style="visibility:hidden"/><img src="'.dirname(get_option('siteurl').'/'.PLUGINDIR.'/'.plugin_basename(__FILE__)).'/check.png"/><img src="'.dirname(get_option('siteurl').'/'.PLUGINDIR.'/'.plugin_basename(__FILE__)).'/error.png"/></div>';
			}
			echo '</div>';
		}

		/**
		* Empty Tables
		*
		* @param  string $table The name of the table to remove
		* @return void
		*/
		function empty_tables($table) {
			global $wpdb;
			static $icon_path;
			if (!isset($icon_path)) {
				$icon_path = dirname(get_option('siteurl').'/'.PLUGINDIR.'/'.plugin_basename(__FILE__));
			}
			
			if ($table == $wpdb->terms) {			
			 	$ret = $wpdb->query("DELETE FROM $wpdb->terms WHERE term_id<>1");
			} elseif ($table == $wpdb->term_taxonomy) {
				$ret = $wpdb->query("DELETE FROM $wpdb->term_taxonomy WHERE term_id<>1");
			} else {
				$ret = $wpdb->query("TRUNCATE TABLE $table");
			}
			
			if ($ret === false) {
				$icon = 'error';
			} else {
				$icon = 'check';
			}

			echo '<img style="vertical-align: text-bottom;" src="'.$icon_path.'/'.$icon.'.png">&nbsp;&nbsp; Deleted table '.$table.'<br/>';
			$wpdb->query("OPTIMIZE TABLE $table");
			echo '<img style="vertical-align: text-bottom;" src="'.$icon_path.'/'.$icon.'.png">&nbsp;&nbsp; Optimized table '.$table.'<br/>';
		}
	}
}




// Instantiate the class
if (class_exists('abSiteReset')) {
	$abPerm = new abSiteReset();
}

?>