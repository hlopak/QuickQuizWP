<?php
/*
  QuickQuiz Worker.
*/

namespace QuickQuiz;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly




class QQWorker extends QQCore 
{

  /**
  */
  function __construct( $legacy )
  {
    parent::__construct( $legacy );
  }




  /**
  */
  public function createTable()
  {
    $this->db->createTables();
  }




  /**
  */
  public function printShortcode( &$globalQuickQuiz )
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];
    $atts = $legacy[ 'atts' ];
    $content = $legacy[ 'content' ];
    $ver = $globalQuickQuiz[ 'ver' ];


    $ret = $this->db->loadRecord(array(
      'alias' => $atts[ 'name' ],
    )); // return => array( $op_result, $res )


    if ( $ret[ 0 ] === false )
    {
      $html = '*';
    }
    else
    {
      $str_add_scripts = "";

      if ( $globalQuickQuiz[ 'qqscripts_already_is_on_page' ] !== true )
      {
        $globalQuickQuiz[ 'qqscripts_already_is_on_page' ] = true;
        // $src_css = plugins_url( "quickquiz/css" ) . '/qqshcode.css';
        // $src_js = plugins_url( "quickquiz/js" ) . '/qqshcode.js';
        $src_css = plugins_url() . "/" . plugin_basename( __DIR__ ) . '/css/qqshcode.css';
        $src_js = plugins_url() . "/" . plugin_basename( __DIR__ ) . '/js/qqshcode.js';
        $str_add_scripts = "<link rel='stylesheet' href='{$src_css}?ver={$ver}' type='text/css' media='all' id='quickquiz-cssstyles' /> \n";
        $str_add_scripts .= "<script type='text/javascript' src='{$src_js}?ver={$ver}' id='quickquiz-jsscripts' async='true'></script> \n";
      }

      $html = '';
      $html .= "\n" . $str_add_scripts . "\n";
      $html .= "\n<div class='quickquizv01'> \n";
      $html .= $ret[ 1 ][ 0 ][ 'html' ];
      $html .= "\n</div> \n";
    }

    return $html;
  }




}//end class
