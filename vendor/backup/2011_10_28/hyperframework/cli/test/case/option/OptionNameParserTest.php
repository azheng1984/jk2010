<?php
class OptionNameParserTest extends PHPUnit_Framework_TestCase {
  public function testParseFullName() {
    $this->assertSame(
      'full_name_option', $this->parse('--full_name_option')
    );
  }

  public function testParseShort() {
    $this->assertSame(
      'test', $this->parse('-t', array('test' => array('short' => 't')))
    );
  }

  public function testParseShortWithAlias() {
    $this->assertSame(
      'test',
      $this->parse('-a', array('test' => array('short' => array('t', 'a'))))
    );
  }

  public function testShortNotAllowed() {
    $this->assertNull($this->parse('-t'));
  }

  public function testGroupedShorts() {
    $this->assertSame(array('-a', '-b'), $this->parse('-ab'));
  }

  private function parse($item, $config = array()) {
    $parser = new OptionNameParser($config);
    return $parser->parse($item);
  }
}