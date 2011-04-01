<?php
class ApplicationTest extends PHPUnit_Framework_TestCase {
  private static $app;

  public static function setUpBeforeClass() {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    self::$app = new Application;
  }

  public function testPathWithParameter() {
    $_SERVER['REQUEST_URI'] = '/?key=value';
    $_ENV['callback'] = array();
    self::$app->run();
    $this->assertEquals(2, count($_ENV['callback']));
    $this->assertEquals('TestAction->GET', $_ENV['callback'][0]);
    $this->assertEquals('TestScreen->render', $_ENV['callback'][1]);
  }

  /**
   * @expectedException NotFoundException
   * @expectedExceptionMessage Path '/inexistent_path' not found
   */
  public function testPathNotFound() {
    $_SERVER['REQUEST_URI'] = '/inexistent_path';
    self::$app->run();
  }

  public function testRewritePath() {
    $_SERVER['REQUEST_URI'] = '/inexistent_path';
    self::$app->run('/');
  }
}