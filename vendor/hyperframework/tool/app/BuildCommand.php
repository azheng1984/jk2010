<?php
class BuildCommand {
  public function execute() {
    $config = $this->getConfig();
    if (!is_array($config)) {
      $config = array($config);
    }
    $exporter = new CacheExporter;
    foreach ($config as $name => $config) {
      $exporter->export($this->dispatch($name, $config));
    }
  }

  private function getConfig() {
    $path = $_SERVER['PWD'].DIRECTORY_SEPARATOR
      .'config'.DIRECTORY_SEPARATOR.'build.config.php';
    if (!file_exists($path)) {
      throw new CommandException(
        "Can't find the 'config".DIRECTORY_SEPARATOR."build.config.php'"
      );
    }
    return require $path;
  }

  private function dispatch($name, $config) {
    if (is_int($name)) {
      list($name, $config) = array($config, null);
    }
    try {
      $reflector = new ReflectionClass($name.'Builder');
      $builder = $reflector->newInstance();
      return $builder->build($config);
    } catch (Exception $exception) {
      throw new CommandException($exception->getMessage());
    }
  }
}