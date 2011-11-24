<?php
class ClassRecognizationHandlerTest extends PHPUnit_Framework_TestCase {
  public function testHandle() {
    $cache = new ClassLoaderCache();
    $handler = new ClassRecognizationHandler($cache);
    $handler->handle('Test.php', null, null);
    $result = $cache->export();
    $this->assertTrue(isset($result[1][0]['Test']));
  }
}