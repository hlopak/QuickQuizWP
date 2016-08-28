<?php
/**
 * @package QuickQuiz
 */
/*
Plugin Name: QuickQuiz
Plugin URI: http://codepen.io/hlopak/pen/vKbWRR
Description: QuickQuiz for learners and teachers.
Version: 0.1
Author: Hlopak
Author URI: https://github.com/hlopak
License: GPLv2 or later
Text Domain: quickquiz
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016 Hlopak.
*/



namespace QuickQuiz;


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) )
{
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

define( 'QuickQuiz_VERSION', '0.1.92' );
define( 'QuickQuiz__MINIMUM_WP_VERSION', '4.5.0' );
define( 'QuickQuiz__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'QuickQuiz_DELETE_LIMIT', 100000 );



$globalQuickQuiz = array();


require_once( ABSPATH . 'wp-includes/pluggable.php' );
$qq_nonce = wp_create_nonce( 'my-quickquiz-plugin' );


require_once( QuickQuiz__PLUGIN_DIR . 'qqcore.class.php' );




/**
*/
function PluginSettings()
{
  add_menu_page( 'Generator for QuickQuiz', 'QuickQuiz', 'edit_posts', __FILE__, 
    __NAMESPACE__ . '\buildPage', 'dashicons-welcome-learn-more', null );
}
add_action( "admin_menu", __NAMESPACE__ . '\PluginSettings' );




/**
*/
function this_is_plugin_activation()
{
  global $wpdb;

  if ( !function_exists('dbDelta') )
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); 

  $QQWorker = new QQWorker(array(
    'wpdb' => $wpdb,
  ));

  $QQWorker->createTable();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\this_is_plugin_activation' );




/**
*
*/
function admin_plugin_scripts()
{
  global $qq_nonce;

  // -- CSS --
  wp_enqueue_style( 'QuickQuiz-style', plugins_url() . "/" . plugin_basename( __DIR__ ) . '/css/settings.css' );

  // -- JS --
  wp_enqueue_script( 'QuickQuiz-settings-js', plugins_url() . "/" . plugin_basename( __DIR__ ) . '/js/settings.js' );  

  // -- JS variables --
  wp_localize_script( 'QuickQuiz-settings-js', 'QQ_vars', array( 
    'csrf_protector' => $qq_nonce,
    'this_plugin_url' => plugins_url() . "/" . plugin_basename( __DIR__ ),
  ));
}
add_action( 'admin_head', __NAMESPACE__ . '\admin_plugin_scripts' );




/**
*
*/
function plugin_scripts()
{
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\plugin_scripts' );




/**
*/
function buildPage()
{

  require( __DIR__ . "/settings.php" );

  $html = getHtml(array(
    'version' => "v." . QuickQuiz_VERSION,
  ));

  echo $html;
}




/**
*/
function securityCheck()
{
  global $wpdb;

  check_ajax_referer( 'my-quickquiz-plugin' );

  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'my-quickquiz-plugin' ) )
  {
    $status['errorMessage'] = __( 'Sorry, you are not allowed to work with data! #1' );
    wp_send_json_error( $status );
    die( __( 'Security check', 'textdomain' ) ); 
  } 
  elseif ( ! current_user_can( 'read' ) )
  {
    $status['errorMessage'] = __( 'Sorry, you are not allowed to work with data! #2' );
    wp_send_json_error( $status );
    die( __( 'Security check', 'textdomain' ) );
  }
}




/**
*/
function quickquiz_shortcode( $atts, $content = null )
{
  global $wpdb;
  global $globalQuickQuiz;

  $QQWorker = new QQWorker(array(
    'wpdb' => $wpdb,
    'atts' => $atts,
    'content' => $content,
  ));

  $globalQuickQuiz[ 'ver' ] = QuickQuiz_VERSION;

  return $QQWorker->printShortcode( $globalQuickQuiz );
}
add_shortcode( 'quickquiz', __NAMESPACE__ . '\quickquiz_shortcode' );








/* **********************************************************
  AJAX...
*/




$ajax_functions = array(
  'save_qqrecord',
  'load_qqrecord',
  'delete_qqrecord',
  'load_qqrecord_list',
);
foreach( $ajax_functions as $af )
  add_action( 'wp_ajax_'.$af , __NAMESPACE__ . '\ajax__'.$af );




/**
*/
function ajax_handle( $op )
{
  global $wpdb;

  securityCheck();

  $QQAjax = new QQAjax(array(
    'wpdb' => $wpdb,
  ));

  if ( $op === 'save' )
    $QQAjax->save_record();
  elseif ( $op === 'load' )
    $QQAjax->load_record();
  elseif ( $op === 'delete' )
    $QQAjax->delete_record();
  elseif ( $op === 'load-list' )
    $QQAjax->load_record_list();
  else
    return null;
}



/**
*/
function ajax__save_qqrecord( )
{
  ajax_handle( 'save' );
}



/**
*/
function ajax__load_qqrecord( )
{
  ajax_handle( 'load' );
}



/**
*/
function ajax__delete_qqrecord( )
{
  ajax_handle( 'delete' );
}



/**
*/
function ajax__load_qqrecord_list( )
{
  ajax_handle( 'load-list' );
}




