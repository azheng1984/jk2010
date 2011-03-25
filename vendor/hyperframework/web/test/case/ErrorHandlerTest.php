<?php
require dirname(dirname(dirname(__FILE__))).'/lib/ErrorHandler.php';
require dirname(dirname(dirname(__FILE__))).'/lib/ApplicationException.php';
require dirname(dirname(dirname(__FILE__))).'/lib/InternalServerErrorException.php';
if (!defined('ROOT_PATH')) {
  define('ROOT_PATH', dirname(dirname(__FILE__)).'/fixture/');
}
define('CONFIG_PATH', ROOT_PATH.'config/');

class ErrorHandlerTest extends PHPUnit_Framework_TestCase {
  public function testHandle() {
    $app = $this->getMock('Application', array('run'));
    $app->expects($this->once())->method('run')->with($this->equalTo('/error/internal_server_error'));
    $handler = new ErrorHandler($app);
    $exception = new Exception;
    try {
      $handler->handle($exception);
    } catch (PHPUnit_Framework_Error $error) {
      $this->assertEquals($_ENV['exception'], $exception);
      return;
    }
    $this->fail();
  }
}