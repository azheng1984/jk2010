<?php
abstract class CliTestCase extends PHPUnit_Framework_TestCase {
  protected function assertOutput($line/*, ...*/) {
    $this->assertEquals(
      implode(PHP_EOL, func_get_args()).PHP_EOL, ob_get_contents()
    );
  }

  protected function setExpectedCommandException($message = null) {
    $this->setExpectedException('CommandException', $message, 1);
  }
}