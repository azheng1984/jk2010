<?php
class OptionArgumentParser {
  private $reader;
  private $isLastCommand;

  public function __construct($reader, $isLastCommand) {
    $this->reader = $reader;
    $this->isLastCommand = $isLastCommand;
  }

  private function parse($standardLength) {
    $arguments = array();
    $count = 0;
    while (($item = $this->reader->read()) !== null) {
      if (strpos($item, '-') === 0 && $item !== '-') {
        $this->reader->move(-1);
        break;
      }
      $arguments[] = $item;
      ++$count;
      if ($count === $standardLength) {
        break;
      }
      $this->reader->move();
    }
    if ($standardLength !== null && $count > $standardLength) {
      return $this->cutArguments($arguments, $count, $standardLength);
    }
    return $arguments;
  }

  private function cutArguments($arguments, $amount, $standardLength) {
    if ($amount === $standardLength + 1 && !$this->isLastCommand) {
      array_pop($arguments);
      $this->reader->move(-1);
      return $arguments;
    }
    if ($this->reader->read() === null) {
      $arguments = array_slice($arguments, 0, $standardLength);
      $this->reader->move($standardLength - $amount);
    }
    return $arguments;
  }
}