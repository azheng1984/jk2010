<?php
abstract class CliTestCase extends PHPUnit_Framework_TestCase {
  protected function setInputArguments(/*...*/) {
    $arguments = func_get_args();
    $_SERVER['argc'] = array_unshift($arguments, 'index.php');
    $_SERVER['argv'] = $arguments;
  }

  protected function assertOutput(/*...*/) {
    $output = $this->getOutput(func_get_args());
    $this->assertSame($output, ob_get_contents());
  }

  protected function setExpectedCommandException($message = null) {
    $this->setExpectedException('CommandException', $message, 1);
  }

  private function getOutput($lines) {
    if (count($lines) !== 0) {
      return implode(PHP_EOL, $lines).PHP_EOL;
    }
    return '';
  }
}