<?php
class ApplicationTest extends PHPUnit_Framework_TestCase {
  private static $app;
  private $inexistentPath = '/inexistent_path';

  public static function setUpBeforeClass() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    self::$app = new Application;
  }

  protected function setUp() {
    $GLOBALS['TEST_CALLBACK_TRACE'] = array();
    $_SERVER['REQUEST_URI'] = $this->inexistentPath;
  }

  public function testPathWithParameter() {
    $_SERVER['REQUEST_URI'] = '/?key=value';
    self::$app->run();
    $this->verifyCallback();
  }

  public function testRewritePath() {
    self::$app->run('/');
    $this->verifyCallback();
  }

  public function testPathNotFound() {
    $this->setExpectedException(
      'NotFoundException', "Path '$this->inexistentPath' not found"
    );
    try {
      self::$app->run();
    } catch (NotFoundException $exception) {
      $this->assertEquals(0, count($GLOBALS['TEST_CALLBACK_TRACE']));
      throw $exception;
    }
  }

  private function verifyCallback() {
    $this->assertEquals(2, count($GLOBALS['TEST_CALLBACK_TRACE']));
    $this->assertEquals('TestAction->GET', $GLOBALS['TEST_CALLBACK_TRACE'][0]);
    $this->assertEquals(
      'TestScreen->render', $GLOBALS['TEST_CALLBACK_TRACE'][1]
    );
  }
}