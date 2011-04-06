<?php
class CommandExplorerTest extends CliTestCase {
  protected function tearDown() {
    ExplorerContext::reset();
  }

  public function testRenderHead() {
    $this->expectOutput(
      'test-command',
      '  test-description'
    );
    ExplorerContext::getExplorer('Command')->render('test-command', array(
      'description' => 'test-description',
    ));
  }

  public function testRenderOptionList() {
    $this->expectOutput(
      '[option]',
      '  --test-object-option(argument)',
      '  --test-flag-option'
    );
    ExplorerContext::getExplorer('Command')->render(null, array(
      'option' => array(
        'test-object-option' => 'TestOption',
        'test-flag-option',
      ),
    ));
  }
}