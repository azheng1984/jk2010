<?php
class BuildCommandTest extends PHPUnit_Framework_TestCase {
  private static $backupFiles = array();

  public static function setUpBeforeClass() {
    $targets = array('config/build.config.php', 'cache/class_loader.cache.php');
    foreach ($targets as $target) {
      $path = ROOT_PATH.$target;
      self::$backupFiles[$path] = file_get_contents($path);
      unlink($path);
    }
    $_SERVER['PWD'] = ROOT_PATH;
  }

  public static function tearDownAfterClass() {
    foreach (self::$backupFiles as $path => $data) {
      file_put_contents($path, $data);
    }
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  protected function tearDown() {
    $path = ROOT_PATH.'cache/test.cache.php';
    if (is_file($path)) {
      unlink($path);
    }
  }

  public function testConfigNotFound() {
    $this->setExpectedException(
      'CommandException',
      "Can't find the 'config".DIRECTORY_SEPARATOR."build.config.php'"
    );
    $this->execute(null);
  }

  public function testConfigIsString() {
    $this->execute('Test');
    $this->verify();
  }

  public function testBuilderClassDoesNotExist() {
    $this->setExpectedException(
      'CommandException', 'Class UnknownBuilder does not exist'
    );
    $this->execute(array('Unknown'));
  }

  public function testDispatchWithoutConfig() {
    $this->execute();
    $this->verify();
  }

  public function testDispatchWithConfig() {
    $this->execute(array('Test' => 'config'));
    $this->verify('config');
  }

  public function testRethrowDispatchException() {
    $this->setExpectedException(
      'CommandException', 'ThrowExceptionBuilder->build'
    );
    $this->execute(array('ThrowException'));
  }

  private function execute($config = array('Test')) {
    if ($config !== null) {
      file_put_contents(
        $_SERVER['PWD'].'config/build.config.php',
        '<?php return '.var_export($config, true).';');
    }
    $command = new BuildCommand;
    $command->execute();
  }

  private function verify($argument = null) {
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertSame(
      'TestBuilder->build', $GLOBALS['TEST_CALLBACK_TRACE'][0]['method']
    );
    $this->assertSame(
      $argument, $GLOBALS['TEST_CALLBACK_TRACE'][0]['argument']
    );
    $this->assertSame(
      '<?php'.PHP_EOL."return 'data';",
      file_get_contents(ROOT_PATH.'cache/test.cache.php')
    );
  }
}