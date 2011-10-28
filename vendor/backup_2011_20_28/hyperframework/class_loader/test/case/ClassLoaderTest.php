<?php
class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  private static $classLoader;

  public static function setUpBeforeClass() {
    self::$classLoader = new ClassLoader;
    self::$classLoader->run();
  }

  public static function tearDownAfterClass() {
    self::$classLoader->stop();
  }

  public function testLoadFromRootPath() {
    new LoadFromRootPath;
  }

  public function testLoadFromRelativePath() {
    new LoadFromRelativePath;
  }

  public function testLoadFromAbsolutePath() {
    new LoadFromTopLevelAbsolutePath;
  }

  public function testLoadFromSecondLevelAbsolutePath() {
    new LoadFromSecondLevelAbsolutePath;
  }
}