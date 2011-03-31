<?php
class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  private $classLoader;

  public function testLoadFromRootPath() {
    new LoadFromRootPath;
  }

  public function testLoadFromRelativePath() {
    new LoadFromRelativePath;
  }

  public function testLoadFromAbsolutePath() {
    new LoadFromTopLevelAbsolutePath;
  }

  public function testLoadFromAbsoluteSecondLevelPath() {
    new LoadFromSecondLevelAbsolutePath;
  }

  protected function setUp() {
    $this->classLoader = new ClassLoader;
    $this->classLoader->run();
  }

  protected function tearDown() {
    $this->classLoader->stop();
  }
}