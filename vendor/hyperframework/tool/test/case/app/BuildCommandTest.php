<?php
class BuildCommandTest extends FileGenerationTestCase {
  private static $cacheFolder;
  private static $configFolder;
  private static $testCachePath;
  private static $testConfigPath;

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$cacheFolder = 'cache'.DIRECTORY_SEPARATOR;
    self::$configFolder = 'config'.DIRECTORY_SEPARATOR;
    mkdir(self::$configFolder);
    self::$testCachePath = self::$cacheFolder.'test.cache.php';
    self::$testConfigPath = self::$configFolder.'build.config.php';
  }

  public static function tearDownAfterClass() {
    rmdir(self::$cacheFolder);
    rmdir(self::$configFolder);
    parent::tearDownAfterClass();
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  protected function tearDown() {
    if (is_file(self::$testConfigPath)) {
      unlink(self::$testConfigPath);
    }
    if (is_file(self::$testCachePath)) {
      unlink(self::$testCachePath);
    }
  }

  public function testConfigNotFound() {
    $this->setExpectedException(
      'CommandException',
      "Can't find the '".self::$testConfigPath."'"
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
        self::$testConfigPath, '<?php return '.var_export($config, true).';'
      );
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
    $cacheVerifier = new TestCacheVerifier;
    $cacheVerifier->verify($this, self::$testCachePath);
  }
}