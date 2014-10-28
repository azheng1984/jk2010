<?php
namespace Hyperframework\Cli;

class OptionArgumentReader {
  private $reader;

  public function __construct($reader) {
    $this->reader = $reader;
  }

  public function read($maximumLength) {
    $arguments = array();
    for ($count = 0; $count !== $maximumLength; ++$count) {
      $this->reader->moveToNext();
      $item = $this->reader->get();
      if ($item === null) {
        break;
      }
      if (strpos($item, '-') === 0 && $item !== '-') {
        $this->reader->moveToPrevious();
        break;
      }
      $arguments[] = $item;
    }
    return $arguments;
  }
}
