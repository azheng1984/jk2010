<?php
!defined('TOOL_PATH')?define('TOOL_PATH', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR) : null;
!defined('CLI_LIB')?define('CLI_LIB', dirname(TOOL_PATH).'/cli/lib/') : null;
!defined('LIB_PATH')?define('LIB_PATH', TOOL_PATH.'lib'.DIRECTORY_SEPARATOR) : null;
!defined('APP_PATH')?define('APP_PATH', TOOL_PATH.'app'.DIRECTORY_SEPARATOR) : null;
require APP_PATH.'BuildCommand.php';
require TOOL_PATH.'test/fixture/lib/TestBuilder.php';
require TOOL_PATH.'test/fixture/lib/TestCache.php';
require TOOL_PATH.'test/fixture/lib/TestExportBuilder.php';
require TOOL_PATH.'test/fixture/lib/TestErrorTriggerBuilder.php';
require_once CLI_LIB.'CommandException.php';

class BuildCommandTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $_SERVER['PWD'] = TOOL_PATH.'test/fixture';
    $cachePath = $_SERVER['PWD'].'/cache';
    if (file_exists($cachePath)) {
      rmdir($cachePath);
    }
  }

  /**
   * @expectedException CommandException
   */
  public function testConfigNotFound() {
    $command = new BuildCommand;
    $command->execute();
  }

  public function testRun() {
    $configPath = $_SERVER['PWD'].'/config/build.config.php';
    file_put_contents($configPath, "<?php return array('Test' => 'test');");
    $command = new BuildCommand;
    $command->execute();
    $this->assertEquals('TestBuilder.build', $_ENV['callback']);
    $this->assertEquals('test', $_ENV['callback_argument']);
  }

  public function testRunWithoutConfig() {
    $configPath = $_SERVER['PWD'].'/config/build.config.php';
    file_put_contents($configPath, "<?php return array('Test');");
    $command = new BuildCommand;
    $command->execute();
    $this->assertEquals('TestBuilder.build', $_ENV['callback']);
    $this->assertEquals(null, $_ENV['callback_argument']);
  }
  
  /**
   * @expectedException CommandException
   * @expectedExceptionMessage ErrorTriggerBuilder::build
   */
  public function testException() {
    $configPath = $_SERVER['PWD'].'/config/build.config.php';
    file_put_contents($configPath, "<?php return array('TestErrorTrigger');");
    $command = new BuildCommand;
    $command->execute();
  }

  public function testExport() {
    $configPath = $_SERVER['PWD'].'/config/build.config.php';
    file_put_contents($configPath, "<?php return array('TestExport');");
    chdir(TOOL_PATH.'test/fixture');
    $command = new BuildCommand;
    $command->execute();
    $path = TOOL_PATH.'test/fixture/cache/test.cache.php';
    $content = '<?php'.PHP_EOL."return 'test_data';";
    $this->assertEquals($content, file_get_contents($path));
  }

  public function tearDown() {
    $configPath = $_SERVER['PWD'].'/config/build.config.php';
    if (file_exists($configPath)) {
      unlink($configPath);
    }
    $cachePath = $_SERVER['PWD'].'/cache/test.cache.php';
    if (file_exists($cachePath)) {
      unlink($cachePath);
    }
  }
}