<?php
class OptionArgumentParser {
  private $reader;
  private $isAfterLeafCommand;

  public function __construct($reader, $isAfterLeafCommand) {
    $this->reader = $reader;
    $this->isAfterLeafCommand = $isAfterLeafCommand;
  }

  private function parse($standardLength) {
    $arguments = array();
    while (($item = $this->reader->read()) !== null) {
      if (strpos($item, '-') === 0 && $item !== '-') {
        $this->reader->move(-1);
        break;
      }
      $arguments[] = $item;
      $this->reader->move();
    }
    $amount = count($arguments);
    if ($amount > $standardLength && $standardLength !== null) {
      return $this->cutArguments($arguments, $amount, $standardLength);
    }
    return $arguments;
  }

  private function cutArguments($arguments, $amount, $standardLength) {
    if ($amount === $standardLength + 1 && !$this->isAfterLeafCommand) {
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