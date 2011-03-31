<?php
class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $_ENV['callback'] = array();
    $processor = new ViewProcessor;
    $processor->run(array('Screen' => 'TestScreen'));
    $this->assertEquals('TestScreen->render', $_ENV['callback'][0]);
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