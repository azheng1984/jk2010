<?php
class PackageExplorerTest extends CliTestCase {
  protected function tearDown() {
    ExplorerContext::reset();
  }

  public function testErrorSubConfig() {
    $this->expectOutputString('');
    ExplorerContext::getExplorer('Package')->render(array('sub' => null));
  }

  public function testRenderList() {
    $this->expectOutput(
      'test-description',
      '',
      '[package]',
      '  test-package',
      '',
      '[command]',
      '  test-command'
    );
    ExplorerContext::getExplorer('Package')->render(array(
      'description' => 'test-description',
      'sub' => array(
        'test-package' => array(
          'option' => array('test-option'),
          'sub' => array(),
        ),
        'test-command' => null,
      )
    ));
  }
}