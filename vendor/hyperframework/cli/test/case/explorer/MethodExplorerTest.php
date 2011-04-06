<?php
class MethodExplorerTest extends CliTestCase {
  protected function tearDown() {
    ExplorerContext::reset();
  }

  public function testNoClass() {
    $this->renderWithoutArgumentList(
      null, array()
    );
  }

  public function testUnknownClass() {
    $this->renderWithoutArgumentList(
      null, array('class' => 'Unknown')
    );
  }

  public function testUnknownMethod() {
    $this->renderWithoutArgumentList('unknown', array('TestCommand'));
  }

  public function testRenderArgumentList() {
    $this->expectOutput(
      'method-name(argument = NULL, ...)'
    );
    ExplorerContext::getExplorer('Method')->render(
      'method-name', 'execute',  array('class' => 'TestCommand', 'infinite')
    );
  }

  private function renderWithoutArgumentList($method, $config) {
    $this->expectOutput(
      'method-name'
    );
    ExplorerContext::getExplorer('Method')->render(
      'method-name', $method, $config
    );
  }
}