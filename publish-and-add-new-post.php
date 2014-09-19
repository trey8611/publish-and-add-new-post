<?php
/*
Plugin Name: Publish And Add New Post
Plugin URI: http://ix3.us/publish-and-add-new-post
Description: Adds 3 radio boxes to the submit box for extra functionality. Normal Behavior, Auto Add New (Let's you automatically go to Add New after Publish/Update of a post), or Auto Visit (Redirects you to the front end URL of the post you just published/updated).
Version: 1.1
Author: Trey
Author URI: http://ix3.us
License: GPL2
*/
/*
 Copyright 2014  ix3.us  (email : trey8611@gmail.com)
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as 
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( !class_exists( 'TPSandAN' ) ) {
	class TPSandAN {
		
		public function __construct() {
			// Hook to add the 3 radio buttons to the submit meta box.
			add_action( 'post_submitbox_misc_actions', array( $this, 'and_add_new' ) );
			
			// Filter to redirect after they hit the Publish/Update button.
			add_filter( 'redirect_post_location', array( $this, 'and_add_new_redirect' ), 10, 2 );
		}
		
		public function and_add_new() {
			// If they had Auto Add New selected on the last post they submitted, auto check it.
			if ( $_REQUEST['aan'] == 'addnew' ) {
				$achecked = 'checked';
				$nchecked = NULL;
			} else {
				// They didn't Auto Add New last time, so make Normal Behavior checked.
				$achecked = NULL;
				$nchecked = 'checked';
			}
			echo '<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">';
			wp_nonce_field( plugin_basename(__FILE__), 'and_add_new_nonce' );
			echo '<input type="radio" name="and_add_new" id="and_add_new-nor" value="normal" '.$nchecked.'> <label for="and_add_new-nor" >Normal Behavior</label><br />';
			echo '<input type="radio" name="and_add_new" id="and_add_new-aan" value="addnew" '.$achecked.'> <label for="and_add_new-aan" >Auto Add New</label><br />';
			echo '<input type="radio" name="and_add_new" id="and_add_new-aav" value="visit"> <label for="and_add_new-aav">Auto View</label>';
			echo '</div>';
		}
		
		public function and_add_new_redirect( $location, $post_id ) {
			if ( wp_verify_nonce( $_POST['and_add_new_nonce'], plugin_basename(__FILE__) ) ) {
				if ( $_POST['and_add_new'] != NULL ) {
					global $post;
					$whattodo = $_POST['and_add_new'];
					switch ( $whattodo ) {
						case 'normal':
							return esc_url_raw( $location );
							break;
						case 'addnew':
							return esc_url_raw( admin_url( 'post-new.php?post_type='.$post->post_type.'&aan=addnew' ) );
							break;
						case 'visit':
							return esc_url_raw( get_permalink( $post->ID ) );
							break;
					}
				} else {
					return $location;
				}
			}
		}
	}
}


$TPSandAN = new TPSandAN();
?>
