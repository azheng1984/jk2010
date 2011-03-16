<?php
class CommandCollection {
  private $indentation = 0;
  private $collections = array();
  private $commands = array();

  public function execute($config) {
    if (!isset($config['sub']) || !is_array($config['sub'])) {
      throw new CommandException('No subcommand in collection configuration');
    }
    $this->initialize($config);
    $this->renderCommand(null, $config);
    if (count($this->collections) !== 0) {
      $this->renderCollection();
    }
    if (count($this->commands) !== 0) {
      $this->renderCommandList();
    }
  }

  private function initialize($config) {
    foreach ($config['sub'] as $name => $item) {
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      if (isset($item['sub'])) {
        $this->collections[$name] = $item;
        continue;
      }
      $this->commands[$name] = $item;
    }
  }

  private function writeLine($value = null) {
    echo str_repeat('  ', $this->indentation), $value, PHP_EOL;
  }

  private function renderCollection() {
    $this->writeLine('[collection]');
    ++$this->indentation;
    foreach ($this->collections as $name => $config) {
      $this->renderCommand($name, $config);
    }
    --$this->indentation;
  }

  private function renderCommandList() {
    $this->writeLine('[command]');
    ++$this->indentation;
    foreach ($this->commands as $name => $config) {
      $this->renderCommand($name, $config);
    }
    --$this->indentation;
  }

  private function renderCommand($name, $config) {
    if ($name !== null) {
      $this->renderHeader($name, $config);
      ++$this->indentation;
    }
    if (isset($config['description'])) {
      $this->writeLine($config['description']);
      $this->writeLine();
    }
    if (isset($config['option'])) {
      $this->renderOptionList($config['option']);
    }
    if ($name !== null) {
      --$this->indentation;
    }
  }

  private function renderOptionList($config) {
    $this->writeLine('[option]');
    ++$this->indentation;
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        $name = $item;
        $item = array();
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      $this->renderOption($name, $item);
    }
    --$this->indentation;
  }

  private function renderOption($name, $config) {
    $name = "--".$name;
    $short = null;
    if (isset($config['short'])) {
      $short = $config['short'];
    }
    if (is_array($short)) {
      $short = implode(', -', $short);
    }
    if ($short !== null) {
      $short = ', -'.$short;
    }
    $this->renderHeader($name.$short, $config);
    if (isset($config['description'])) {
      ++$this->indentation;
      $this->writeLine($config['description']);
      $this->writeLine();
      --$this->indentation;
    }
  }

  private function renderHeader($name, $config) {
    if (!isset($config['class'])) {
      $this->writeLine($name);
      return;
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $args = $reflector->getParameters();
    $header = $name;
    if (count($args) !== 0) {
      $count = 0;
      $header .= '(';
      foreach ($args as $item) {
        $item->isOptional();
        $header .= $item->getName();
      }
      $header .= ')';
    }
    $this->writeLine($header);
  }
}