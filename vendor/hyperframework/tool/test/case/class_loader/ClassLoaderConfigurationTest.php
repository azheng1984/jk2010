<?php
class ClassLoaderConfigurationTest extends PHPUnit_Framework_TestCase {
  public function testStringConfig() {
    $config = 'app';
  }

  public function testFirstLevelConfig() {
    $config = array('app');
  }

  public function testSecondLevelConfig() {
    $config = array('app' => array());
  }

  public function testFirstLevelFullPathConfig() {
    $config = array('app' => array());
  }

  public function testSecondLevelFullPathConfig() {
    //$config = array('app' => );
  }
}