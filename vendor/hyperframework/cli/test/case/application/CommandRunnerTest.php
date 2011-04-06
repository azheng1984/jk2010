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
    $this->setExpectedCommandException('Class is not defined');
    self::$runner->run(array(), null, null);
  }

  public function testClassDoesNotExist() {
    $this->setExpectedCommandException('Class Unkonwn does not exist');
    self::$runner->run(array('class' => 'Unkonwn'), null, null);
  }

  public function testMethodDoesNotExist() {
    $this->setExpectedCommandException(
      'Method MethodDoesNotExistCommand::execute() does not exist'
    );
    self::$runner->run(
      array('class' => 'MethodDoesNotExistCommand'), null, null
    );
  }

  public function testArgumentLengthIsNotMatched() {
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