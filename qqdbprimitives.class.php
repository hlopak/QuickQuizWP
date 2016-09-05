<?php
/*
  QuickQuiz DB primitives.
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


namespace QuickQuiz;



class QQDBPrimitives
{
   /* QQ Core (Parent) */
  protected $core;

   /* Mutual data */
  protected $legacy = array();
 
  /**
  */
  function __construct( $core, $legacy )
  {
    $this->core = $core;
    $this->legacy = $legacy;
  }




  /**
  */
  public function createTables()
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $sql = "CREATE TABLE quickquiz (
        ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        t timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        tc timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        visible TINYINT(1) unsigned NOT NULL DEFAULT 1,
        trash TINYINT(1) unsigned NOT NULL DEFAULT 0,
        alias varchar(255) NOT NULL DEFAULT '',
        hash varchar(255) NOT NULL DEFAULT '',
        json text DEFAULT NULL,
        html text DEFAULT NULL,
        tmp varchar(10) NOT NULL DEFAULT '',
        PRIMARY KEY (ID),
        KEY alias (alias)
      ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

    $created = dbDelta( $sql );
  }




  /**
  */
  public function saveRecord( $arr )
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $ID = null;
    $hash = null;
    $action = null;
    $op_result = false;
    $salt = 'solka.7802vn584290s';

    $defaults = array(
      'alias' => '',
      'hash' => '',
      'json' => '[]',
      'html' => '',
    );
    $arr = wp_parse_args( $arr, $defaults );


    $sql = "
      SELECT * FROM `quickquiz`
      WHERE `trash` = '0' AND `visible` = '1' 
        AND (`alias` = '%s' OR `hash` = '%s')
      LIMIT 1
    ;";

    $res = $wpdb->get_results($wpdb->prepare( $sql, 
      esc_html__( $arr[ 'alias' ]),
      esc_html__( $arr[ 'hash' ])
    ), ARRAY_A );


    if ( $res === false || $res === null || $res === array() )
      $action = 'insert';
    else
      $action = 'update';


    if ( $action === 'insert' )
    {
      $r = rand( 10000, 88888 );
      $new_alias = $arr['alias'] == '' ? "QuickQuiz-" . $r : $arr['alias'];
      $hash = md5( $salt . time() . $r );

      $res2 = $wpdb->insert( 
        'quickquiz', 
        array( 
          'alias' => $new_alias,
          'hash' => $hash,
          'json' => $arr['json'],
          'html' => $arr['html']
        ), 
        array( 
          '%s',
          '%s'
        )
      );

      if ( $res2 !== false )
      {
        $ID = $wpdb->insert_id;
        $op_result = true;
      }
      else
      {
        $ID = null;
        $op_result = false;
      }
    }


    if ( $action === 'update' )
    {
      $ID = $res[ 0 ][ 'ID' ];
      $hash = $res[ 0 ][ 'hash' ];

      $res2 = $wpdb->update(
        'quickquiz', 
        array( 
          'json' => $arr['json'],
          'html' => $arr['html']
        ), 
        array( 'ID' => $ID ), 
        array( 
          '%s',
          '%s'
        ), 
        array( '%d' ) 
      );

      if ( $res2 !== false )
      {
        $op_result = true;
      }
      else
      {
        $op_result = false;
      }
    }

    return array( $op_result, $ID, $hash, $action );
  }




  /**
  */
  public function loadRecord( $arr )
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $ID = null;
    $op_result = false;
    $action = '';

    $defaults = array(
      'alias' => '',
      'hash' => '',
    );
    $arr = wp_parse_args( $arr, $defaults );


    $sql = "
      SELECT * FROM `quickquiz`
      WHERE `trash` = '0' AND `visible` = '1'
        AND (`alias` = '%s' OR `hash` = '%s')
      LIMIT 1
    ;";

    $res = $wpdb->get_results($wpdb->prepare( $sql, 
      esc_html__( $arr[ 'alias' ]),
      esc_html__( $arr[ 'hash' ])
    ), ARRAY_A );


    if ( $res === false || $res === null || $res === array() )
    {
      $op_result = false;
    }
    else
    {
      $op_result = true;
    }

    // $this->core->printDebug(array( 'YY-11', $sql, $arr, '$op_result, $res:', $op_result, $res ));
    return array( $op_result, $res );
  }




  /**
  */
  public function deleteRecord( $arr )
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $ID = null;
    $op_result = false;
    $action = '';

    $defaults = array(
      'alias' => '',
      'hash' => '',
    );
    $arr = wp_parse_args( $arr, $defaults );


    $sql = "
      SELECT * FROM `quickquiz`
      WHERE `trash` = '0' AND `visible` = '1'
        AND (`alias` = '%s' OR `hash` = '%s')
      LIMIT 1
    ;";

    $res = $wpdb->get_results($wpdb->prepare( $sql, 
      esc_html__( $arr[ 'alias' ]),
      esc_html__( $arr[ 'hash' ])
    ), ARRAY_A );


    if ( $res === false || $res === null || $res === array() )
    {
      $op_result = false;
    }
    else
    {
      $ID = $res[ 0 ][ 'ID' ];
      $hash = $res[ 0 ][ 'hash' ];

      $res2 = $wpdb->update(
        'quickquiz', 
        array( 
          'trash' => 1,
        ), 
        array( 'ID' => $ID ), 
        array( ),
        array( )
      );

      $this->cleanTable( 'quickquiz' );

      if ( $res2 !== false )
      {
        $op_result = true;
      }
      else
      {
        $op_result = false;
      }
    }

    return array( $op_result, $res );
  }




  /**
  */
  public function loadRecordslist( $arr )
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $ID = null;
    $op_result = false;
    $action = '';
    $ret = array();

    $defaults = array(
      'alias' => '',
      'hash' => '',
    );
    $arr = wp_parse_args( $arr, $defaults );


    $sql = "
      SELECT * FROM `quickquiz`
      WHERE `trash` = '0' AND `visible` = '1'
        AND (NOT `alias` = '')
      LIMIT 1000
    ;";

    $res = $wpdb->get_results($wpdb->prepare( $sql ), ARRAY_A );


    if ( $res === false || $res === null || $res === array() )
    {
      $op_result = false;
    }
    else
    {
      $op_result = true;
      foreach( $res as $rowKey => $rowVal )
      {
        $ret[ $rowKey ] = $rowVal[ 'alias' ];
      }
    }

    // $this->core->printDebug(array( 'YY-11', $sql, $arr, '$op_result, $res:', $op_result, $res ));
    return array( $op_result, $ret );
  }




  /**
  */
  public function cleanTable( $tbl )
  {
    $legacy = $this->legacy;
    $wpdb = $legacy[ 'wpdb' ];

    $sql = "
      DELETE FROM `{$tbl}`
      WHERE `trash` = '1'
        AND `tc` < DATE_SUB( NOW(), INTERVAL 1 MONTH )
    ;";
    $res = $wpdb->get_results($wpdb->prepare( $sql ), ARRAY_A );

    if ( $res === false || $res === null || $res === array() )
    {
      $op_result = false;
    }
    else
    {
      $op_result = true;
    }
    
    // $this->core->printDebug( $arr );
    return $op_result;
  }




}//end class
