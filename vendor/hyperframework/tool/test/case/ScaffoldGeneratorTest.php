<?php
class ScaffoldGeneratorTest extends PHPUnit_Framework_TestCase {
  public static function setUpBeforeClass() {
    $_SERVER['OLD_PWD'] = $_SERVER['PWD'];
    $_SERVER['PWD'] = ROOT_PATH.'tmp';
    mkdir($_SERVER['PWD']);
    
  }

  public static function tearDownAfterClass() {
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
    $generator = new ScaffoldGenerator;
    $generator->generate('test');
    $generator->generate('test');
  }

  public function testGenerateDirectory() {
    $generator = new ScaffoldGenerator;
    $generator->generate('folder/');
    $this->assertTrue(file_exists('folder/'));
  }

  public function testGenerateWriteableDirectory() {
    $generator = new ScaffoldGenerator;
    $generator->generate(array('folder/' => 0777));
    $this->verifyMode('folder/', '0777');
  }

  public function testGenerateFile() {
    $generator = new ScaffoldGenerator;
    $generator->generate(array('folder/file' => array(0775, 'first_line', 'second_line')));
    $this->verifyMode('folder/file', '0775');
  }

  private function verifyMode($path, $mode) {
    $this->assertSame($mode, substr(sprintf('%o', fileperms($path)), -4));
  }
}