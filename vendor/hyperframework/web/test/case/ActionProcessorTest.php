<?php
class ActionProcessorTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  public function testExecuteMethod() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->process();
    $this->assertEquals(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertEquals('TestAction->GET', $GLOBALS['TEST_CALLBACK_TRACE'][0]);
  }

  public function testMethodNotAllowed() {
    $this->setExpectedException('MethodNotAllowedException');
    $_SERVER['REQUEST_METHOD'] = 'POST';
    try {
      $this->process();
    } catch (MethodNotAllowedException $exception) {
      $this->assertEquals(0, count($GLOBALS['TEST_CALLBACK_TRACE']));
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