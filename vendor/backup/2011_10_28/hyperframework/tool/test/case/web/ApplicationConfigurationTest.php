<?php
class ApplicationConfigurationTest extends PHPUnit_Framework_TestCase {
  public function testConfigIsUnacceptable() {
    $this->setExpectedException(
      'Exception', "Application handler 'Action' do not accept config"
    );
    $this->extract(array('Action' => 'config'));
  }

  public function testNoConfigIsUnacceptable() {
    $this->setExpectedException(
      'Exception', "Application handler 'View' must contain config"
    );
    $this->extract('View');
  }

  public function testCreateHandlerWithConfig() {
    $handlers = $this->extract(array('View' => 'Screen'));
    $this->assertTrue($handlers['View'] instanceof ViewHandler);
  }

  public function testCreateHandlerWithoutConfig() {
    $handlers = $this->extract('Action');
    $this->assertTrue($handlers['Action'] instanceof ActionHandler);
  }

  private function extract($config) {
    $configuration = new ApplicationConfiguration;
    return $configuration->extract($config);
  }
}