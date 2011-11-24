<?php
class CommandRunnerTest extends CliTestCase {
  private static $runner;

  public static function setUpBeforeClass() {
    self::$runner = new CommandRunner;
  }

  public static function tearDownAfterClass() {
    ExplorerContext::reset();
  }

  public function testRenderPackageExplorer() {
    ob_start();
    self::$runner->run(
      array('sub' => array('test' => 'TestCommand')), null, null
    );
    $this->assertOutput(
      '[command]',
      '  test(argument = NULL)'
    );
    ob_end_clean();
  }

  public function testClassIsNotDefined() {
    $this->setExpectedCommandException('Command class not defined');
    self::$runner->run(array(), null, null);
  }

  public function testClassDoesNotExist() {
    $class = 'Unknown';
    $this->setExpectedCommandException("Class $class does not exist");
    self::$runner->run(array('class' => $class), null, null);
  }

  public function testMethodDoesNotExist() {
    $this->setExpectedCommandException(
      'Method NoMethodCommand::execute() does not exist'
    );
    self::$runner->run(
      array('class' => 'NoMethodCommand'), null, null
    );
  }

  public function testArgumentLengthError() {
    $this->setExpectedCommandException();
    self::$runner->run(
      array('class' => 'TestCommand'),
      null,
      array('argument', 'additional_argument')
    );
  }

  public function testInfiniteCommand() {
    self::$runner->run(
      array('class' => 'TestCommand', 'infinite'),
      null,
      array('argument', 'additional_argument')
    );
  }
}