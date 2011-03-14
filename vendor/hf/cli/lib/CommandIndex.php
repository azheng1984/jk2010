<?php
class CommandIndex {
  public function render($config) {
    if (isset($config['description'])) {
      echo $config['description'].PHP_EOL;
    }
    if (isset($config['option'])) {
      echo PHP_EOL.'[option]'.PHP_EOL;
      foreach ($config['option'] as $name => $item) {
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
        echo PHP_EOL;
      }
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
        echo PHP_EOL.'[index]'.PHP_EOL;
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
          echo "  ".$name;
          if (isset($item['description'])) {
            echo PHP_EOL.'    '.$item['description'].PHP_EOL;
          }
          echo PHP_EOL;
        }
      }
    }
  }
}