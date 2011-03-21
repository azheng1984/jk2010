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
    $generator = new CacheGenerator;
    foreach (require $configPath as $name => $config) {
      $result = $this->dispatch($name, $config);
      if ($result !== null) {
        $generator->generate($result);
      }
    }
  }

  private function dispatch($name, $config) {
    if (is_int($name)) {
      $name = $config;
      $config = null;
    }
    $class = $name.'Builder';
    $builder = new $class($config);
    return $builder->build();
  }
}