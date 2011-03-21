<?php
class BuildCommand {
  private $config;

  public function execute() {
    $configPath = (
      getcwd().DIRECTORY_SEPARATOR.
      'config'.DIRECTORY_SEPARATOR.'build.config.php'
    );
    if (!file_exists($configPath)) {
      throw new CommandException(
        "can't find the 'config".DIRECTORY_SEPARATOR."build.config.php'"
      );
    }
    foreach (require $configPath as $name => $config) {
      $this->dispatch($name, $config);
    }
  }

  private function dispatch($name, $config) {
    if (is_int($name)) {
      $name = $config;
      $config = null;
    }
    $class = $name.'Builder';
    $builder = new $class($config);
    $builder->build();
  }
}