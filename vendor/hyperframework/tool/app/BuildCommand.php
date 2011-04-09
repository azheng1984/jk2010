<?php
class BuildCommand {
  private $cacheFolder;

  public function execute() {
    $config = $this->getConfig();
    if (!is_array($config)) {
      $config = array($config);
    }
    foreach ($config as $name => $config) {
      $result = $this->dispatch($name, $config);
      if ($result !== null) {
        $this->export($result);
      }
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

  private function export($result) {
    list($name, $cache) = $result->export();
    file_put_contents(
      $this->getCachePath($name),
      '<?php'.PHP_EOL.'return '.var_export($cache, true).';'
    );
  }

  private function getCachePath($name) {
    if ($this->cacheFolder === null) {
        $this->cacheFolder = 'cache';
        $this->createCacheFolder();
    }
    return $this->cacheFolder.DIRECTORY_SEPARATOR.$name.'.cache.php';
  }

  private function createCacheFolder() {
    if (!file_exists($this->cacheFolder)) {
      mkdir($this->cacheFolder);
      chmod($this->cacheFolder, 0777);
    }
  }
}