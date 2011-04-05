<?php
/*
Plugin Name: AutoBlogged Post Variables Dump
Plugin URI: http://www.autoblogged.com
Description: Shows the contents of all the AutoBlogged post variables when processing feeds manually.
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

if (!class_exists('ab_vardump')) {
	class ab_vardump {

		// Constructor
		function __construct(){
			add_action('autoblogged_post', array(&$this,'ab_addon_action'));
			include_once 'Var_Dump.php';
		}

		// Callback function
		function ab_addon_action($postinfo) {
			if (current_user_can('publish_posts')) {
				Var_Dump::displayInit(array('display_mode' => 'HTML4_Text'));
				Var_Dump::display($postinfo);
			}
			return;
		}
	}
}

// Init the class
if (class_exists('ab_vardump')) {
	$ab_addon_var = new ab_vardump();
}
?>