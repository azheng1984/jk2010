<?php
class PackageExplorerTest extends OutputTestCase {
  private static $explorer;

  public static function setUpBeforeClass() {
    self::$explorer = new PackageExplorer;;
  }

  /**
   * @expectedException CommandException
   * @expectedExceptionMessage No command in package
   */
  public function testEmptyPackage() {
    self::$explorer->render(array('sub' => null));
  }

  public function testEmptyList() {
    self::$explorer->render(array('sub' => array()));
    $this->expectOutputString('');
  }

  public function testRenderList() {
    self::$explorer->render(array(
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