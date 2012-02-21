<?php

/*
Plugin Name: AutoBlogged - Contentboss spinner integration 
Version: 0.9
Plugin URI: http://autoblogged.com
Description: Runs articles through the ContentBoss spinner API
Author: ContentBoss
Author URI: http://www.contentboss.com
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

///////////////////////////////////////////////////////////////////////////////////////////////////////
// START OF CONTENTBOSS SETTINGS
// your contentboss ID number. This is a number, not your email address.
define('CBOSS_USERID', '');	

// your contentboss password.
define('CBOSS_PWD', '');

// ADVANCED SETTINGS - You can leave these on the defaults if you like.

// specify reserved words as a comma separated list eg 
// cat,CAT,Cat,moggie,Moggie
// notice that cat is not the same as CAT which is not the same as Cat
define('CBOSS_RESERVEDWORDS', '');

// Paraphrasing means reducing the length of the post by randomly dropping sentences. 
// Normally, spinning alone is sufficient to make a post unique in the eyes of the search engines, 
// but if your niche is highly competitive, paraphrasing will help make your posts even more different 
// from the originals. Valid values: 0, 1, 2, 3
// 0 = none
// 1 = mild
// 2 = medium
// 3 = heavy paraphrasing
define('CBOSS_PARAPHRASING', '0');

// when we are allowed to spin (is it a manual post, or automatic?) valid values, 0, 1, 2
// 0 = all posts
// 1 = manual posts only
// 2 = autoposts only
define('CBOSS_ONLYSPIN', '0');

// minimum length of text we want to spin - every call uses a cboss credit, so you probably 
// don't want to spin tiny snippets.
// length of text is in characters (bytes, not words), and can be any value between 0 and 2500.
define('CBOSS_MINLENGTH', '250');

// the spinner will only process up to 5000 chars - about 700 words on average. If your post is longer
// you can instruct contentboss to trim it before spinning so it will 'fit'.
// If you set this to anything except 'Y', posts longer than 5000 chars will not be spun.
define('CBOSS_TRIMIFTOOLONG', 'Y');

// version - don't change this unless instructed to.
define('CBOSS_VERSION', '1.05');

// END OF CONTENTBOSS SETTINGS
///////////////////////////////////////////////////////////////////////////////////////////////////////

if (!class_exists('cBossSpinnerIntegration')) {

	/**
	* Main plugin class
	*
	*/
	class cBossSpinnerIntegration {
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

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $content;
			if( trim(CBOSS_USERID) == '' || trim(CBOSS_PWD) == '' ){
				// todo - error message, userid pwd missing
				return $content;
			}
			 
			// can we spin this post?
			if(empty( $_POST ) && CBOSS_ONLYSPIN == 1) return $content;		// autopost
			if(!empty( $_POST ) && CBOSS_ONLYSPIN == 2) return $content;	// manual post
		
			// is it below the min text length? no point spinning 100 char rss snippets, for example
			if(strlen($content) < CBOSS_MINLENGTH){
				// todo - set an error message

				return $content;		// text too short.
			}
	
			if(strlen($content) > 5000){
		 		if(CBOSS_TRIMIFTOOLONG == 'Y'){
					$content = $this->cboss01_shave($content);
				} else {
				// todo - error message - cant spin this length of text
					return $content;
				}
			}
	
			$response = wp_remote_post("http://www.contentboss.com/members/ajs3.php", array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array( 'userid' => CBOSS_USERID, 'pwd' => CBOSS_PWD , 'text' =>$content, 'reservedwords' => CBOSS_RESERVEDWORDS, 'paraphrasing' => CBOSS_PARAPHRASING, 'ver' => CBOSS_VERSION),
			'cookies' => array()
				)
			);
				
			if( is_wp_error( $response ) ) {
				// todo - error message - extract from wp error
				return $content;	
			} else {
				// got something
				$ret = $response['body'];
				// any cboss-specific errors, such as login fail?
				if(trim($this->cboss01_extractstring($ret,"[*NG]","[*NGEND]")) != ''){
					// todo - error message - report cboss error
					return $content;	
				}
			}
	
			if(strpos($ret, "[*OK]") !== false){		
				// got something back. extract it, split into title and content
				$spuntext = $this->cboss01_extractstring($ret,"[*CBAJSSTART]","[*CBAJSEND]");
				// wordpress will have 'helpfully' converted all sorts of stuff into para tags
				// as we don't want 'hanging periods' in their own paragraphs, lose em. Thanks, WP.
				$spuntext = str_replace("</p>.", " ", $spuntext);
				$spuntext = str_replace("<p>.", " ", $spuntext);
				$spuntext = str_replace("</p> .", " ", $spuntext);
				$spuntext = str_replace("<p> .", " ", $spuntext);
				
				return $spuntext;
			} else {
				// no [*OK] and no error, but something went wrong...
				// todo - error message
				return $content;
			}


			// Content must not be empty. If there was an error, return the original value of content
			return $content;
		}
		
		/**
		* string manipulation routine
		*
		* @return substring, or false
		* @access public
		*/
		public function cboss01_extractstring($s,$s1,$s2) {
			$start = strpos($s, $s1);
			$end = strpos($s, $s2, $start);
			if($start !== false && $end !== false )
			{
				return substr($s, $start + strlen($s1), $end - ($start + strlen($s1)));
			}
			return false;
		}
		/**
		* tail a piece of text to bring it below the 5,000 char limit. look for a good break point.
		*
		* @return substring
		* @access public
		*/
		public function cboss01_shave($s) {
			if(strlen($s) < 5000) return $s;
			$s = substr($s,0,4999);	// 5k is the absolute limit at this time - about 700 words, a LONG article
			for($i=strlen($s)-1;$i > 4500;$i--){	// find a nice break point
				if(substr($s,$i,2) == ". " || substr($s,$i,2) == "! " || substr($s,$i,2) == "? "){
					return substr($s, 0, $i+1);
				}
			}
			return $s;
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
cBossSpinnerIntegration::get_instance();

?>