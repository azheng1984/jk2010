<?php
class NewCommandTest extends FileGenerationTestCase {
  protected function tearDown() {
    if (file_exists('test')) {
      unlink('test');
    }
  }

  public function testInvalidType() {
    $this->setExpectedException(
      'CommandException', "Application type 'unknown' is invalid"
    );
    $command = new NewCommand;
    $command->execute('unknown', null);
  }

  public function testLocalHyperfrmawork() {
    $command = new NewCommand;
    $relativePath = 'vendor'.DIRECTORY_SEPARATOR.'hyperframework';
    $command->execute(
      'test', $_SERVER['PWD'].DIRECTORY_SEPARATOR.$relativePath
    );
    $this->assertSame(
      'ROOT_PATH.'.var_export($relativePath, true).PHP_EOL
        .'ROOT_PATH.HYPERFRAMEWORK_PATH',
      file_get_contents('test')
    );
  }

  public function testSystemLevelHyperfrmawork() {
    $command = new NewCommand;
    $command->execute('test', 'folder');
    $this->assertSame(
      "'folder'".PHP_EOL.'HYPERFRAMEWORK_PATH', file_get_contents('test')
    );
  }
}