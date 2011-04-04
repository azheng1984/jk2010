<?php
class PackageExplorerTest extends CliTestCase {
  private static $explorer;

  public static function setUpBeforeClass() {
    self::$explorer = new PackageExplorer;
  }

  public static function tearDownAfterClass() {
    ExplorerContext::reset();
  }

  public function testErrorSubConfig() {
    $this->expectOutputString('');
    self::$explorer->render(array('sub' => null));
  }

  public function testRenderList() {
    $this->expectOutput(
      'test-description',
      '',
      '[package]',
      '  test-package',
      '    test-package-description',
      '',
      '[command]',
      '  test-command(argument = NULL)'
    );
    self::$explorer->render(array(
      'description' => 'test-description',
      'sub' => array(
        'test-package' => array(
          'description' => 'test-package-description',
          'option' => array('test-option'),
          'sub' => array(),
        ),
        'test-command' => 'TestCommand',
      )
    ));
  }
}