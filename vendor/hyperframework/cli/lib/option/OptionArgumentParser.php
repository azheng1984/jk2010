<?php
class OptionArgumentParser {
  private $reader;

  public function __construct($reader) {
    $this->reader = $reader;
  }

  public function parse($standardLength) {
    $arguments = array();
    $count = 0;
    while ($count !== $standardLength) {
      $this->reader->moveToNext();
      $item = $this->reader->get();
      if ($item === null || ($item !== '-' && strpos($item, '-') === 0)) {
        $this->reader->moveToPrevious();
        break;
      }
      $arguments[] = $item;
      ++$count;
    }
    return $arguments;
  }
}