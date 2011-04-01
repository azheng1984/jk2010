<?php
class ErrorHandlerTest extends PHPUnit_Framework_TestCase {
  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testReloadApplication() {
    $app = $this->getMock('Application', array('run'));
    $app->expects($this->once())->method('run')->with(
      $this->equalTo('/error/internal_server_error')
    );
    $exception = new Exception;
    $handler = new ErrorHandler($app);
    try {
      $handler->handle($exception);
    } catch (PHPUnit_Framework_Error $error) {
      $this->assertEquals($exception, $_ENV['exception']);
      throw $error;
    }
  }
}