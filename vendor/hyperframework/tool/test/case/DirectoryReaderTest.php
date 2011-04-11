<?php
class DirectoryReaderTest extends PHPUnit_Framework_TestCase {
  private $reader;

  public function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
    $this->reader = new DirectoryReader(new TestDirectoryReaderHandler); 
  }

  public function testPathDoesNotExist() {
    $this->setExpectedException(
      'Exception',
      "Path '".$_SERVER['PWD'].DIRECTORY_SEPARATOR
        ."unknown_path' does not exist"
    );
    $this->reader->read(null, 'unknown_path');
  }

  public function testReadRootPath() {
    $this->reader->read(ROOT_PATH.'lib/test_directory_reader/.');
    $this->reader->read(ROOT_PATH.'lib/test_directory_reader', '.');
    $this->assertSame(2, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->verifyFullPathFirstLevelFileArgument();
    $this->verifyFullPathFirstLevelFileArgument(1);
  }

  public function testReadRelativePath() {
    $this->reader->read(null, 'lib/test_directory_reader/.');
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->verifyArgument();
  }

  public function testReadRecursively() {
    $this->reader->read(null, 'lib/test_directory_reader');
    $this->assertSame(2, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->verifyArgument();
    $this->verifyArgument(
      1, 'SecondLevelFile.php', 'lib/test_directory_reader/second_level'
    );
  }

  public function testRootPathIsFullPath() {
    $this->reader->read(
      ROOT_PATH.'lib/test_directory_reader/FirstLevelFile.php'
    );
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->verifyFullPathFirstLevelFileArgument();
  }

  private function verifyFullPathFirstLevelFileArgument($index = 0) {
    $this->verifyArgument(
      $index, 'FirstLevelFile.php', null, ROOT_PATH.'lib/test_directory_reader'
    );
  }

  private function verifyArgument(
    $index = 0,
    $fileName = 'FirstLevelFile.php',
    $relativeFolder = 'lib/test_directory_reader',
    $rootFolder = null) {
    $this->assertSame(
      array(
        'file_name' => $fileName,
        'relative_folder' => $relativeFolder,
        'root_folder' => $rootFolder,
      ),
      $GLOBALS['TEST_CALLBACK_TRACE'][$index]['argument']
    );
  }
}