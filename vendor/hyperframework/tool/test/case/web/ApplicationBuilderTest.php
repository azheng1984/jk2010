<?php
class ApplicationBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuild() {
    $builder  = new ApplicationBuilder;
    $this->assertTrue(
      $builder->build('View') instanceof ApplicationCache
    );
  }
}