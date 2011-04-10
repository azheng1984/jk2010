<?php
class DirectoryReaderTest extends PHPUnit_Framework_TestCase {
  public function testPathDoesNotExist() {
    $reader = new DirectoryReader(null);
    //$reader->read(null, 'unknown_path');
  }

  public function testReadRootPath() {
    $reader = new DirectoryReader(null);
    //$reader->read(ROOT_PATH.'lib/test_directory_reader/.');
    //$reader->read(ROOT_PATH.'lib/test_directory_reader', '.');
  }

  public function testReadRelativePath() {
    $reader = new DirectoryReader(null);
    //$reader->read(null, 'lib/test_directory_reader/.');
  }

  public function testReadRecursively() {
    $reader = new DirectoryReader(null);
    //$reader->read('lib/test_directory_reader');
  }

  public function testFullPath() {
    
  }

  public function testRootPathIsFullPath() {
    
  }

  public function testRelativePathIsFileName() {
    
  }
}