<?php
class CacheExporterTest extends FileGenerationTestCase {
  private static $cacheFolder;
  private static $testCachePath;

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$cacheFolder = 'cache'.DIRECTORY_SEPARATOR;
    self::$testCachePath = self::$cacheFolder.'test.cache.php';
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