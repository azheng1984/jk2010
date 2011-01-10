<?php
define('SITE_PATH', dirname(dirname(__FILE__)).'/');
require SITE_PATH.'lib/ErrorHandler.php';
require SITE_PATH.'lib/Exception/ApplicationException.php';
require SITE_PATH.'lib/Exception/NotFoundException.php';

class ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
  public function testHandleException()
  {
    $app = $this->getMock('Application', array('run'));
    $app->expects($this->once())->method('run')->with($this->equalTo('error/internal_server_error'));
    $handler = new ErrorHandler($app);
    $exception = new Exception;
    try {
      $handler->handle($exception);
    } catch (PHPUnit_Framework_Error $ex) {
      $this->assertEquals(ErrorHandler::getException(), $exception);
      return;
    }
    $this->fail();
  }

  public function testHandleNotFoundException()
  {
    $app = $this->getMock('Application', array('run'));
    $app->expects($this->once())->method('run')->with($this->equalTo('error/not_found'));
    $handler = new ErrorHandler($app);
    $exception = new NotFoundException;
    try {
      $handler->handle($exception);
    } catch (PHPUnit_Framework_Error $ex) {
      $this->assertEquals(ErrorHandler::getException(), $exception);
    }
  }
}