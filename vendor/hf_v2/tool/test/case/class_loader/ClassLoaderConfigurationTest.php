<?php
class ClassLoaderConfigurationTest extends PHPUnit_Framework_TestCase {
  public function testRelativePath() {
    $this->extract('first_level', array(array(null, 'first_level')));
  }

  public function testUnixFullPath() {
    $this->extract(
      DIRECTORY_SEPARATOR.'first_level',
      array(array(DIRECTORY_SEPARATOR.'first_level', null))
    );
  }

  public function testWindowsFullPath() {
    if (DIRECTORY_SEPARATOR === '\\') {
      $this->extract('c:\\', array(array('c:\\', null)));
    }
  }

  public function testSecondLevelPath() {
    $this->extract(
      array('first_level' => 'second_level'),
      array(array(null, 'first_level'.DIRECTORY_SEPARATOR.'second_level'))
    );
  }

  public function testFirstLevelPathList() {
    $this->extract(
      array('first', 'second'),
      array(array(null, 'first'), array(null, 'second'))
    );
  }

  public function testSecondLevelPathList() {
    $this->extract(
      array(
        'first_level' => array('second_level_first', 'second_level_second')
      ),
      array(
        array(null, 'first_level'.DIRECTORY_SEPARATOR.'second_level_first'),
        array(null, 'first_level'.DIRECTORY_SEPARATOR.'second_level_second')
      )
    );
  }

  private function extract($config, $expected) {
    $configration = new ClassLoaderConfiguration;
    $this->assertSame($expected, $configration->extract($config));
  }

  private function getErrorSeparator() {
    return DIRECTORY_SEPARATOR === '/' ? '\\' : '/';
  }
}