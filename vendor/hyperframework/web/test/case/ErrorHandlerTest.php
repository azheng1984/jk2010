<?php
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