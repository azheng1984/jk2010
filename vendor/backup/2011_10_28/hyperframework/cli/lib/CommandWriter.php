<?php
class CommandWriter {
  private $indentation = 0;
  private $isInsertEmptyLine = false;

  public function writeLine($value = null) {
    if ($value === null) {
      $this->isInsertEmptyLine = true;
      return;
    }
    if ($this->indentation < 0) {
      throw new CommandException("Indentation '$this->indentation' is invalid");
    }
    if ($this->isInsertEmptyLine) {
      $this->insertEmptyLine();
    }
    echo str_repeat('  ', $this->indentation), $value, PHP_EOL;
  }

  public function increaseIndentation() {
    ++$this->indentation;
  }

  public function decreaseIndentation() {
    --$this->indentation;
  }

  private function insertEmptyLine() {
    echo PHP_EOL;
    $this->isInsertEmptyLine = false;
    return;
  }
}