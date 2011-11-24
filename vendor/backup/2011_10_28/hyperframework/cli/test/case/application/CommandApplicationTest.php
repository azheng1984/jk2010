<?php
class CommandApplicationTest extends CliTestCase {
  private static $configPath;

  public static function setUpBeforeClass() {
    self::$configPath = CONFIG_PATH.'command_application.config.php';
  }

  public static function tearDownAfterClass() {
    unlink(self::$configPath);
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
  }

  public function testStringConfig() {
    $this->runApplication('TestCommand');
    $this->verifyCallback();
  }

  public function testExpansion() {
    $this->runApplication(
      array('sub' => array(
        'alias' => array('expansion' => 'test'), 'test' => 'TestCommand'
      )),
      array('alias')
    );
    $this->verifyCallback();
  }

  public function testStartWithDashArgument() {
    $this->runApplication(
      array('sub' => array('test' => 'TestCommand')),
      array('test', '--', '-test')
    );
    $this->assertSame(1, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->verifyCallback('-test');
  }

  public function testDashArgument() {
    $this->runApplication(array('class' => 'TestCommand'), array('-'));
    $this->verifyCallback('-');
  }

  public function testTopLevelOption() {
    $this->runApplication(
      array('option' => 'top_level_option', 'class' => 'TestCommand'),
      array('--top_level_option')
    );
    $this->assertSame(
      true, $GLOBALS['TEST_CALLBACK_TRACE'][0]['option']['top_level_option']
    );
  }

  public function testSecondLevelOption() {
    $this->runApplication(
      array('sub' => array(
        'test' => array(
          'class' => 'TestCommand', 'option' => 'second_level_optoin'
        )
      )),
      array('test', '--second_level_optoin')
    );
    $this->assertSame(true, 
      true, $GLOBALS['TEST_CALLBACK_TRACE'][0]['option']['second_level_optoin']
    );
  }

  public function testUndefinedCommand() {
    $this->setExpectedCommandException("Command 'test' not found");
    $this->runApplication(array(), array('test'));
  }

  public function testUndefinedOption() {
    $this->setExpectedCommandException();
    $this->runApplication(array(), array('--test'));
  }

  private function runApplication($config = array(), $arguments = array()) {
    file_put_contents(
      self::$configPath, '<?php return '.var_export($config, true).';'
    );
    call_user_func_array(array($this, 'setInputArguments'), $arguments);
    $app = new CommandApplication;
    $app->run();
  }

  private function verifyCallback($argument = null) {
    $trace = $GLOBALS['TEST_CALLBACK_TRACE'];
    $this->assertSame(1, count($trace));
    $this->assertSame('TestCommand->execute', $trace[0]['name']);
    $this->assertSame($argument, $trace[0]['argument']);
  }
}