<?php
class CliTestCase extends PHPUnit_Extensions_OutputTestCase {
  protected function expectOutput($line/*, ...*/) {
    $this->expectOutputString(implode(PHP_EOL, func_get_args()).PHP_EOL);
  }

  protected function setExpectedCommandException($message = null) {
    $this->setExpectedException('CommandException', $message, 1);
  }
}