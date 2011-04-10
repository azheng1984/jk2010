<?php
class ClassLoaderBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuildByConfiguration() {
    $_SERVER['PWD'] = TEST_PATH.'fixture';
    $builder = new ClassLoaderBuilder;
    $this->assertTrue(
      $builder->build('app') instanceof ClassLoaderCache
    );
  }
}