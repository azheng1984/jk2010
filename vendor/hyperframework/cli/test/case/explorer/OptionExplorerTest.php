<?php
class OptionExplorerTest extends CliTestCase {
  protected function tearDown() {
    ExplorerContext::reset();
  }

  public function testRenderHead() {
    $this->expectOutput(
      '--test-option',
      '  test-option-description'
    );
    ExplorerContext::getExplorer('Option')->render('test-option', array(
      'description' => 'test-option-description',
    ));
  }

  public function testRenderShortList() {
    $this->expectOutput(
      '--test-option, -first-short-option, -second-short-option'
    );
    ExplorerContext::getExplorer('Option')->render('test-option', array(
      'short' => array('first-short-option', 'second-short-option'),
    ));
  }

  public function testRenderShort() {
    $this->expectOutput(
      '--test-option, -short-option'
    );
    ExplorerContext::getExplorer('Option')->render('test-option', array(
      'short' => 'short-option',
    ));
  }
}