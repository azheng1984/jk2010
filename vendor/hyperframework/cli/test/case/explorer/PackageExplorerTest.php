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

  public function testX() {
    $explorer = new PackageExplorer;
    $explorer->render(array('sub' => array('test' => 'TestCommand')));
    $this->expectOutput(
      '[command]',
      '  test(argument = NULL)'
    );
  }
}