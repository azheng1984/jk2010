<?php
class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $_ENV['callback'] = array();
  }

  public function testRenderView() {
    $this->process();
    $this->assertEquals(1, count($_ENV['callback']));
    $this->assertEquals('TestScreen->render', $_ENV['callback'][0]);
  }

  /**
   * @expectedException UnsupportedMediaTypeException
   */
  public function testMethodNotAllowed() {
    $_ENV['media_type'] = 'Handheld';
    try {
      $this->process();
    } catch (UnsupportedMediaTypeException $exception) {
      $this->assertEquals(0, count($_ENV['callback']));
      throw $exception;
    }
  }

  private function process() {
    $processor = new ViewProcessor;
    $processor->run(array('Screen' => 'TestScreen'));
  }
}