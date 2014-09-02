<?php
class FileGenerationTestCase extends PHPUnit_Framework_TestCase {
  public static function setUpBeforeClass() {
    $_SERVER['OLD_PWD'] = $_SERVER['PWD'];
    $_SERVER['PWD'] = ROOT_PATH.'tmp';
    mkdir($_SERVER['PWD']);
    chdir($_SERVER['PWD']);
  }

  public static function tearDownAfterClass() {
    chdir($_SERVER['OLD_PWD']);
    rmdir($_SERVER['PWD']);
    $_SERVER['PWD'] = $_SERVER['OLD_PWD'];
  }
}