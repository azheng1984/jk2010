<?php
class FileGenerationTestCase extends PHPUnit_Framework_TestCase {
  private static $currentWorkingDirectory;

  public static function setUpBeforeClass() {
    self::$currentWorkingDirectory = getcwd();
    $_SERVER['OLD_PWD'] = $_SERVER['PWD'];
    $_SERVER['PWD'] = ROOT_PATH.'tmp';
    mkdir($_SERVER['PWD']);
    chdir($_SERVER['PWD']);
  }

  public static function tearDownAfterClass() {
    chdir(self::$currentWorkingDirectory);
    rmdir($_SERVER['PWD']);
    $_SERVER['PWD'] = $_SERVER['OLD_PWD'];
  }
}