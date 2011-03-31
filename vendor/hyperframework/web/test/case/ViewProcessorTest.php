<?php
class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $processor = new ViewProcessor;
    $processor->run(array('Screen' => 'TestScreen'));
    $this->assertEquals('TestScreen->render', $_ENV['callback']);
  }

  /**
   * @expectedException UnsupportedMediaTypeException
   */
  public function testMethodNotAllowed() {
    $processor = new ViewProcessor;
    $_ENV['media_type'] = 'Handheld';
    $processor->run(array('Screen' => 'TestScreen'));
  }
}