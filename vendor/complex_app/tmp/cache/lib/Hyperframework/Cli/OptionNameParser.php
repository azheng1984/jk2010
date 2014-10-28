<?php
namespace Hyperframework\Cli;

class OptionNameParser {
  private $shorts = array();

  public function __construct($config) {
    foreach ($config as $key => $value) {
      if (!isset($value['short'])) {
        continue;
      }
      if (!is_array($value['short'])) {
        $this->shorts[$value['short']] = $key;
        continue;
      }
      foreach ($value['short'] as $item) {
        $this->shorts[$item] = $key;
      }
    }
  }

  public function parse($item) {
    if (strpos($item, '--') === 0) {
      return substr($item, 2);
    }
    $shorts = substr($item, 1);
    if (strlen($shorts) === 1) {
      return $this->getFullName($shorts);
    }
    $options = array();
    foreach (str_split($shorts) as $item) {
      $options[] = '-'.$item;
    }
    return $options;
  }

  private function getFullName($short) {
    if (!isset($this->shorts[$short])) {
      return;
    }
    return $this->shorts[$short];
  }
}
