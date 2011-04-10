<?php
class ApplicationBuilderTest extends PHPUnit_Framework_TestCase {
  public function testBuild() {
    $_SERVER['PWD'] = TEST_PATH.'fixture';
    $builder  = new ApplicationBuilder;
    $this->assertTrue(
      $builder->build('Action') instanceof ApplicationCache
    );
  }
}