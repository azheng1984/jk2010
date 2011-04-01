<?php
class ActionProcessorTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $_ENV['callback_trace'] = array();
  }

  public function testExecuteMethod() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->process();
    $this->assertEquals(1, count($_ENV['callback_trace']));
    $this->assertEquals('TestAction->GET', $_ENV['callback_trace'][0]);
  }

  /**
   * @expectedException MethodNotAllowedException
   */
  public function testMethodNotAllowed() {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    try {
      $this->process();
    } catch (MethodNotAllowedException $exception) {
      $this->assertEquals(0, count($_ENV['callback_trace']));
      throw $exception;
    }
  }

  private function process() {
    $processor = new ActionProcessor;
    $processor->run(
      array('class' => 'TestAction', 'method' => array('GET'))
    );
  }
}