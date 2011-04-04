<?php
class ApplicationTest extends PHPUnit_Framework_TestCase {
  private static $app;
  private $inexistentPath = '/inexistent_path';

  public static function setUpBeforeClass() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    self::$app = new Application;
  }

  protected function setUp() {
    $_ENV['callback_trace'] = array();
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
      $this->assertEquals(0, count($_ENV['callback_trace']));
      throw $exception;
    }
  }

  private function verifyCallback() {
    $this->assertEquals(2, count($_ENV['callback_trace']));
    $this->assertEquals('TestAction->GET', $_ENV['callback_trace'][0]);
    $this->assertEquals('TestScreen->render', $_ENV['callback_trace'][1]);
  }
}