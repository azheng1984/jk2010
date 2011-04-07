<?php
class OptionArgumentParserTest extends CliTestCase {
  public function testNullMaximumLength() {
    $this->setInputArguments('argument');
    $this->assertEquals(array('argument'), $this->parse(null));
  }

  public function testParseUntilMaximumLength() {
    $this->setInputArguments('first_argument', 'second_argument');
    $this->assertEquals(array('first_argument'), $this->parse(1));
  }

  public function testParseUntilAnotherOption() {
    $this->setInputArguments('argument', '--option');
    $this->assertEquals(array('argument'), $this->parse(1));
  }

  public function testParseUntilEndOfInput() {
    $this->setInputArguments('argument');
    $this->assertEquals(array('argument'), $this->parse(2));
  }

  private function parse($maximumLength) {
    $parser = new OptionArgumentParser(new CommandReader);
    return $parser->parse($maximumLength);
  }
}