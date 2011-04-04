<?php
class PackageExplorerTest extends CliTestCase {
  private static $explorer;

  public static function setUpBeforeClass() {
    self::$explorer = new PackageExplorer;;
  }

  public function testErrorConfig() {
    $this->setExpectedCommandException('No command in package');
    $this->expectOutputString('');
    self::$explorer->render(array('sub' => 'test'));
  }

  public function testEmptyList() {
    $this->expectOutputString('');
    self::$explorer->render(array('sub' => array()));
  }

  public function testRenderList() {
    $this->expectOutput(
      'test-description',
      '',
      '[package]',
      '  test-package',
      '',
      '[command]',
      '  test-command(argument = NULL)'
    );
    self::$explorer->render(array(
      'description' => 'test-description',
      'sub' => array(
        'test-package' => array('sub' => array('option' => 'test-option')),
        'test-command' => 'TestCommand',
      )
    ));
  }
}