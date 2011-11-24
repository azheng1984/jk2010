<?php
class OptionArgumentReaderTest extends CliTestCase {
  public function testNullMaximumLength() {
    $this->setInputArguments('--option', 'argument');
    $this->assertSame(array('argument'), $this->parse(null));
  }

  public function testParseUntilMaximumLength() {
    $this->setInputArguments('--option', 'first_argument', 'second_argument');
    $this->assertSame(array('first_argument'), $this->parse(1));
  }

  public function testParseUntilAnotherOption() {
    $this->setInputArguments('--option', 'argument', '--another_option');
    $this->assertSame(array('argument'), $this->parse(1));
  }

  public function testParseUntilEndOfInput() {
    $this->setInputArguments('--option', 'argument');
    $this->assertSame(array('argument'), $this->parse(2));
  }

  private function parse($maximumLength) {
    $reader = new OptionArgumentReader(new CommandReader);
    return $reader->read($maximumLength);
  }
}