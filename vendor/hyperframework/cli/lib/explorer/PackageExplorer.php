<?php
class PackageExplorer {
  public function render($config) {
    ExplorerContext::reset();
    if (!is_array($config['sub'])) {
      $config['sub'] = array();
    }
    ExplorerContext::getExplorer('Command')->render(null, $config);
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
    $writer = ExplorerContext::getWriter();
    $writer->writeLine("[$type]");
    $writer->increaseIndentation();
    foreach ($values as $name => $config) {
      ExplorerContext::getExplorer('Command')->render($name, $config);
    }
    $writer->decreaseIndentation();
    $writer->writeLine();
  }
}