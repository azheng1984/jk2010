<?php
class NewCommand {
  public function execute($type, $hyperframeworkPath = HYPERFRAMEWORK_PATH) {
    $configPath = CONFIG_PATH.'new/'.$type.'.config.php';
    if (!file_exists($configPath)) {
      throw new CommandException("Application type '$type' is invalide");
    }
    if (count(scandir(getcwd())) !== 2) {
      throw new CommandException('directory must empty');
    }
    $this->setPath($hyperframeworkPath);
    $config = require $configPath;
    foreach ($config as $path => $content) {
      if (is_int($path)) {
        list($path, $content) = array($content, null);
      }
      if (substr($path, -1) === '/') {
        $this->generateDirectory($path, $content);
        continue;
      }
      $this->generateFile($path, $content);
    }
  }

  private function setPath($hyperframeworkPath) {
    $_ENV['class_loader_prefix'] = 'HYPERFRAMEWORK_PATH';
    if (strpos(HYPERFRAMEWORK_PATH, getcwd()) === 0) {
      $_ENV['class_loader_prefix'] = 'ROOT_PATH.'.$_ENV['class_loader_prefix'];
      $hyperframeworkPath = str_replace(getcwd(), '', $hyperframeworkPath);
    }
    $_ENV['hyperframework_path'] = var_export($hyperframeworkPath, true);
  }

  private function generateFile($path, $content) {
    $this->generateDirectory(dirname($path));
    $mode = null;
    if (isset($content[0]) && is_int($content[0])) {
      $mode = array_shift($content);
    }
    $output = null;
    if ($content !== null) {
      $output = implode(PHP_EOL, $content);
    }
    file_put_contents($path, $output);
    if ($mode !== null) {
      chmod($path, $mode);
    }
  }

  private function generateDirectory($path, $mode = null) {
    if ($mode === null) {
      $mode = 0755;
    }
    if (!is_dir($path)) {
      $mask = umask(0);
      mkdir($path, $mode, true);
      umask($mask);
    }
  }
}