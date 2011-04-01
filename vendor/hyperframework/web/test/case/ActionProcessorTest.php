<?php
class ActionProcessorTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $_ENV['callback'] = array();
  }

  public function testExecuteActionMethod() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->process();
    $this->assertEquals(1, count($_ENV['callback']));
    $this->assertEquals('TestAction->GET', $_ENV['callback'][0]);
  }

  /**
   * @expectedException MethodNotAllowedException
   */
  public function testMethodNotAllowed() {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    try {
      $this->process();
    } catch (MethodNotAllowedException $exception) {
      $this->assertEquals(0, count($_ENV['callback']));
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