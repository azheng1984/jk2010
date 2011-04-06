<?php
class MethodExplorerTest extends ExplorerTestCase {
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
    ExplorerContext::getExplorer('Method')->render(
      'method-name', 'execute',  array('class' => 'TestCommand', 'infinite')
    );
    $this->assertOutput(
      'method-name(argument = NULL, ...)'
    );
  }

  private function renderWithoutArgumentList($method, $config) {
    ExplorerContext::getExplorer('Method')->render(
      'method-name', $method, $config
    );
    $this->assertOutput(
      'method-name'
    );
  }
}