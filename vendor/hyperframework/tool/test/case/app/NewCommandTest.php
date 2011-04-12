<?php
class NewCommandTest extends PHPUnit_Framework_TestCase {
  public static function setUpBeforeClass() {
    $_SERVER['OLD_PWD'] = $_SERVER['PWD'];
    $_SERVER['PWD'] = ROOT_PATH.'tmp';
    mkdir($_SERVER['PWD']);
    chdir($_SERVER['PWD']);
  }

  public static function tearDownAfterClass() {
    chdir($_SERVER['OLD_PWD']);
    rmdir($_SERVER['PWD']);
    $_SERVER['PWD'] = $_SERVER['OLD_PWD'];
  }

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

  public function testHyperfrmaworkIncluded() {
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

  public function testHyperfrmaworkIsGlobal() {
    $command = new NewCommand;
    $command->execute('test', 'folder');
    $this->assertSame(
      "'folder'".PHP_EOL.'HYPERFRAMEWORK_PATH', file_get_contents('test')
    );
  }
}