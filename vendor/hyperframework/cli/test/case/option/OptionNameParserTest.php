<?php
class OptionNameParserTest extends PHPUnit_Framework_TestCase {
  public function testParseFullName() {
    $this->assertEquals(
      'full_name_option', $this->parse('--full_name_option')
    );
  }

  public function testParseShort() {
    $this->assertEquals(
      'test', $this->parse('-t', array('test' => array('short' => 't')))
    );
  }

  public function testParseShortWithAlias() {
    $this->assertEquals(
      'test',
      $this->parse('-a', array('test' => array('short' => array('t', 'a'))))
    );
  }

  public function testShortNotAllowed() {
    $this->assertNull($this->parse('-t'));
  }

  public function testGroupedShorts() {
    $this->assertEquals(array('-a', '-b'), $this->parse('-ab'));
  }

  private function parse($item, $config = array()) {
    $parser = new OptionNameParser($config);
    return $parser->parse($item);
  }
}