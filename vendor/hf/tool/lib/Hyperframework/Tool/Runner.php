<?php
namespace Hyperframework\Tool;

class Runner {
    public static function run() {
        $hyperframeworkPath = dirname(ROOT_PATH) . DIRECTORY_SEPARATOR . 'lib' .
            DIRECTORY_SEPARATOR . 'Hyperframework' . DIRECTORY_SEPARATOR;
        require $hyperframeworkPath . 'Config.php';
        \Hyperframework\Config::set('Hyperframework\AppPath', ROOT_PATH);
        require $hyperframeworkPath . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        \Hyperframework\Cli\ExceptionHandler::run();
        \Hyperframework\Cli\Application::run();
    }
}
