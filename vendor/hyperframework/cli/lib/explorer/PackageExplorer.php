<?php
class PackageExplorer {
  public function __construct() {
    $_ENV['writer'] = new CommandWriter;
    $_ENV['rendering_proxy'] = new ExplorerRenderingProxy;
  }

  public function render($config) {
    if (!is_array($config['sub'])) {
      $config['sub'] = array();
    }
    $_ENV['rendering_proxy']->render('Command', array(null, $config));
    foreach ($this->getList($config['sub']) as $type => $values) {
      if (count($values) !== 0) {
        $this->renderList($type, $values);
      }
    }
  }

  private function getList($subConfig) {
    $result = array('package' => array(), 'command' => array());
    foreach ($subConfig as $name => $item) {
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
    $writer = $_ENV['writer'];
    $writer->writeLine("[$type]");
    $writer->increaseIndentation();
    foreach ($values as $name => $config) {
      $_ENV['rendering_proxy']->render('Command', array($name, $config));
    }
    $writer->decreaseIndentation();
    $writer->writeLine();
  }
}