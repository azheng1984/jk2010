<?php
require ROOT_PATH.'lib/Processor/ActionProcessor.php';

class TestAction {
  private static $result;

  public static function getResult() {
    return self::$result;
  }

  public function GET() {
    self::$result = 'hi';
  }
}

class ActionProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $processor = new ActionProcessor;
    $processor->run(array(
      'class' => 'TestAction',
      'method' => array('GET')));
    $this->assertEquals('hi', TestAction::getResult());
  }
}