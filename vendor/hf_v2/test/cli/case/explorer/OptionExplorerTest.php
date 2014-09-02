<?php
class OptionExplorerTest extends ExplorerTestCase {
  public function testRenderHead() {
    ExplorerContext::getExplorer('Option')->render(
      'option', array('description' => 'option_description')
    );
    $this->assertOutput(
      '--option',
      '  option_description'
    );
  }

  public function testRenderShortList() {
    ExplorerContext::getExplorer('Option')->render(
      'option',
      array('short' => array('first_short_option', 'second_short_option'))
    );
    $this->assertOutput(
      '--option, -first_short_option, -second_short_option'
    );
  }

  public function testRenderShort() {
    ExplorerContext::getExplorer('Option')->render(
      'option', array('short' => 'short_option')
    );
    $this->assertOutput(
      '--option, -short_option'
    );
  }
}