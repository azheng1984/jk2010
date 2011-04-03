<?php
class OutputTestCase extends PHPUnit_Extensions_OutputTestCase {
  protected function expectOutput($line/*, ...*/) {
    $this->expectOutputString(implode(PHP_EOL, func_get_args()).PHP_EOL);
  }
}