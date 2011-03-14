<?php
class CommandCollection {
  public function render($config) {
    if (isset($config['description'])) {
      echo $config['description'].PHP_EOL;
    }
    if (isset($config['option'])) {
      $this->renderOption($config['option']);
    }
    if (isset($config['sub'])) {
      $commands = array();
      $indexes = array();
      foreach ($config['sub'] as $name => $item) {
        if (is_array($item) && isset($item['sub'])) {
          $indexes[$name] = $item;
        } else {
          $commands[$name] = $item;
        }
      }
      if (count($indexes) !== 0) {
        echo PHP_EOL.'[collection]'.PHP_EOL;
        foreach ($indexes as $name => $item) {
          echo "  ".$name;
          if (isset($item['description'])) {
            echo PHP_EOL.'    '.$item['description'].PHP_EOL;
          }
          echo PHP_EOL;
        }
      }
      if (count($commands) !== 0) {
        echo PHP_EOL.'[command]'.PHP_EOL;
        foreach ($commands as $name => $item) {
          echo $name;
          if (isset($item['description'])) {
            echo PHP_EOL.'  '.$item['description'].PHP_EOL;
          }
          if (is_array($item) && isset($item['option'])) {
            $this->renderOption($item['option']);
          }
          echo PHP_EOL;
        }
      }
    }
  }

  private function renderOption($config) {
    echo PHP_EOL.'  [option]'.PHP_EOL;
    foreach ($config as $name => $item) {
      echo "  --".$name;
      if (isset($item['short'])) {
        if (is_array($item['short'])) {
          $item['short'] = implode(', -', $item['short']);
        }
        echo ', -'.$item['short'];
        if (isset($item['description'])) {
          echo PHP_EOL.'    '.$item['description'].PHP_EOL;
        }
      }
      echo ''.PHP_EOL;
    }
  }
}