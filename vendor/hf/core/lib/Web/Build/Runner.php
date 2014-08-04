<?php
namespace Hyperframework\Web\Build;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\Cli\ExceptionHandler;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        //path_info cache
        //preprocess composer class_loader cache
        //generate asset cache
        self::buildPathInfoCache('App');
        self::buildPathInfoCache('ErrorApp');
    }

    private static function buildPathInfoCache($type) {
        $root = Hyeprframework\APP_ROOT_PATH
            . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $type;
        $pathInfo = array();

        $scanner = new DirectoryScanner(function($path) use (&$pathInfo){
            $name = basename($path, $relativePath);
            if ($name === 'Action') {
                ActionInfoBuilder::($relativePath, $pathInfo[$path]);
            } else {
                ViewInfoBuilder::($relativePath, $pathInfo[$path]);
            }
        }, function($path, $relativePath) use (&$pathInfo) {
            $name = basename($path);
            if ($name === 'Action') {
            } else {
            }
        });
        $scanner->run();
    }

    private static function get($path, $folder, &$pathInfo) {
        foreach(scandir($folder) as $entry) {
            if ($entry === '.'
                || $entry === '..'
            ) {
                continue;
            }
            if (is_dir($folder . DIRECTORY_SEPARATOR . $entry)) {
                self::get($folder);
            }
            $name = ClassRecognizer::getName($entry);
            if ($name === null) {
                continue;
            }
            if ($name === 'Action') {
                ActionInfoBuilder::run($namespace . '\\' . $name, $pathInfo);
            } else {
                $viewTypes[] = $name;
            }
        }
        if (count($viewTypes) !== 0) {
            $viewOrder = null;
            if (isset($options['view_order']) !== false) {
                $viewOrder = $options['view_order'];
            }
            ViewInfoBuilder::run(
                $namespace, $viewTypes, $viewOrder, $pathInfo
            );
        }
        $pathInfo['namespace'] = $namespace;
    }

    private static function convertToPath($namespace) {
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ExceptionHandler::run();
    }
}
