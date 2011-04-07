<?php
abstract class CliTestCase extends PHPUnit_Framework_TestCase {
  protected function setInputArguments() {
    $arguments = func_get_args();
    $_SERVER['argc'] = array_unshift($arguments, 'index.php');
    $_SERVER['argv'] = $arguments;
  }

  protected function assertOutput() {
    $output = null;
    $lines = func_get_args();
    if (count($lines) !== 0) {
      $output = implode(PHP_EOL, func_get_args()).PHP_EOL;
    }
    $this->assertEquals($output, ob_get_contents());
  }

  protected function setExpectedCommandException($message = null) {
    $this->setExpectedException('CommandException', $message, 1);
  }
}