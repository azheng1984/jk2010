<?php
class ViewHandlerTest extends PHPUnit_Framework_TestCase {
  public function testNotView() {
    $this->assertNull($this->handle('Test', null));
  }

  public function testInvalidType() {
    $this->setExpectedException('Exception', "View type 'test' is invalid");
    $this->assertNull($this->handle(null, null, 'test'));
  }

  public function testNoRenderingMethod() {
    $fullPath = ROOT_PATH.'app/TestNoRenderingMethodScreen.php';
    $this->setExpectedException(
      'Exception', "Rendering method of view not found in '$fullPath'"
    );
    $this->handle('TestNoRenderingMethodScreen', $fullPath);
  }

  public function testPrivateRenderingMethod() {
    $fullPath = ROOT_PATH.'app/TestPrivateRenderingMethodScreen.php';
    $this->setExpectedException(
      'Exception', "Rendering method of view not public in '$fullPath'"
    );
    $this->handle('TestPrivateRenderingMethodScreen', $fullPath);
  }

  public function testCache() {
    $this->assertSame(
      array('Screen' => 'TestScreen'),
      $this->handle('TestScreen', ROOT_PATH.'app/TestScreen.php')
    );
  }

  private function handle($class, $fullPath, $type = 'Screen') {
    $handler = new ViewHandler($type);
    return $handler->handle($class, $fullPath);
  }
}