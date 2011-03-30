<?php
define('LIB_PATH', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR);
require LIB_PATH.'application/CommandRunner.php';
require LIB_PATH.'CommandReader.php';
require LIB_PATH.'ArgumentVerifier.php';
require LIB_PATH.'CommandException.php';
require LIB_PATH.'application/CommandApplication.php';
require LIB_PATH.'option/OptionParser.php';
require LIB_PATH.'option/OptionNameParser.php';
require LIB_PATH.'option/OptionArgumentParser.php';

if (!defined('ROOT_PATH')) {
  define('ROOT_PATH', dirname(dirname(dirname(__FILE__))).'/fixture/');
}
define('CONFIG_PATH', ROOT_PATH.'config/');
require ROOT_PATH.'app/TestCommand.php';

class CommandApplicationTest extends PHPUnit_Framework_TestCase  {
  public function testRun() {
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return array('sub' => array('test' => 'TestCommand'));");
    $_SERVER['argc'] = '4';
    $_SERVER['argv'] = array('index.php', 'test', '--', '-test_argument');
    $app = new CommandApplication;
    $app->run();
    $this->assertEquals('TestCommand.execute', $_ENV['callback']);
    $this->assertEquals('-test_argument', $_ENV['argument']);
    $_SERVER['argc'] = '3';
    $_SERVER['argv'] = array('index.php', 'test', '-');
    $app = new CommandApplication;
    $app->run();
    $this->assertEquals('-', $_ENV['argument']);
  }

  public function testStringConfig() {
    $_SERVER['argc'] = '1';
    $_SERVER['argv'] = array('index.php');
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return 'TestCommand';");
    $app = new CommandApplication;
    $app->run();
    $this->assertEquals('TestCommand.execute', $_ENV['callback']);
  }

  public function testExpansion() {
    $_SERVER['argc'] = '2';
    $_SERVER['argv'] = array('index.php', 'alias');
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return array('sub' => array('alias' => array('expansion' => 'test'), 'test' => 'TestCommand'));");
    $app = new CommandApplication;
    $app->run();
    $this->assertEquals('TestCommand.execute', $_ENV['callback']);
  }

  public function testOption() {
    $_SERVER['argc'] = '2';
    $_SERVER['argv'] = array('index.php', '--test');
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return array('option' => 'test', 'class' => 'TestCommand');");
    $app = new CommandApplication;
    $app->run();
    $this->assertEquals(true, $_ENV['option']['test']);
    $_SERVER['argc'] = '3';
    $_SERVER['argv'] = array('index.php', 'test', '--test');
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return array('sub' => array('test' => array('class' => 'TestCommand', 'option' => 'test')));");
    $app = new CommandApplication;
    $app->run();
    $this->assertEquals(true, $_ENV['option']['test']);
  }

  /**
   * @expectedException CommandException
  */
  public function testOptionNotConfig() {
    $_SERVER['argc'] = '2';
    $_SERVER['argv'] = array('index.php', '--test');
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return array();");
    $app = new CommandApplication;
    $app->run();
  }

  /**
   * @expectedException CommandException
   * @expectedExceptionMessage Command 'test' not found
   */
  public function testCommandNotFound() {
    $_SERVER['argc'] = '2';
    $_SERVER['argv'] = array('index.php', 'test');
    file_put_contents(CONFIG_PATH.'command_application.config.php', "<?php return array();");
    $app = new CommandApplication;
    $app->run();
  }
}