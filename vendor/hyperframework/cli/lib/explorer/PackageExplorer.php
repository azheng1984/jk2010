<?php
class PackageExplorer {
  private $commandExplorer;
  private $writer;

  public function __construct() {
    $this->commandExplorer = new CommandExplorer;
  }

  public function render($config, $writer) {
    $this->writer = $writer;
    if (!isset($config['sub']) || !is_array($config['sub'])) {
      throw new CommandException('No subcommand in the package');
    }
    $this->commandExplorer->render(null, $config, $writer);
    $packages = array();
    $commands = array();
    foreach ($config['sub'] as $name => $item) {
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      if (isset($item['sub'])) {
        unset($item['option']);
        $packages[$name] = $item;
        continue;
      }
      $commands[$name] = $item;
    }
    if (count($packages) !== 0) {
      $this->renderCommandList('package', $packages);
    }
    if (count($commands) !== 0) {
      $this->renderCommandList('command', $commands);
    }
  }

  private function renderCommandList($type, $values) {
    $this->writer->writeLine("[$type]");
    $this->writer->increaseIndentation();
    foreach ($values as $name => $config) {
      $this->commandExplorer->render($name, $config, $this->writer);
    }
    $this->writer->decreaseIndentation();
  }
}