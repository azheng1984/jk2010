<?php
require ROOT_PATH.'lib/ClassLoader.php';

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  public function testLoadSuccess() {
    $classLoader = new ClassLoader;
    $classLoader->run();
    $test = new Application;
  }

  /**
   * @expectedException InternalServerErrorException
   */
  public function testLoadFail() {
    $classLoader = new ClassLoader;
    $classLoader->run();
    $test = new Unknown;
  }
}