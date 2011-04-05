<?php

/*
Plugin Name: Disable Auto Formatting
Version: 0.1
Plugin URI: http://autoblogged.com
Description: Disables wptexturize, convert_smilies, convert_chars, and wpautop
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
 * @copyright Copyright (c)2011 AutoBlogged, ALL RIGHTS RESERVED
 * @version   SVN: $Id:$
 */

	// To leave any of these filters intact, comment out any of the following lines
	remove_filter('the_content', 'wptexturize');
	remove_filter('the_content', 'convert_smilies');
	remove_filter('the_content', 'convert_chars');
	remove_filter('the_content', 'wpautop');
	
	
	remove_filter('the_excerpt', 'wptexturize');
	remove_filter('the_excerpt', 'convert_smilies');
	remove_filter('the_excerpt', 'convert_chars');
	remove_filter('the_excerpt', 'wpautop');

?>
