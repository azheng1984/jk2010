<?php
class ApplicationCacheTest extends PHPUnit_Framework_TestCase {
  public function testProcessorList() {
    $cache = $this->getCache();
    $this->assertSame(
      array('application', array(array('View' => 'ViewProcessor'))),
      $cache->export()
    );
  }

  public function testMultipleConfigInSameProcessor() {
    $cache = $this->getCache();
    $cache->append('path', 'View', 'first');
    $cache->append('path', 'View', 'second');
    $result = $cache->export();
    $this->assertSame(
      array('View' => array('first', 'second')),
      $result[1]['/path']
    );
  }

  public function testOverwriteConfigInSameProcessor() {
    $cache = $this->getCache();
    $cache->append('path', 'View', array('key' => 'first'));
    $cache->append('path', 'View', array('key' => 'second'));
    $result = $cache->export();
    $this->assertSame(
      array('View' => array('key' => 'second')),
      $result[1]['/path']
    );
  }

  private function getCache() {
    return new ApplicationCache( array('View' => new ViewHandler('Screen')));
  }
}