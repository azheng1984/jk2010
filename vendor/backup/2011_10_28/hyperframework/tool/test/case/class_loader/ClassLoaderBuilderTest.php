<?php
class ClassLoaderBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuildByConfiguration() {
    $builder = new ClassLoaderBuilder;
    $this->assertTrue($builder->build('app') instanceof ClassLoaderCache);
  }
}