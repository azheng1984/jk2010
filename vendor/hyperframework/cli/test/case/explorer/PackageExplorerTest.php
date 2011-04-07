<?php
class PackageExplorerTest extends ExplorerTestCase {
  public function testErrorSubConfig() {
    ExplorerContext::getExplorer('Package')->render(array('sub' => null));
    $this->assertOutput();
  }

  public function testRenderList() {
    ExplorerContext::getExplorer('Package')->render(
      array(
        'description' => 'test-description',
        'sub' => array(
          'test-package' => array(
            'option' => array('test-option'),
            'sub' => array(),
          ),
          'test-command' => null,
        )
      )
    );
    $this->assertOutput(
      'test-description',
      '',
      '[package]',
      '  test-package',
      '',
      '[command]',
      '  test-command'
    );
  }
}