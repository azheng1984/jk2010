<?php
class BuildCommandTest extends PHPUnit_Framework_TestCase {
  private static $classLoaderCache;
  private static $buildConfig;

  public static function setUpBeforeClass() {
    self::$classLoaderCache = file_get_contents(
      ROOT_PATH.'cache/class_loader.cache.php'
    );
    unlink(ROOT_PATH.'cache/class_loader.cache.php');
    self::$buildConfig = file_get_contents(
      ROOT_PATH.'config/build.config.php'
    );
    unlink(ROOT_PATH.'config/build.config.php');
    $_SERVER['PWD'] = ROOT_PATH;
  }

  public static function tearDownAfterClass() {
    file_put_contents(ROOT_PATH.'cache/class_loader.cache.php', self::$classLoaderCache);
    file_put_contents(ROOT_PATH.'config/build.config.php', self::$buildConfig);
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  protected function tearDown() {
    if (is_file(ROOT_PATH.'cache/test.cache.php')) {
      unlink(ROOT_PATH.'cache/test.cache.php');
    }
  }

  public function testConfigNotFound() {
    $this->setExpectedException(
      'CommandException',
      "Can't find the 'config".DIRECTORY_SEPARATOR."build.config.php'"
    );
    $command = new BuildCommand;
    $command->execute();
  }

  public function testBuilderClassDoesNotExist() {
    $this->setExpectedException(
      'CommandException', 'Class UnknownBuilder does not exist'
    );
    $configPath = $_SERVER['PWD'].'config/build.config.php';
    file_put_contents($configPath, "<?php return array('Unknown');");
    $command = new BuildCommand;
    $command->execute();
  }

  public function testConfigIsString() {
    $configPath = $_SERVER['PWD'].'config/build.config.php';
    file_put_contents($configPath, "<?php return 'Test';");
    $command = new BuildCommand;
    $command->execute();
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertSame('TestBuilder->build', $GLOBALS['TEST_CALLBACK_TRACE'][0]['method']);
    $this->assertNull($GLOBALS['TEST_CALLBACK_TRACE'][0]['argument']);
  }

  public function testDispatchWithoutConfig() {
    $configPath = $_SERVER['PWD'].'config/build.config.php';
    file_put_contents($configPath, "<?php return array('Test');");
    $command = new BuildCommand;
    $command->execute();
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertSame('TestBuilder->build', $GLOBALS['TEST_CALLBACK_TRACE'][0]['method']);
    $this->assertNull($GLOBALS['TEST_CALLBACK_TRACE'][0]['argument']);
  }

  public function testDispatchWithConfig() {
    $configPath = $_SERVER['PWD'].'config/build.config.php';
    file_put_contents($configPath, "<?php return array('Test' => 'config');");
    $command = new BuildCommand;
    $command->execute();
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertSame('TestBuilder->build', $GLOBALS['TEST_CALLBACK_TRACE'][0]['method']);
    $this->assertSame('config', $GLOBALS['TEST_CALLBACK_TRACE'][0]['argument']);
  }

  public function testRethrowDispatchException() {
    $this->setExpectedException(
      'CommandException', 'ThrowExceptionBuilder->build'
    );
    $configPath = $_SERVER['PWD'].'config/build.config.php';
    file_put_contents($configPath, "<?php return array('ThrowException');");
    $command = new BuildCommand;
    $command->execute();
  }

  public function testExportCache() {
    rmdir( ROOT_PATH.'cache');
    $configPath = $_SERVER['PWD'].'config/build.config.php';
    file_put_contents($configPath, "<?php return array('Test');");
    chdir(ROOT_PATH);
    $command = new BuildCommand;
    $command->execute();
    $this->assertSame('0777', substr(sprintf('%o', fileperms(ROOT_PATH.'cache')), -4));
    $path = ROOT_PATH.'cache/test.cache.php';
    $content = '<?php'.PHP_EOL."return 'data';";
    $this->assertSame($content, file_get_contents($path));
  }
}