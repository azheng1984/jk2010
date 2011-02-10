<?php
require ROOT_PATH.'lib/Application.php';

class ApplicationTest extends PHPUnit_Framework_TestCase {
  private $processorMock;
  private $processorMockCachePath;

  public function setUp() {
    $this->processorMock = $this->getMock('Processor', array('run'));
    $mockClassName = get_class($this->processorMock);
    $this->processorMockCachePath = TEST_PATH."cache/Processor/$mockClassName.cache.php";
    file_put_contents($this->processorMockCachePath, "<?php return array('test' => array('hi'));");
  }

  public function testRun() {
    $this->processorMock->expects($this->once())->method('run')->with($this->equalTo(array('hi')));
    $app = new Application($this->processorMock);
    $app->run('test');
  }

  public function tearDown() {
    unlink($this->processorMockCachePath);
  }
}