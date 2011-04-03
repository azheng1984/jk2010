<?php
class CommandApplicationTest extends PHPUnit_Framework_TestCase {
  private static $configPath;

  public static function setUpBeforeClass() {
    self::$configPath = CONFIG_PATH.'command_application.config.php';
  }

  public static function tearDownAfterClass() {
    if (file_exists(self::$configPath)) {
      unlink(self::$configPath);
    }
  }

  protected function setUp() {
    $_ENV['callback_trace'] = array();
  }

  public function testParseOption() {
    $this->runApplication(
      array('sub' => array('test' => 'TestCommand')),
      array('test', '--', '-test')
    );
    $this->assertEquals(1, count($_ENV['callback_trace']));
    $trace = $_ENV['callback_trace'][0];
    $this->assertEquals('TestCommand.execute', $trace['name']);
    $this->assertEquals('-test', $trace['argument']);
  }

  public function testParseArgument() {
    $this->runApplication(array('class' => 'TestCommand'), array('-'));
    $this->assertEquals(1, count($_ENV['callback_trace']));
    $this->assertEquals('-', $_ENV['callback_trace'][0]['argument']);
  }

  public function testStringConfig() {
    $this->runApplication('TestCommand');
    $this->assertEquals(1, count($_ENV['callback_trace']));
    $this->assertEquals(
      'TestCommand.execute', $_ENV['callback_trace'][0]['name']
    );
  }

  public function testExpansion() {
    $this->runApplication(
      array('sub' => array(
        'alias' => array('expansion' => 'test'), 'test' => 'TestCommand'
      )),
      array('alias')
    );
    $this->assertEquals('TestCommand.execute', $_ENV['callback_trace'][0]['name']);
  }

  public function testTopLevelCommandOption() {
    $this->runApplication(
      array('option' => 'test', 'class' => 'TestCommand'), array('--test')
    );
    $this->assertEquals(true, $_ENV['option']['test']);
  }

  public function testSecondLevelCommandOption() {
    $this->runApplication(
      array('sub' => array(
        'test' => array('class' => 'TestCommand', 'option' => 'test')
      )),
      array('test', '--test')
    );
    $this->assertEquals(true, $_ENV['option']['test']);
  }

  /**
   * @expectedException CommandException
   */
  public function testOptionUndefined() {
    $this->runApplication(array(), array('--test'));
  }

  /**
   * @expectedException CommandException
   * @expectedExceptionMessage Command 'test' not found
   */
  public function testCommandUndefined() {
    $this->runApplication(array(), array('test'));
  }

  private function runApplication($config = array(), $input = array()) {
    $this->setConfig($config);
    $this->setInput($input);
    $app = new CommandApplication;
    $app->run();
  }

  private function setConfig($value) {
    file_put_contents(
      self::$configPath, '<?php return '.var_export($value, true).';'
    );
  }

  private function setInput($value) {
    $_SERVER['argc'] = array_unshift($value, 'index.php');
    $_SERVER['argv'] = $value;
  }
}