<?php
class ActionHandlerTest extends PHPUnit_Framework_TestCase {
  public function testNotAction() {
    $this->assertNull($this->handle('Test.php'));
  }

  public function testPublicLowerCaseMethod() {
    $fullPath = ROOT_PATH.'app/TestPublicLowerCaseMethodAction.php';
    $this->setExpectedException(
      'Exception', "Invalid action method 'get' in $fullPath"
    );
    $this->handle($fullPath);
  }

  public function testUpperCaseMethod() {
    $this->assertSame(
      array('class' => 'TestAction', 'method' => array('GET')),
      $this->handle(ROOT_PATH.'app/TestAction.php')
    );
  }

  private function handle($fullPath) {
    $handler = new ActionHandler;
    return $handler->handle(basename($fullPath), $fullPath);
  }
}