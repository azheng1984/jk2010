<?php
class NewCommand {
  public function execute($type, $hyperframeworkPath = HYPERFRAMEWORK_PATH) {
    $configPath = CONFIG_PATH.'new/'.$type.'.config.php';
    if (!file_exists($configPath)) {
      throw new CommandException("Application type '$type' is invalid");
    }
    if (count(scandir($_SERVER['PWD'])) !== 2) {
      throw new CommandException('directory must empty');
    }
    $this->initialize($hyperframeworkPath);
    $generator = new ScaffoldGenerator;
    $generator->generate(require $configPath);
  }

  private function initialize($hyperframeworkPath) {
    $_ENV['class_loader_prefix'] = 'HYPERFRAMEWORK_PATH';
    if (strpos(HYPERFRAMEWORK_PATH, $_SERVER['PWD']) === 0) {
      $_ENV['class_loader_prefix'] = 'ROOT_PATH.'.$_ENV['class_loader_prefix'];
      $hyperframeworkPath = str_replace(
        $_SERVER['PWD'], '', $hyperframeworkPath
      );
    }
    $_ENV['hyperframework_path'] = var_export($hyperframeworkPath, true);
  }
}