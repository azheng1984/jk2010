<?php
namespace Hyperframework\Tool\App;

class NewCommand {
    public function execute($type, $hyperframeworkPath = '~') {
        $config = \Hyperframework\ConfigLoader::load(__CLASS__ . '\ConfigPath', 'new/' .  $type);
        // CONFIG_PATH.'new'.DIRECTORY_SEPARATOR.$type.'.config.php';
//        if (!file_exists($config)) {
//            throw new CommandException("Application type '$type' is invalid");
//        }
        $this->initialize($hyperframeworkPath);
        $generator = new \Hyperframework\Tool\ScaffoldGenerator;
        try {
            $generator->generate($config);
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
