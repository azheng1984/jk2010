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
    $this->verifyHandlerCount(2);
    $this->verifyFullPathFirstLevelFileArgument();
    $this->verifyFullPathFirstLevelFileArgument(1);
  }

  public function testReadRelativePath() {
    $this->reader->read(null, 'lib/test_directory_reader/.');
    $this->verifyHandlerCount(1);
    $this->verifyArgument();
  }

  public function testReadRecursively() {
    $this->reader->read(null, 'lib/test_directory_reader');
    $this->verifyHandlerCount(2);
    $this->verifyArgument();
    $this->verifyArgument(
      1, 'SecondLevelFile.php', 'lib/test_directory_reader/second_level'
    );
  }

  public function testRootPathIsFullPath() {
    $this->reader->read(
      ROOT_PATH.'lib/test_directory_reader/FirstLevelFile.php'
    );
    $this->verifyHandlerCount(1);
    $this->verifyFullPathFirstLevelFileArgument();
  }

  private function verifyHandlerCount($expected) {
    $this->assertSame($expected, count($GLOBALS['TEST_CALLBACK_TRACE']));
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

  private function verifyFullPathFirstLevelFileArgument($index = 0) {
    $this->verifyArgument(
      $index, 'FirstLevelFile.php', null, ROOT_PATH.'lib/test_directory_reader'
    );
  }
}