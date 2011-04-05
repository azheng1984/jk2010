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
    $this->expectOutput(
      '[command]',
      '  test(argument = NULL)'
    );
    self::$runner->run(array('sub' => array('test' => 'TestCommand')), null);
  }

  public function testClassIsNotDefined() {
    $this->setExpectedCommandException('Class is not defined');
    self::$runner->run(array(), null);
  }

  public function testClassDoesNotExist() {
    $this->setExpectedCommandException('Class Unkonwn does not exist');
    self::$runner->run(array('class' => 'Unkonwn'), null);
  }

  public function testMethodDoesNotExist() {
    $this->setExpectedCommandException(
      'Method MethodDoesNotExistCommand::execute() does not exist'
    );
    self::$runner->run(array('class' => 'MethodDoesNotExistCommand'), null);
  }

  public function testArgumentLengthIsNotMatched() {
    $this->setExpectedCommandException();
    self::$runner->run(
      array('class' => 'TestCommand'),
      array('argument', 'additional_argument')
    );
  }

  public function testInfiniteCommand() {
    self::$runner->run(
      array('class' => 'TestCommand', 'infinite'),
      array('argument', 'additional_argument')
    );
  }
}