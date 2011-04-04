<?php
class PackageExplorerTest extends OutputTestCase {
  /**
   * @expectedException CommandException
   * @expectedExceptionMessage No command in package
   */
  public function testEmptyPackage() {
    $explorer = new PackageExplorer;
    $explorer->render(array('sub' => null));
  }

  public function testEmptyList() {
    $explorer = new PackageExplorer;
    $explorer->render(array('sub' => array()));
    $this->expectOutputString('');
  }

  public function testRenderList() {
    $explorer = new PackageExplorer;
    $explorer->render(array(
      'description' => 'test-description',
      'sub' => array(
        'test-package' => array('sub' => array('option' => 'test-option')),
        'test-command' => 'TestCommand',
      )
    ));
    $this->expectOutput(
      'test-description',
      '',
      '[package]',
      '  test-package',
      '',
      '[command]',
      '  test-command(argument = NULL)'
    );
  }
}