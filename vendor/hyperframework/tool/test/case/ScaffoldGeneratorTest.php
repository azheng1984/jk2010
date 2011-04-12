<?php
class ScaffoldGeneratorTest extends FileGenerationTestCase {
  private static $generator;

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$generator = new ScaffoldGenerator;
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
    $this->assertSame(
      'first_line'.PHP_EOL.'second_line', file_get_contents('folder/file')
    );
    $this->verifyMode('folder/file', '0666');
  }

  private function verifyMode($path, $mode) {
    $this->assertSame($mode, substr(sprintf('%o', fileperms($path)), -4));
  }
}