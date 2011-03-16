<?php
class CommandBundle {
  public function render($config) {
    if (isset($config['description'])) {
      echo $config['description'].PHP_EOL.PHP_EOL;
    }
    if (isset($config['option'])) {
      $this->renderOption($config['option'], false);
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
        echo '[bundle]'.PHP_EOL;
        foreach ($indexes as $name => $item) {
          echo '  '.$name.PHP_EOL;
          if (isset($item['description'])) {
            echo '    '.$item['description'].PHP_EOL.PHP_EOL;
          }
        }
      }
      if (count($commands) !== 0) {
        echo '[command]'.PHP_EOL;
        foreach ($commands as $name => $item) {
          echo '  '.$name;
          if (is_array($item) && isset($item['class'])) {
            $this->renderArgument($item['class']);
          } else {
            $this->renderArgument($item);
          }
          echo PHP_EOL;
          if (is_array($item)) {
            if (isset($item['description'])) {
              echo '    '.$item['description'].PHP_EOL.PHP_EOL;
            }
            if (isset($item['option'])) {
              $this->renderOption($item['option'], true);
            }
          }
        }
      }
    } else {
      if (is_array($config) && isset($config['class'])) {
        $this->renderArgument($config['class']);
      } else {
        $this->renderArgument($config);
      }
    }
  }

  private function renderOption($config, $isSubCommand) {
    $prefix = $isSubCommand ? '    ' : '';
    echo $prefix.'[option]'.PHP_EOL;
    foreach ($config as $name => $item) {
      echo "$prefix  --".$name;
      if (isset($item['short'])) {
        if (is_array($item['short'])) {
          $item['short'] = implode(', -', $item['short']);
        }
        echo ', -'.$item['short'].PHP_EOL;
        if (isset($item['description'])) {
          echo $prefix.'    '.$item['description'].PHP_EOL;
        }
        echo PHP_EOL;
      }
    }
  }

  private function renderArgument($class) {
    $reflector = new ReflectionMethod($class, 'execute');
    $args = $reflector->getParameters();
    if (count($args) !== 0) {
      $count = 0;
      echo '(';
      foreach ($args as $item) {
        $item->isOptional();
        echo $item->getName();
      }
      echo ')';
    }
  }
}