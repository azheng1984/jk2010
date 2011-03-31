<?php
class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  public function testLoad() {
    $this->classLoader = new ClassLoader;
    $this->classLoader->run();
    foreach (array('A', 'B', 'C', 'D') as $class) {
      new $class;
    }
    $this->classLoader->stop();
  }
}