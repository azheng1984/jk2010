<?php
class ExplorerContextTest extends PHPUnit_Framework_TestCase {
  public static function tearDownAfterClass() {
    ExplorerContext::reset();
  }

  public function testCacheExplorer() {
    $explorer = ExplorerContext::getExplorer('Package');
    $this->assertEquals($explorer, ExplorerContext::getExplorer('Package'));
  }

  public function testCacheWriter() {
    $writer = ExplorerContext::getWriter();
    $this->assertEquals($writer, ExplorerContext::getWriter());
  }
}