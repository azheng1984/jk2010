<?php
class ApplicationTest extends PHPUnit_Framework_TestCase {
  private $cachePath;

  protected function setUp() {
    $this->cachePath = CACHE_PATH."application.cache.php";
    $cache = array(
      array('Test' => 'TestProcessor'),
      '/' => array('Test' => 'test')
    );
    file_put_contents(
      $this->cachePath, "<?php return ".var_export($cache, true).";"
    );
  }

  public function testRun() {
    $_SERVER['REQUEST_URI'] = '/';
    $app = new Application;
    $app->run();
    $this->assertEquals('TestProcessor->run', $_ENV['callback']);
    $this->assertEquals('test', $_ENV['callback_argument']);
  }

  protected function tearDown() {
    unlink($this->cachePath);
  }
}