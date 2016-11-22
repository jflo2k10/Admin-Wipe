<?php
/**
* Plugin Name: Admin Wipe
* Plugin URI: http://www.jimfloss.com
* Description: Wipe the Admin Panel
* Version: 1.0 
* Author: Jim
* Author URI: http://www.jimfloss.com
* License: GPL2
*/

defined('ABSPATH') or die();

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

function create_page() {
  $home = get_page_by_path( 'home', OBJECT, 'post_type' );
  
  $args = array (
  	'post_type'       => array('post', 'page'),
  	'post_status'     => 'any',
  	'posts_per_page'  => -1,
  );
  
  $query = new WP_Query( $args );
  
  update_option('blogdescription', '');
  update_option('default_comment_status', 'closed');
  update_option('start_of_week', 0);
  update_option('timezone_string', 'America/New_York');
  update_option('thumbnail_crop', 0);
  update_option('permalink_structure ', '/%postname%/');
  
  foreach($query as $q) :
    if( is_object($q) ) {
    	if($q->post_title != 'Home') {
      	wp_delete_post( $q->ID, true );
    	}
  	}
  endforeach;

  if ( !$home ) {
    $user_id = get_current_user_id();
  
    $page = array(
        'post_title'  => 'Home',
        'post_status' => 'publish',
        'post_author' => $user_id,
        'post_type'   => 'page',
    );

    $page_exists = get_page_by_title( $page['post_title'] );

    if( $page_exists == null ) {
      // Page doesn't exist, so lets add it
      $insert = wp_insert_post( $page );
      if( $insert ) {
        // Make front page
        update_option( 'page_on_front', $insert );
        update_option( 'show_on_front', 'page' );
      }
    } else {
      deactivate_plugins( plugin_basename( __FILE__ ) );
    }
  }
}
add_action( 'wp_loaded', 'create_page' );