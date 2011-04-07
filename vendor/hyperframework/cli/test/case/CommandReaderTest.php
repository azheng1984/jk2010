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
    $this->assertEquals('test', $reader->get());
  }

  public function testExpand() {
    $this->setInputArguments('alias', 'argument');
    $reader = new CommandReader;
    $reader->expand(array('target', 'target_argument'));
    $reader->moveToNext();
    $this->assertEquals('target', $reader->get());
    $reader->moveToNext();
    $this->assertEquals('target_argument', $reader->get());
    $reader->moveToNext();
    $this->assertEquals('argument', $reader->get());
  }
}