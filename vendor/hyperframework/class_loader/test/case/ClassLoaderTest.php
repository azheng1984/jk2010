<?php
class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  private $classLoader;

  protected function setUp() {
    $this->classLoader = new ClassLoader;
    $this->classLoader->run();
  }

  public function testLoadFromRootPath() {
    new TestRootPath;
  }

  public function testLoadFromRelativePath() {
    new TestRelativePath;
  }

  public function testLoadFromAbsolutePath() {
    new TestAbsolutePath;
  }

  public function testLoadFromAbsoluteSecondLevelPath() {
    new TestAbsoluteSecondLevelPath;
  }

  protected function tearDown() {
    $this->classLoader->stop();
  }
}