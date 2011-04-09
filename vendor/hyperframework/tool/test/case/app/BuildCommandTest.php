<?php
class BuildCommandTest extends PHPUnit_Framework_TestCase {
  private static $backupFiles = array();
  private static $testCachePath;

  public static function setUpBeforeClass() {
    self::backup();
    self::$testCachePath = ROOT_PATH
      .'cache'.DIRECTORY_SEPARATOR.'test.cache.php';
    $_SERVER['PWD'] = ROOT_PATH;
  }

  public static function tearDownAfterClass() {
    self::restore();
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  protected function tearDown() {
    if (is_file(self::$testCachePath)) {
      unlink(self::$testCachePath);
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

  private static function backup() {
    $targets = array(
      'config'.DIRECTORY_SEPARATOR.'build.config.php',
      'cache'.DIRECTORY_SEPARATOR.'class_loader.cache.php'
    );
    foreach ($targets as $target) {
      $path = ROOT_PATH.$target;
      self::$backupFiles[$path] = file_get_contents($path);
      unlink($path);
    }
  }

  private static function restore() {
      foreach (self::$backupFiles as $path => $data) {
      file_put_contents($path, $data);
    }
  }

  private function execute($config = array('Test')) {
    if ($config !== null) {
      file_put_contents(
        $_SERVER['PWD'].'config'.DIRECTORY_SEPARATOR.'build.config.php',
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
    $this->assertTrue(file_exists(self::$testCachePath));
  }
}