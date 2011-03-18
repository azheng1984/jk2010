<?php
class PackageView {
  private $writter;
  private $commandView;

  public function __construct() {
    $this->writter = $_ENV['command_writer'];
    $this->commandView = new CommandView;
  }

  public function render($config) {
    if (!isset($config['sub']) || !is_array($config['sub'])) {
      throw new CommandException('No subcommand in the package');
    }
    $this->commandView->render(null, $config);
    $packages = array();
    $commands = array();
    foreach ($config['sub'] as $name => $item) {
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      if (isset($item['sub'])) {
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

  private function renderCommandList($name, $values) {
    $this->writter->writeLine("[$name]");
    $this->writter->indent();
    foreach ($values as $name => $config) {
      $this->commandView->render($name, $config);
    }
    $this->writter->indent(false);
  }
}