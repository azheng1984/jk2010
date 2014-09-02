<?php
class CommandExplorerTest extends ExplorerTestCase {
  public function testRenderHead() {
    ExplorerContext::getExplorer('Command')->render(
      'command', array('description' => 'description')
    );
    $this->assertOutput(
      'command',
      '  description'
    );
  }

  public function testRenderOptionList() {
    ExplorerContext::getExplorer('Command')->render(
      null, array(
        'option' => array(
          'object_option' => 'TestOption',
          'flag_option',
        ),
      )
    );
    $this->assertOutput(
      '[option]',
      '  --object_option(argument)',
      '  --flag_option'
    );
  }
}