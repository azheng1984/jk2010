<?php
class BuildCommandTest extends PHPUnit_Framework_TestCase {
  public function testExecute() {
    $_SERVER['PWD'] = dirname(dirname(__FILE__));
    $command = new BuildCommand;
    $command->execute();
  }
}