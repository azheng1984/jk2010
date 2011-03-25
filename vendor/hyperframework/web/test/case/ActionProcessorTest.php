<?php
require dirname(dirname(dirname(__FILE__))).'/lib/action/ActionProcessor.php';
if (!class_exists('ApplicationException')) {
  require dirname(dirname(dirname(__FILE__))).'/lib/ApplicationException.php';
}
require dirname(dirname(dirname(__FILE__))).'/lib/action/MethodNotAllowedException.php';
require dirname(dirname(__FILE__)).'/fixture/app/TestAction.php';

class ActionProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $processor = new ActionProcessor;
    $processor->run(array(
      'class' => 'TestAction', 'method' => array('GET')
    ));
    $this->assertEquals('TestAction.GET', $_ENV['callback']);
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