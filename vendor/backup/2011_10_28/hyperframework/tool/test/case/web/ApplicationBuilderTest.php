<?php
class ApplicationBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuild() {
    $builder = new ApplicationBuilder;
    $this->assertNotNull($builder->build(array('View' => array())));
  }
}