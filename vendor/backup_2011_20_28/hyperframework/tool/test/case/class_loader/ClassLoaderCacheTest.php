<?php
class ClassLoaderCacheTest extends PHPUnit_Framework_TestCase {
  public function testConflict() {
    $this->setExpectedException(
      'Exception',
      "Conflict class 'Test':".PHP_EOL
        .DIRECTORY_SEPARATOR.'Test.php'.PHP_EOL
        .DIRECTORY_SEPARATOR.'Duplication.php'
    );
    $cache = new ClassLoaderCache;
    $cache->append('Test', DIRECTORY_SEPARATOR.'Test.php', null, null);
    $cache->append('Test', DIRECTORY_SEPARATOR.'Duplication.php', null, null);
  }

  public function testCurrentWorkingFolder() {
    $this->append(
      DIRECTORY_SEPARATOR.'Test.php',
      null,
      null,
      array(array('Test' => true), array())
    );
  }

  public function testRelativeFolderWithoutRootFolder() {
    $cache = new ClassLoaderCache;
    $this->append(
      DIRECTORY_SEPARATOR.'relative_folde'.DIRECTORY_SEPARATOR.'Test.php',
      'relative_folder',
      null,
      array(array('Test' => 0), array('relative_folder'))
    );
  }

  public function testRootFolderWithoutRelativeFolder() {
    $rootFolder = DIRECTORY_SEPARATOR.'root_folder';
    $this->append(
      $rootFolder.DIRECTORY_SEPARATOR.'Test.php',
      null,
      $rootFolder,
      array(
        array('Test' => 0),
        array(array($rootFolder)))
    );
  }

  public function testRelativeFolderWithRootFolder() {
    $relativeFolder = 'relative_folder';
    $rootFolder = DIRECTORY_SEPARATOR.'root_folder';
    $this->append(
      $rootFolder.DIRECTORY_SEPARATOR
        .$relativeFolder.DIRECTORY_SEPARATOR.'Test.php',
      $relativeFolder,
      $rootFolder,
      array(
        array('Test' => 1),
        array(array($rootFolder), array($relativeFolder, 0))
      )
    );
  }

  private function append($fullPath, $relativeFolder, $rootFolder, $expected) {
    $cache = new ClassLoaderCache;
    $cache->append('Test', $fullPath, $relativeFolder, $rootFolder);
    $this->assertEquals(array('class_loader', $expected), $cache->export());
  }
}