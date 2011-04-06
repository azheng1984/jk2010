<?php
class OptionExplorerTest extends ExplorerTestCase {
  public function testRenderHead() {
    ExplorerContext::getExplorer('Option')->render('test-option', array(
      'description' => 'test-option-description',
    ));
    $this->assertOutput(
      '--test-option',
      '  test-option-description'
    );
  }

  public function testRenderShortList() {
    ExplorerContext::getExplorer('Option')->render('test-option', array(
      'short' => array('first-short-option', 'second-short-option'),
    ));
    $this->assertOutput(
      '--test-option, -first-short-option, -second-short-option'
    );
  }

  public function testRenderShort() {
    ExplorerContext::getExplorer('Option')->render('test-option', array(
      'short' => 'short-option',
    ));
    $this->assertOutput(
      '--test-option, -short-option'
    );
  }
}