<?php
class ClassRecognizationHandlerTest extends PHPUnit_Framework_TestCase {
  public function testStartsWithLowerCase() {
    $result = $this->handle('test.php');
    $this->assertSame(0, count($result[1][0]));
  }

  public function testStartsWithUpperCase() {
    $result = $this->handle('Test.php');
    $this->assertTrue(isset($result[1][0]['Test']));
  }

  private function handle($fileName) {
    $cache = new ClassLoaderCache();
    $handler = new ClassRecognizationHandler($cache);
    $handler->handle($fileName, null, null);
    return $cache->export();
  }
}