<?php
class OptionParserTest extends CliTestCase {
  public function testGroupedShorts() {
    $this->setInputArguments('-ab');
    list($parser, $reader) = $this->getOptionParser();
    $this->assertNull($parser->parse());
    foreach (array('a', 'b') as $short) {
      $reader->moveToNext();
      $this->assertEquals('-'.$short, $reader->get());
    }
  }

  public function testExpansion() {
    $this->setInputArguments('--alias');
    list($parser, $reader) = $this->getOptionParser(
      array('alias' => array('expansion' => 'target'))
    );
    $this->assertNull($parser->parse());
    $reader->moveToNext();
    $this->assertEquals('target', $reader->get());
  }

  public function testNotAllowedOption() {
    $item = '--test';
    $this->setExpectedCommandException("Option '$item' not allowed");
    $this->setInputArguments('--test');
    $this->parse();
  }

  public function testFlagOption() {
    $this->setInputArguments('--test');
    list($name, $value) = $this->parse(array('test'));
    $this->assertTrue($value);
  }

  public function testObjectOption() {
    $this->setInputArguments('--test', 'argument');
    list($name, $value) = $this->parse(array('test' => 'TestOption'));
    $this->assertEquals('TestOption', get_class($value));
  }

  public function testRethrowObjectBuildException() {
    $item = '--test';
    $this->setExpectedCommandException(
      "Option '$item':Argument length error(Expected:1 Actual:0)"
    );
    $this->setInputArguments($item);
    $this->parse(array('test' => 'TestOption'));
  }

  private function getOptionParser($config = null) {
    $reader = new CommandReader;
    return array(new OptionParser($reader, $config), $reader);
  }

  private function parse($config = null) {
    list($parser, $reader) = $this->getOptionParser($config);
    return $parser->parse();
  }
}