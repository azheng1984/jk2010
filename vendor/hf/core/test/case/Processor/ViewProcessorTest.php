<?php
require ROOT_PATH.'lib/Processor/ViewProcessor.php';

class TestView {
  private static $result;

  public static function getResult() {
    return self::$result;
  }

  public function render() {
    self::$result = 'hi';
  }
}

class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $processor = new ViewProcessor;
    $processor->run(array('screen' => 'TestView'));
    $this->assertEquals('hi', TestView::getResult());
  }
}