<?php
class ScaffoldGeneratorTest extends PHPUnit_Framework_TestCase {
  private static $generator;

  public static function setUpBeforeClass() {
    $_SERVER['OLD_PWD'] = $_SERVER['PWD'];
    $_SERVER['PWD'] = ROOT_PATH.'tmp';
    mkdir($_SERVER['PWD']);
    chdir($_SERVER['PWD']);
    self::$generator = new ScaffoldGenerator;
  }

  public static function tearDownAfterClass() {
    chdir($_SERVER['OLD_PWD']);
    rmdir($_SERVER['PWD']);
    $_SERVER['PWD'] = $_SERVER['OLD_PWD'];
  }

  protected  function tearDown() {
    if (file_exists('test')) {
      unlink('test');
    }
    if (file_exists('folder/file')) {
      unlink('folder/file');
    }
    if (file_exists('folder')) {
      rmdir('folder');
    }
  }

  public function testFileExisted() {
    $this->setExpectedException('Exception', "File 'test' existed");
    self::$generator->generate('test');
    self::$generator->generate('test');
  }

  public function testGenerateDirectory() {
    self::$generator->generate('folder/');
    $this->assertTrue(is_dir('folder'));
  }

  public function testGenerateWriteableDirectory() {
    self::$generator->generate(array('folder/' => 0777));
    $this->verifyMode('folder', '0777');
  }

  public function testGenerateFile() {
    self::$generator->generate(
      array('folder/file' => array(0666, 'first_line', 'second_line'))
    );
    $this->verifyMode('folder/file', '0666');
  }

  private function verifyMode($path, $mode) {
    $this->assertSame($mode, substr(sprintf('%o', fileperms($path)), -4));
  }
}