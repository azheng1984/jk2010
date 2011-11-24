<?php
class PackageExplorerTest extends ExplorerTestCase {
  public function testErrorSubConfig() {
    ExplorerContext::getExplorer('Package')->render(array('sub' => null));
    $this->assertOutput();
  }

  public function testRenderList() {
    ExplorerContext::getExplorer('Package')->render(
      array(
        'description' => 'description',
        'sub' => array(
          'package_name' => array(
            'option' => array('option_name'),
            'sub' => array(),
          ),
          'command_name' => null,
        )
      )
    );
    $this->assertOutput(
      'description',
      '',
      '[package]',
      '  package_name',
      '',
      '[command]',
      '  command_name'
    );
  }
}