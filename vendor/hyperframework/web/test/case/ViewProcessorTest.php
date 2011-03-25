<?php
require dirname(dirname(dirname(__FILE__))).'/lib/view/ViewProcessor.php';
if (!class_exists('ApplicationException')) {
  require dirname(dirname(dirname(__FILE__))).'/lib/ApplicationException.php';
}
require dirname(dirname(dirname(__FILE__))).'/lib/view/UnsupportedMediaTypeException.php';
require dirname(dirname(__FILE__)).'/fixture/app/TestScreen.php';

class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  public function testRun() {
    $processor = new ViewProcessor;
    $processor->run(array('Screen' => 'TestScreen'));
    $this->assertEquals('TestScreen.render', $_ENV['callback']);
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