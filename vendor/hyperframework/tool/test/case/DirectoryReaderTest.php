<?php
class DirectoryReaderTest extends PHPUnit_Framework_TestCase {
  private $reader;

  protected function setUp() {
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
    $this->verifyCallbackCount(2);
    $this->verifyFullPathFirstLevelFileArgument();
    $this->verifyFullPathFirstLevelFileArgument(1);
  }

  public function testReadRelativePath() {
    $this->reader->read(null, 'lib/test_directory_reader/.');
    $this->verifyCallbackCount(1);
    $this->verifyRelativePathFirstLevelFileArgument();
  }

  public function testReadRecursively() {
    $this->reader->read(null, 'lib/test_directory_reader');
    $this->verifyCallbackCount(2);
    $this->verifyRelativePathFirstLevelFileArgument();
    $this->verifyArgument(
      1,
      $this->getPath(
        ROOT_PATH.'lib',
        'test_directory_reader',
        'second_level',
        'SecondLevelFile.php'
      ),
      $this->getPath('lib', 'test_directory_reader', 'second_level')
    );
  }

  public function testRootPathIsFullPath() {
    $this->reader->read(
      ROOT_PATH.'lib/test_directory_reader/FirstLevelFile.php'
    );
    $this->verifyCallbackCount(1);
    $this->verifyFullPathFirstLevelFileArgument();
  }

  private function getPath() {
    $sections = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $sections);
  }

  private function verifyCallbackCount($expected) {
    $this->assertSame($expected, count($GLOBALS['TEST_CALLBACK_TRACE']));
  }

  private function verifyRelativePathFirstLevelFileArgument($index = 0) {
    $this->verifyArgument(
      $index,
      ROOT_PATH.$this->getPath(
        'lib', 'test_directory_reader', 'FirstLevelFile.php'
      ),
      $this->getPath('lib', 'test_directory_reader')
    );
  }

  private function verifyFullPathFirstLevelFileArgument($index = 0) {
    $this->verifyArgument(
      $index,
      ROOT_PATH.$this->getPath(
        'lib', 'test_directory_reader', 'FirstLevelFile.php'
      ),
      null,
      ROOT_PATH.$this->getPath('lib', 'test_directory_reader')
    );
  }

  private function verifyArgument(
    $index = 0, $fullPath ,$relativeFolder, $rootFolder = null
  ) {
    $this->assertSame(
      array(
        'full_path' => $fullPath,
        'relative_folder' => $relativeFolder,
        'root_folder' => $rootFolder,
      ),
      $GLOBALS['TEST_CALLBACK_TRACE'][$index]['argument']
    );
  }
}