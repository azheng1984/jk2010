<?php
class BuildCommand {
  private $config;

  public function execute() {
    $configPath = (
      getcwd().DIRECTORY_SEPARATOR
      .'config'.DIRECTORY_SEPARATOR.'build.config.php'
    );
    if (!file_exists($configPath)) {
      throw new CommandException(
        "can't find the 'config".DIRECTORY_SEPARATOR."build.config.php'"
      );
    }
    foreach (require $configPath as $name => $config) {
      try {
       $result = $this->dispatch($name, $config);
      } catch (Exception $exception) {
        throw new CommandException($exception->getMessage());
      }
      if ($result !== null) {
        $this->export($result);
      }
    }
  }

  private function dispatch($name, $config) {
    if (is_int($name)) {
      list($name, $config) = array($config, null);
    }
    $class = $name.'Builder';
    $builder = new $class;
    return $builder->build($config);
  }

  private function export($result) {
    list($name, $cache) = $result->export();
    $path = 'cache'.DIRECTORY_SEPARATOR.$name.'.cache.php';
    file_put_contents(
      $path, '<?php'.PHP_EOL.'return '.var_export($cache, true).';'
    );
  }
}