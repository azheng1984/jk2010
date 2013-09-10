<?php
class CommandReaderTest extends CliTestCase {
  public function testGetAfterLast() {
    $this->setInputArguments('test');
    $reader = new CommandReader;
    $reader->moveToNext();
    $this->assertNull($reader->get());
  }

  public function testGetBeforeFirst() {
    $this->setInputArguments('test');
    $reader = new CommandReader;
    $reader->moveToPrevious();
    $this->assertSame('test', $reader->get());
  }

  public function testExpand() {
    $this->setInputArguments('alias', 'argument');
    $reader = new CommandReader;
    $reader->expand(array('target', 'target_argument'));
    foreach (array('target', 'target_argument', 'argument') as $value) {
      $reader->moveToNext();
      $this->assertSame($value, $reader->get());
    }
  }
}