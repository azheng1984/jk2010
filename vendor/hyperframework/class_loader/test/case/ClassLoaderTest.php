<?php
class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  private $classLoader;

  protected function setUp() {
    $this->classLoader = new ClassLoader;
    $this->classLoader->run();
  }

  public function testLoad() {
    foreach (array('A', 'B', 'C', 'D') as $class) {
      new $class;
    }
  }

  protected function tearDown() {
    $this->classLoader->stop();
  }
}