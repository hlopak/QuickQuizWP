<?php
/*
  QuickQuiz Core.
*/

namespace QuickQuiz;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



require_once( __DIR__ . '/qqworker.class.php' );
require_once( __DIR__ . '/qqajax.class.php' );
require_once( __DIR__ . '/qqdbprimitives.class.php' );



abstract class QQCore
{
  /* Mutual data. Useful data from parent. */
  protected $legacy = array();

  /* Class of DB primitives */
  protected $db = null;


  /**
  */
  function __construct( $legacy )
  {
    $this->legacy = $legacy;
    $this->db = new QQDBPrimitives( $this, $legacy );
  }




  abstract public function createTable();
  abstract public function printShortcode( &$v );




  /**
  */
  public function printDebug( $arr, $die=true )
  {
    $flash_ret = ob_get_contents();
    while ( ob_get_level() > 0 )
      ob_end_clean();
    echo "<xmp>\n" . print_r( $arr, true ) . "\n</xmp><div>\n" . $flash_ret . "\n</div>";
    if ( $die === true )
      die();
  }


}//end class
