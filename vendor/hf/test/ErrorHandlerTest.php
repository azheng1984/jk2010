<?php
define('SITE_PATH', dirname(dirname(__FILE__)).'/');
require SITE_PATH.'lib/ErrorHandler.php';

class ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {

  }

  /**
   * @expectedException Exception
   */
  public function testRender()
  {
    throw new Exception;
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testGetException()
  {
    $a = $b;
  }
}