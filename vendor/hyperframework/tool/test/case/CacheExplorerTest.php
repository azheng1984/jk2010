<?php
class CacheExplorerTest extends PHPUnit_Framework_TestCase {
  private static $cacheFolder;
  private static $testCachePath;

  public static function setUpBeforeClass() {
    $_SERVER['PWD'] = ROOT_PATH.'tmp';
    mkdir($_SERVER['PWD']);
    self::$cacheFolder = $_SERVER['PWD'].DIRECTORY_SEPARATOR.'cache';
    self::$testCachePath = self::$cacheFolder
      .DIRECTORY_SEPARATOR.'test.cache.php';
  }

  public static function tearDownAfterClass() {
    rmdir($_SERVER['PWD']);
  }

  protected function tearDown() {
    if (is_file(self::$testCachePath)) {
      unlink(self::$testCachePath);
    }
    if (is_dir(self::$cacheFolder)) {
      rmdir(self::$cacheFolder);
    }
  }

  public function testNullResult () {
    $exporter = new CacheExporter;
    $exporter->export(null);
    $this->assertFalse(is_dir(self::$cacheFolder));
  }

  public function testNotNullResult () {
    $exporter = new CacheExporter;
    $exporter->export(new TestCache);
    $cacheVerifier = new TestCacheVerifier;
    $cacheVerifier->verify($this, self::$testCachePath);
  }
}