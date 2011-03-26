<?php
require dirname(dirname(dirname(__FILE__))).'/lib/Application.php';
if (!defined('ROOT_PATH')) {
  define('ROOT_PATH', dirname(dirname(__FILE__)).'/fixture/');
}
require ROOT_PATH.'/lib/TestProcessor.php';
define('CACHE_PATH', ROOT_PATH.'cache/');

class ApplicationTest extends PHPUnit_Framework_TestCase {
  private $cachePath;

  public function setUp() {
    $this->cachePath = CACHE_PATH."application.cache.php";
    $cache = array(
      array('Test' => 'TestProcessor'),
      '/' => array('Test' => 'test_processor_cache')
    );
    file_put_contents(
      $this->cachePath, "<?php return ".var_export($cache, true).";"
    );
  }

  public function testRun() {
    $_SERVER['REQUEST_URI'] = '/';
    $app = new Application;
    $app->run();
    $this->assertEquals('TestProcessor.run', $_ENV['callback']);
    $this->assertEquals('test_processor_cache', $_ENV['callback_argument']);
  }

  public function tearDown() {
    unlink($this->cachePath);
  }
}