<?php
class ActionHandlerTest extends PHPUnit_Framework_TestCase {
  public function testNotAction() {
    $this->assertNull($this->handle('Test', 'Test.php'));
  }

  public function testPublicLowerCaseMethod() {
    $fullPath = ROOT_PATH.'app/TestPublicLowerCaseMethodAction.php';
    $this->setExpectedException(
      'Exception', "Invalid action method 'get' in '$fullPath'"
    );
    $this->handle('TestPublicLowerCaseMethodAction', $fullPath);
  }

  public function testPublicUpperCaseMethod() {
    $this->assertSame(
      array('class' => 'TestAction', 'method' => array('GET')),
      $this->handle('TestAction', ROOT_PATH.'app/TestAction.php')
    );
  }

  private function handle($class, $fullPath) {
    $handler = new ActionHandler;
    return $handler->handle($class, $fullPath);
  }
}