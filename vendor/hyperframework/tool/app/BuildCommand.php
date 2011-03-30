<?php
class BuildCommand {
  public function execute() {
    foreach ($this->getConfig() as $name => $config) {
      $result = $this->dispatch($name, $config);
      if ($result !== null) {
        $this->export($result);
      }
    }
  }

  private function getConfig() {
    $configPath = (
      $_SERVER['PWD'].DIRECTORY_SEPARATOR
      .'config'.DIRECTORY_SEPARATOR.'build.config.php'
    );
    if (!file_exists($configPath)) {
      throw new CommandException(
        "can't find the 'config".DIRECTORY_SEPARATOR."build.config.php'"
      );
    }
    return require $configPath;
  }

  private function dispatch($name, $config) {
    if (is_int($name)) {
      list($name, $config) = array($config, null);
    }
    $class = $name.'Builder';
    $builder = new $class;
    try {
      return $builder->build($config);
    } catch (Exception $exception) {
      throw new CommandException($exception->getMessage());
    }
  }

  private function export($result) {
    if (!is_dir('cache')) {
      mkdir('cache');
    }
    list($name, $cache) = $result->export();
    $path = 'cache'.DIRECTORY_SEPARATOR.$name.'.cache.php';
    file_put_contents(
      $path, '<?php'.PHP_EOL.'return '.var_export($cache, true).';'
    );
  }
}