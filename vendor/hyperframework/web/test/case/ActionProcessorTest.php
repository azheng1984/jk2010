<?php
class ActionProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_ENV['callback'] = array();
    $processor = new ActionProcessor;
    $processor->run(array(
      'class' => 'TestAction', 'method' => array('GET')
    ));
    $this->assertEquals('TestAction->GET', $_ENV['callback'][0]);
  }

  /**
   * @expectedException MethodNotAllowedException
   */
  public function testMethodNotAllowed() {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $processor = new ActionProcessor;
    $processor->run(array(
      'class' => 'TestAction', 'method' => array()
    ));
  }
}