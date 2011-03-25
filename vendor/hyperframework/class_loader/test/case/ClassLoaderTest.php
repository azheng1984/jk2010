<?php
class ClassLoaderTest extends PHPUnit_Framework_TestCase {
  public function testLoad() {
    require dirname(dirname(dirname(__FILE__))).'/lib/ClassLoader.php';
    define('ROOT_PATH', dirname(dirname(__FILE__)).'/fixture/');
    define('CACHE_PATH', ROOT_PATH.'cache/');
    $classLoader = new ClassLoader;
    $classLoader->run();
    foreach (array('A', 'B', 'C', 'D') as $class) {
      new $class;
    }
  }
}