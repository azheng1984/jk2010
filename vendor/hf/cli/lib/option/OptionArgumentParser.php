<?php
class OptionArgumentParser {
  private $reader;

  public function __construct($reader) {
    $this->reader = $reader;
  }

  private function parse($standardLength) {
    $arguments = array();
    $count = 0;
    while ($count !== $standardLength) {
      $item = $this->reader->move()->read();
      if ($item === null || ($item !== '-' && strpos($item, '-') === 0)) {
        $this->reader->move(-1);
        break;
      }
      $arguments[] = $item;
      ++$count;
    }
    return $arguments;
  }
}