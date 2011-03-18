<?php
class CommandWriter {
  private $indentation = 0;

  public function writeLine($value = null) {
    if ($value === null) {
      echo PHP_EOL;
      return;
    }
    if ($this->indentation < 0) {
      throw new CommandException('indentation is negative');
    }
    echo str_repeat('  ', $this->indentation), $value, PHP_EOL;
  }

  public function indent($isForward = true) {
    $this->indentation += $isForward ? 1 : -1;
  }
}