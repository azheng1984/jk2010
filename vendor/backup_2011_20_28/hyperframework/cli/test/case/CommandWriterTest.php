<?php
class CommandWriterTest extends CliTestCase {
  private $writer;

  protected function setUp() {
    ob_start();
    $this->writer = new CommandWriter;
  }

  protected function tearDown() {
    ob_end_clean();
  }

  public function testIncreaseIndentation() {
    $this->writer->increaseIndentation();
    $this->writer->writeLine('');
    $this->assertOutput('  ');
  }

  public function testDecreaseIndentation() {
    $this->writer->increaseIndentation();
    $this->writer->decreaseIndentation();
    $this->writer->writeLine('');
    $this->assertOutput('');
  }

  public function testInsertEmptyLine() {
    $this->writer->writeLine();
    $this->writer->writeLine();
    $this->writer->writeLine('');
    $this->writer->writeLine();
    $this->assertOutput('', '');
  }

  public function testNegativeIndentation() {
    $this->setExpectedCommandException("Indentation '-1' is invalid");
    $this->writer->decreaseIndentation();
    $this->writer->writeLine('');
  }
}