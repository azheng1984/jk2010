<?php
class OptionParserTest extends CliTestCase {
  public function testParseNotAllowedOption() {
    $item = '--test';
    $this->setExpectedCommandException("Option '$item' not allowed");
    $this->parse(null, array($item));
  }

  public function testParseCombinedShorts() {
    $item = '-ab';
    list($parser, $reader) = $this->getParser(null, array($item));
    $this->assertNull($parser->parse());
    foreach (array('a', 'b') as $short) {
      $reader->moveToNext();
      $this->assertEquals('-'.$short, $reader->get());
    }
  }

  public function testExpand() {
    list($parser, $reader) = $this->getParser(
      array('alias' => array('expansion' => 'target')), array('--alias')
    );
    $this->assertNull($parser->parse());
    $reader->moveToNext();
    $this->assertEquals('target', $reader->get());
  }

  public function testParseFlag() {
    list($name, $value) = $this->parse(
      array('test'), array('--test')
    );
    $this->assertTrue($value);
  }

  public function testParseObject() {
    list($name, $value) = $this->parse(
      array('test' => 'TestOption'), array('--test', 'argument')
    );
    $this->assertEquals('TestOption', get_class($value));
  }

  private function getParser($config, $arguments) {
    $_SERVER['argc'] = array_unshift($arguments, 'index.php');
    $_SERVER['argv'] = $arguments;
    $reader = new CommandReader;
    return array(new OptionParser($reader, $config), $reader);
  }

  private function parse($config, $arguments) {
    list($parser, $reader) = $this->getParser($config, $arguments);
    return $parser->parse();
  }
}