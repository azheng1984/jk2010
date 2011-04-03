<?php
class CommandRunnerTest extends PHPUnit_Extensions_OutputTestCase {
  public function testPackage() {
    $this->runCommand(array('sub' => array('test' => 'TestCommand')), null);
    $this->expectOutputString(
      '[command]'.PHP_EOL.'  test(argument = NULL)'.PHP_EOL
    );
  }

  /**
   * @expectedException CommandException
   * @expectedExceptionMessage Class not defined
   */
  public function testClassNotFound() {
    $this->runCommand(array(), null);
  }

  /**
   * @expectedException CommandException
   */
  public function testArgumentNotMatched() {
    $this->runCommand(
      array('class' => 'TestCommand'),
      array('argument', 'additional_argument')
    );
  }

  public function testInfiniteCommand() {
    $this->runCommand(
      array('class' => 'TestCommand', 'infinite'),
      array('argument', 'additional_argument')
    );
  }

  private function runCommand($config, $arguments) {
    $runner = new CommandRunner;
    $runner->run($config, $arguments);
  }
}