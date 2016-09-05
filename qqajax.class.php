<?php
/*
  QuickQuiz Ajax.
*/

namespace QuickQuiz;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



class QQAjax extends QQWorker
{

  /**
  */
  function __construct( $legacy )
  {
    parent::__construct( $legacy );
  }




  /**
  */
  public function save_record()
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    // $slug = sanitize_key( wp_unslash( $_POST['slug'] ) );
    $something = stripslashes( $_POST[ 'something' ] );
    $data = json_decode( $something, true );
    $json_last_error = json_last_error();

    if ( JSON_ERROR_NONE !== $json_last_error )
    {
      wp_send_json_error(array(
        'error' => $json_last_error,
        'msg' => 'Cannot decode JSON on entrance!',
      ));
      return;
    }


    $ret = $this->db->saveRecord(array(
      'alias' => $data[ 'alias' ],
      'json' => json_encode( $data[ 'records' ] ),
      'html' => $data[ 'result' ],
    )); // return => array( $op_result, $ID, $hash, $action )


    if ( $ret[ 0 ] === false )
    {
      wp_send_json_error(array(
        'error' => 301,
        'msg' => 'Cannot save record to DB!',
      ));
      return;
    }
    else
    {
      wp_send_json_success(array(
        'error' => 0,
        'action' => $ret[ 3 ],
        'alias' => $data[ 'alias' ],
        'hash' => $ret[ 2 ],
      ));
    }

  }




  /**
  */
  public function load_record()
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $something = stripslashes( $_POST[ 'something' ] );
    $data = json_decode( $something, true );
    $json_last_error = json_last_error();

    if ( JSON_ERROR_NONE !== $json_last_error )
    {
      wp_send_json_error(array(
        'error' => $json_last_error,
        'msg' => 'Cannot decode JSON on entrance!',
      ));
      return;
    }

    $ret = $this->db->loadRecord(array(
      'alias' => $data[ 'alias' ],
    )); // return => array( $op_result, $res )


    if ( $ret[ 0 ] === false )
    {
      wp_send_json_error(array(
        'error' => 404,
        'msg' => 'Cannot read record from DB!',
      ));
      return;
    }
    else
    {
      $json = json_decode( $ret[ 1 ][ 0 ][ 'json' ], true );
      $json_last_error = json_last_error();
      if ( JSON_ERROR_NONE !== $json_last_error )
        $json = '';

      wp_send_json_success(array(
        'error' => 0,
        'data' => array(
          'alias' => $ret[ 1 ][ 0 ][ 'alias' ],
          'hash' => $ret[ 1 ][ 0 ][ 'hash' ],
          'json' => $json,
          'html' => $ret[ 1 ][ 0 ][ 'html' ],
        )
      ));
    }

  }




  /**
  */
  public function delete_record()
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $something = stripslashes( $_POST[ 'something' ] );
    $data = json_decode( $something, true );
    $json_last_error = json_last_error();

    if ( JSON_ERROR_NONE !== $json_last_error )
    {
      wp_send_json_error(array(
        'error' => $json_last_error,
        'msg' => 'Cannot decode JSON on entrance!',
      ));
      return;
    }

    $ret = $this->db->deleteRecord(array(
      'alias' => $data[ 'alias' ],
    )); // return => array( $op_result, $res )


    if ( $ret[ 0 ] === false )
    {
      wp_send_json_error(array(
        'error' => 302,
        'msg' => 'Cannot delete record from DB!',
      ));
      return;
    }
    else
    {
      wp_send_json_success(array(
        'error' => 0,
      ));
    }

  }




  /**
  */
  public function load_record_list()
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $something = stripslashes( $_POST[ 'something' ] );
    $data = json_decode( $something, true );
    $json_last_error = json_last_error();

    if ( JSON_ERROR_NONE !== $json_last_error )
    {
      wp_send_json_error(array(
        'error' => $json_last_error,
        'msg' => 'Cannot decode JSON on entrance!',
      ));
      return;
    }

    $ret = $this->db->loadRecordslist(array( )); // return => array( $op_result, $res )


    if ( $ret[ 0 ] === false )
    {
      wp_send_json_error(array(
        'error' => 404,
        'msg' => 'Cannot read record from DB!',
      ));
      return;
    }
    else
    {
      wp_send_json_success(array(
        'error' => 0,
        'data' => $ret[ 1 ],
      ));
    }

  }



}//end class
