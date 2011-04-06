<?php
class CommandExplorerTest extends ExplorerTestCase {
  public function testRenderHead() {
    ExplorerContext::getExplorer('Command')->render(
      'test-command', array('description' => 'test-description')
    );
    $this->assertOutput(
      'test-command',
      '  test-description'
    );
  }

  public function testRenderOptionList() {
    ExplorerContext::getExplorer('Command')->render(
      null, array(
        'option' => array(
          'test-object-option' => 'TestOption',
          'test-flag-option',
        ),
      )
    );
    $this->assertOutput(
      '[option]',
      '  --test-object-option(argument)',
      '  --test-flag-option'
    );
  }
}