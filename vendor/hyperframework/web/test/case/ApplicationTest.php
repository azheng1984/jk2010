<?php
class ApplicationTest extends PHPUnit_Framework_TestCase {
  private static $app;

  public static function setUpBeforeClass() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    self::$app = new Application;
  }

  protected function setUp() {
    $_ENV['callback'] = array();
  }

  public function testPathWithParameter() {
    $_SERVER['REQUEST_URI'] = '/?key=value';
    self::$app->run();
    $this->verifyCallback();
  }

  /**
   * @expectedException NotFoundException
   * @expectedExceptionMessage Path '/inexistent_path' not found
   */
  public function testPathNotFound() {
    $_SERVER['REQUEST_URI'] = '/inexistent_path';
    try {
      self::$app->run();
    } catch (NotFoundException $exception) {
      $this->assertEquals(0, count($_ENV['callback']));
      throw $exception;
    }
  }

  public function testRewritePath() {
    $_SERVER['REQUEST_URI'] = '/inexistent_path';
    $_ENV['callback'] = array();
    self::$app->run('/');
    $this->verifyCallback();
  }

  private function verifyCallback() {
    $this->assertEquals(2, count($_ENV['callback']));
    $this->assertEquals('TestAction->GET', $_ENV['callback'][0]);
    $this->assertEquals('TestScreen->render', $_ENV['callback'][1]);
  }
}