<?php
class ViewProcessorTest extends PHPUnit_Framework_TestCase {
  public static function tearDownAfterClass() {
    unset($_SERVER['REQUEST_MEDIA_TYPE']);
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  public function testRenderView() {
    $this->process();
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertSame(
      'TestScreen->render', $GLOBALS['TEST_CALLBACK_TRACE'][0]
    );
  }

  public function testMethodNotAllowed() {
    $this->setExpectedException('Hyperframework\Web\UnsupportedMediaTypeException');
    $_SERVER['REQUEST_MEDIA_TYPE'] = 'Handheld';
    try {
      $this->process();
    } catch (Hyperframework\Web\UnsupportedMediaTypeException $exception) {
      $this->assertSame(0, count($GLOBALS['TEST_CALLBACK_TRACE']));
      throw $exception;
    }
  }

  private function process() {
    $processor = new Hyperframework\Web\ViewProcessor;
    $processor->run(array('Screen' => 'TestScreen'));
  }
}
