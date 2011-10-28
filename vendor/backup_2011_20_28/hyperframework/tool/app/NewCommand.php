<?php
class NewCommand {
  public function execute($type, $hyperframeworkPath = HYPERFRAMEWORK_PATH) {
    $configPath = CONFIG_PATH.'new'.DIRECTORY_SEPARATOR.$type.'.config.php';
    if (!file_exists($configPath)) {
      throw new CommandException("Application type '$type' is invalid");
    }
    $this->initialize($hyperframeworkPath);
    $generator = new ScaffoldGenerator;
    try {
      $generator->generate(require $configPath);
    } catch (Exception $exception) {
      throw new CommandException($exception->getMessage());
    }
  }

  private function initialize($hyperframeworkPath) {
    if (strpos($hyperframeworkPath, $_SERVER['PWD']) === 0) {
      $GLOBALS['HYPERFRAMEWORK_PATH'] = 'ROOT_PATH.'.var_export(
        str_replace(
          $_SERVER['PWD'].DIRECTORY_SEPARATOR, '', $hyperframeworkPath
        ),
        true
      );
      $GLOBALS['CLASS_LOADER_PREFIX'] = 'ROOT_PATH.HYPERFRAMEWORK_PATH';
      return;
    }
    $GLOBALS['HYPERFRAMEWORK_PATH'] = var_export($hyperframeworkPath, true);
    $GLOBALS['CLASS_LOADER_PREFIX'] = 'HYPERFRAMEWORK_PATH';
  }
}