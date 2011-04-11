<?php
class ApplicationHandlerTest extends PHPUnit_Framework_TestCase {
  public function testHandle() {
    $handlers = array('Action' => new ActionHandler);
    $cache = new ApplicationCache($handlers);
    $handler = new ApplicationHandler($handlers, $cache);
    $handler->handle(
      ROOT_PATH.'app'.DIRECTORY_SEPARATOR.'TestAction.php', 'app'
    );
    $result = $cache->export();
    $this->assertTrue(count($result[1]) > 0);
  }
}