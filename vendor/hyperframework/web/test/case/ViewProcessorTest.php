<?php
class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $_ENV['callback_trace'] = array();
  }

  public function testRenderView() {
    $this->process();
    $this->assertEquals(1, count($_ENV['callback_trace']));
    $this->assertEquals('TestScreen->render', $_ENV['callback_trace'][0]);
  }

  /**
   * @expectedException UnsupportedMediaTypeException
   */
  public function testMethodNotAllowed() {
    $_ENV['media_type'] = 'Handheld';
    try {
      $this->process();
    } catch (UnsupportedMediaTypeException $exception) {
      $this->assertEquals(0, count($_ENV['callback_trace']));
      throw $exception;
    }
  }

  private function process() {
    $processor = new ViewProcessor;
    $processor->run(array('Screen' => 'TestScreen'));
  }
}