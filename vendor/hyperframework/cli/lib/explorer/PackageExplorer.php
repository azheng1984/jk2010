<?php
class PackageExplorer {
  private $writer;
  private $commandExplorer;

  public function render($config, $writer) {
    $this->writer = $writer;
    $this->commandExplorer = new CommandExplorer;
    if (!isset($config['sub']) || !is_array($config['sub'])) {
      throw new CommandException('No subcommand in the package');
    }
    $this->commandExplorer->render(null, $config, $writer);
    foreach ($this->getList($config) as $type => $values) {
      if (count($values) !== 0) {
        $this->renderList($type, $values);
      }
    }
  }

  private function getList($config) {
    $result = array('package' => array(), 'command' => array());
    foreach ($config['sub'] as $name => $item) {
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      if (isset($item['sub'])) {
        unset($item['option']);
        $result['package'][$name] = $item;
        continue;
      }
      $result['command'][$name] = $item;
    }
    return $result;
  }

  private function renderList($type, $values) {
    $this->writer->writeLine("[$type]");
    $this->writer->increaseIndentation();
    foreach ($values as $name => $config) {
      $this->commandExplorer->render($name, $config, $this->writer);
    }
    $this->writer->decreaseIndentation();
  }
}