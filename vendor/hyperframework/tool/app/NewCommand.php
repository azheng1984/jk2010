<?php
class NewCommand {
  public function execute($type, $hyperframeworkPath = HYPERFRAMEWORK_PATH) {
    $configPath = CONFIG_PATH.'new/'.$type.'.config.php';
    if (!file_exists($configPath)) {
      throw new CommandException("Application type '$type' is invalide");
    }
    if (count(scandir($_SERVER['PWD'])) !== 2) {
      throw new CommandException('directory must empty');
    }
    $this->setEnvironment($hyperframeworkPath);
    $generator = new FileGenerator();
    $config = require $configPath;
    foreach ($config as $path => $content) {
      if (is_int($path)) {
        list($path, $content) = array($content, null);
      }
      if (substr($path, -1) === '/') {
        $generator->generateDirectory($path, $content);
        continue;
      }
      $generator->generateFile($path, $content);
    }
  }

  private function setEnvironment($hyperframeworkPath) {
    $_ENV['class_loader_prefix'] = 'HYPERFRAMEWORK_PATH';
    if (strpos(HYPERFRAMEWORK_PATH, $_SERVER['PWD']) === 0) {
      $_ENV['class_loader_prefix'] = 'ROOT_PATH.'.$_ENV['class_loader_prefix'];
      $hyperframeworkPath = str_replace($_SERVER['PWD'], '', $hyperframeworkPath);
    }
    $_ENV['hyperframework_path'] = var_export($hyperframeworkPath, true);
  }
}