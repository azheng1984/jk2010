<?php
class CommandRunnerTest extends PHPUnit_Extensions_OutputTestCase {
  private static $runner;

  public static function setUpBeforeClass() {
    self::$runner = new CommandRunner;
  }

  public function testPackage() {
    self::$runner->run(array('sub' => array('test' => 'TestCommand')), null);
    $this->expectOutputString(
      '[command]'.PHP_EOL.'  test(argument = NULL)'.PHP_EOL
    );
  }

  /**
   * @expectedException CommandException
   * @expectedExceptionMessage Class not defined
   */
  public function testClassNotDefined() {
    self::$runner->run(array(), null);
  }

  /**
   * @expectedException CommandException
   */
  public function testArgumentNotMatched() {
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