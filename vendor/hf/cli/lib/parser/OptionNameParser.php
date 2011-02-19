<?php

class OptionNameParser {
  private function getName($item) {
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
      throw new Exception("Option '$short' not allowed");
    }
    return $this->shorts[$short];
  }
}

?>